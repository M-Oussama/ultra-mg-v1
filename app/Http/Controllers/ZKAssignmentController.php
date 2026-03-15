<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ZKEmployee;
use App\Models\EmployeeZKAssignment;
use App\Models\ZKEmployeeAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ZKAssignmentController extends Controller
{
    /**
     * Get list of all ZK employees from the device/sync table.
     */
    public function getZKEmployees()
    {
        $zkEmployees = ZKEmployee::with('currentAssignment.employee')->get();

        return response()->json([
            'status' => 'success',
            'data' => $zkEmployees
        ]);
    }

    /**
     * Get list of employees who are not currently linked to any ZK ID.
     * This includes employees who never had an assignment or those whose 
     * assignments have all been released.
     */
    public function getUnlinkedEmployees()
    {
        $employees = Employee::whereDoesntHave('currentZkAssignment')->get();

        return response()->json([
            'status' => 'success',
            'data' => $employees
        ]);
    }

    /**
     * Link an employee to a ZK ID.
     * If the employee or the ZK ID are already active in another assignment,
     * those assignments will be automatically released.
     */
    public function linkEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'zk_employee_id' => 'required|exists:zk_employees,id',
            'assigned_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignedAt = $request->assigned_at ? Carbon::parse($request->assigned_at) : now();

        // 1. Release any current assignment for this employee
        EmployeeZKAssignment::where('employee_id', $request->employee_id)
            ->whereNull('released_at')
            ->update(['released_at' => $assignedAt]);

        // 2. Release any current assignment for this ZK ID (if someone else was using it)
        EmployeeZKAssignment::where('zk_employee_id', $request->zk_employee_id)
            ->whereNull('released_at')
            ->update(['released_at' => $assignedAt]);

        // 3. Create new assignment
        $assignment = EmployeeZKAssignment::create([
            'employee_id' => $request->employee_id,
            'zk_employee_id' => $request->zk_employee_id,
            'assigned_at' => $assignedAt,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Employee linked successfully',
            'data' => $assignment->load(['employee', 'zkEmployee'])
        ]);
    }

    /**
     * Release an active assignment (unlink).
     */
    public function releaseAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'released_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $releasedAt = $request->released_at ? Carbon::parse($request->released_at) : now();

        $updated = EmployeeZKAssignment::where('employee_id', $request->employee_id)
            ->whereNull('released_at')
            ->update(['released_at' => $releasedAt]);

        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active assignment found for this employee'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Employee unlinked successfully'
        ]);
    }

    /**
     * Get ZK IDs that are NOT currently assigned to any employee.
     */
    public function getAvailableZKEmployees()
    {
        $available = ZKEmployee::whereDoesntHave('currentAssignment')->get();

        return response()->json([
            'status' => 'success',
            'data' => $available
        ]);
    }

    /**
     * Get the assignment history for a specific employee.
     */
    public function getEmployeeHistory($employeeId)
    {
        $history = EmployeeZKAssignment::with('zkEmployee')
            ->where('employee_id', $employeeId)
            ->orderBy('assigned_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }
    /**
     * Get the attendance history for a specific employee based on their ZK assignments.
     */
    public function getAttendanceHistory($employeeId)
    {
        $assignments = EmployeeZKAssignment::where('employee_id', $employeeId)->get();

        if ($assignments->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        $query = ZKEmployeeAttendance::query();

        $query->where(function ($q) use ($assignments) {
            foreach ($assignments as $assignment) {
                $q->orWhere(function ($sq) use ($assignment) {
                    $sq->where('employee_id', $assignment->zk_employee_id)
                       ->where('punched_at', '>=', $assignment->assigned_at);
                    
                    if ($assignment->released_at) {
                        $sq->where('punched_at', '<=', $assignment->released_at);
                    }
                });
            }
        });

        $attendances = $query->orderBy('punched_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $attendances
        ]);
    }

    /**
     * Get list of employees who attended on a specific date.
     */
    public function getAttendanceByDate($date)
    {
        try {
            $searchDate = Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid date format'
            ], 400);
        }

        // Logic: 
        // 1. Get all punches for that date.
        // 2. Join with assignments to find which real employee owned that ZK ID at that time.
        // 3. Join with employees table to get their details.
        
        $attendances = ZKEmployeeAttendance::whereDate('punched_at', $searchDate)
            ->join('employee_zk_assignments', function($join) {
                $join->on('zk_employee_attendances.employee_id', '=', 'employee_zk_assignments.zk_employee_id')
                     ->whereColumn('zk_employee_attendances.punched_at', '>=', 'employee_zk_assignments.assigned_at')
                     ->where(function($query) {
                         $query->whereColumn('zk_employee_attendances.punched_at', '<=', 'employee_zk_assignments.released_at')
                               ->orWhereNull('employee_zk_assignments.released_at');
                     });
            })
            ->join('employees', 'employee_zk_assignments.employee_id', '=', 'employees.id')
            ->select(
                'employees.id as employee_id',
                'employees.name',
                'employees.surname',
                'zk_employee_attendances.employee_id as zk_id',
                'zk_employee_attendances.punched_at',
                'zk_employee_attendances.type'
            )
            ->orderBy('zk_employee_attendances.punched_at', 'asc')
            ->get();

        // Group by employee to show who attended (rather than just individual punches if preferred)
        // But usually, an attendance report shows the timing.
        // Let's return the list of unique employees who attended, maybe with their punches.
        
        $grouped = $attendances->groupBy('employee_id')->map(function ($items) {
            $first = $items->first();
            return [
                'employee_id' => $first->employee_id,
                'name' => $first->name,
                'surname' => $first->surname,
                'total_punches' => $items->count(),
                'punches' => $items->map(function($p) {
                    return [
                        'punched_at' => $p->punched_at,
                        'type' => $p->type,
                        'zk_id' => $p->zk_id
                    ];
                })
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'date' => $searchDate,
            'data' => $grouped
        ]);
    }

    /**
     * Get the assignment history for a specific ZK ID.
     */
    public function getZKHistory($zkEmployeeId)
    {
        $history = EmployeeZKAssignment::with('employee')
            ->where('zk_employee_id', $zkEmployeeId)
            ->orderBy('assigned_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'zk_employee_id' => $zkEmployeeId,
            'data' => $history
        ]);
    }
}
