<?php

namespace App\Http\Controllers;

use App\Models\Cashbook;
use App\Models\Transaction;
use App\Services\CashbookImportService;
use App\Services\CashbookNotificationService;
use App\Services\CashbookService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CashbookController extends Controller
{
    public function __construct(
        protected CashbookService $cashbookService,
        protected CashbookNotificationService $notificationService,
        protected CashbookImportService $importService,
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $search = trim((string) $request->query('search', ''));

        $query = $this->cashbookService->cashbooksWithAggregatesForUser($user);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $cashbooks = $query
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Cashbook $cashbook) => $this->cashbookService->formatCashbook($cashbook))
            ->values()
            ->all();

        return response()->json([
            'data' => $cashbooks,
            'meta' => [
                'count' => count($cashbooks),
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        $cashbook = Cashbook::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
        ]);

        $this->notificationService->notifyCashbookCreated($cashbook, $user);

        return response()->json([
            'message' => 'Cashbook created successfully',
            'cashbook' => $this->cashbookService->formatCashbook($cashbook),
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $cashbook = $this->findCashbookOrFail($request, $id);

        return response()->json([
            'cashbook' => $this->cashbookService->formatCashbook($cashbook),
            'summary' => $this->cashbookService->getSummary($cashbook),
            'lookups' => $this->cashbookService->getLookupPayload($cashbook),
            'members' => $this->cashbookService->getMembersPayload($cashbook),
            'recent_transactions' => $this->recentTransactionsPayload($cashbook),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $cashbook = $this->findCashbookOrFail($request, $id);
        $cashbook->update($validated);
        $updatedCashbook = $this->cashbookService->cashbooksWithAggregatesForUser($request->user())
            ->where('id', $cashbook->id)
            ->first();

        $this->notificationService->notifyCashbookUpdated($cashbook, $request->user());

        return response()->json([
            'message' => 'Cashbook updated successfully',
            'cashbook' => $updatedCashbook ? $this->cashbookService->formatCashbook($updatedCashbook) : $this->cashbookService->formatCashbook($cashbook),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $cashbook = $this->findCashbookOrFail($request, $id);
        $this->notificationService->notifyCashbookDeleted($cashbook, $request->user());
        $cashbook->delete();

        return response()->json(['message' => 'Cashbook deleted successfully']);
    }

    public function summary(Request $request, $id)
    {
        $cashbook = $this->findCashbookOrFail($request, $id);

        return response()->json([
            'cashbook' => $this->cashbookService->formatCashbook($cashbook),
            'summary' => $this->cashbookService->getSummary($cashbook),
        ]);
    }

    public function exportPdf(Request $request, $id)
    {
        $cashbook = $this->findCashbookOrFail($request, $id);
        $cashbookData = $this->cashbookService->formatCashbook($cashbook);
        $summary = $this->cashbookService->getSummary($cashbook);
        $lookups = $this->cashbookService->getLookupPayload($cashbook);
        $transactions = $this->recentTransactionsPayload($cashbook, false);

        $pdf = Pdf::loadView('exports.cashbook_statement_pdf', [
            'cashbook' => $cashbookData,
            'summary' => $summary,
            'lookups' => $lookups,
            'transactions' => $transactions,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Cashbook_' . $cashbook->id . '_Statement.pdf');
    }

    public function importExcel(Request $request, $id): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:51200',
        ]);

        $cashbook = $this->findCashbookOrFail($request, $id);
        $stats = $this->importService->importLegacySpreadsheet(
            $cashbook,
            $request->file('file'),
            $request->user()
        );

        $freshCashbook = $this->cashbookService->cashbooksWithAggregatesForUser($request->user())
            ->where('id', $cashbook->id)
            ->first() ?? $cashbook->fresh();

        $this->notificationService->notifyImportCompleted($cashbook, $stats, $request->user());

        return response()->json([
            'message' => 'Legacy cashbook spreadsheet imported successfully',
            'stats' => $stats,
            'cashbook' => $freshCashbook ? $this->cashbookService->formatCashbook($freshCashbook) : $this->cashbookService->formatCashbook($cashbook),
            'summary' => $this->cashbookService->getSummary($cashbook),
            'recent_transactions' => $this->recentTransactionsPayload($cashbook),
            'lookups' => $this->cashbookService->getLookupPayload($cashbook),
        ]);
    }

    private function findCashbookOrFail(Request $request, int $id): Cashbook
    {
        $user = $request->user();

        $cashbook = $this->cashbookService->cashbooksWithAggregatesForUser($user)
            ->where('id', $id)
            ->first();

        abort_if(!$cashbook, 404, 'Cashbook not found');

        return $cashbook;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function recentTransactionsPayload(Cashbook $cashbook, bool $limitToRecent = true): array
    {
        $query = Transaction::query()
            ->where('cashbook_id', $cashbook->id)
            ->with(['contact', 'category', 'paymentMode', 'media', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->orderByRaw('transaction_time is null')
            ->orderBy('transaction_time', 'desc')
            ->orderBy('id', 'desc');

        if ($limitToRecent) {
            $query->limit(10);
        }

        return $query->get()
            ->map(fn (Transaction $transaction) => $this->cashbookService->formatTransaction($transaction))
            ->values()
            ->all();
    }
}
