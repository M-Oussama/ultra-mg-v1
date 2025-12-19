<?php

namespace App\Http\Controllers;

use App\Models\RawLog;
use App\Models\User;
use App\Models\ZKAttendance;
use App\Models\ZKEmployee;
use App\Models\ZKEmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZKPushController extends Controller
{

    public function handle(Request $request)
    {
        Log::info("ZK /zk/push hit", ['payload' => $request->all()]);

        $uid = $request->input('uid');
        $timestamp = $request->input('timestamp');

        Log::info("ZK /zk/push hit", ['payload' => $request->all()]);

        if (!$uid || !$timestamp) {
            return response()->json(['status' => 'fail', 'message' => 'Missing uid or timestamp'], 400);
        }

        $user = User::where('user_id', $uid)->first();
        if (!$user) {
            return response()->json(['status' => 'fail', 'message' => 'User not found'], 404);
        }

        $log = RawLog::create([
            'user_id' => $user->id,
            'timestamp' => $timestamp,
        ]);

        // Human-readable log: infer login/logout by same-day parity
        try {
            $ts = Carbon::parse($timestamp);
            $action = $this->determineAction($user->id, $ts);
            Log::info(sprintf('Attendance: %s (PIN %s) %s at %s', $user->name ?? ('User#'.$user->id), $uid, strtoupper($action), $ts->format('Y-m-d H:i:s')));
        } catch (\Throwable $e) {
            // ignore logging errors
        }

      //  $this->updateAttendanceFromRawLogs($user->id, Carbon::parse($timestamp)->toDateString());

        return response()->json(['status' => 'success']);
    }

    // Handle ZKTeco iClock protocol push (commonly posts to /iclock/cdata)
    public function handleIclock(Request $request)
    {
          Log::info('cam');
        Log::info('ZK /iclock/cdata hit', [
            'method' => $request->method(),
            'query' => $request->query(),
            'headers' => $request->headers->all(),
        ]);
        // Devices may send GET with query or POST form-encoded
        $all = $request->all();
        Log::info('ZK /iclock/cdata hit', [
            'method' => $request->method(),
            'query' => $request->query(),
            'payload' => $all,
            'headers' => $request->headers->all(),
        ]);

        // Reply to options query so device knows server capabilities
        $options = $request->query('options');
        if ($options) {
            // Common options expected by iClock devices
            $body = implode("\n", [
                'ATTLOGStamp=0',
                'OPERLOGStamp=0',
                'ErrorDelay=30',
                'Delay=10',
                'TransTimes=00:00;14:05',
                'TransInterval=1',
                'Realtime=1',
                'Encrypt=0',
            ]) . "\n"; // end with newline
            return response($body, 200)->header('Content-Type', 'text/plain');
        }

        // Typical fields: table=ATTLOG, Pin, Time or DateTime, Verify, Status, WorkCode
        $table = $request->input('table', $request->input('Table'));
        if ($table && strtoupper($table) !== 'ATTLOG') {
            // Not an attendance log; acknowledge and exit
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        $pin = $request->input('Pin', $request->input('pin', $request->input('uid')));
        $time = $request->input('Time', $request->input('DateTime', $request->input('timestamp')));

        // Some firmwares POST plain text lines.
        // Formats observed:
        //   1) "PIN=123\tTime=2025-09-02 09:00:00\tVerify=1\tStatus=0\tWorkCode=0"
        //   2) "123\t2025-09-02 09:00:00\t1\t0\t0" (no keys, tab/space separated)
        if (strtoupper((string) $table) === 'ATTLOG' && in_array($request->getMethod(), ['POST', 'PUT'])) {
            $raw = (string) $request->getContent();
            if ($raw !== '') {
                Log::info('ZK iClock raw body', ['len' => strlen($raw), 'preview' => substr($raw, 0, 200)]);

                $this->storeAttendance($raw);
                // If direct fields missing, try to parse line-by-line
                if (!$pin || !$time) {
                    $saved = 0;
                    $lines = preg_split('/\r?\n/', $raw);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if ($line === '') continue;

                        $lpin = null; $ltime = null;
                        // Pattern 1: keyed
                        if (preg_match('/(?:^|\t)PIN=([^\t\r\n]+)/', $line, $m1)) {
                            $lpin = trim($m1[1]);
                        }
                        if (preg_match('/(?:^|\t)(?:Time|DateTime)=([^\t\r\n]+)/', $line, $m2)) {
                            $ltime = trim($m2[1]);
                        }

                        // Pattern 2: no keys, first token=PIN, second token=DateTime
                        if (!$lpin || !$ltime) {
                            // Split on tabs or multiple spaces
                            $tokens = preg_split('/\s+/', $line);
                            if (count($tokens) >= 2) {
                                // token[0] pin like digits, token[1] datetime
                                if (preg_match('/^\d+$/', $tokens[0]) && preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}$/', $tokens[1])) {
                                    $lpin = $lpin ?: $tokens[0];
                                    $ltime = $ltime ?: $tokens[1];
                                }
                            }
                        }

                        if ($lpin && $ltime) {
                            $user = User::where('user_id', $lpin)->first();
                            
                            try {
                                $tsLine = Carbon::parse($ltime);
                            } catch (\Throwable $e) {
                                Log::warning('ZK iClock timestamp parse failed (line)', ['time' => $ltime, 'line' => $line]);
                                continue;
                            }

                            // Store in the new table requested by user
                            // Format: employee_id, time, type
                            // The user mentioned: 2 is id, 2025-12-19 21:34:02 is time, t1 is type (?)
                            // Based on log: "2\t2025-12-19 21:34:02\t255\t1\t0\t0\t0\t0\t0\t0\t"
                            // Token 0: PIN, Token 1: TIME, Token 3: Status (usually 0: check-in, 1: check-out)
                            
                            $tokens = preg_split('/\s+/', $line);
                            $type = null;
                            if (isset($tokens[3]) && is_numeric($tokens[3])) {
                                $type = (int) $tokens[3];
                            }

                            ZKEmployeeAttendance::create([
                                'employee_id' => $lpin,
                                'user_id' => $user ? $user->id : null,
                                'timestamp' => $tsLine,
                                'type' => $type,
                                'raw_line' => $line,
                            ]);

                            if (!$user) {
                                Log::warning('ZK iClock user not found for PIN (line)', ['pin' => $lpin, 'line' => $line]);
                                continue;
                            }
                            
                            RawLog::create([
                                'user_id' => $user->id,
                                'timestamp' => $tsLine,
                            ]);
                            // Human readable per line
                            try {
                                $actionLine = $this->determineAction($user->id, $tsLine);
                                Log::info(sprintf('Attendance: %s (PIN %s) %s at %s', $user->name ?? ('User#'.$user->id), $lpin, strtoupper($actionLine), $tsLine->format('Y-m-d H:i:s')));
                            } catch (\Throwable $e) {}
                            $saved++;
                        }
                    }

                    if ($saved > 0) {
                        // We handled entries; acknowledge
                        return response('OK', 200)->header('Content-Type', 'text/plain');
                    }
                }

                // If still not parsed, attempt single-extraction on the whole body for keyed format
                if ((!$pin || !$time)) {
                    if (preg_match('/(?:^|\n)PIN=([^\t\n\r]+)/', $raw, $m1)) {
                        $pin = $pin ?: trim($m1[1]);
                    }
                    if (preg_match('/\b(?:Time|DateTime)=([^\t\n\r]+)/', $raw, $m2)) {
                        $time = $time ?: trim($m2[1]);
                    }
                    Log::info('ZK iClock raw body parsed (fallback)', ['pin' => $pin, 'time' => $time]);
                }
            }
        }

        if (!$pin || !$time) {
            // Acknowledge to prevent device retry; log for diagnostics
            Log::warning('ZK iClock missing Pin/Time', ['payload' => $all]);
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        // Find app user mapped to device PIN via users.user_id
        $user = User::where('user_id', $pin)->first();
        if (!$user) {
            Log::warning('ZK iClock user not found for PIN', ['pin' => $pin]);
            // Still acknowledge to avoid device retry storms
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        try {
            $ts = Carbon::parse($time);
        } catch (\Throwable $e) {
            Log::warning('ZK iClock timestamp parse failed', ['time' => $time, 'error' => $e->getMessage()]);
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        // Store raw log linked to internal user id (consistent with existing handle())
        RawLog::create([
            'user_id' => $user->id,
            'timestamp' => $ts,
        ]);

       

        // Human-readable log: infer login/logout by same-day parity
        try {
            $action = $this->determineAction($user->id, $ts);
            Log::info(sprintf('Attendance: %s (PIN %s) %s at %s', $user->name ?? ('User#'.$user->id), $pin, strtoupper($action), $ts->format('Y-m-d H:i:s')));
        } catch (\Throwable $e) {
            // ignore logging errors
        }

        // iClock expects plain text OK
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function storeAttendance($raw)
    {
     
        $raw = trim($raw);

        // ZK may send multiple records
        $lines = preg_split("/\r\n|\n|\r/", $raw);

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            $parts = explode("\t", trim($line));

            // Must have at least user_id, time, and type index
            if (count($parts) < 4) {
                continue;
            }

            $userId = $parts[0] ?? null;
            $time   = $parts[1] ?? null;
            $type   = $parts[3] ?? null;

            // Validate user_id
            if (!is_numeric($userId)) {
                continue;
            }

            // Validate type (IN/OUT)
            if (!is_numeric($type)) {
                continue;
            }

            // Validate datetime
            try {
                $datetime = Carbon::createFromFormat('Y-m-d H:i:s', $time);
            } catch (\Exception $e) {
                continue;
            }

            $zk_employee = ZKEmployee::where('zk_id', (int) $userId)->first();
            if(!$zk_employee) {
               $zk_employee =  ZKEmployee::create([
                    'zk_id'=> (int) $userId,
                    'name' => "",
                    "surname" => ""
                ]);
            }
            // ✅ Everything is valid → store
            ZKEmployeeAttendance::create([
                'employee_id'   => (int) $userId,
                'punched_at'=> $datetime,
                'type'      => (int) $type,
            ]);

            // Optional clean log
            Log::info('ZK Attendance stored', [
                'user_id' => $userId,
                'time'    => $datetime->toDateTimeString(),
                'type'    => $type,
            ]);
       
    }
}
    // Devices poll this for pending server commands
    public function handleIclockGetRequest(Request $request)
    {
        Log::info('ZK /iclock/getrequest hit', [
            'method' => $request->method(),
            'query' => $request->query(),
            'headers' => $request->headers->all(),
        ]);
        // No pending commands; many firmwares expect literal 'OK'
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    // Device submits command results here; acknowledge
    public function handleIclockDeviceCmd(Request $request)
    {
        Log::info('ZK /iclock/devicecmd hit', [
            'method' => $request->method(),
            'query' => $request->query(),
            'payload' => $request->all(),
            'raw' => $request->getContent(),
            'headers' => $request->headers->all(),
        ]);
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    // Determine login/logout by counting same-day punches before/including this timestamp
    private function determineAction(int $userId, Carbon $ts): string
    {
        $count = RawLog::where('user_id', $userId)
            ->whereDate('timestamp', $ts->toDateString())
            ->where('timestamp', '<=', $ts)
            ->count();
        // If this is the 1st, 3rd, 5th... punch of the day => LOGIN, else LOGOUT
        return ($count % 2 === 1) ? 'login' : 'logout';
    }

    private function updateAttendanceFromRawLogs($userId, $date)
    {
        $logs = RawLog::where('user_id', $userId)
            ->whereDate('timestamp', $date)
            ->orderBy('timestamp')
            ->get();

        if ($logs->count() < 2) return; // Not enough logs yet

        $checkIn = Carbon::parse($logs->first()->timestamp);
        $checkOut = Carbon::parse($logs->last()->timestamp);
        $workedMinutes = $checkIn->diffInMinutes($checkOut);

        ZKAttendance::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'worked_minutes' => $workedMinutes,
            ]
        );
    }
}
