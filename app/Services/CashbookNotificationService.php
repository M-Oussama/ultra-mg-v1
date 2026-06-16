<?php

namespace App\Services;

use App\Models\Cashbook;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;

class CashbookNotificationService
{
    public function __construct(protected FirebaseCloudMessagingService $messaging)
    {
    }

    public function notifyCashbookCreated(Cashbook $cashbook, ?User $actor = null): void
    {
        $this->broadcast(
            $cashbook,
            'Cashbook created',
            sprintf('%s created %s.', $this->actorName($actor), $cashbook->name),
            [
                'entity_type' => 'cashbook',
                'entity_action' => 'created',
            ],
        );
    }

    public function notifyCashbookUpdated(Cashbook $cashbook, ?User $actor = null): void
    {
        $this->broadcast(
            $cashbook,
            'Cashbook updated',
            sprintf('%s updated %s.', $this->actorName($actor), $cashbook->name),
            [
                'entity_type' => 'cashbook',
                'entity_action' => 'updated',
            ],
        );
    }

    public function notifyCashbookDeleted(Cashbook $cashbook, ?User $actor = null): void
    {
        $this->broadcast(
            $cashbook,
            'Cashbook deleted',
            sprintf('%s deleted %s.', $this->actorName($actor), $cashbook->name),
            [
                'entity_type' => 'cashbook',
                'entity_action' => 'deleted',
            ],
        );
    }

    public function notifyLookupChanged(Cashbook $cashbook, string $lookupType, string $action, string $label, ?User $actor = null): void
    {
        $lookupTypeLabel = match ($lookupType) {
            'contacts' => 'contact',
            'categories' => 'category',
            'payment-modes' => 'payment mode',
            default => 'lookup',
        };

        $this->broadcast(
            $cashbook,
            ucfirst($lookupTypeLabel) . ' ' . $action,
            sprintf('%s %s %s %s.', $this->actorName($actor), $action, $lookupTypeLabel, $label),
            [
                'entity_type' => 'lookup',
                'lookup_type' => $lookupType,
                'entity_action' => $action,
            ],
        );
    }

    public function notifyTransactionChanged(Cashbook $cashbook, Transaction $transaction, string $action, ?User $actor = null): void
    {
        $amount = number_format((float) $transaction->amount, 2, '.', ',');
        $typeLabel = $transaction->type === 'income' ? 'cash in' : 'cash out';

        $this->broadcast(
            $cashbook,
            ucfirst($typeLabel) . ' ' . $action,
            sprintf(
                '%s %s %s of %s in %s.',
                $this->actorName($actor),
                $action,
                $typeLabel,
                $amount,
                $cashbook->name
            ),
            [
                'entity_type' => 'transaction',
                'entity_action' => $action,
                'transaction_id' => $transaction->id,
                'cashbook_id' => $cashbook->id,
            ],
        );
    }

    public function notifyMemberChanged(Cashbook $cashbook, User $member, string $action, ?User $actor = null, ?string $role = null): void
    {
        $roleLabel = $role !== null && $role !== '' ? " as {$role}" : '';

        $this->broadcast(
            $cashbook,
            'Cashbook member ' . $action,
            sprintf(
                '%s %s %s%s in %s.',
                $this->actorName($actor),
                $action,
                $member->name,
                $roleLabel,
                $cashbook->name
            ),
            [
                'entity_type' => 'cashbook_member',
                'entity_action' => $action,
                'member_user_id' => $member->id,
                'member_role' => $role,
                'cashbook_id' => $cashbook->id,
            ],
        );
    }

    public function notifyImportCompleted(Cashbook $cashbook, array $stats, ?User $actor = null): void
    {
        $inserted = (int) ($stats['imported'] ?? 0);
        $deleted = (int) ($stats['deleted'] ?? 0);
        $updated = (int) ($stats['updated'] ?? 0);

        $summary = sprintf(
            '%s synchronized %s row%s from the legacy spreadsheet.',
            $this->actorName($actor),
            number_format($inserted + $updated),
            ($inserted + $updated) === 1 ? '' : 's'
        );

        if ($deleted > 0) {
            $summary .= sprintf(' %s imported row%s were replaced.', number_format($deleted), $deleted === 1 ? '' : 's');
        }

        $this->broadcast(
            $cashbook,
            'Cashbook synchronized',
            $summary,
            [
                'entity_type' => 'cashbook_import',
                'entity_action' => 'sync',
                'cashbook_id' => $cashbook->id,
                'imported_rows' => $inserted,
                'deleted_rows' => $deleted,
                'updated_rows' => $updated,
            ],
        );
    }

    private function broadcast(Cashbook $cashbook, string $title, string $body, array $data = []): void
    {
        if (!$this->messaging->isConfigured()) {
            return;
        }

        $recipients = $this->recipientUsers($cashbook);

        foreach ($recipients as $recipient) {
            if (trim((string) $recipient->fcm_token) === '') {
                continue;
            }

            $this->messaging->sendToToken($recipient->fcm_token, $title, $body, $data + [
                'cashbook_id' => $cashbook->id,
                'cashbook_name' => $cashbook->name,
            ]);
        }
    }

    /**
     * @return Collection<int, User>
     */
    private function recipientUsers(Cashbook $cashbook): Collection
    {
        $query = User::query()->whereNotNull('fcm_token');

        if ($cashbook->organization_id) {
            $query->where(function ($builder) use ($cashbook) {
                $builder->where('organization_id', $cashbook->organization_id)
                    ->orWhere('id', $cashbook->user_id);
            });
        } else {
            $query->where('id', $cashbook->user_id);
        }

        return $query->get()->unique('id')->values();
    }

    private function actorName(?User $actor): string
    {
        return $actor?->name ?: 'Someone';
    }
}
