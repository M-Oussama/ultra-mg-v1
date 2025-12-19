<?php

namespace App\Services;

class MB160Service
{
    private $ip;
    private $port;
    private $socket;

    public function __construct($ip = '192.168.1.201', $port = 4370)
    {
        $this->ip = $ip;
        $this->port = $port;
    }

    public function connect()
    {
        $this->socket = fsockopen($this->ip, $this->port, $errno, $errstr, 10);
        if (!$this->socket) {
            throw new \Exception("Connection failed: $errstr ($errno)");
        }

        stream_set_timeout($this->socket, 2);
        return $this->socket;
    }

    public function close()
    {
        if ($this->socket) {
            fclose($this->socket);
        }
    }

    // 👉 Example: send raw command
    private function sendCommand($command, $data = '')
    {
        $sessionId = 0;
        $replyId = rand(1, 9999);

        $buf = pack('vvvv', $command, $replyId, $sessionId, strlen($data)) . $data;

        fwrite($this->socket, $buf);
        return fread($this->socket, 1024);
    }

    // 👉 Example: get attendance logs (raw)
    public function getAttendanceLogs()
    {
        // Command for attendance log request
        $CMD_ATTLOG_REQ = 0x0122;

        $response = $this->sendCommand($CMD_ATTLOG_REQ);

        return bin2hex($response); // later we’ll parse it
    }
}
