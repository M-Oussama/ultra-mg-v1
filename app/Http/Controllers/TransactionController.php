<?php

namespace App\Http\Controllers;

use App\Models\Cashbook;
use App\Models\Transaction;
use App\Services\CashbookNotificationService;
use App\Services\CashbookService;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function __construct(
        protected CashbookService $cashbookService,
        protected CashbookNotificationService $notificationService,
    ) {
    }

    public function index(Request $request, $cashbook_id)
    {
        $cashbook = $this->findCashbookOrFail($request, (int) $cashbook_id);
        $query = $this->baseTransactionQuery($cashbook);

        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('note', 'like', '%' . $search . '%')
                    ->orWhereRaw('CAST(amount AS CHAR) LIKE ?', ['%' . $search . '%'])
                    ->orWhereHas('contact', function ($relation) use ($search) {
                        $relation->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('category', function ($relation) use ($search) {
                        $relation->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('paymentMode', function ($relation) use ($search) {
                        $relation->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', trim((string) $request->input('type')));
        }

        if ($request->filled('contact_id')) {
            $query->where('contact_id', $request->integer('contact_id'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('payment_mode_id')) {
            $query->where('payment_mode_id', $request->integer('payment_mode_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->input('to'));
        }

        $transactions = $query
            ->get()
            ->map(fn (Transaction $transaction) => $this->cashbookService->formatTransaction($transaction))
            ->values()
            ->all();

        return response()->json([
            'data' => $transactions,
            'meta' => [
                'count' => count($transactions),
            ],
        ]);
    }

    public function show(Request $request, $id)
    {
        $transaction = $this->findTransactionOrFail($request, (int) $id);

        return response()->json([
            'transaction' => $this->cashbookService->formatTransaction($transaction),
        ]);
    }

    public function store(Request $request, $cashbook_id)
    {
        $cashbook = $this->findCashbookOrFail($request, (int) $cashbook_id);
        $validated = $request->validate($this->transactionRules($cashbook, true));

        $transaction = Transaction::create(array_merge(
            $this->transactionPayload($validated),
            [
                'cashbook_id' => $cashbook->id,
                'user_id' => $request->user()->id,
                'organization_id' => $request->user()->organization_id,
            ]
        ));

        $this->syncAttachments($transaction, $request);
        $this->notificationService->notifyTransactionChanged($cashbook, $transaction->refresh(), 'added', $request->user());

        return response()->json([
            'message' => 'Transaction added successfully',
            'transaction' => $this->cashbookService->formatTransaction($transaction->refresh()),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $transaction = $this->findTransactionOrFail($request, (int) $id);
        $validated = $request->validate($this->transactionRules($transaction->cashbook, false));

        $payload = $this->transactionPayload($validated);
        if (!array_key_exists('transaction_time', $validated) && $transaction->transaction_time !== null) {
            $payload['transaction_time'] = $transaction->transaction_time;
        }

        $transaction->update($payload);

        if ($request->boolean('replace_attachments')) {
            $transaction->clearMediaCollection('attachments');
        }

        $deletedAttachments = (array) $request->input('deleted_attachments', []);
        foreach ($deletedAttachments as $identifier) {
            $media = $transaction->getMedia('attachments')->first(function ($item) use ($identifier) {
                return (string) $item->id === (string) $identifier
                    || $item->getUrl() === $identifier
                    || $item->getFullUrl() === $identifier;
            });

            if ($media) {
                $media->delete();
            }
        }

        $this->syncAttachments($transaction, $request);
        $this->notificationService->notifyTransactionChanged($transaction->cashbook, $transaction->refresh(), 'updated', $request->user());

        return response()->json([
            'message' => 'Transaction updated successfully',
            'transaction' => $this->cashbookService->formatTransaction($transaction->refresh()),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $transaction = $this->findTransactionOrFail($request, (int) $id);
        $cashbook = $transaction->cashbook;
        $this->notificationService->notifyTransactionChanged($cashbook, $transaction, 'deleted', $request->user());
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }

    private function baseTransactionQuery(Cashbook $cashbook)
    {
        return Transaction::query()
            ->where('cashbook_id', $cashbook->id)
            ->with(['contact', 'category', 'paymentMode', 'media', 'user'])
            ->orderByDesc('transaction_date')
            ->orderByRaw('transaction_time is null')
            ->orderByDesc('transaction_time')
            ->orderByDesc('id');
    }

    private function transactionRules(Cashbook $cashbook, bool $required): array
    {
        return [
            'type' => array_merge($required ? ['required'] : ['sometimes'], ['in:income,expense']),
            'amount' => array_merge($required ? ['required'] : ['sometimes'], ['numeric', 'min:0.01']),
            'note' => array_merge($required ? [] : ['sometimes'], ['nullable', 'string', 'max:1000']),
            'transaction_date' => array_merge($required ? ['required'] : ['sometimes'], ['date']),
            'transaction_time' => ['sometimes', 'nullable', 'string', 'max:32'],
            'contact_id' => array_merge($required ? [] : ['sometimes'], [
                'nullable',
                'integer',
                Rule::exists('cashbook_contacts', 'id')->where(function ($query) use ($cashbook) {
                    $query->where('cashbook_id', $cashbook->id);
                }),
            ]),
            'category_id' => array_merge($required ? [] : ['sometimes'], [
                'nullable',
                'integer',
                Rule::exists('cashbook_categories', 'id')->where(function ($query) use ($cashbook) {
                    $query->where('cashbook_id', $cashbook->id);
                }),
            ]),
            'payment_mode_id' => array_merge($required ? [] : ['sometimes'], [
                'nullable',
                'integer',
                Rule::exists('cashbook_payment_modes', 'id')->where(function ($query) use ($cashbook) {
                    $query->where('cashbook_id', $cashbook->id);
                }),
            ]),
            'attachment' => ['sometimes', 'file', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,application/pdf', 'max:10240'],
            'attachments' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if (is_array($value) || $value instanceof UploadedFile) {
                        return;
                    }

                    $fail('The attachments field must be a file or an array of files.');
                },
            ],
            'attachments.*' => ['file', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,application/pdf', 'max:10240'],
            'deleted_attachments' => ['sometimes', 'array'],
            'deleted_attachments.*' => ['string'],
            'replace_attachments' => ['sometimes', 'boolean'],
        ];
    }

    private function transactionPayload(array $validated): array
    {
        return array_filter([
            'type' => $validated['type'] ?? null,
            'amount' => $validated['amount'] ?? null,
            'note' => $validated['note'] ?? null,
            'transaction_date' => $validated['transaction_date'] ?? null,
            'transaction_time' => $validated['transaction_time'] ?? null,
            'contact_id' => $validated['contact_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'payment_mode_id' => $validated['payment_mode_id'] ?? null,
        ], static fn ($value) => $value !== null);
    }

    private function syncAttachments(Transaction $transaction, Request $request): void
    {
        $files = [];

        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $files[] = is_array($attachment) ? $attachment : [$attachment];
        }

        if ($request->hasFile('attachments')) {
            $attachments = $request->file('attachments');
            $files[] = is_array($attachments) ? $attachments : [$attachments];
        }

        foreach ($files as $fileGroup) {
            foreach ($fileGroup as $file) {
                $transaction->addMedia($file)->toMediaCollection('attachments');
            }
        }
    }

    private function findCashbookOrFail(Request $request, int $cashbookId): Cashbook
    {
        $cashbook = $this->cashbookService->cashbooksWithAggregatesForUser($request->user())
            ->where('id', $cashbookId)
            ->first();

        abort_if(!$cashbook, 404, 'Cashbook not found');

        return $cashbook;
    }

    private function findTransactionOrFail(Request $request, int $transactionId): Transaction
    {
        $transaction = Transaction::query()
            ->with(['cashbook', 'contact', 'category', 'paymentMode', 'media', 'user'])
            ->where('id', $transactionId)
            ->firstOrFail();

        abort_unless($this->cashbookService->authorizeCashbook($transaction->cashbook, $request->user()), 403, 'Unauthorized');

        return $transaction;
    }
}
