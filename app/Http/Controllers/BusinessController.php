<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function current(Request $request): JsonResponse
    {
        $user = $request->user();
        $organization = $user?->organization;

        return response()->json([
            'business' => $organization ? [
                'id' => $organization->id,
                'name' => $organization->name,
                'description' => $organization->description,
                'created_at' => optional($organization->created_at)->toIso8601String(),
                'updated_at' => optional($organization->updated_at)->toIso8601String(),
            ] : null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $organization = Organization::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $user = $request->user();
        $user->forceFill([
            'organization_id' => $organization->id,
        ])->save();

        return response()->json([
            'message' => 'Business created successfully',
            'business' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'description' => $organization->description,
                'created_at' => optional($organization->created_at)->toIso8601String(),
                'updated_at' => optional($organization->updated_at)->toIso8601String(),
            ],
            'business_id' => $organization->id,
            'business_name' => $organization->name,
        ], 201);
    }
}
