<?php

namespace App\Services;

use App\Models\Cashbook;
use App\Models\CashbookCategory;
use App\Models\CashbookContact;
use App\Models\CashbookPaymentMode;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class CashbookImportService
{
    /**
     * @var array<string, mixed>
     */
    private array $lookupCache = [];

    public function importLegacySpreadsheet(Cashbook $cashbook, UploadedFile $file, User $actor): array
    {
        $path = $file->getRealPath();
        abort_if($path === false || $path === null, 422, 'Unable to read the uploaded spreadsheet.');
        $sourceFileName = $file->getClientOriginalName();

        $sheetData = Excel::toArray([], $path)[0] ?? [];

        if (count($sheetData) < 2) {
            throw ValidationException::withMessages([
                'file' => 'The spreadsheet must contain a header row and at least one data row.',
            ]);
        }

        $headers = $this->buildHeaderMap(array_shift($sheetData));
        $this->ensureRequiredHeaders($headers);

        $stats = [
            'imported' => 0,
            'skipped' => 0,
            'deleted' => 0,
            'contacts_created' => 0,
            'categories_created' => 0,
            'payment_modes_created' => 0,
        ];

        DB::transaction(function () use ($cashbook, $actor, $headers, $sheetData, $sourceFileName, &$stats) {
            $stats['deleted'] = Transaction::query()
                ->where('cashbook_id', $cashbook->id)
                ->where('import_source', 'legacy_excel')
                ->delete();

            foreach ($sheetData as $index => $row) {
                $rowNumber = $index + 2;
                $normalized = $this->normalizeRow($headers, $row);

                if ($this->isBlankRow($normalized)) {
                    continue;
                }

                $payload = $this->buildTransactionPayload(
                    cashbook: $cashbook,
                    actor: $actor,
                    sourceFileName: $sourceFileName,
                    row: $normalized,
                    rowNumber: $rowNumber,
                    stats: $stats
                );

                if ($payload === null) {
                    $stats['skipped']++;
                    continue;
                }

                Transaction::create($payload);
                $stats['imported']++;
            }
        });

        return $stats;
    }

    private function ensureRequiredHeaders(array $headers): void
    {
        foreach (['date', 'remark', 'category', 'mode', 'entry_by', 'cash_in', 'cash_out', 'balance'] as $required) {
            if (!in_array($required, $headers, true)) {
                throw ValidationException::withMessages([
                    'file' => "Missing required spreadsheet column: {$required}",
                ]);
            }
        }
    }

    /**
     * @param  array<int, string>  $headerRow
     * @return array<int, string>
     */
    private function buildHeaderMap(array $headerRow): array
    {
        $headers = [];

        foreach ($headerRow as $index => $column) {
            $headers[$index] = $this->normalizeHeader((string) $column);
        }

        return $headers;
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizeRow(array $headers, array $row): array
    {
        $normalized = [];

        foreach ($headers as $index => $header) {
            $normalized[$header] = $row[$index] ?? null;
        }

        return $normalized;
    }

    private function normalizeHeader(string $header): string
    {
        $header = strtolower(trim($header));
        $header = preg_replace('/[^a-z0-9]+/', '_', $header) ?? $header;

        return trim($header, '_');
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $stats
     */
    private function buildTransactionPayload(Cashbook $cashbook, User $actor, string $sourceFileName, array $row, int $rowNumber, array &$stats): ?array
    {
        $date = $this->parseDate($row['date'] ?? null);
        if ($date === null) {
            return null;
        }

        $time = $this->parseTime($row['time'] ?? null);

        $remark = trim((string) ($row['remark'] ?? ''));
        $categoryLabel = trim((string) ($row['category'] ?? ''));
        $modeLabel = trim((string) ($row['mode'] ?? ''));
        $contactLabel = trim((string) ($row['party'] ?? ''));
        $entryByLabel = trim((string) ($row['entry_by'] ?? ''));

        $cashIn = $this->parseAmount($row['cash_in'] ?? null);
        $cashOut = $this->parseAmount($row['cash_out'] ?? null);

        if ($cashIn <= 0 && $cashOut <= 0) {
            return null;
        }

        [$type, $amount] = $this->resolveTransactionTypeAndAmount($cashIn, $cashOut);

        $contact = $this->resolveContact($cashbook, $actor, $contactLabel, $stats);
        $category = $this->resolveCategory($cashbook, $actor, $categoryLabel, $type, $stats);
        $paymentMode = $this->resolvePaymentMode($cashbook, $actor, $modeLabel, $stats);
        $entryBy = $this->resolveEntryBy($entryByLabel, $cashbook, $actor);

        return array_filter([
            'cashbook_id' => $cashbook->id,
            'user_id' => $entryBy->id,
            'organization_id' => $cashbook->organization_id,
            'contact_id' => $contact?->id,
            'category_id' => $category?->id,
            'payment_mode_id' => $paymentMode?->id,
            'type' => $type,
            'amount' => $amount,
            'note' => $remark !== '' ? $remark : null,
            'transaction_date' => $date->toDateString(),
            'transaction_time' => $time,
            'import_source' => 'legacy_excel',
            'source_file_name' => $sourceFileName,
            'source_row_number' => $rowNumber,
            'source_payload' => [
                'date' => $date->toDateString(),
                'time' => $time,
                'remark' => $remark,
                'party' => $contactLabel,
                'category' => $categoryLabel,
                'mode' => $modeLabel,
                'entry_by' => $entryByLabel,
                'cash_in' => $cashIn,
                'cash_out' => $cashOut,
                'balance' => $this->parseAmount($row['balance'] ?? null),
                'row_number' => $rowNumber,
            ],
        ], static fn ($value) => $value !== null);
    }

    private function resolveTransactionTypeAndAmount(float $cashIn, float $cashOut): array
    {
        if ($cashIn > 0 && $cashOut <= 0) {
            return ['income', $cashIn];
        }

        if ($cashOut > 0 && $cashIn <= 0) {
            return ['expense', $cashOut];
        }

        if ($cashIn >= $cashOut) {
            return ['income', max($cashIn, $cashOut)];
        }

        return ['expense', max($cashIn, $cashOut)];
    }

    private function resolveContact(Cashbook $cashbook, User $actor, string $label, array &$stats): ?CashbookContact
    {
        if ($label === '') {
            return null;
        }

        $key = 'contact|' . mb_strtolower($label);
        if (array_key_exists($key, $this->lookupCache)) {
            return $this->lookupCache[$key];
        }

        $query = CashbookContact::withTrashed()
            ->where('cashbook_id', $cashbook->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($label)]);

        $lookup = $query->first();
        if ($lookup) {
            if ($lookup->trashed()) {
                $lookup->restore();
            }
            $lookup->fill([
                'user_id' => $actor->id,
                'organization_id' => $cashbook->organization_id,
            ])->save();
        } else {
            $lookup = CashbookContact::create([
                'cashbook_id' => $cashbook->id,
                'user_id' => $actor->id,
                'organization_id' => $cashbook->organization_id,
                'name' => $label,
            ]);
            $stats['contacts_created']++;
        }

        return $this->lookupCache[$key] = $lookup;
    }

    private function resolveCategory(Cashbook $cashbook, User $actor, string $label, string $type, array &$stats): ?CashbookCategory
    {
        if ($label === '') {
            return null;
        }

        $key = 'category|' . mb_strtolower($label);
        if (array_key_exists($key, $this->lookupCache)) {
            /** @var CashbookCategory $lookup */
            $lookup = $this->lookupCache[$key];
            $this->syncCategoryKind($lookup, $type);
            return $lookup;
        }

        $query = CashbookCategory::withTrashed()
            ->where('cashbook_id', $cashbook->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($label)]);

        $lookup = $query->first();
        if ($lookup) {
            if ($lookup->trashed()) {
                $lookup->restore();
            }
            $lookup->fill([
                'user_id' => $actor->id,
                'organization_id' => $cashbook->organization_id,
            ])->save();
            $this->syncCategoryKind($lookup, $type);
        } else {
            $lookup = CashbookCategory::create([
                'cashbook_id' => $cashbook->id,
                'user_id' => $actor->id,
                'organization_id' => $cashbook->organization_id,
                'name' => $label,
                'kind' => $type,
            ]);
            $stats['categories_created']++;
        }

        return $this->lookupCache[$key] = $lookup;
    }

    private function syncCategoryKind(CashbookCategory $lookup, string $type): void
    {
        $currentKind = $lookup->kind;
        if ($currentKind === null || $currentKind === '') {
            $lookup->update(['kind' => $type]);
            return;
        }

        if ($currentKind !== $type && $currentKind !== 'both') {
            $lookup->update(['kind' => 'both']);
        }
    }

    private function resolvePaymentMode(Cashbook $cashbook, User $actor, string $label, array &$stats): ?CashbookPaymentMode
    {
        if ($label === '') {
            return null;
        }

        $key = 'payment|' . mb_strtolower($label);
        if (array_key_exists($key, $this->lookupCache)) {
            return $this->lookupCache[$key];
        }

        $query = CashbookPaymentMode::withTrashed()
            ->where('cashbook_id', $cashbook->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($label)]);

        $lookup = $query->first();
        if ($lookup) {
            if ($lookup->trashed()) {
                $lookup->restore();
            }
            $lookup->fill([
                'user_id' => $actor->id,
                'organization_id' => $cashbook->organization_id,
            ])->save();
        } else {
            $lookup = CashbookPaymentMode::create([
                'cashbook_id' => $cashbook->id,
                'user_id' => $actor->id,
                'organization_id' => $cashbook->organization_id,
                'name' => $label,
            ]);
            $stats['payment_modes_created']++;
        }

        return $this->lookupCache[$key] = $lookup;
    }

    private function resolveEntryBy(string $label, Cashbook $cashbook, User $actor): User
    {
        if ($label === '') {
            return $actor;
        }

        $normalized = mb_strtolower($label);

        $query = User::query();
        if ($cashbook->organization_id) {
            $query->where(function ($builder) use ($cashbook) {
                $builder->where('organization_id', $cashbook->organization_id)
                    ->orWhere('id', $cashbook->user_id);
            });
        }

        $match = $query
            ->where(function ($builder) use ($normalized) {
                $builder->whereRaw('LOWER(name) = ?', [$normalized])
                    ->orWhereRaw('LOWER(email) = ?', [$normalized])
                    ->orWhereRaw("LOWER(COALESCE(fcm_device_name, '')) = ?", [$normalized]);
            })
            ->first();

        if ($match) {
            return $match;
        }

        $partial = User::query()
            ->when($cashbook->organization_id, function ($query) use ($cashbook) {
                $query->where(function ($builder) use ($cashbook) {
                    $builder->where('organization_id', $cashbook->organization_id)
                        ->orWhere('id', $cashbook->user_id);
                });
            })
            ->whereRaw('LOWER(name) LIKE ?', ['%' . $normalized . '%'])
            ->first();

        return $partial ?: $actor;
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->startOfDay();
        }

        try {
            return Carbon::parse((string) $value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseTime(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('H:i:s');
        }

        try {
            return Carbon::parse((string) $value)->format('H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseAmount(mixed $value): float
    {
        if ($value === null) {
            return 0.0;
        }

        $normalized = str_replace([',', ' '], '', (string) $value);
        if ($normalized === '' || strtolower($normalized) === 'null') {
            return 0.0;
        }

        return (float) $normalized;
    }
}
