<?php

namespace App\Services;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FirebaseCloudMessagingService
{
    private const TOKEN_URI = 'https://oauth2.googleapis.com/token';
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    public function isConfigured(): bool
    {
        return $this->serviceAccount() !== null;
    }

    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        if (trim($token) === '' || !$this->isConfigured()) {
            return false;
        }

        $accessToken = $this->accessToken();
        $projectId = $this->projectId();

        if ($accessToken === null || $projectId === null) {
            return false;
        }

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $this->stringifyData($data),
            ],
        ];

        if (empty($payload['message']['data'])) {
            unset($payload['message']['data']);
        }

        try {
            $client = new Client([
                'timeout' => 20,
                'connect_timeout' => 10,
            ]);

            $client->post(sprintf('https://fcm.googleapis.com/v1/projects/%s/messages:send', $projectId), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning('FCM send failed', [
                'error' => $e->getMessage(),
                'project_id' => $projectId,
            ]);

            return false;
        }
    }

    private function accessToken(): ?string
    {
        return Cache::remember('firebase.fcm.access_token', Carbon::now()->addMinutes(50), function () {
            return $this->fetchAccessToken();
        });
    }

    private function fetchAccessToken(): ?string
    {
        $serviceAccount = $this->serviceAccount();

        if ($serviceAccount === null) {
            return null;
        }

        $privateKey = str_replace('\n', "\n", (string) Arr::get($serviceAccount, 'private_key'));
        $clientEmail = (string) Arr::get($serviceAccount, 'client_email');
        $privateKeyId = (string) Arr::get($serviceAccount, 'private_key_id');
        $tokenUri = (string) Arr::get($serviceAccount, 'token_uri', self::TOKEN_URI);

        if ($privateKey === '' || $clientEmail === '') {
            return null;
        }

        $now = time();
        $claims = [
            'iss' => $clientEmail,
            'scope' => self::SCOPE,
            'aud' => $tokenUri,
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $jwt = JWT::encode($claims, $privateKey, 'RS256', $privateKeyId ?: null);

        try {
            $client = new Client([
                'timeout' => 20,
                'connect_timeout' => 10,
            ]);

            $response = $client->post($tokenUri, [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ],
            ]);

            $payload = json_decode((string) $response->getBody(), true);

            return is_array($payload) ? ($payload['access_token'] ?? null) : null;
        } catch (\Throwable $e) {
            Log::warning('Unable to fetch Firebase access token', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function projectId(): ?string
    {
        $configured = trim((string) config('services.firebase.project_id'));
        if ($configured !== '') {
            return $configured;
        }

        $serviceAccount = $this->serviceAccount();
        if ($serviceAccount === null) {
            return null;
        }

        $projectId = trim((string) Arr::get($serviceAccount, 'project_id'));
        return $projectId !== '' ? $projectId : null;
    }

    private function serviceAccount(): ?array
    {
        $configured = trim((string) config('services.firebase.credentials'));
        if ($configured === '') {
            return null;
        }

        $path = $configured;
        if (!preg_match('/^(?:[A-Za-z]:\\\\|\\\\|\/)/', $path)) {
            $path = base_path($path);
        }

        if (is_file($path)) {
            $contents = file_get_contents($path);
            if ($contents === false) {
                return null;
            }

            $decoded = json_decode($contents, true);
            return is_array($decoded) ? $decoded : null;
        }

        $decoded = json_decode($configured, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function stringifyData(array $data): array
    {
        return collect($data)
            ->filter(static fn ($value) => $value !== null && $value !== '')
            ->map(static fn ($value) => is_bool($value) ? ($value ? 'true' : 'false') : (string) $value)
            ->all();
    }
}
