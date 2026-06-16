<?php

namespace App\Console\Commands;

use App\Services\AttendanceAlertService;
use Illuminate\Console\Command;

class SendAttendanceAlerts extends Command
{
    protected $signature = 'attendance:send-alerts';

    protected $description = 'Send daily attendance alerts for upcoming vacations and expiring contracts.';

    public function handle(AttendanceAlertService $alertService): int
    {
        $result = $alertService->sendDailyNotifications(3);

        $this->info(sprintf(
            'Attendance alerts processed. Notifications sent: %d',
            (int) ($result['sent'] ?? 0)
        ));

        return self::SUCCESS;
    }
}
