<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ZKEmployee;
use App\Models\ZKEmployeeAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ZKMobileApiController extends Controller
{
    /**
     * Login User and return Bearer Token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $token = $user->createToken('ZKMobileApp')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'token'  => $token,
                'user'   => $user
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    /**
     * Get all ZK Employees.
     */
    public function getEmployees()
    {
        $employees = ZKEmployee::all();
        return response()->json([
            'status' => 'success',
            'data'   => $employees
        ]);
    }

    /**
     * Get Attendance for each employee per month.
     * Default to current month/year if not provided.
     */
    public function getAttendance(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year  = $request->input('year', Carbon::now()->year);

        // Fetch ZK attendance records for the month
        $attendances = ZKEmployeeAttendance::whereYear('punched_at', $year)
            ->whereMonth('punched_at', $month)
            ->orderBy('punched_at', 'asc')
            ->get()
            ->groupBy('employee_id');

        // Prepare response structure
        $result = [];
        $employees = ZKEmployee::all();

        foreach ($employees as $employee) {
            $employeeAtt = $attendances->get($employee->zk_id) ?? [];
            
            $result[] = [
                'employee'   => $employee,
                'attendances' => $employeeAtt
            ];
        }

        // Also include employees that might have attendance but no ZKEmployee record?
        // Ideally we iterate known employees. If generic attendance is needed, we can just dump $attendances.
        // But the user asked "for each employee", so grouping by employee makes sense.
        
        // Let's also check if there are attendance records for IDs not in ZKEmployee (unlikely if synced, but possible)
        // For now, listing all ZKEmployees with their attendance seems correct.

        return response()->json([
            'status' => 'success',
            'period' => [
                'month' => $month,
                'year'  => $year
            ],
            'data'   => $result
        ]);
    }
}
