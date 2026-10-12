<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function templates(): JsonResponse
    {
        $wabaId = config('services.whatsapp.waba_id');
        $accessToken = config('services.whatsapp.access_token');
        if (blank($wabaId) || blank($accessToken)) {
            return response()->json(['message' => 'WhatsApp Cloud API is not configured on the server.'], 503);
        }

        $url = 'https://graph.facebook.com/'.config('services.whatsapp.graph_version', 'v25.0')."/{$wabaId}/message_templates";
        $after = null;
        $templates = collect();
        $statusCounts = [];

        // Meta paginates templates. Approved templates are not guaranteed to
        // be on the first page, especially for long-lived business accounts.
        // Use the cursor instead of following Meta's arbitrary `next` URL.
        for ($page = 0; $page < 20; $page++) {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(15)
                ->get($url, array_filter([
                    'fields' => 'name,language,status,category',
                    'limit' => 250,
                    'after' => $after,
                ], fn ($value) => $value !== null));

            if (! $response->successful()) {
                return response()->json(['message' => $this->metaError($response)], $response->status());
            }

            foreach ((array) $response->json('data', []) as $template) {
                if (! is_array($template)) {
                    continue;
                }

                $status = strtoupper(trim((string) ($template['status'] ?? 'UNKNOWN')));
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
                if ($status === 'APPROVED') {
                    $templates->push($template);
                }
            }

            $nextUrl = $response->json('paging.next');
            $nextAfter = $response->json('paging.cursors.after');
            if (! is_string($nextUrl) || $nextUrl === ''
                || ! is_string($nextAfter) || $nextAfter === '' || $nextAfter === $after) {
                break;
            }
            $after = $nextAfter;
        }

        $templates = $templates
            ->unique(fn (array $template) => strtolower((string) ($template['name'] ?? ''))
                .':'.strtolower((string) ($template['language'] ?? '')))
            ->values();

        return response()->json([
            'data' => $templates,
            'meta' => [
                'approved_count' => $templates->count(),
                'status_counts' => $statusCounts,
            ],
        ]);
    }

    public function broadcast(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:4096',
            'type' => 'nullable|in:text,template',
            'template_name' => 'nullable|string|max:512',
            'template_language' => 'nullable|string|max:50',
            'recipients' => 'required|array|min:1|max:250',
            'recipients.*.phone' => 'required|string|max:40',
            'recipients.*.name' => 'nullable|string|max:255',
            'recipients.*.client_id' => 'nullable',
            'recipients.*.temporary' => 'nullable|boolean',
        ]);

        $type = $validated['type'] ?? 'text';
        if ($type === 'text' && blank($validated['message'] ?? null)) {
            return response()->json(['message' => 'A message is required.'], 422);
        }
        if ($type === 'template' && blank($validated['template_name'] ?? null)) {
            return response()->json(['message' => 'Choose an approved WhatsApp template.'], 422);
        }

        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $accessToken = config('services.whatsapp.access_token');
        if (blank($phoneNumberId) || blank($accessToken)) {
            return response()->json([
                'message' => 'WhatsApp Cloud API is not configured on the server.',
            ], 503);
        }

        $sent = 0;
        $failed = 0;
        $errors = [];
        $messageIds = [];

        foreach ($validated['recipients'] as $recipient) {
            $phone = $this->normalizePhone($recipient['phone']);
            if ($phone === null) {
                $failed++;
                $errors[] = [
                    'phone' => $recipient['phone'],
                    'error' => 'Invalid phone number.',
                ];

                continue;
            }

            $response = $this->sendMessage(
                $phone,
                $type,
                $validated['message'] ?? '',
                $validated['template_name'] ?? null,
                $validated['template_language'] ?? null,
            );
            if ($response->successful()) {
                $sent++;
                foreach ((array) $response->json('messages', []) as $message) {
                    if (is_array($message) && isset($message['id'])) {
                        $messageIds[] = $message['id'];
                    }
                }

                continue;
            }

            $failed++;
            $errors[] = [
                'phone' => $phone,
                'error' => $this->metaError($response),
            ];
        }

        return response()->json([
            // Meta acceptance is not delivery; delivery requires webhooks.
            'accepted' => $sent,
            'sent' => $sent,
            'failed' => $failed,
            'total' => count($validated['recipients']),
            'message_ids' => $messageIds,
            'errors' => $errors,
        ]);
    }

    private function sendMessage(
        string $phone,
        string $type,
        string $message,
        ?string $templateName,
        ?string $templateLanguage,
    ): Response {
        $version = config('services.whatsapp.graph_version', 'v25.0');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $url = "https://graph.facebook.com/{$version}/{$phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
        ];

        if ($type === 'template') {
            $payload['type'] = 'template';
            $payload['template'] = [
                'name' => $templateName,
                'language' => [
                    'code' => $templateLanguage ?: 'en_US',
                ],
            ];
        } else {
            $payload['type'] = 'text';
            $payload['text'] = [
                'preview_url' => true,
                'body' => $message,
            ];
        }

        return Http::withToken(config('services.whatsapp.access_token'))
            ->acceptJson()
            ->timeout(30)
            ->post($url, $payload);
    }

    private function normalizePhone(string $raw): ?string
    {
        $phone = preg_replace('/\D+/', '', trim($raw));
        if ($phone === null || $phone === '') {
            return null;
        }

        if (str_starts_with($phone, '00')) {
            $phone = substr($phone, 2);
        } elseif (str_starts_with($phone, '0')) {
            $phone = config('services.whatsapp.country_code', '213').substr($phone, 1);
        }

        return strlen($phone) >= 8 ? $phone : null;
    }

    private function metaError(Response $response): string
    {
        $error = $response->json('error');
        if (is_array($error) && isset($error['message'])) {
            return (string) $error['message'];
        }

        return 'Meta returned HTTP '.$response->status().'.';
    }
}
