<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MB160Controller extends Controller
{
    private $ip = "192.168.1.201"; // device IP
    private $port = 4370; // default ZKTeco port
    private $sessionId = 0;

    // Entry point
    public function getLogs()
    {
        $socket = @fsockopen($this->ip, $this->port, $errno, $errstr, 2);

        if (!$socket) {
            return response()->json(['error' => "Connection failed: $errstr ($errno)"]);
        }

        // Step 1: Connect
        $this->sendCommand($socket, 1000); // CMD_CONNECT
        $this->readResponse($socket);

        // Step 2: Get Attendance Logs
        $this->sendCommand($socket, 13); // CMD_ATTLOG_RRQ
        $data = $this->readResponse($socket);

        fclose($socket);

        if (!$data) {
            return response()->json(['logs' => []]);
        }

        // Step 3: Parse logs (basic parsing, just dump raw for now)
        return response()->json([
            'raw_logs' => bin2hex($data), // debug
            'length' => strlen($data)
        ]);
    }

    private function sendCommand($socket, $command, $commandString = '')
    {
        $sessionId = $this->sessionId;
        $replyId = rand(1, 65535);
        $buf = $this->createHeader($command, $sessionId, $replyId, $commandString);

        fwrite($socket, $buf);
    }

    private function readResponse($socket)
    {
        $data = fread($socket, 4096);
        return $data;
    }

    private function createHeader($command, $sessionId, $replyId, $commandString)
    {
        $commandString = $commandString ? $commandString : '';
        $length = 8 + strlen($commandString);
        $buf = pack('SSSS', $command, $length, $sessionId, $replyId) . $commandString;

        return $buf;
    }
}
