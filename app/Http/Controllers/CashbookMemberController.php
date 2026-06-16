<?php

namespace App\Http\Controllers;

use App\Models\Cashbook;
use App\Models\CashbookMember;
use App\Models\User;
use App\Services\CashbookNotificationService;
use App\Services\CashbookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CashbookMemberController extends Controller
{
    public function __construct(
        protected CashbookService $cashbookService,
        protected CashbookNotificationService $notificationService,
    ) {
    }

    public function index(Request $request, $id): JsonResponse
    {
        $cashbook = $this->findCashbookOrFail($request, (int) $id);

        return response()->json([
            'data' => $this->cashbookService->getMembersPayload($cashbook),
            'meta' => [
                'count' => count($this->cashbookService->getMembersPayload($cashbook)),
            ],
        ]);
    }

    public function store(Request $request, $id): JsonResponse
    {
        $cashbook = $this->findCashbookOrFail($request, (int) $id);
        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($cashbook) {
                    $query->where('organization_id', $cashbook->organization_id);
                }),
            ],
            'role' => ['required', 'string', Rule::in(['viewer', 'editor', 'manager'])],
        ]);

        $memberUser = User::query()
            ->where('organization_id', $cashbook->organization_id)
            ->where('id', $validated['user_id'])
            ->firstOrFail();

        abort_if((int) $memberUser->id === (int) $cashbook->user_id, 422, 'The book owner is already a member.');

        $member = CashbookMember::updateOrCreate(
            [
                'cashbook_id' => $cashbook->id,
                'user_id' => $memberUser->id,
            ],
            [
                'role' => $validated['role'],
            ]
        );

        $this->notificationService->notifyMemberChanged($cashbook, $memberUser, 'added', $request->user(), $validated['role']);

        return response()->json([
            'message' => 'Member added successfully',
            'member' => $this->formatMember($cashbook, $memberUser, $member),
            'members' => $this->cashbookService->getMembersPayload($cashbook->fresh(['user', 'members']) ?? $cashbook),
        ], 201);
    }

    public function update(Request $request, $cashbookId, $memberId): JsonResponse
    {
        $cashbook = $this->findCashbookOrFail($request, (int) $cashbookId);
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(['viewer', 'editor', 'manager'])],
        ]);

        $member = CashbookMember::query()
            ->where('cashbook_id', $cashbook->id)
            ->where('id', (int) $memberId)
            ->firstOrFail();

        $member->update([
            'role' => $validated['role'],
        ]);

        $this->notificationService->notifyMemberChanged($cashbook, $member->user, 'updated', $request->user(), $validated['role']);

        return response()->json([
            'message' => 'Member updated successfully',
            'member' => $this->formatMember($cashbook, $member->user, $member),
            'members' => $this->cashbookService->getMembersPayload($cashbook->fresh(['user', 'members']) ?? $cashbook),
        ]);
    }

    public function destroy(Request $request, $cashbookId, $memberId): JsonResponse
    {
        $cashbook = $this->findCashbookOrFail($request, (int) $cashbookId);

        $member = CashbookMember::query()
            ->with('user')
            ->where('cashbook_id', $cashbook->id)
            ->where('id', (int) $memberId)
            ->firstOrFail();

        $memberUser = $member->user;
        $member->delete();

        if ($memberUser) {
            $this->notificationService->notifyMemberChanged($cashbook, $memberUser, 'removed', $request->user(), $member->role);
        }

        return response()->json([
            'message' => 'Member removed successfully',
            'members' => $this->cashbookService->getMembersPayload($cashbook->fresh(['user', 'members']) ?? $cashbook),
        ]);
    }

    private function findCashbookOrFail(Request $request, int $cashbookId): Cashbook
    {
        $cashbook = $this->cashbookService->cashbooksWithAggregatesForUser($request->user())
            ->where('id', $cashbookId)
            ->first();

        abort_if(!$cashbook, 404, 'Cashbook not found');

        return $cashbook;
    }

    private function formatMember(Cashbook $cashbook, User $user, ?CashbookMember $membership = null): array
    {
        return [
            'id' => $membership?->id ?? $user->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'role' => $membership?->role ?? 'viewer',
            'is_owner' => false,
            'cashbook_id' => $cashbook->id,
            'created_at' => optional($membership?->created_at)->toIso8601String(),
            'updated_at' => optional($membership?->updated_at)->toIso8601String(),
        ];
    }
}
