<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeCareer;
use App\Models\User;
use App\Models\YearlyVacation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AttendanceAlertService
{
    public function __construct(protected FirebaseCloudMessagingService $messaging)
    {
    }

    public function buildOverview(?User $user = null, int $windowDays = 3, int $limit = 5): array
    {
        $today = Carbon::today();
        $windowEnd = $today->copy()->addDays($windowDays);

        $employees = $this->visibleEmployeesQuery($user)
            ->orderBy('name')
            ->orderBy('surname')
            ->get(['id', 'name', 'surname', 'user_id', 'active']);

        $employeeIds = $employees->pluck('id')->all();

        if (empty($employeeIds)) {
            return $this->emptyOverview();
        }

        $careers = EmployeeCareer::query()
            ->with(['employee:id,name,surname,user_id'])
            ->whereIn('employee_id', $employeeIds)
            ->orderBy('employee_id')
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();

        $vacations = YearlyVacation::query()
            ->with([
                'employee:id,name,surname,user_id',
                'employee_career:id,employee_id,start_date,end_date,real_start_date,real_end_date,position,position_ar',
            ])
            ->whereIn('employee_id', $employeeIds)
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();

        $activeCareerByEmployee = [];
        $latestCareerByEmployee = [];

        foreach ($careers as $career) {
            $latestCareerByEmployee[$career->employee_id] = $career;

            if ($this->isCareerActive($career, $today)) {
                $activeCareerByEmployee[$career->employee_id] = $career;
            }
        }

        $vacationsByCareer = $vacations->groupBy(static function (YearlyVacation $vacation): int {
            return (int) $vacation->employee_career_id;
        });

        $incomingVacations = [];
        $ongoingVacations = [];

        foreach ($careers as $career) {
            if (!$this->isCareerActive($career, $today)) {
                continue;
            }

            $careerVacations = $vacationsByCareer->get($career->id, collect());
            $accrualStart = null;

            $lastEndedVacation = $careerVacations
                ->filter(static function (YearlyVacation $vacation) use ($today): bool {
                    return Carbon::parse($vacation->end_date)->startOfDay()->lte($today);
                })
                ->sortByDesc('end_date')
                ->first();

            $accrualStart = $lastEndedVacation
                ? Carbon::parse($lastEndedVacation->end_date)->startOfDay()
                : $this->careerStartDate($career);

            $dueDate = $lastEndedVacation
                ? Carbon::parse($lastEndedVacation->end_date)->startOfDay()->addYear()
                : $this->careerStartDate($career)->addYear();

            if ($dueDate->lte($windowEnd)) {
                $incomingVacations[] = $this->mapIncomingVacation(
                    $career,
                    $lastEndedVacation,
                    $today,
                    $dueDate,
                    $accrualStart,
                    $this->accruedVacationDays($accrualStart, $today),
                );
            }
        }

        foreach ($vacations as $vacation) {
            $startDate = Carbon::parse($vacation->start_date)->startOfDay();
            $endDate = Carbon::parse($vacation->end_date)->startOfDay();

            if ($startDate->lte($today) && $endDate->betweenIncluded($today, $windowEnd)) {
                $ongoingVacations[] = $this->mapVacation(
                    $vacation,
                    $today,
                    'ongoing',
                    $windowEnd,
                );
            }
        }

        $contractsEndingSoon = [];
        $withoutContract = [];

        foreach ($employees as $employee) {
            $activeCareer = $activeCareerByEmployee[$employee->id] ?? null;
            $latestCareer = $latestCareerByEmployee[$employee->id] ?? null;

            if ($activeCareer) {
                $contractEndDate = $this->contractReviewDate($activeCareer);
                if ($contractEndDate !== null && $contractEndDate->lte($windowEnd)) {
                    $contractsEndingSoon[] = $this->mapContract($activeCareer, $today, $contractEndDate);
                }
                continue;
            }

            $withoutContract[] = $this->mapWithoutContract($employee, $latestCareer, $today);
        }

        $sortByDays = static function (array $left, array $right): int {
            return ($left['days_remaining'] ?? PHP_INT_MAX) <=> ($right['days_remaining'] ?? PHP_INT_MAX);
        };

        usort($incomingVacations, $sortByDays);
        usort($ongoingVacations, $sortByDays);
        usort($contractsEndingSoon, $sortByDays);
        usort($withoutContract, $sortByDays);

        $slice = static function (array $items) use ($limit): array {
            if ($limit <= 0) {
                return $items;
            }

            return array_slice($items, 0, $limit);
        };

        return [
            'summary' => [
                'employees_total' => $employees->count(),
                'contracts_total' => $careers->count(),
                'vacations_total' => $vacations->count(),
                'incoming_vacations_total' => count($incomingVacations),
                'ongoing_vacations_total' => count($ongoingVacations),
                'contracts_ending_soon_total' => count($contractsEndingSoon),
                'without_contract_total' => count($withoutContract),
            ],
            'incoming_vacations' => $slice($incomingVacations),
            'ongoing_vacations' => $slice($ongoingVacations),
            'contracts_ending_soon' => $slice($contractsEndingSoon),
            'without_contract' => $slice($withoutContract),
        ];
    }

    public function sendDailyNotifications(int $windowDays = 3): array
    {
        if (!$this->messaging->isConfigured()) {
            return [
                'sent' => 0,
                'overview' => $this->buildOverview(null, $windowDays),
            ];
        }

        $overview = $this->buildOverview(null, $windowDays, 50);
        $recipientIds = collect([
            $overview['incoming_vacations'],
            $overview['ongoing_vacations'],
            $overview['contracts_ending_soon'],
            $overview['without_contract'],
        ])
            ->flatten(1)
            ->pluck('recipient_user_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($recipientIds)) {
            return [
                'sent' => 0,
                'overview' => $overview,
            ];
        }

        /** @var Collection<int, User> $users */
        $users = User::query()
            ->whereIn('id', $recipientIds)
            ->whereNotNull('fcm_token')
            ->get(['id', 'name', 'fcm_token'])
            ->keyBy('id');

        $sent = 0;
        foreach ([
            'incoming_vacations',
            'ongoing_vacations',
            'contracts_ending_soon',
            'without_contract',
        ] as $category) {
            foreach ($overview[$category] as $item) {
                $recipientId = (int) ($item['recipient_user_id'] ?? 0);
                if ($recipientId <= 0) {
                    continue;
                }

                $recipient = $users->get($recipientId);
                if (!$recipient || trim((string) $recipient->fcm_token) === '') {
                    continue;
                }

                $payload = [
                    'entity_type' => 'attendance_alert',
                    'entity_action' => $category,
                    'alert_category' => $category,
                    'employee_id' => (string) ($item['employee_id'] ?? ''),
                    'career_id' => (string) ($item['career_id'] ?? ''),
                    'vacation_id' => (string) ($item['vacation_id'] ?? ''),
                    'days_remaining' => (string) ($item['days_remaining'] ?? ''),
                ];

                if ($this->messaging->sendToToken(
                    $recipient->fcm_token,
                    $this->notificationTitle($category, $item),
                    $item['notification_body'] ?? $item['status_label'] ?? 'Attendance update',
                    $payload,
                )) {
                    $sent++;
                }
            }
        }

        return [
            'sent' => $sent,
            'overview' => $overview,
        ];
    }

    private function visibleEmployeesQuery(?User $user): Builder
    {
        $query = Employee::query();

        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                $deptIds = $user->departments->pluck('id')->toArray();
                $query->whereHas('user', function ($userQuery) use ($deptIds) {
                    $userQuery->whereHas('departments', function ($departmentQuery) use ($deptIds) {
                        $departmentQuery->whereIn('departments.id', $deptIds);
                    });
                });
            } else {
                $query->where('user_id', $user->id);
            }
        }

        return $query;
    }

    private function isCareerActive(EmployeeCareer $career, Carbon $today): bool
    {
        $startDate = $this->careerStartDate($career);
        $realEndDate = $career->real_end_date ? Carbon::parse($career->real_end_date)->startOfDay() : null;

        if ($startDate->gt($today)) {
            return false;
        }

        return $realEndDate === null || $realEndDate->gte($today);
    }

    private function careerStartDate(EmployeeCareer $career): Carbon
    {
        $startDate = $career->real_start_date ?? $career->start_date;

        return Carbon::parse($startDate)->startOfDay();
    }

    private function contractEndDate(EmployeeCareer $career): ?Carbon
    {
        $endDate = $career->real_end_date ?? $career->end_date;

        if ($endDate !== null) {
            return Carbon::parse($endDate)->startOfDay();
        }

        return $this->careerStartDate($career)->copy()->addYear();
    }

    private function contractReviewDate(EmployeeCareer $career): Carbon
    {
        $plannedEnd = $this->careerStartDate($career)->copy()->addYear();
        $endDate = $career->real_end_date ?? $career->end_date;

        if ($endDate !== null) {
            $actualEnd = Carbon::parse($endDate)->startOfDay();
            if ($actualEnd->lt($plannedEnd)) {
                return $actualEnd;
            }
        }

        return $plannedEnd;
    }

    private function mapVacation(
        YearlyVacation $vacation,
        Carbon $today,
        string $status,
        Carbon $windowEnd,
    ): array {
        $startDate = Carbon::parse($vacation->start_date)->startOfDay();
        $endDate = Carbon::parse($vacation->end_date)->startOfDay();
        $employee = $vacation->employee;
        $career = $vacation->employee_career;

        $daysRemaining = $status === 'ongoing'
            ? max(0, $today->diffInDays($endDate, false))
            : max(0, $today->diffInDays($startDate, false));

        $label = $status === 'ongoing'
            ? $this->daysLabel($daysRemaining, 'returns')
            : $this->daysLabel($daysRemaining, 'starts');

        return [
            'vacation_id' => (int) $vacation->id,
            'career_id' => (int) $vacation->employee_career_id,
            'employee_id' => (int) $vacation->employee_id,
            'recipient_user_id' => (int) ($employee?->user_id ?? 0),
            'employee_name' => trim((string) ($employee?->name ?? '')) ?: 'Employee',
            'employee_surname' => trim((string) ($employee?->surname ?? '')),
            'full_name' => $this->fullName($employee?->name, $employee?->surname),
            'position' => $career?->position_ar ?: ($career?->position ?? 'Leave record'),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'days_remaining' => $daysRemaining,
            'status' => $status,
            'status_label' => $label,
            'notification_body' => $status === 'ongoing'
            ? sprintf('%s returns in %s.', $this->fullName($employee?->name, $employee?->surname), $this->daysLabel($daysRemaining, 'days'))
            : sprintf('%s starts in %s.', $this->fullName($employee?->name, $employee?->surname), $this->daysLabel($daysRemaining, 'days')),
        ];
    }

    private function mapIncomingVacation(
        EmployeeCareer $career,
        ?YearlyVacation $lastVacation,
        Carbon $today,
        Carbon $dueDate,
        Carbon $accrualStart,
        float $accruedDays,
    ): array {
        $employee = $career->employee;
        $daysRemaining = $today->diffInDays($dueDate, false);
        $accrualMonths = $this->accrualMonths($accrualStart, $today);
        $daysDescription = $daysRemaining < 0
            ? sprintf('was due %s ago', $this->daysLabel(abs($daysRemaining), 'days'))
            : ($daysRemaining === 0
                ? 'is due today'
                : sprintf('is due in %s', $this->daysLabel($daysRemaining, 'days')));

        return [
            'vacation_id' => $lastVacation ? (int) $lastVacation->id : null,
            'career_id' => (int) $career->id,
            'employee_id' => (int) $career->employee_id,
            'recipient_user_id' => (int) ($employee?->user_id ?? 0),
            'employee_name' => trim((string) ($employee?->name ?? '')) ?: 'Employee',
            'employee_surname' => trim((string) ($employee?->surname ?? '')),
            'full_name' => $this->fullName($employee?->name, $employee?->surname),
            'position' => $career->position_ar ?: ($career->position ?? 'Vacation'),
            'start_date' => $dueDate->toDateString(),
            'end_date' => $lastVacation?->end_date ? Carbon::parse($lastVacation->end_date)->startOfDay()->toDateString() : null,
            'days_remaining' => $daysRemaining,
            'accrual_months' => round($accrualMonths, 2),
            'accrued_days' => $accruedDays,
            'status' => 'incoming_vacation',
            'status_label' => $this->daysLabel($daysRemaining, 'starts'),
            'notification_body' => sprintf(
                '%s vacation %s. Accrued entitlement: %s days.',
                $this->fullName($employee?->name, $employee?->surname),
                $daysDescription,
                $this->formatDays($accruedDays)
            ),
        ];
    }

    private function mapContract(EmployeeCareer $career, Carbon $today, Carbon $alertEndDate): array
    {
        $employee = $career->employee;
        $daysRemaining = $today->diffInDays($alertEndDate, false);
        $activeMonths = $this->accrualMonths($this->careerStartDate($career), $today);
        $deadlineText = $daysRemaining < 0
            ? sprintf('is overdue by %s', $this->daysLabel(abs($daysRemaining), 'days'))
            : ($daysRemaining === 0
                ? 'ends today'
                : sprintf('ends in %s', $this->daysLabel($daysRemaining, 'days')));

        return [
            'career_id' => (int) $career->id,
            'employee_id' => (int) $career->employee_id,
            'recipient_user_id' => (int) ($employee?->user_id ?? 0),
            'employee_name' => trim((string) ($employee?->name ?? '')) ?: 'Employee',
            'employee_surname' => trim((string) ($employee?->surname ?? '')),
            'full_name' => $this->fullName($employee?->name, $employee?->surname),
            'position' => $career->position_ar ?: ($career->position ?? 'Contract'),
            'start_date' => $this->careerStartDate($career)->toDateString(),
            'end_date' => $alertEndDate->toDateString(),
            'days_remaining' => $daysRemaining,
            'status' => 'contract_ending',
            'status_label' => $this->daysLabel($daysRemaining, 'ends'),
            'notification_body' => sprintf(
                '%s contract has been active for %s months and %s.',
                $this->fullName($employee?->name, $employee?->surname),
                $this->formatDays($activeMonths),
                $deadlineText
            ),
        ];
    }

    private function mapWithoutContract(Employee $employee, ?EmployeeCareer $latestCareer, Carbon $today): array
    {
        $nextStart = $latestCareer ? $this->careerStartDate($latestCareer) : null;
        $endedAt = $latestCareer
            ? $this->contractEndDate($latestCareer)
            : null;
        $daysSinceEnd = $endedAt ? max(0, $endedAt->diffInDays($today, false)) : 0;

        $statusLabel = $nextStart !== null && $nextStart->gt($today)
            ? $this->daysLabel($today->diffInDays($nextStart, false), 'starts')
            : ($daysSinceEnd > 0
                ? sprintf('%s without a contract', $this->daysLabel($daysSinceEnd, 'days'))
                : 'No active contract');

        return [
            'career_id' => (int) ($latestCareer?->id ?? 0),
            'employee_id' => (int) $employee->id,
            'recipient_user_id' => (int) ($employee->user_id ?? 0),
            'employee_name' => trim((string) ($employee->name ?? '')) ?: 'Employee',
            'employee_surname' => trim((string) ($employee->surname ?? '')),
            'full_name' => $this->fullName($employee->name, $employee->surname),
            'position' => $latestCareer?->position_ar ?: ($latestCareer?->position ?? 'No active contract'),
            'start_date' => $latestCareer ? $this->careerStartDate($latestCareer)->toDateString() : null,
            'end_date' => $latestCareer ? $this->contractEndDate($latestCareer)?->toDateString() : null,
            'days_remaining' => $daysSinceEnd > 0 ? $daysSinceEnd : ($nextStart ? max(0, $today->diffInDays($nextStart, false)) : 0),
            'status' => 'without_contract',
            'status_label' => $statusLabel,
            'notification_body' => sprintf('%s has no active contract.', $this->fullName($employee->name, $employee->surname)),
        ];
    }

    private function daysLabel(int $days, string $mode): string
    {
        if ($days < 0) {
            $days = abs($days);
            return $days === 1 ? 'overdue by 1 day' : "overdue by {$days} days";
        }

        if ($days <= 0) {
            return match ($mode) {
                'starts' => 'starts today',
                'returns' => 'returns today',
                'ends' => 'ends today',
                default => 'today',
            };
        }

        return match ($mode) {
            'starts' => $days === 1 ? 'starts in 1 day' : "starts in {$days} days",
            'returns' => $days === 1 ? 'returns in 1 day' : "returns in {$days} days",
            'ends' => $days === 1 ? 'ends in 1 day' : "ends in {$days} days",
            default => $days === 1 ? '1 day' : "{$days} days",
        };
    }

    private function accrualMonths(Carbon $from, Carbon $to): float
    {
        if ($to->lte($from)) {
            return 0.0;
        }

        if (method_exists($from, 'floatDiffInMonths')) {
            return max(0.0, (float) $from->floatDiffInMonths($to));
        }

        return max(0.0, (float) $from->diffInMonths($to));
    }

    private function accruedVacationDays(Carbon $from, Carbon $to): float
    {
        return round($this->accrualMonths($from, $to) * 2.5, 2);
    }

    private function formatDays(float $value): string
    {
        $formatted = number_format($value, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }

    private function notificationTitle(string $category, array $item): string
    {
        $name = $item['full_name'] ?? $item['employee_name'] ?? 'Employee';
        $daysRemaining = (int) ($item['days_remaining'] ?? 0);

        return match ($category) {
            'incoming_vacations' => $daysRemaining < 0
                ? "{$name} vacation is overdue"
                : "{$name} vacation starts soon",
            'ongoing_vacations' => $daysRemaining < 0
                ? "{$name} vacation ended"
                : "{$name} returns soon",
            'contracts_ending_soon' => $daysRemaining < 0
                ? "{$name} contract is overdue"
                : "{$name} contract ends soon",
            'without_contract' => "{$name} needs a contract",
            default => 'Attendance alert',
        };
    }

    private function fullName(?string $name, ?string $surname): string
    {
        return trim(sprintf('%s %s', (string) $name, (string) $surname));
    }

    private function emptyOverview(): array
    {
        return [
            'summary' => [
                'employees_total' => 0,
                'contracts_total' => 0,
                'vacations_total' => 0,
                'incoming_vacations_total' => 0,
                'ongoing_vacations_total' => 0,
                'contracts_ending_soon_total' => 0,
                'without_contract_total' => 0,
            ],
            'incoming_vacations' => [],
            'ongoing_vacations' => [],
            'contracts_ending_soon' => [],
            'without_contract' => [],
        ];
    }
}
