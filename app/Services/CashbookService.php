<?php

namespace App\Services;

use App\Models\Cashbook;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CashbookService
{
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isGlobalAdmin()) {
            return $query;
        }

        if ($user->organization_id) {
            return $query->where('organization_id', $user->organization_id);
        }

        return $query->where('user_id', $user->id);
    }

    public function cashbooksWithAggregatesForUser(User $user): Builder
    {
        return $this->scopeForUser(Cashbook::query(), $user)
            ->select('cashbooks.*')
            ->selectRaw("(select coalesce(sum(case when type = 'income' then amount else 0 end), 0) from transactions where transactions.cashbook_id = cashbooks.id and transactions.deleted_at is null) as total_income")
            ->selectRaw("(select coalesce(sum(case when type = 'expense' then amount else 0 end), 0) from transactions where transactions.cashbook_id = cashbooks.id and transactions.deleted_at is null) as total_expense")
            ->selectRaw("(select coalesce(sum(case when type = 'income' then amount else 0 end), 0) - coalesce(sum(case when type = 'expense' then amount else 0 end), 0) from transactions where transactions.cashbook_id = cashbooks.id and transactions.deleted_at is null) as balance")
            ->selectRaw("(select coalesce(count(*), 0) from transactions where transactions.cashbook_id = cashbooks.id and transactions.deleted_at is null) as transaction_count")
            ->selectRaw("(select coalesce(count(*), 0) + 1 from cashbook_members where cashbook_members.cashbook_id = cashbooks.id) as member_count")
            ->selectRaw("(select max(transaction_date) from transactions where transactions.cashbook_id = cashbooks.id and transactions.deleted_at is null) as last_transaction_date");
    }

    public function authorizeCashbook(Cashbook $cashbook, User $user): bool
    {
        if ($user->isGlobalAdmin()) {
            return true;
        }

        if ($user->organization_id) {
            return (int) $cashbook->organization_id === (int) $user->organization_id;
        }

        return (int) $cashbook->user_id === (int) $user->id;
    }

    public function getSummary(Cashbook $cashbook): array
    {
        $summary = Transaction::query()
            ->where('cashbook_id', $cashbook->id)
            ->whereNull('deleted_at')
            ->selectRaw("coalesce(sum(case when type = 'income' then amount else 0 end), 0) as total_income")
            ->selectRaw("coalesce(sum(case when type = 'expense' then amount else 0 end), 0) as total_expense")
            ->selectRaw("coalesce(count(*), 0) as transaction_count")
            ->selectRaw("max(transaction_date) as last_activity")
            ->first();

        $totalIncome = (float) ($summary->total_income ?? 0);
        $totalExpense = (float) ($summary->total_expense ?? 0);

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
            'transaction_count' => (int) ($summary->transaction_count ?? 0),
            'last_activity' => $summary->last_activity,
        ];
    }

    public function formatCashbook(Cashbook $cashbook): array
    {
        $totalIncome = (float) ($cashbook->getAttribute('total_income') ?? 0);
        $totalExpense = (float) ($cashbook->getAttribute('total_expense') ?? 0);
        $balance = $cashbook->getAttribute('balance');
        $transactionCount = (int) ($cashbook->getAttribute('transaction_count') ?? 0);
        $memberCount = (int) ($cashbook->getAttribute('member_count') ?? 1);

        return [
            'id' => $cashbook->id,
            'name' => $cashbook->name,
            'description' => $cashbook->description,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $balance !== null ? (float) $balance : $totalIncome - $totalExpense,
            'transaction_count' => $transactionCount,
            'member_count' => $memberCount,
            'last_transaction_date' => $cashbook->getAttribute('last_transaction_date'),
            'created_at' => optional($cashbook->created_at)->toIso8601String(),
            'updated_at' => optional($cashbook->updated_at)->toIso8601String(),
        ];
    }

    public function formatTransaction(Transaction $transaction): array
    {
        $transaction->loadMissing(['contact', 'category', 'paymentMode', 'media', 'user']);

        return [
            'id' => $transaction->id,
            'cashbook_id' => $transaction->cashbook_id,
            'user_id' => $transaction->user_id,
            'user_name' => $transaction->user?->name,
            'contact_id' => $transaction->contact_id,
            'category_id' => $transaction->category_id,
            'payment_mode_id' => $transaction->payment_mode_id,
            'type' => $transaction->type,
            'amount' => (float) $transaction->amount,
            'note' => $transaction->note,
            'transaction_date' => optional($transaction->transaction_date)->format('Y-m-d'),
            'transaction_time' => $transaction->transaction_time ? Carbon::parse($transaction->transaction_time)->format('H:i:s') : null,
            'contact_name' => $transaction->contact?->name,
            'category_name' => $transaction->category?->name,
            'payment_mode_name' => $transaction->paymentMode?->name,
            'import_source' => $transaction->import_source,
            'source_row_number' => $transaction->source_row_number,
            'user' => $transaction->user ? [
                'id' => $transaction->user->id,
                'name' => $transaction->user->name,
            ] : null,
            'contact' => $transaction->contact ? $this->formatLookup($transaction->contact) : null,
            'category' => $transaction->category ? $this->formatLookup($transaction->category) : null,
            'payment_mode' => $transaction->paymentMode ? $this->formatLookup($transaction->paymentMode) : null,
            'attachments' => $transaction->getMedia('attachments')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'name' => $media->name ?: $media->file_name,
                    'file_name' => $media->file_name,
                    'url' => $media->getFullUrl(),
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'extension' => $media->extension,
                    'is_image' => str_starts_with((string) $media->mime_type, 'image/'),
                ];
            })->values()->all(),
            'created_at' => optional($transaction->created_at)->toIso8601String(),
            'updated_at' => optional($transaction->updated_at)->toIso8601String(),
        ];
    }

    public function formatLookup(Model $lookup): array
    {
        return [
            'id' => $lookup->id,
            'name' => $lookup->name,
            'description' => $lookup->description ?? $lookup->notes ?? null,
            'phone' => $lookup->phone ?? null,
            'email' => $lookup->email ?? null,
            'kind' => $lookup->kind ?? $lookup->type ?? null,
            'created_at' => optional($lookup->created_at)->toIso8601String(),
            'updated_at' => optional($lookup->updated_at)->toIso8601String(),
            'deleted_at' => optional($lookup->deleted_at)->toIso8601String(),
        ];
    }

    /**
     * @param  Collection<int, Model>  $items
     * @return array<int, array<string, mixed>>
     */
    public function formatLookupCollection(Collection $items): array
    {
        return $items->map(fn (Model $item) => $this->formatLookup($item))->values()->all();
    }

    public function getLookupPayload(Cashbook $cashbook): array
    {
        return [
            'contacts' => $this->formatLookupCollection($cashbook->contacts()->orderBy('name')->get()),
            'categories' => $this->formatLookupCollection($cashbook->categories()->orderBy('name')->get()),
            'payment_modes' => $this->formatLookupCollection($cashbook->paymentModes()->orderBy('name')->get()),
        ];
    }

    public function getMembersPayload(Cashbook $cashbook): array
    {
        $cashbook->loadMissing(['user', 'members']);

        $members = [
            [
                'id' => $cashbook->user->id,
                'user_id' => $cashbook->user->id,
                'name' => $cashbook->user->name,
                'email' => $cashbook->user->email,
                'phone' => $cashbook->user->phone ?? null,
                'role' => 'owner',
                'is_owner' => true,
                'created_at' => optional($cashbook->created_at)->toIso8601String(),
                'updated_at' => optional($cashbook->updated_at)->toIso8601String(),
            ],
        ];

        foreach ($cashbook->members as $member) {
            $members[] = [
                'id' => $member->pivot->id ?? $member->id,
                'user_id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone ?? null,
                'role' => $member->pivot->role ?? 'viewer',
                'permissions' => $member->pivot->permissions ?? null,
                'is_owner' => false,
                'created_at' => optional($member->pivot->created_at ?? null)->toIso8601String(),
                'updated_at' => optional($member->pivot->updated_at ?? null)->toIso8601String(),
            ];
        }

        return $members;
    }

    public function getGlobalBalance(?int $userId, ?int $organizationId = null): float
    {
        $query = Transaction::query()->whereNull('deleted_at');

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        } elseif ($userId) {
            $query->where('user_id', $userId);
        }

        $summary = (clone $query)
            ->selectRaw("coalesce(sum(case when type = 'income' then amount else 0 end), 0) as total_income")
            ->selectRaw("coalesce(sum(case when type = 'expense' then amount else 0 end), 0) as total_expense")
            ->first();

        $income = (float) ($summary->total_income ?? 0);
        $expense = (float) ($summary->total_expense ?? 0);

        return $income - $expense;
    }
}
