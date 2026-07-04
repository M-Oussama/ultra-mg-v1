<?php

namespace App\Http\Controllers;

use App\Models\EmployeeCareer;
use App\Models\YearlyVacation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VacationController extends Controller
{
    public function getVacations(Request $request)
    {
        $searchValue = $request->input('searchValue', '');
        $perPage = (int) $request->input('perPage', 10);
        $currentPage = (int) $request->input('currentPage', 1);
        $sortDirection = strtolower((string) $request->input('sortDirection', 'desc')) === 'asc' ? 'asc' : 'desc';

        $vacations = YearlyVacation::with(['employee', 'employee_career'])
            ->orderBy('start_date', $sortDirection)
            ->orderBy('id', $sortDirection);

        $user = auth()->user();
        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                $deptIds = $user->departments->pluck('id')->toArray();
                $vacations->whereHas('employee', function ($q) use ($deptIds) {
                    $q->whereIn('department_id', $deptIds);
                });
            } else {
                $vacations->whereHas('employee', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        if ($searchValue !== '') {
            $vacations->where(function ($query) use ($searchValue) {
                $query->whereHas('employee', function ($employeeQuery) use ($searchValue) {
                    $employeeQuery->where(function ($nested) use ($searchValue) {
                        $nested->where('name', 'like', '%' . $searchValue . '%')
                            ->orWhere('surname', 'like', '%' . $searchValue . '%');
                    });
                })->orWhere('vacation_year', 'like', '%' . $searchValue . '%');
            });
        }

        $page = $vacations->paginate($perPage, ['*'], 'page', $currentPage);

        return response()->json([
            'list' => $page,
            'totalPage' => $page->lastPage(),
            'totalVacations' => $page->total(),
        ]);
    }

    public function getVacationsByEmployee(Request $request, $id)
    {
        $career = EmployeeCareer::find($id);
        if (!$career) {
            throw new BadRequestHttpException('Employee career not found');
        }

        $perPage = (int) $request->input('perPage', 10);
        $currentPage = (int) $request->input('currentPage', 1);

        $vacations = YearlyVacation::with(['employee', 'employee_career'])
            ->where('employee_id', $career->employee_id)
            ->orderBy('start_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $currentPage);

        return response()->json([
            'list' => $vacations,
            'totalPage' => $vacations->lastPage(),
            'totalVacations' => $vacations->total(),
        ]);
    }

    public function getVacationsByCareer(Request $request, $id)
    {
        $career = EmployeeCareer::find($id);
        if (!$career) {
            throw new BadRequestHttpException('Employee career not found');
        }

        $perPage = (int) $request->input('perPage', 10);
        $currentPage = (int) $request->input('currentPage', 1);

        $vacations = YearlyVacation::with(['employee', 'employee_career'])
            ->where('employee_career_id', $career->id)
            ->orderBy('start_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $currentPage);

        return response()->json([
            'list' => $vacations,
            'totalPage' => $vacations->lastPage(),
            'totalVacations' => $vacations->total(),
        ]);
    }

    public function getEmployeeVacationSummary($id): JsonResponse
    {
        $employeeCareers = EmployeeCareer::where('employee_id', $id)
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();

        if ($employeeCareers->isEmpty()) {
            return response()->json([
                'employee_id' => (int) $id,
                'accrual_rate_per_month' => 2.5,
                'totals' => [
                    'worked_days' => 0,
                    'worked_months' => 0,
                    'accrued_days' => 0,
                    'used_days' => 0,
                    'balance_days' => 0,
                ],
                'current_group' => null,
                'groups' => [],
                'careers' => [],
            ]);
        }

        $vacations = YearlyVacation::where('employee_id', $id)
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();

        $vacationsByCareer = $vacations->groupBy('employee_career_id');

        $groups = [];
        $careerSummaries = [];
        $currentGroup = null;
        $groupId = 1;

        $overallWorkedDays = 0;
        $overallAccrued = 0.0;
        $overallUsed = 0.0;

        foreach ($employeeCareers as $career) {
            if ($currentGroup === null) {
                $currentGroup = $this->makeGroupSummary($groupId, $career);
            }

            $periodEnd = $this->resolvePeriodEnd($career);
            $workedDays = $this->calculateWorkedDays($career->start_date, $periodEnd);
            $workingMonths = round($workedDays / 30, 2);
            $accruedDays = round($workingMonths * 2.5, 2);
            $usedDays = (float) ($vacationsByCareer->get($career->id, collect())->sum('count') ?? 0);
            $balanceDays = round($accruedDays - $usedDays, 2);

            $careerSummaries[] = [
                'employee_career_id' => $career->id,
                'group_id' => $groupId,
                'start_date' => $career->start_date,
                'end_date' => $career->end_date,
                'real_start_date' => $career->real_start_date,
                'real_end_date' => $career->real_end_date,
                'worked_days' => $workedDays,
                'worked_months' => $workingMonths,
                'accrued_days' => $accruedDays,
                'used_days' => round($usedDays, 2),
                'balance_days' => $balanceDays,
                'vacation_count' => (int) ($vacationsByCareer->get($career->id, collect())->count() ?? 0),
                'is_closed' => (bool) $career->real_end_date,
            ];

            $currentGroup['career_ids'][] = $career->id;
            $currentGroup['careers'][] = $career->id;
            $currentGroup['worked_days'] += $workedDays;
            $currentGroup['working_months'] = round($currentGroup['worked_days'] / 30, 2);
            $currentGroup['accrued_days'] = round($currentGroup['accrued_days'] + $accruedDays, 2);
            $currentGroup['used_days'] = round($currentGroup['used_days'] + $usedDays, 2);
            $currentGroup['end_date'] = $periodEnd;
            $currentGroup['last_career_id'] = $career->id;
            $currentGroup['last_real_end_date'] = $career->real_end_date;

            $overallWorkedDays += $workedDays;
            $overallAccrued = round($overallAccrued + $accruedDays, 2);
            $overallUsed = round($overallUsed + $usedDays, 2);

            if ($career->real_end_date) {
                $currentGroup['closed_at'] = $career->real_end_date;
                $currentGroup['is_current'] = false;
                $currentGroup['balance_days'] = round($currentGroup['accrued_days'] - $currentGroup['used_days'], 2);
                $groups[] = $currentGroup;
                $currentGroup = null;
                $groupId++;
            }
        }

        if ($currentGroup !== null) {
            $currentGroup['is_current'] = true;
            $currentGroup['balance_days'] = round($currentGroup['accrued_days'] - $currentGroup['used_days'], 2);
            $groups[] = $currentGroup;
        }

        $currentGroupSummary = count($groups) > 0 ? $groups[count($groups) - 1] : null;

        return response()->json([
            'employee_id' => (int) $id,
            'accrual_rate_per_month' => 2.5,
            'totals' => [
                'worked_days' => $overallWorkedDays,
                'worked_months' => round($overallWorkedDays / 30, 2),
                'accrued_days' => round($overallAccrued, 2),
                'used_days' => round($overallUsed, 2),
                'balance_days' => round($overallAccrued - $overallUsed, 2),
            ],
            'current_group' => $currentGroupSummary,
            'groups' => $groups,
            'careers' => $careerSummaries,
        ]);
    }

    public function store(Request $request, $id)
    {
        $career = EmployeeCareer::find($id);
        if (!$career) {
            throw new BadRequestHttpException('Employee career not found');
        }

        $validated = Validator::make($request->all(), [
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'count' => 'required|integer|min:1',
            'vacationYear' => 'nullable|string|max:32',
        ])->validate();

        $vacationYear = trim((string) ($validated['vacationYear'] ?? ''));
        if ($vacationYear === '') {
            $vacationYear = Carbon::parse($validated['startDate'])->year . '/' . Carbon::parse($validated['endDate'])->year;
        }

        $vacation = YearlyVacation::create([
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'count' => (int) $validated['count'],
            'vacation_year' => $vacationYear,
            'employee_id' => $career->employee_id,
            'employee_career_id' => $career->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Record created successfully',
            'vacation' => $vacation->load(['employee', 'employee_career']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $vacation = YearlyVacation::find($id);
        if (!$vacation) {
            throw new BadRequestHttpException('Vacation record not found');
        }

        $validated = Validator::make($request->all(), [
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'count' => 'required|integer|min:1',
            'vacationYear' => 'nullable|string|max:32',
        ])->validate();

        $vacationYear = trim((string) ($validated['vacationYear'] ?? ''));
        if ($vacationYear === '') {
            $vacationYear = Carbon::parse($validated['startDate'])->year . '/' . Carbon::parse($validated['endDate'])->year;
        }

        $vacation->update([
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'count' => (int) $validated['count'],
            'vacation_year' => $vacationYear,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Record updated successfully',
            'vacation' => $vacation->fresh(['employee', 'employee_career']),
        ]);
    }

    public function destroy($id)
    {
        $vacation = YearlyVacation::find($id);
        if (!$vacation) {
            throw new BadRequestHttpException('Vacation record not found');
        }

        $vacation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully',
        ]);
    }

    public function getVacation($id)
    {
        $vacation = YearlyVacation::with(['employee', 'employee_career'])->find($id);
        if (!$vacation) {
            throw new BadRequestHttpException('Vacation record not found');
        }

        return response()->json([
            'success' => true,
            'vacation' => $vacation,
        ]);
    }

    protected function calculateWorkedDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($end->lt($start)) {
            return 0;
        }

        return $start->diffInDays($end) + 1;
    }

    protected function resolvePeriodEnd(EmployeeCareer $career): string
    {
        if ($career->end_date) {
            return (string) $career->end_date;
        }

        if ($career->real_end_date) {
            return (string) $career->real_end_date;
        }

        return Carbon::today()->toDateString();
    }

    protected function makeGroupSummary(int $groupId, EmployeeCareer $career): array
    {
        return [
            'id' => $groupId,
            'title' => 'Employment block ' . $groupId,
            'career_ids' => [],
            'careers' => [],
            'start_date' => $career->start_date,
            'end_date' => null,
            'closed_at' => null,
            'last_career_id' => null,
            'last_real_end_date' => null,
            'worked_days' => 0,
            'working_months' => 0,
            'accrued_days' => 0.0,
            'used_days' => 0.0,
            'balance_days' => 0.0,
            'is_current' => false,
        ];
    }

    /**
     * Import yearly_vacations from CSV.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);
        foreach (['id', 'start_date', 'end_date', 'count', 'employee_id', 'employee_career_id'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $warnings = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'start_date' => $rowData['start_date'] ?? null,
                'end_date' => $rowData['end_date'] ?? null,
                'count' => isset($rowData['count']) ? (int) $rowData['count'] : 0,
                'vacation_year' => trim((string) ($rowData['vacation_year'] ?? $rowData['year'] ?? '')),
                'employee_id' => isset($rowData['employee_id']) ? (int) $rowData['employee_id'] : null,
                'employee_career_id' => isset($rowData['employee_career_id']) ? (int) $rowData['employee_career_id'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'count' => 'required|integer|min:0',
                'vacation_year' => 'nullable|string|max:32',
                'employee_id' => 'required|integer',
                'employee_career_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $errors[] = ['row' => $rowNumber, 'errors' => $validator->errors()->all()];
                continue;
            }

            if ($payload['vacation_year'] === '') {
                $payload['vacation_year'] = null;
            }

            if (!empty($payload['id']) && DB::table('yearly_vacations')->where('id', $payload['id'])->exists()) {
                $warnings[] = ['row' => $rowNumber, 'warning' => 'Yearly vacation id exists, inserted with auto-generated id.'];
                unset($payload['id']);
            }

            DB::table('yearly_vacations')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ]);
    }
}
