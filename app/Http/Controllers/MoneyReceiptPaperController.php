<?php

namespace App\Http\Controllers;

use App\Models\MoneyReceiptPaper;
use Illuminate\Http\Request;

class MoneyReceiptPaperController extends Controller
{
    public function list(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $companyId = (int) ($validated['company_id'] ?? 2);
        $search = trim((string) ($validated['search'] ?? ''));

        $query = MoneyReceiptPaper::query()
            ->where('company_id', $companyId)
            ->orderByDesc('payment_date')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('recipient_name', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhere('currency', 'like', "%{$search}%");
            });
        }

        $papers = $query->get();

        return response()->json([
            'papers' => $papers->map(fn (MoneyReceiptPaper $paper) => $this->formatPaper($paper))->values()->all(),
            'meta' => [
                'count' => $papers->count(),
                'company_id' => $companyId,
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePaper($request, true);
        $paper = MoneyReceiptPaper::create([
            'company_id' => (int) ($validated['company_id'] ?? 2),
            'user_id' => $request->user()?->id,
            'recipient_name' => trim($validated['recipient_name']),
            'amount' => $validated['amount'],
            'currency' => strtoupper($validated['currency']),
            'payment_date' => $validated['payment_date'],
            'reason' => $this->nullableText($validated['reason'] ?? null),
        ]);

        return response()->json([
            'message' => 'Money receipt paper created successfully',
            'paper' => $this->formatPaper($paper),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $paper = MoneyReceiptPaper::findOrFail($id);
        $validated = $this->validatePaper($request, false);

        $paper->update([
            'company_id' => (int) ($validated['company_id'] ?? $paper->company_id),
            'recipient_name' => array_key_exists('recipient_name', $validated)
                ? trim($validated['recipient_name'])
                : $paper->recipient_name,
            'amount' => $validated['amount'] ?? $paper->amount,
            'currency' => array_key_exists('currency', $validated)
                ? strtoupper($validated['currency'])
                : $paper->currency,
            'payment_date' => $validated['payment_date'] ?? $paper->payment_date,
            'reason' => array_key_exists('reason', $validated)
                ? $this->nullableText($validated['reason'])
                : $paper->reason,
        ]);

        return response()->json([
            'message' => 'Money receipt paper updated successfully',
            'paper' => $this->formatPaper($paper->refresh()),
        ]);
    }

    public function delete($id)
    {
        $paper = MoneyReceiptPaper::findOrFail($id);
        $paper->delete();

        return response()->json(['message' => 'Money receipt paper deleted successfully']);
    }

    private function validatePaper(Request $request, bool $required): array
    {
        $requiredOrSometimes = $required ? 'required' : 'sometimes';

        return $request->validate([
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'recipient_name' => [$requiredOrSometimes, 'string', 'max:255'],
            'amount' => [$requiredOrSometimes, 'numeric', 'min:0.01'],
            'currency' => [$requiredOrSometimes, 'in:DZD,USD,EUR'],
            'payment_date' => [$requiredOrSometimes, 'date'],
            'reason' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ]);
    }

    private function nullableText(?string $value): ?string
    {
        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }

    private function formatPaper(MoneyReceiptPaper $paper): array
    {
        return [
            'id' => $paper->id,
            'company_id' => $paper->company_id,
            'user_id' => $paper->user_id,
            'recipient_name' => $paper->recipient_name,
            'amount' => (float) $paper->amount,
            'currency' => strtoupper($paper->currency),
            'payment_date' => $paper->payment_date?->toDateString(),
            'reason' => $paper->reason,
            'created_at' => optional($paper->created_at)?->toIso8601String(),
            'updated_at' => optional($paper->updated_at)?->toIso8601String(),
        ];
    }
}
