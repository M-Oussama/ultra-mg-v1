<?php

namespace App\Services;

use App\Models\Cashbook;
use App\Models\Transaction;

class CashbookService
{
    /**
     * Calculate the summary for a specific cashbook.
     *
     * @param Cashbook $cashbook
     * @return array
     */
    public function getSummary(Cashbook $cashbook): array
    {
        $income = $cashbook->transactions()->where('type', 'income')->sum('amount');
        $expense = $cashbook->transactions()->where('type', 'expense')->sum('amount');

        return [
            'total_income' => (float) $income,
            'total_expense' => (float) $expense,
            'balance' => (float) ($income - $expense),
            'transaction_count' => $cashbook->transactions()->count(),
            'last_activity' => $cashbook->transactions()->latest('transaction_date')->value('transaction_date')
        ];
    }

    /**
     * Get the total net balance across all cashbooks for a scope.
     *
     * @param int|null $userId
     * @param int|null $organizationId
     * @return float
     */
    public function getGlobalBalance(?int $userId, ?int $organizationId = null): float
    {
        $query = Transaction::query();

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        } else {
            $query->where('user_id', $userId);
        }

        $income = (clone $query)->where('type', 'income')->sum('amount');
        $expense = (clone $query)->where('type', 'expense')->sum('amount');

        return (float) ($income - $expense);
    }
}
