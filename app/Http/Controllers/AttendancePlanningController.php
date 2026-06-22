<?php

namespace App\Http\Controllers;

use App\Models\AttendanceActiveEmployee;
use App\Models\AttendanceActiveEmployeeMonth;
use App\Models\Employee;
use App\Models\EmployeeMonthlyPayroll;
use App\Models\EmployeeMonthlyWorkDay;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        [$activeIds, $isPrefilledFromPreviousMonth, $prefilledFromMonth, $prefilledFromYear] = $this->resolveActiveEmployeeIds($month, $year);

        return response()->json([
            'employees' => $employees,
            'active_employee_ids' => $activeIds,
            'month' => $month,
            'year' => $year,
            'is_prefilled_from_previous_month' => $isPrefilledFromPreviousMonth,
            'prefilled_from_month' => $prefilledFromMonth,
            'prefilled_from_year' => $prefilledFromYear,
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

        $this->confirmActiveEmployeeMonth($month, $year);

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

        [$activeIds, $isPrefilledFromPreviousMonth, $prefilledFromMonth, $prefilledFromYear] = $this->resolveActiveEmployeeIds($month, $year);

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
            'is_prefilled_from_previous_month' => $isPrefilledFromPreviousMonth,
            'prefilled_from_month' => $prefilledFromMonth,
            'prefilled_from_year' => $prefilledFromYear,
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

    public function getPayrollSheet(Request $request): JsonResponse
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        [$activeIds, $isPrefilledFromPreviousMonth, $prefilledFromMonth, $prefilledFromYear] = $this->resolveActiveEmployeeIds($month, $year);

        $employees = Employee::query()
            ->orderBy('name')
            ->get();

        $workDayEntries = EmployeeMonthlyWorkDay::query()
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('employee_id');

        $payrollEntries = EmployeeMonthlyPayroll::query()
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('employee_id');

        $activeMap = array_flip($activeIds);

        $employees->each(function (Employee $employee) use ($workDayEntries, $payrollEntries, $activeMap) {
            $payroll = $payrollEntries[$employee->id] ?? null;
            $employee->setAttribute('work_days', (int) ($workDayEntries[$employee->id]->work_days ?? 0));
            $employee->setAttribute('monthly_salary', (float) ($payroll->monthly_salary ?? 0));
            $employee->setAttribute('objectives_amount', (float) ($payroll->objectives_amount ?? 0));
            $employee->setAttribute('is_payroll_selected', isset($activeMap[$employee->id]));
        });

        return response()->json([
            'employees' => $employees,
            'active_employee_ids' => $activeIds,
            'selected_employee_ids' => $activeIds,
            'month' => $month,
            'year' => $year,
            'months' => $this->buildPayrollMonthSummaries(),
            'is_prefilled_from_previous_month' => $isPrefilledFromPreviousMonth,
            'prefilled_from_month' => $prefilledFromMonth,
            'prefilled_from_year' => $prefilledFromYear,
        ]);
    }

    public function savePayrollSheet(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000'],
            'entries' => ['required', 'array'],
            'entries.*.employee_id' => ['required', 'integer', 'exists:employees,id'],
            'entries.*.work_days' => ['required', 'integer', 'min:0', 'max:31'],
            'entries.*.monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'entries.*.objectives_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $month = (int) $validated['month'];
        $year = (int) $validated['year'];
        $entries = collect($validated['entries'])
            ->map(static function (array $entry) {
                return [
                    'employee_id' => (int) $entry['employee_id'],
                    'work_days' => (int) $entry['work_days'],
                    'monthly_salary' => (float) ($entry['monthly_salary'] ?? 0),
                    'objectives_amount' => (float) ($entry['objectives_amount'] ?? 0),
                ];
            })
            ->unique('employee_id')
            ->values();

        DB::transaction(function () use ($entries, $month, $year) {
            $employeeIds = $entries->pluck('employee_id')->values()->all();
            $now = now();

            AttendanceActiveEmployee::query()
                ->where('month', $month)
                ->where('year', $year)
                ->delete();

            if (!empty($employeeIds)) {
                AttendanceActiveEmployee::query()->insert(array_map(static fn (int $employeeId) => [
                    'employee_id' => $employeeId,
                    'month' => $month,
                    'year' => $year,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $employeeIds));
            }

            $stalePayrollQuery = EmployeeMonthlyPayroll::query()
                ->where('month', $month)
                ->where('year', $year);

            if (!empty($employeeIds)) {
                $stalePayrollQuery->whereNotIn('employee_id', $employeeIds);
            }

            $stalePayrollQuery->delete();

            foreach ($entries as $entry) {
                EmployeeMonthlyWorkDay::updateOrCreate(
                    [
                        'employee_id' => $entry['employee_id'],
                        'month' => $month,
                        'year' => $year,
                    ],
                    [
                        'work_days' => $entry['work_days'],
                    ]
                );

                EmployeeMonthlyPayroll::updateOrCreate(
                    [
                        'employee_id' => $entry['employee_id'],
                        'month' => $month,
                        'year' => $year,
                    ],
                    [
                        'monthly_salary' => $entry['monthly_salary'],
                        'objectives_amount' => $entry['objectives_amount'],
                    ]
                );
            }

            $this->confirmActiveEmployeeMonth($month, $year);
        });

        return response()->json([
            'message' => 'Payroll sheet saved successfully.',
            'selected_employee_ids' => $entries->pluck('employee_id')->values()->all(),
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Build a lightweight list of saved payroll months so the Flutter UI can
     * show month history in both card and table views without extra API calls.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildPayrollMonthSummaries(): array
    {
        $payrollGroups = EmployeeMonthlyPayroll::query()
            ->select([
                'employee_id',
                'month',
                'year',
                'monthly_salary',
                'objectives_amount',
                'updated_at',
            ])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->groupBy(fn (EmployeeMonthlyPayroll $entry) => $entry->year . '-' . $entry->month);

        $workDayGroups = EmployeeMonthlyWorkDay::query()
            ->select([
                'employee_id',
                'month',
                'year',
                'work_days',
                'updated_at',
            ])
            ->get()
            ->groupBy(fn (EmployeeMonthlyWorkDay $entry) => $entry->year . '-' . $entry->month);

        return collect($payrollGroups->keys())
            ->merge($workDayGroups->keys())
            ->unique()
            ->map(function (string $key) use ($payrollGroups, $workDayGroups) {
                [$year, $month] = array_map('intval', explode('-', $key));
                $payrollEntries = collect($payrollGroups->get($key, []));
                $workDayEntries = collect($workDayGroups->get($key, []))->keyBy('employee_id');
                $employeeIds = $payrollEntries
                    ->pluck('employee_id')
                    ->merge($workDayEntries->pluck('employee_id'))
                    ->map(fn ($value) => (int) $value)
                    ->unique()
                    ->values();

                $grossSalary = 0.0;
                $objectivesTotal = 0.0;
                $totalPayable = 0.0;

                foreach ($employeeIds as $employeeId) {
                    $payroll = $payrollEntries->firstWhere('employee_id', $employeeId);
                    $workDays = (int) ($workDayEntries->get($employeeId)?->work_days ?? 0);
                    $monthlySalary = (float) ($payroll?->monthly_salary ?? 0);
                    $objectivesAmount = (float) ($payroll?->objectives_amount ?? 0);
                    $salaryPart = $monthlySalary * ($workDays / 30);
                    $employeeTotal = $salaryPart + $objectivesAmount;

                    $grossSalary += $salaryPart;
                    $objectivesTotal += $objectivesAmount;
                    $totalPayable += $employeeTotal;
                }

                $updatedAt = collect($payrollEntries)
                    ->pluck('updated_at')
                    ->merge($workDayEntries->pluck('updated_at'))
                    ->filter()
                    ->sortDesc()
                    ->first();

                return [
                    'month' => $month,
                    'year' => $year,
                    'label' => Carbon::create($year, $month, 1)->format('F Y'),
                    'employee_count' => $employeeIds->count(),
                    'gross_salary' => round($grossSalary, 2),
                    'objectives_total' => round($objectivesTotal, 2),
                    'total_payable' => round($totalPayable, 2),
                    'updated_at' => $updatedAt ? Carbon::parse($updatedAt)->toDateTimeString() : null,
                ];
            })
            ->sortByDesc(fn (array $item) => ($item['year'] * 100) + $item['month'])
            ->values()
            ->all();
    }

    /**
     * Resolve the active employees for the requested month.
     *
     * If the requested month has not yet been confirmed, we prefill it from
     * the previous month so the user can review the same roster and then save
     * it to confirm the month.
     *
     * @return array{0: array<int>, 1: bool, 2: int|null, 3: int|null}
     */
    private function resolveActiveEmployeeIds(int $month, int $year): array
    {
        $currentActiveIds = AttendanceActiveEmployee::query()
            ->where('month', $month)
            ->where('year', $year)
            ->where('is_active', true)
            ->pluck('employee_id')
            ->map(fn ($value) => (int) $value)
            ->values()
            ->all();

        $snapshotExists = AttendanceActiveEmployeeMonth::query()
            ->where('month', $month)
            ->where('year', $year)
            ->exists();

        if (!empty($currentActiveIds) || $snapshotExists) {
            return [$currentActiveIds, false, null, null];
        }

        $previousMonth = Carbon::create($year, $month, 1)->subMonthNoOverflow();
        $previousActiveIds = AttendanceActiveEmployee::query()
            ->where('month', $previousMonth->month)
            ->where('year', $previousMonth->year)
            ->where('is_active', true)
            ->pluck('employee_id')
            ->map(fn ($value) => (int) $value)
            ->values()
            ->all();

        if (!empty($previousActiveIds)) {
            return [
                $previousActiveIds,
                true,
                (int) $previousMonth->month,
                (int) $previousMonth->year,
            ];
        }

        return [[], false, null, null];
    }

    private function confirmActiveEmployeeMonth(int $month, int $year): void
    {
        $snapshot = AttendanceActiveEmployeeMonth::query()->firstOrNew([
            'month' => $month,
            'year' => $year,
        ]);

        if (!$snapshot->exists) {
            $previousMonth = Carbon::create($year, $month, 1)->subMonthNoOverflow();
            $snapshot->copied_from_month = $previousMonth->month;
            $snapshot->copied_from_year = $previousMonth->year;
        }

        $snapshot->confirmed_at = now();
        $snapshot->save();
    }
}
