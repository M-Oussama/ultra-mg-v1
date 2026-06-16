<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|max:512',
            'platform' => 'nullable|string|max:20',
            'device_name' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $user->forceFill([
            'fcm_token' => $validated['token'],
            'fcm_platform' => $validated['platform'] ?? null,
            'fcm_device_name' => $validated['device_name'] ?? null,
            'fcm_token_updated_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Push token registered successfully',
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->forceFill([
            'fcm_token' => null,
            'fcm_platform' => null,
            'fcm_device_name' => null,
            'fcm_token_updated_at' => null,
        ])->save();

        return response()->json([
            'message' => 'Push token cleared successfully',
        ]);
    }
}
