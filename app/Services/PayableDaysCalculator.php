<?php

namespace App\Services;

use App\Models\EmployeeAttendance;
use App\Models\Holiday;
use Carbon\Carbon;

class PayableDaysCalculator
{
    private const WEEKEND_DAYS = [Carbon::THURSDAY, Carbon::FRIDAY];

    public function calculate(int $employeeId, int $month, int $year): array
    {
        $presentDays = EmployeeAttendance::query()
            ->where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('present', true)
            ->get(['date']);

        $holidayDates = Holiday::query()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('date')
            ->mapWithKeys(static fn ($date) => [(string) $date => true]);

        $normalDays = 0;
        $extraDays = 0;

        foreach ($presentDays as $presentDay) {
            $date = Carbon::parse($presentDay->date);
            $isExtra = in_array($date->dayOfWeek, self::WEEKEND_DAYS, true)
                || $holidayDates->has($date->toDateString());

            if ($isExtra) {
                $extraDays++;
            } else {
                $normalDays++;
            }
        }

        // TODO: unworked weekend pay rule, to be added later
        return [
            'normal_days' => $normalDays,
            'extra_days' => $extraDays,
            'payable_days' => $normalDays + (2 * $extraDays),
        ];
    }
}
