<?php

namespace App\Http\Controllers;

use App\Models\EntrepreneurSettlement;
use App\Models\EntrepreneurSettlementTransaction;
use App\Models\SettlementEntrepreneur;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EntrepreneurSettlementController extends Controller
{
    public function listEntrepreneurs(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
        ]);

        $companyId = (int) ($validated['company_id'] ?? 2);
        $entrepreneurs = SettlementEntrepreneur::query()
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return response()->json([
            'entrepreneurs' => $entrepreneurs
                ->map(fn (SettlementEntrepreneur $entrepreneur) => $this->formatEntrepreneur($entrepreneur))
                ->values()
                ->all(),
            'meta' => [
                'count' => $entrepreneurs->count(),
                'company_id' => $companyId,
            ],
        ]);
    }

    public function storeEntrepreneur(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $companyId = (int) ($validated['company_id'] ?? 2);
        $name = trim($validated['name']);
        $entrepreneur = SettlementEntrepreneur::firstOrCreate(
            [
                'company_id' => $companyId,
                'name' => $name,
            ],
            [
                'user_id' => $request->user()?->id,
            ],
        );

        return response()->json([
            'message' => 'Entrepreneur saved successfully',
            'entrepreneur' => $this->formatEntrepreneur($entrepreneur),
        ], 201);
    }

    public function deleteEntrepreneur($id)
    {
        $entrepreneur = SettlementEntrepreneur::findOrFail($id);

        if ($entrepreneur->settlements()->exists()) {
            return response()->json([
                'message' => 'Delete this entrepreneur\'s States before deleting the entrepreneur.',
            ], 422);
        }

        $entrepreneur->delete();

        return response()->json(['message' => 'Entrepreneur deleted successfully']);
    }

    public function list(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $companyId = (int) ($validated['company_id'] ?? 2);
        $search = trim((string) ($validated['search'] ?? ''));
        $query = EntrepreneurSettlement::query()
            ->where('company_id', $companyId)
            ->with(['entrepreneur', 'transactions' => function ($transactionQuery) {
                $transactionQuery
                    ->orderBy('transaction_date')
                    ->orderBy('id');
            }])
            ->orderByDesc('settlement_date')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('entrepreneur_name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $settlements = $query->get();

        return response()->json([
            'settlements' => $settlements
                ->map(fn (EntrepreneurSettlement $settlement) => $this->formatSettlement($settlement))
                ->values()
                ->all(),
            'meta' => [
                'count' => $settlements->count(),
                'company_id' => $companyId,
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateSettlement($request, true);
        $companyId = (int) ($validated['company_id'] ?? 2);
        $entrepreneur = $this->findEntrepreneur((int) $validated['entrepreneur_id'], $companyId);
        $settlement = EntrepreneurSettlement::create([
            'company_id' => $companyId,
            'entrepreneur_id' => $entrepreneur->id,
            'user_id' => $request->user()?->id,
            'entrepreneur_name' => $entrepreneur->name,
            'reference' => $this->nullableText($validated['reference'] ?? null),
            'base_amount' => $validated['base_amount'],
            'base_direction' => $validated['base_direction'] ?? 'in',
            'currency' => strtoupper($validated['currency']),
            'settlement_date' => $validated['settlement_date'],
            'status' => $validated['status'] ?? 'open',
            'notes' => $this->nullableText($validated['notes'] ?? null),
        ]);

        $settlement->load(['entrepreneur', 'transactions']);

        return response()->json([
            'message' => 'Entrepreneur settlement created successfully',
            'settlement' => $this->formatSettlement($settlement),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $settlement = EntrepreneurSettlement::findOrFail($id);
        $validated = $this->validateSettlement($request, false);
        $companyId = (int) ($validated['company_id'] ?? $settlement->company_id);
        $entrepreneur = array_key_exists('entrepreneur_id', $validated)
            ? $this->findEntrepreneur((int) $validated['entrepreneur_id'], $companyId)
            : null;

        $settlement->update([
            'company_id' => $companyId,
            'entrepreneur_id' => $entrepreneur?->id ?? $settlement->entrepreneur_id,
            'entrepreneur_name' => $entrepreneur?->name ?? $settlement->entrepreneur_name,
            'reference' => array_key_exists('reference', $validated)
                ? $this->nullableText($validated['reference'])
                : $settlement->reference,
            'base_amount' => $validated['base_amount'] ?? $settlement->base_amount,
            'base_direction' => $validated['base_direction'] ?? $settlement->base_direction,
            'currency' => array_key_exists('currency', $validated)
                ? strtoupper($validated['currency'])
                : $settlement->currency,
            'settlement_date' => $validated['settlement_date'] ?? $settlement->settlement_date,
            'status' => $validated['status'] ?? $settlement->status,
            'notes' => array_key_exists('notes', $validated)
                ? $this->nullableText($validated['notes'])
                : $settlement->notes,
        ]);

        $settlement->load(['entrepreneur', 'transactions']);

        return response()->json([
            'message' => 'Entrepreneur settlement updated successfully',
            'settlement' => $this->formatSettlement($settlement->refresh()->load(['entrepreneur', 'transactions'])),
        ]);
    }

    public function delete($id)
    {
        EntrepreneurSettlement::findOrFail($id)->delete();
        return response()->json(['message' => 'Entrepreneur settlement deleted successfully']);
    }

    public function storeTransaction(Request $request, $settlementId)
    {
        $settlement = EntrepreneurSettlement::findOrFail($settlementId);
        $validated = $this->validateTransaction($request, true);
        $transaction = $settlement->transactions()->create([
            'user_id' => $request->user()?->id,
            'direction' => $validated['direction'],
            'amount' => $validated['amount'],
            'description' => trim($validated['description']),
            'transaction_date' => $validated['transaction_date'],
        ]);

        return response()->json([
            'message' => 'Settlement transaction created successfully',
            'transaction' => $this->formatTransaction($transaction),
        ], 201);
    }

    public function updateTransaction(Request $request, $id)
    {
        $transaction = EntrepreneurSettlementTransaction::findOrFail($id);
        $validated = $this->validateTransaction($request, false);
        $transaction->update([
            'direction' => $validated['direction'] ?? $transaction->direction,
            'amount' => $validated['amount'] ?? $transaction->amount,
            'description' => array_key_exists('description', $validated)
                ? trim($validated['description'])
                : $transaction->description,
            'transaction_date' => $validated['transaction_date'] ?? $transaction->transaction_date,
        ]);

        return response()->json([
            'message' => 'Settlement transaction updated successfully',
            'transaction' => $this->formatTransaction($transaction->refresh()),
        ]);
    }

    public function deleteTransaction($id)
    {
        EntrepreneurSettlementTransaction::findOrFail($id)->delete();
        return response()->json(['message' => 'Settlement transaction deleted successfully']);
    }

    private function validateSettlement(Request $request, bool $required): array
    {
        $requiredOrSometimes = $required ? 'required' : 'sometimes';
        return $request->validate([
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'entrepreneur_id' => [$required ? 'required' : 'sometimes', 'integer', 'exists:settlement_entrepreneurs,id'],
            'entrepreneur_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'reference' => ['sometimes', 'nullable', 'string', 'max:255'],
            'base_amount' => [$requiredOrSometimes, 'numeric', 'min:0'],
            'base_direction' => [$required ? 'required' : 'sometimes', 'in:in,out'],
            'currency' => [$requiredOrSometimes, 'in:DZD,USD,EUR'],
            'settlement_date' => [$requiredOrSometimes, 'date'],
            'status' => ['sometimes', 'in:open,settled'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ]);
    }

    private function validateTransaction(Request $request, bool $required): array
    {
        $requiredOrSometimes = $required ? 'required' : 'sometimes';
        return $request->validate([
            'direction' => [$requiredOrSometimes, 'in:credit,deduction'],
            'amount' => [$requiredOrSometimes, 'numeric', 'min:0.01'],
            'description' => [$requiredOrSometimes, 'string', 'max:255'],
            'transaction_date' => [$requiredOrSometimes, 'date'],
        ]);
    }

    private function nullableText(?string $value): ?string
    {
        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }

    private function findEntrepreneur(int $id, int $companyId): SettlementEntrepreneur
    {
        $entrepreneur = SettlementEntrepreneur::query()
            ->where('company_id', $companyId)
            ->find($id);

        if ($entrepreneur === null) {
            throw ValidationException::withMessages([
                'entrepreneur_id' => 'The selected entrepreneur does not belong to this company.',
            ]);
        }

        return $entrepreneur;
    }

    private function formatSettlement(EntrepreneurSettlement $settlement): array
    {
        $transactions = $settlement->relationLoaded('transactions')
            ? $settlement->transactions
            : $settlement->transactions()->orderBy('transaction_date')->orderBy('id')->get();
        $credit = $transactions->where('direction', 'credit')->sum('amount');
        $deduction = $transactions->where('direction', 'deduction')->sum('amount');
        $base = (float) $settlement->base_amount;
        $signedBase = $settlement->base_direction === 'out' ? -$base : $base;

        return [
            'id' => $settlement->id,
            'company_id' => $settlement->company_id,
            'entrepreneur_id' => $settlement->entrepreneur_id,
            'user_id' => $settlement->user_id,
            'entrepreneur' => $settlement->relationLoaded('entrepreneur') && $settlement->entrepreneur
                ? $this->formatEntrepreneur($settlement->entrepreneur)
                : null,
            'entrepreneur_name' => $settlement->entrepreneur_name,
            'reference' => $settlement->reference,
            'base_amount' => (float) $settlement->base_amount,
            'base_direction' => $settlement->base_direction ?? 'in',
            'currency' => strtoupper($settlement->currency),
            'settlement_date' => $settlement->settlement_date?->toDateString(),
            'status' => $settlement->status,
            'notes' => $settlement->notes,
            'credit_total' => (float) $credit,
            'deduction_total' => (float) $deduction,
            'final_amount' => $signedBase + (float) $credit - (float) $deduction,
            'transactions' => $transactions->map(fn (EntrepreneurSettlementTransaction $transaction) => $this->formatTransaction($transaction))->values()->all(),
            'created_at' => optional($settlement->created_at)?->toIso8601String(),
            'updated_at' => optional($settlement->updated_at)?->toIso8601String(),
        ];
    }

    private function formatTransaction(EntrepreneurSettlementTransaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'settlement_id' => $transaction->settlement_id,
            'user_id' => $transaction->user_id,
            'direction' => $transaction->direction,
            'amount' => (float) $transaction->amount,
            'description' => $transaction->description,
            'transaction_date' => $transaction->transaction_date?->toDateString(),
            'created_at' => optional($transaction->created_at)?->toIso8601String(),
            'updated_at' => optional($transaction->updated_at)?->toIso8601String(),
        ];
    }

    private function formatEntrepreneur(SettlementEntrepreneur $entrepreneur): array
    {
        return [
            'id' => $entrepreneur->id,
            'company_id' => $entrepreneur->company_id,
            'user_id' => $entrepreneur->user_id,
            'name' => $entrepreneur->name,
        ];
    }
}
