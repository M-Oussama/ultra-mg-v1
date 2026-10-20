<?php

namespace App\Services;

use App\Models\ImportAuditEvent;
use App\Models\ImportParentInvoice;
use App\Models\ImportParentInvoiceLine;
use App\Models\ImportPartialInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImportAllocationService
{
    public function createPartial(ImportParentInvoice $parent, array $data): ImportPartialInvoice
    {
        return DB::transaction(function () use ($parent, $data) {
            $lockedParent = ImportParentInvoice::query()
                ->whereKey($parent->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! empty($data['idempotency_key'])) {
                $existing = ImportPartialInvoice::with('lines')
                    ->where('import_parent_invoice_id', $lockedParent->id)
                    ->where('idempotency_key', $data['idempotency_key'])
                    ->first();
                if ($existing) {
                    return $existing;
                }
            }

            $requestedLineIds = collect($data['lines'])->pluck('parent_line_id')->all();
            $parentLines = ImportParentInvoiceLine::query()
                ->where('import_parent_invoice_id', $lockedParent->id)
                ->whereIn('id', $requestedLineIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($parentLines->count() !== count($requestedLineIds)) {
                throw ValidationException::withMessages([
                    'lines' => ['Every allocation must reference a line from this parent invoice.'],
                ]);
            }

            $allocatedByLine = $this->allocatedMilliByLine($lockedParent->id, true);
            $linePayloads = [];
            $errors = [];

            foreach ($data['lines'] as $index => $requested) {
                $line = $parentLines[(int) $requested['parent_line_id']];
                $requestedMilli = $this->toFixedInt($requested['quantity'], 3);
                $parentMilli = $this->toFixedInt($line->quantity, 3);
                $allocatedMilli = $allocatedByLine[$line->id] ?? 0;
                $remainingMilli = $parentMilli - $allocatedMilli;

                if ($requestedMilli > $remainingMilli) {
                    $errors["lines.$index.quantity"] = [
                        'Requested '.$this->fromFixedInt($requestedMilli, 3)
                        .' exceeds the remaining '.$this->fromFixedInt($remainingMilli, 3)
                        .' for parent line '.$line->id.'.',
                    ];

                    continue;
                }

                $linePayloads[] = [
                    'import_parent_invoice_line_id' => $line->id,
                    'quantity' => $this->fromFixedInt($requestedMilli, 3),
                    'commercial_unit_price_snapshot' => $line->commercial_unit_price,
                ];
            }

            $allocatedContainers = (int) ImportPartialInvoice::query()
                ->where('import_parent_invoice_id', $lockedParent->id)
                ->where('status', '!=', 'cancelled')
                ->sum('container_allocation_count');
            $remainingContainers = $lockedParent->planned_container_count - $allocatedContainers;

            if ((int) $data['container_allocation_count'] > $remainingContainers) {
                $errors['container_allocation_count'] = [
                    'Requested '.$data['container_allocation_count']
                    .' containers exceeds the remaining '.$remainingContainers.'.',
                ];
            }

            if ($errors) {
                throw ValidationException::withMessages($errors);
            }

            $partial = $lockedParent->partialInvoices()->create([
                'number' => $data['number'],
                'container_allocation_count' => $data['container_allocation_count'],
                'freight_amount' => $data['freight_amount'] ?? '0',
                'status' => 'draft',
                'revision' => 1,
                'idempotency_key' => $data['idempotency_key'] ?? null,
            ]);
            $partial->lines()->createMany($linePayloads);

            if (! empty($data['container_ids'])) {
                DB::table('import_containers')
                    ->whereIn('id', $data['container_ids'])
                    ->where('import_operation_id', $lockedParent->import_operation_id)
                    ->update([
                        'import_parent_invoice_id' => $lockedParent->id,
                        'import_partial_invoice_id' => $partial->id,
                        'updated_at' => now(),
                    ]);
            }

            ImportAuditEvent::create([
                'entity_type' => ImportPartialInvoice::class,
                'entity_id' => $partial->id,
                'action' => 'created',
                'actor_id' => Auth::id(),
                'after_json' => $partial->load('lines')->toArray(),
                'occurred_at' => now(),
            ]);

            return $partial->load('lines.parentLine');
        }, 3);
    }

    public function balances(ImportParentInvoice $parent): array
    {
        $parent->load(['lines', 'partialInvoices.lines.parentLine', 'supplier']);
        $allocatedByLine = $this->allocatedMilliByLine($parent->id);

        $lines = $parent->lines->map(function (ImportParentInvoiceLine $line) use ($allocatedByLine) {
            $quantity = $this->toFixedInt($line->quantity, 3);
            $allocated = $allocatedByLine[$line->id] ?? 0;

            return [
                'parent_line_id' => $line->id,
                'product_id' => $line->product_id,
                'description' => $line->description_snapshot,
                'unit' => $line->unit,
                'quantity' => $this->fromFixedInt($quantity, 3),
                'allocated_quantity' => $this->fromFixedInt($allocated, 3),
                'remaining_quantity' => $this->fromFixedInt($quantity - $allocated, 3),
                'commercial_unit_price' => (string) $line->commercial_unit_price,
            ];
        })->values()->all();

        $parentTotalQty = (float) $parent->lines->sum(fn ($l) => (float) $l->quantity);
        $parentTotalAmount = (float) $parent->lines->sum(fn ($l) => (float) $l->quantity * (float) $l->commercial_unit_price);

        $partials = $parent->partialInvoices->map(function ($partial) {
            $qty = (float) $partial->lines->sum('quantity');
            $amount = (float) $partial->lines->sum(fn ($l) => (float) $l->quantity * (float) $l->commercial_unit_price_snapshot);

            return [
                'id' => $partial->id,
                'number' => $partial->number,
                'container_allocation_count' => (int) $partial->container_allocation_count,
                'freight_amount' => (string) $partial->freight_amount,
                'status' => $partial->status,
                'revision' => (int) $partial->revision,
                'lines' => $partial->lines->map(fn ($line) => [
                    'id' => $line->id,
                    'parent_line_id' => $line->import_parent_invoice_line_id,
                    'product_id' => $line->parentLine?->product_id,
                    'description' => $line->parentLine?->description_snapshot ?? '',
                    'unit' => $line->parentLine?->unit ?? 'pcs',
                    'quantity' => (string) $line->quantity,
                    'commercial_unit_price_snapshot' => (string) $line->commercial_unit_price_snapshot,
                ])->values()->all(),
                'total_quantity' => $qty,
                'total_amount' => $amount,
            ];
        })->values()->all();

        $allocatedContainers = (int) $parent->partialInvoices
            ->where('status', '!=', 'cancelled')
            ->sum('container_allocation_count');

        return [
            'parent_invoice_id' => $parent->id,
            'number' => $parent->number,
            'currency' => $parent->currency,
            'status' => $parent->status,
            'revision' => $parent->revision,
            'total_quantity' => $parentTotalQty,
            'total_amount' => $parentTotalAmount,
            'lines' => $lines,
            'partials' => $partials,
            'planned_containers' => $parent->planned_container_count,
            'freight_per_container' => (string) ($parent->freight_per_container ?? '0'),
            'show_container_price' => $parent->show_container_price ?? true,
            'sales_supplier_id' => $parent->sales_supplier_id,
            'supplier_name' => $parent->supplier?->full_name ?? $parent->supplier?->name ?? '',
            'supplier' => $parent->supplier,
            'allocated_containers' => $allocatedContainers,
            'remaining_containers' => $parent->planned_container_count - $allocatedContainers,
        ];
    }

    private function allocatedMilliByLine(int $parentId, bool $lock = false): array
    {
        $query = DB::table('import_partial_invoice_lines as allocation')
            ->join('import_partial_invoices as partial', 'partial.id', '=', 'allocation.import_partial_invoice_id')
            ->join('import_parent_invoice_lines as parent_line', 'parent_line.id', '=', 'allocation.import_parent_invoice_line_id')
            ->where('parent_line.import_parent_invoice_id', $parentId)
            ->whereNull('partial.deleted_at')
            ->where('partial.status', '!=', 'cancelled')
            ->select(['allocation.import_parent_invoice_line_id', 'allocation.quantity']);

        if ($lock) {
            $query->lockForUpdate();
        }

        $allocated = [];
        foreach ($query->get() as $row) {
            $lineId = (int) $row->import_parent_invoice_line_id;
            $allocated[$lineId] = ($allocated[$lineId] ?? 0)
                + $this->toFixedInt($row->quantity, 3);
        }

        return $allocated;
    }

    private function toFixedInt($value, int $scale): int
    {
        $text = trim((string) $value);
        [$whole, $fraction] = array_pad(explode('.', $text, 2), 2, '');
        $fraction = substr(str_pad($fraction, $scale, '0'), 0, $scale);

        return ((int) $whole * (10 ** $scale)) + (int) $fraction;
    }

    private function fromFixedInt(int $value, int $scale): string
    {
        $factor = 10 ** $scale;

        return intdiv($value, $factor).'.'.str_pad((string) ($value % $factor), $scale, '0', STR_PAD_LEFT);
    }
}
