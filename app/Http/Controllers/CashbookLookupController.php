<?php

namespace App\Http\Controllers;

use App\Models\Cashbook;
use App\Models\CashbookCategory;
use App\Models\CashbookContact;
use App\Models\CashbookPaymentMode;
use App\Services\CashbookNotificationService;
use App\Services\CashbookService;
use Illuminate\Http\Request;

class CashbookLookupController extends Controller
{
    public function __construct(
        protected CashbookService $cashbookService,
        protected CashbookNotificationService $notificationService,
    ) {
    }

    public function index(Request $request, int $cashbookId)
    {
        $cashbook = $this->findCashbookOrFail($request, $cashbookId);

        return response()->json([
            'data' => $this->cashbookService->getLookupPayload($cashbook),
        ]);
    }

    public function list(Request $request, int $cashbookId, string $type)
    {
        $cashbook = $this->findCashbookOrFail($request, $cashbookId);
        $model = $this->lookupModel($type);

        $items = $model::query()
            ->where('cashbook_id', $cashbook->id)
            ->orderBy('name')
            ->get()
            ->map(fn ($item) => $this->cashbookService->formatLookup($item))
            ->values()
            ->all();

        return response()->json(['data' => $items]);
    }

    public function store(Request $request, int $cashbookId, string $type)
    {
        $cashbook = $this->findCashbookOrFail($request, $cashbookId);
        $validated = $request->validate($this->validationRules($type));
        $model = $this->lookupModel($type);

        $lookup = $model::create(array_merge(
            $validated,
            [
                'cashbook_id' => $cashbook->id,
                'user_id' => $request->user()->id,
                'organization_id' => $request->user()->organization_id,
            ]
        ));

        $this->notificationService->notifyLookupChanged($cashbook, $type, 'created', $lookup->name, $request->user());

        return response()->json([
            'message' => 'Lookup entry created successfully',
            'lookup' => $this->cashbookService->formatLookup($lookup),
            'lookups' => $this->cashbookService->getLookupPayload($cashbook),
        ], 201);
    }

    public function update(Request $request, int $cashbookId, string $type, int $lookupId)
    {
        $cashbook = $this->findCashbookOrFail($request, $cashbookId);
        $validated = $request->validate($this->validationRules($type));
        $model = $this->lookupModel($type);

        $lookup = $model::query()
            ->where('cashbook_id', $cashbook->id)
            ->where('id', $lookupId)
            ->firstOrFail();

        $lookup->update($validated);
        $this->notificationService->notifyLookupChanged($cashbook, $type, 'updated', $lookup->name, $request->user());

        return response()->json([
            'message' => 'Lookup entry updated successfully',
            'lookup' => $this->cashbookService->formatLookup($lookup->refresh()),
            'lookups' => $this->cashbookService->getLookupPayload($cashbook),
        ]);
    }

    public function destroy(Request $request, int $cashbookId, string $type, int $lookupId)
    {
        $cashbook = $this->findCashbookOrFail($request, $cashbookId);
        $model = $this->lookupModel($type);

        $lookup = $model::query()
            ->where('cashbook_id', $cashbook->id)
            ->where('id', $lookupId)
            ->firstOrFail();

        $this->notificationService->notifyLookupChanged($cashbook, $type, 'deleted', $lookup->name, $request->user());
        $lookup->delete();

        return response()->json([
            'message' => 'Lookup entry deleted successfully',
            'lookups' => $this->cashbookService->getLookupPayload($cashbook),
        ]);
    }

    private function lookupModel(string $type): string
    {
        return match ($this->normalizeType($type)) {
            'contacts' => CashbookContact::class,
            'categories' => CashbookCategory::class,
            'payment-modes' => CashbookPaymentMode::class,
            default => abort(404, 'Unknown cashbook lookup type'),
        };
    }

    private function validationRules(string $type): array
    {
        return match ($this->normalizeType($type)) {
            'contacts' => [
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'notes' => 'nullable|string|max:1000',
            ],
            'categories' => [
                'name' => 'required|string|max:255',
                'kind' => 'nullable|in:income,expense,both',
                'notes' => 'nullable|string|max:1000',
            ],
            'payment-modes' => [
                'name' => 'required|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ],
            default => abort(404, 'Unknown cashbook lookup type'),
        };
    }

    private function normalizeType(string $type): string
    {
        return str_replace('_', '-', strtolower(trim($type)));
    }

    private function findCashbookOrFail(Request $request, int $cashbookId): Cashbook
    {
        $cashbook = $this->cashbookService->cashbooksWithAggregatesForUser($request->user())
            ->where('id', $cashbookId)
            ->first();

        abort_if(!$cashbook, 404, 'Cashbook not found');

        return $cashbook;
    }
}
