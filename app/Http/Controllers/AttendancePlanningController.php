<?php

namespace App\Http\Controllers;

use App\Models\AttendanceActiveEmployee;
use App\Models\Employee;
use App\Models\EmployeeMonthlyWorkDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendancePlanningController extends Controller
{
    public function getActiveEmployees(Request $request): JsonResponse
    {
        $searchValue = trim((string) $request->query('searchValue', ''));
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $employeesQuery = Employee::query()->orderBy('name');
        if ($searchValue !== '') {
            $employeesQuery->where(function ($query) use ($searchValue) {
                $query->where('name', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('surname', 'LIKE', '%' . $searchValue . '%')
                    ->orWhereRaw("CONCAT(COALESCE(name, ''), ' ', COALESCE(surname, '')) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        $employees = $employeesQuery->get();
        $activeIds = AttendanceActiveEmployee::query()
            ->where('month', $month)
            ->where('year', $year)
            ->where('is_active', true)
            ->pluck('employee_id')
            ->values();

        return response()->json([
            'employees' => $employees,
            'active_employee_ids' => $activeIds,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function saveActiveEmployees(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000'],
            'employee_ids' => ['required', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
        ]);

        $employeeIds = array_values(array_unique(array_map('intval', $validated['employee_ids'])));
        $month = (int) $validated['month'];
        $year = (int) $validated['year'];

        AttendanceActiveEmployee::query()
            ->where('month', $month)
            ->where('year', $year)
            ->delete();

        if (!empty($employeeIds)) {
            $now = now();
            $rows = array_map(static fn (int $employeeId) => [
                'employee_id' => $employeeId,
                'month' => $month,
                'year' => $year,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ], $employeeIds);

            AttendanceActiveEmployee::query()->insert($rows);
        }

        return response()->json([
            'message' => 'Active employees saved successfully.',
            'active_employee_ids' => $employeeIds,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function getMonthlyWorkDays(Request $request): JsonResponse
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $activeIds = AttendanceActiveEmployee::query()
            ->where('month', $month)
            ->where('year', $year)
            ->where('is_active', true)
            ->pluck('employee_id')
            ->values()
            ->all();

        $employees = Employee::query()
            ->whereIn('id', $activeIds)
            ->orderBy('name')
            ->get();

        $entries = EmployeeMonthlyWorkDay::query()
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('employee_id');

        $employees->each(function (Employee $employee) use ($entries) {
            $employee->setAttribute('work_days', (int) ($entries[$employee->id]->work_days ?? 0));
        });

        return response()->json([
            'employees' => $employees,
            'month' => $month,
            'year' => $year,
            'active_employee_ids' => $activeIds,
        ]);
    }

    public function saveMonthlyWorkDays(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000'],
            'entries' => ['required', 'array'],
            'entries.*.employee_id' => ['required', 'integer', 'exists:employees,id'],
            'entries.*.work_days' => ['required', 'integer', 'min:0', 'max:31'],
        ]);

        foreach ($validated['entries'] as $entry) {
            EmployeeMonthlyWorkDay::updateOrCreate(
                [
                    'employee_id' => (int) $entry['employee_id'],
                    'month' => (int) $validated['month'],
                    'year' => (int) $validated['year'],
                ],
                [
                    'work_days' => (int) $entry['work_days'],
                ]
            );
        }

        return response()->json([
            'message' => 'Monthly work days saved successfully.',
        ]);
    }
}
