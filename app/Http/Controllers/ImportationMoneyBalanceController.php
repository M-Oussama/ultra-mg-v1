<?php

namespace App\Http\Controllers;

use App\Models\ImportationMoneyBalanceEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImportationMoneyBalanceController extends Controller
{
    public function list(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'year' => ['sometimes', 'integer', 'min:2000', 'max:2100'],
            'month' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'search' => ['sometimes', 'string', 'max:255'],
        ])->validate();

        $companyId = (int) ($validated['company_id'] ?? 2);
        $year = array_key_exists('year', $validated)
            ? (int) $validated['year']
            : null;
        $month = array_key_exists('month', $validated)
            ? (int) $validated['month']
            : null;
        $search = trim((string) ($validated['search'] ?? ''));

        $query = ImportationMoneyBalanceEntry::query()
            ->where('company_id', $companyId);

        if ($year !== null) {
            $query->where('year', $year);
        }
        if ($month !== null) {
            $query->where('month', $month);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('group_title', 'like', '%' . $search . '%')
                    ->orWhere('note', 'like', '%' . $search . '%')
                    ->orWhereRaw('CAST(amount AS CHAR) LIKE ?', ['%' . $search . '%']);
            });
        }

        $entries = $query
            ->orderByRaw('entry_date is null')
            ->orderBy('entry_date')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'entries' => $entries->values()->map(fn (ImportationMoneyBalanceEntry $entry) => $this->formatEntry($entry))->all(),
            'groups' => $this->groupPayload($entries),
            'summary' => $this->summaryPayload($entries),
            'meta' => [
                'count' => $entries->count(),
                'company_id' => $companyId,
                'year' => $year,
                'month' => $month,
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'group_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'direction' => ['required', 'in:in,out'],
            'affects_balance' => ['sometimes', 'nullable', 'boolean'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'in_usd' => ['sometimes', 'nullable', 'numeric'],
            'out_usd' => ['sometimes', 'nullable', 'numeric'],
            'rest_usd' => ['sometimes', 'nullable', 'numeric'],
            'total_5_percent' => ['sometimes', 'nullable', 'numeric'],
            'half_out_usd' => ['sometimes', 'nullable', 'numeric'],
            'rest_percent' => ['sometimes', 'nullable', 'numeric'],
            'total_in' => ['sometimes', 'nullable', 'numeric'],
            'containers_fees' => ['sometimes', 'nullable', 'numeric'],
            'total_out' => ['sometimes', 'nullable', 'numeric'],
            'entry_date' => ['sometimes', 'nullable', 'date'],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ]);

        $companyId = (int) ($validated['company_id'] ?? 2);
        $groupTitle = trim((string) ($validated['group_title'] ?? ''));
        $groupTitle = $groupTitle !== '' ? $groupTitle : 'General';
        $year = (int) $validated['year'];
        $month = (int) $validated['month'];

        $entry = ImportationMoneyBalanceEntry::create([
            'company_id' => $companyId,
            'user_id' => $request->user()?->id,
            'year' => $year,
            'month' => $month,
            'group_title' => $groupTitle,
            'direction' => $validated['direction'],
            'affects_balance' => $validated['affects_balance'] ?? null,
            'amount' => $validated['amount'],
            'note' => $validated['note'] ?? null,
            'in_usd' => $validated['in_usd'] ?? null,
            'out_usd' => $validated['out_usd'] ?? null,
            'rest_usd' => $validated['rest_usd'] ?? null,
            'total_5_percent' => $validated['total_5_percent'] ?? null,
            'half_out_usd' => $validated['half_out_usd'] ?? null,
            'rest_percent' => $validated['rest_percent'] ?? null,
            'total_in' => $validated['total_in'] ?? null,
            'containers_fees' => $validated['containers_fees'] ?? null,
            'total_out' => $validated['total_out'] ?? null,
            'entry_date' => $validated['entry_date'] ?? now()->toDateString(),
            'sort_order' => $validated['sort_order'] ?? $this->nextSortOrder($companyId, $year, $month, $groupTitle),
        ]);

        return response()->json([
            'message' => 'Money balance entry created successfully',
            'entry' => $this->formatEntry($entry),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $entry = ImportationMoneyBalanceEntry::findOrFail($id);

        $validated = $request->validate([
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'year' => ['sometimes', 'integer', 'min:2000', 'max:2100'],
            'month' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'group_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'direction' => ['sometimes', 'in:in,out'],
            'affects_balance' => ['sometimes', 'nullable', 'boolean'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'note' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'in_usd' => ['sometimes', 'nullable', 'numeric'],
            'out_usd' => ['sometimes', 'nullable', 'numeric'],
            'rest_usd' => ['sometimes', 'nullable', 'numeric'],
            'total_5_percent' => ['sometimes', 'nullable', 'numeric'],
            'half_out_usd' => ['sometimes', 'nullable', 'numeric'],
            'rest_percent' => ['sometimes', 'nullable', 'numeric'],
            'total_in' => ['sometimes', 'nullable', 'numeric'],
            'containers_fees' => ['sometimes', 'nullable', 'numeric'],
            'total_out' => ['sometimes', 'nullable', 'numeric'],
            'entry_date' => ['sometimes', 'nullable', 'date'],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ]);

        $companyId = (int) ($validated['company_id'] ?? $entry->company_id);
        $year = (int) ($validated['year'] ?? $entry->year);
        $month = (int) ($validated['month'] ?? $entry->month);
        $groupTitle = array_key_exists('group_title', $validated)
            ? trim((string) ($validated['group_title'] ?? ''))
            : $entry->group_title;
        $groupTitle = $groupTitle !== '' ? $groupTitle : 'General';

        $payload = [
            'company_id' => $companyId,
            'year' => $year,
            'month' => $month,
            'group_title' => $groupTitle,
            'direction' => $validated['direction'] ?? $entry->direction,
            'affects_balance' => array_key_exists('affects_balance', $validated)
                ? $validated['affects_balance']
                : $entry->affects_balance,
            'amount' => $validated['amount'] ?? $entry->amount,
            'note' => array_key_exists('note', $validated) ? $validated['note'] : $entry->note,
            'in_usd' => array_key_exists('in_usd', $validated) ? $validated['in_usd'] : $entry->in_usd,
            'out_usd' => array_key_exists('out_usd', $validated) ? $validated['out_usd'] : $entry->out_usd,
            'rest_usd' => array_key_exists('rest_usd', $validated) ? $validated['rest_usd'] : $entry->rest_usd,
            'total_5_percent' => array_key_exists('total_5_percent', $validated) ? $validated['total_5_percent'] : $entry->total_5_percent,
            'half_out_usd' => array_key_exists('half_out_usd', $validated) ? $validated['half_out_usd'] : $entry->half_out_usd,
            'rest_percent' => array_key_exists('rest_percent', $validated) ? $validated['rest_percent'] : $entry->rest_percent,
            'total_in' => array_key_exists('total_in', $validated) ? $validated['total_in'] : $entry->total_in,
            'containers_fees' => array_key_exists('containers_fees', $validated) ? $validated['containers_fees'] : $entry->containers_fees,
            'total_out' => array_key_exists('total_out', $validated) ? $validated['total_out'] : $entry->total_out,
            'entry_date' => array_key_exists('entry_date', $validated) ? $validated['entry_date'] : $entry->entry_date,
            'sort_order' => array_key_exists('sort_order', $validated)
                ? $validated['sort_order']
                : $entry->sort_order,
        ];

        $entry->update($payload);

        return response()->json([
            'message' => 'Money balance entry updated successfully',
            'entry' => $this->formatEntry($entry->refresh()),
        ]);
    }

    public function delete($id)
    {
        $entry = ImportationMoneyBalanceEntry::findOrFail($id);
        $entry->delete();

        return response()->json(['message' => 'Money balance entry deleted successfully']);
    }

    private function nextSortOrder(int $companyId, int $year, int $month, string $groupTitle): int
    {
        $max = ImportationMoneyBalanceEntry::query()
            ->where('company_id', $companyId)
            ->where('year', $year)
            ->where('month', $month)
            ->where('group_title', $groupTitle)
            ->max('sort_order');

        return ((int) $max) + 1;
    }

    /**
     * @return array<string, mixed>
     */
    private function formatEntry(ImportationMoneyBalanceEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'company_id' => $entry->company_id,
            'user_id' => $entry->user_id,
            'year' => $entry->year,
            'month' => $entry->month,
            'group_title' => $entry->group_title ?: 'General',
            'direction' => $entry->direction,
            'affects_balance' => $entry->affects_balance === null
                ? null
                : (bool) $entry->affects_balance,
            'amount' => (float) $entry->amount,
            'note' => $entry->note,
            'in_usd' => $entry->in_usd !== null ? (float) $entry->in_usd : null,
            'out_usd' => $entry->out_usd !== null ? (float) $entry->out_usd : null,
            'rest_usd' => $entry->rest_usd !== null ? (float) $entry->rest_usd : null,
            'total_5_percent' => $entry->total_5_percent !== null ? (float) $entry->total_5_percent : null,
            'half_out_usd' => $entry->half_out_usd !== null ? (float) $entry->half_out_usd : null,
            'rest_percent' => $entry->rest_percent !== null ? (float) $entry->rest_percent : null,
            'total_in' => $entry->total_in !== null ? (float) $entry->total_in : null,
            'containers_fees' => $entry->containers_fees !== null ? (float) $entry->containers_fees : null,
            'total_out' => $entry->total_out !== null ? (float) $entry->total_out : null,
            'entry_date' => $entry->entry_date?->toDateString(),
            'sort_order' => $entry->sort_order,
            'created_at' => optional($entry->created_at)?->toIso8601String(),
            'updated_at' => optional($entry->updated_at)?->toIso8601String(),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ImportationMoneyBalanceEntry>  $entries
     * @return array<int, array<string, mixed>>
     */
    private function groupPayload($entries): array
    {
        return collect($entries)
            ->groupBy(function (ImportationMoneyBalanceEntry $entry) {
                return trim((string) ($entry->group_title ?: 'General'));
            })
            ->map(function ($groupEntries, $groupTitle) {
                $formattedEntries = $groupEntries
                    ->values()
                    ->map(fn (ImportationMoneyBalanceEntry $entry) => $this->formatEntry($entry))
                    ->all();

                $summary = $this->summaryPayload($groupEntries);

                return [
                    'group_title' => $groupTitle ?: 'General',
                    'entry_count' => count($formattedEntries),
                    'summary' => $summary,
                    'entries' => $formattedEntries,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ImportationMoneyBalanceEntry>  $entries
     * @return array<string, mixed>
     */
    private function summaryPayload($entries): array
    {
        $totalIn = 0.0;
        $totalOut = 0.0;

        foreach ($this->balanceImpactEntries($entries) as $entry) {
            $amount = (float) $entry->amount;
            if ($entry->direction === 'in') {
                $totalIn += $amount;
            } else {
                $totalOut += $amount;
            }
        }

        return [
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'balance' => $totalIn - $totalOut,
            'entry_count' => collect($entries)->count(),
            'group_count' => collect($entries)
                ->groupBy(fn (ImportationMoneyBalanceEntry $entry) => trim((string) ($entry->group_title ?: 'General')))
                ->count(),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ImportationMoneyBalanceEntry>  $entries
     * @return \Illuminate\Support\Collection<int, ImportationMoneyBalanceEntry>
     */
    private function balanceImpactEntries($entries)
    {
        return collect($entries)
            ->groupBy(function (ImportationMoneyBalanceEntry $entry) {
                return trim((string) ($entry->group_title ?: 'General'));
            })
            ->flatMap(function ($groupEntries) {
                $hasSummaryLine = $groupEntries->contains(
                    fn (ImportationMoneyBalanceEntry $entry) => $this->isSummaryLine($entry->note),
                );

                return $groupEntries->filter(function (ImportationMoneyBalanceEntry $entry) use ($hasSummaryLine) {
                    if ($entry->affects_balance !== null) {
                        return (bool) $entry->affects_balance;
                    }

                    if (!$hasSummaryLine) {
                        return true;
                    }

                    return $entry->direction === 'in' || $this->isSummaryLine($entry->note);
                });
            })
            ->values();
    }

    private function isSummaryLine(?string $note): bool
    {
        $normalized = trim((string) preg_replace('/[^a-z0-9%]+/i', ' ', strtolower((string) $note)));

        return str_starts_with($normalized, 'calc ')
            || str_contains($normalized, 'total invoice')
            || str_contains($normalized, 'invoice total')
            || str_contains($normalized, 'total facture')
            || str_contains($normalized, 'facture totale');
    }
}
