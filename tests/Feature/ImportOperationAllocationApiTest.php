<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ImportOperationAllocationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_and_partial_allocations_return_exact_remaining_balances(): void
    {
        Sanctum::actingAs(User::factory()->create(['email' => 'admin@gmail.com']));

        $operation = $this->postJson('/api/importations', [
            'reference' => 'IMP-2026-001',
            'currency' => 'usd',
            'origin' => 'Ningbo, China',
            'destination' => 'Algiers, Algeria',
        ])->assertCreated()->json('operation');

        $parent = $this->postJson("/api/importations/{$operation['id']}/parent-invoices", [
            'number' => 'A',
            'currency' => 'USD',
            'planned_container_count' => 5,
            'freight_per_container' => '1000.0000',
            'lines' => [
                [
                    'description' => 'Packaging cups',
                    'unit' => 'pcs',
                    'quantity' => '2000000.000',
                    'commercial_unit_price' => '0.1250',
                ],
                [
                    'description' => 'Packaging lids',
                    'unit' => 'pcs',
                    'quantity' => '500000.000',
                    'commercial_unit_price' => '0.0500',
                ],
            ],
        ])->assertCreated()->json('parent_invoice');

        $lineId = $parent['lines'][0]['id'];
        $secondLineId = $parent['lines'][1]['id'];
        $this->createPartial($operation['id'], $parent['id'], 'A1', 'allocation-a1', $lineId, '400000.000');
        $this->createPartial($operation['id'], $parent['id'], 'A2', 'allocation-a2', $lineId, '350000.000');

        $this->getJson("/api/importations/{$operation['id']}/parent-invoices/{$parent['id']}/balances")
            ->assertOk()
            ->assertJsonPath('lines.0.allocated_quantity', '750000.000')
            ->assertJsonPath('lines.0.remaining_quantity', '1250000.000')
            ->assertJsonPath('allocated_containers', 2)
            ->assertJsonPath('remaining_containers', 3)
            ->assertJsonPath('partials.0.lines.0.parent_line_id', $lineId)
            ->assertJsonPath('partials.0.lines.0.description', 'Packaging cups')
            ->assertJsonPath('partials.0.lines.0.quantity', '400000.000')
            ->assertJsonPath('partials.0.lines.0.commercial_unit_price_snapshot', '0.1250');

        $this->getJson("/api/importations/{$operation['id']}/overview")
            ->assertOk()
            ->assertJsonPath('balances.0.partials.1.lines.0.quantity', '350000.000');

        $partialId = \App\Models\ImportPartialInvoice::where('number', 'A1')->value('id');
        $allocationId = \App\Models\ImportPartialInvoiceLine::where('import_partial_invoice_id', $partialId)->value('id');
        $containerId = \Illuminate\Support\Facades\DB::table('import_containers')->insertGetId([
            'import_operation_id' => $operation['id'],
            'import_partial_invoice_id' => $partialId,
            'status' => 'planned',
        ]);
        $costId = \Illuminate\Support\Facades\DB::table('import_costs')->insertGetId([
            'import_operation_id' => $operation['id'],
            'import_partial_invoice_id' => $partialId,
            'category' => 'freight', 'value_type' => 'estimate', 'amount' => 1000, 'currency' => 'USD',
        ]);
        $this->postJson("/api/importations/{$operation['id']}/parent-invoices/{$parent['id']}/partials/{$partialId}", [
            'number' => 'A1 revised', 'freight_amount' => '1200.00',
        ])->assertOk()
            ->assertJsonPath('partial_invoice.id', $partialId)
            ->assertJsonPath('partial_invoice.lines.0.id', $allocationId)
            ->assertJsonPath('balances.partials.0.lines.0.quantity', '400000.000');
        $this->assertDatabaseHas('import_containers', ['id' => $containerId, 'import_partial_invoice_id' => $partialId]);
        $this->assertDatabaseHas('import_costs', ['id' => $costId, 'import_partial_invoice_id' => $partialId]);

        $this->postJson("/api/importations/{$operation['id']}/parent-invoices/{$parent['id']}/partials/{$partialId}", [
            'number' => 'A1 revised',
            'lines' => [
                ['parent_line_id' => $lineId, 'quantity' => '400000.000'],
                ['parent_line_id' => $secondLineId, 'quantity' => '100000.000'],
            ],
        ])->assertOk()
            ->assertJsonPath('partial_invoice.id', $partialId)
            ->assertJsonPath('partial_invoice.lines.0.id', $allocationId)
            ->assertJsonPath('partial_invoice.lines.1.import_parent_invoice_line_id', $secondLineId)
            ->assertJsonPath('balances.partials.0.lines.1.quantity', '100000.000');
        $this->assertDatabaseHas('import_containers', ['id' => $containerId, 'import_partial_invoice_id' => $partialId]);
        $this->assertDatabaseHas('import_costs', ['id' => $costId, 'import_partial_invoice_id' => $partialId]);
    }

    public function test_over_allocation_is_rejected_and_idempotent_retry_does_not_allocate_twice(): void
    {
        Sanctum::actingAs(User::factory()->create(['email' => 'admin@gmail.com']));

        $operationId = $this->postJson('/api/importations', [
            'reference' => 'IMP-2026-002',
            'currency' => 'USD',
        ])->assertCreated()->json('operation.id');

        $parent = $this->postJson("/api/importations/{$operationId}/parent-invoices", [
            'number' => 'A',
            'currency' => 'USD',
            'planned_container_count' => 5,
            'lines' => [[
                'description' => 'Packaging cups',
                'quantity' => '2000000.000',
                'commercial_unit_price' => '0.1250',
            ]],
        ])->assertCreated()->json('parent_invoice');

        $lineId = $parent['lines'][0]['id'];
        $first = $this->createPartial($operationId, $parent['id'], 'A1', 'same-request', $lineId, '750000.000');
        $retry = $this->createPartial($operationId, $parent['id'], 'A1', 'same-request', $lineId, '750000.000');
        $this->assertSame($first['id'], $retry['id']);

        $this->postJson("/api/importations/{$operationId}/parent-invoices/{$parent['id']}/partials", [
            'number' => 'A2',
            'container_allocation_count' => 1,
            'idempotency_key' => 'over-allocation',
            'lines' => [[
                'parent_line_id' => $lineId,
                'quantity' => '1300000.000',
            ]],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['lines.0.quantity'])
            ->assertJsonFragment([
                'Requested 1300000.000 exceeds the remaining 1250000.000 for parent line '.$lineId.'.',
            ]);

        $this->assertDatabaseCount('import_partial_invoices', 1);
        $this->assertDatabaseCount('import_partial_invoice_lines', 1);
    }

    public function test_loaded_partial_keeps_real_values_when_planned_quantity_is_reduced_and_a_product_is_added(): void
    {
        Sanctum::actingAs(User::factory()->create(['email' => 'admin@gmail.com']));

        $operationId = $this->postJson('/api/importations', [
            'reference' => 'IMP-2026-003',
            'currency' => 'USD',
        ])->assertCreated()->json('operation.id');

        $parent = $this->postJson("/api/importations/{$operationId}/parent-invoices", [
            'number' => 'A',
            'currency' => 'USD',
            'planned_container_count' => 2,
            'lines' => [
                [
                    'description' => 'Glass jars',
                    'quantity' => '500000.000',
                    'commercial_unit_price' => '0.1250',
                    'customs_percentage' => '5.0000',
                ],
                [
                    'description' => 'Jar lids',
                    'quantity' => '200000.000',
                    'commercial_unit_price' => '0.0500',
                    'customs_percentage' => '10.0000',
                ],
            ],
        ])->assertCreated()->json('parent_invoice');

        $partial = $this->createPartial(
            $operationId,
            $parent['id'],
            'A1',
            'loaded-allocation',
            $parent['lines'][0]['id'],
            '300000.000'
        );
        $allocation = \App\Models\ImportPartialInvoiceLine::findOrFail($partial['lines'][0]['id']);
        $allocation->update([
            'real_quantity' => '287560.000',
            'real_unit_price' => '0.1280',
        ]);
        \App\Models\ImportPartialInvoice::whereKey($partial['id'])->update(['loaded_at' => now()]);
        $otherPartial = \App\Models\ImportPartialInvoice::create([
            'import_parent_invoice_id' => $parent['id'],
            'number' => 'A2',
            'container_allocation_count' => 0,
            'freight_amount' => '0',
            'status' => 'draft',
            'revision' => 1,
        ]);
        $otherPartial->lines()->create([
            'import_parent_invoice_line_id' => $parent['lines'][0]['id'],
            'quantity' => '300000.000',
            'commercial_unit_price_snapshot' => '0.1250',
        ]);
        \Illuminate\Support\Facades\DB::table('import_containers')->insert([
            'import_operation_id' => $operationId,
            'import_parent_invoice_id' => $parent['id'],
            'import_partial_invoice_id' => $partial['id'],
            'status' => 'planned',
            'transitaire_fee_dzd' => '100000.0000',
        ]);

        $this->postJson(
            "/api/importations/{$operationId}/parent-invoices/{$parent['id']}/partials/{$partial['id']}",
            [
                'number' => 'A1',
                'container_allocation_count' => 1,
                'agent_percentage' => '10.0000',
                'bank_exchange_rate_dzd' => '140.0000',
                'transport_amount' => '1000.0000',
                'transport_customs_percentage' => '5.0000',
                'transport_exchange_rate_dzd' => '140.0000',
                'lines' => [
                    ['parent_line_id' => $parent['lines'][0]['id'], 'quantity' => '280000.000'],
                    ['parent_line_id' => $parent['lines'][1]['id'], 'quantity' => '50000.000'],
                ],
            ]
        )->assertOk()
            ->assertJsonPath('partial_invoice.id', $partial['id'])
            ->assertJsonPath('partial_invoice.lines.0.id', $allocation->id)
            ->assertJsonPath('partial_invoice.lines.0.quantity', '280000.000')
            ->assertJsonPath('partial_invoice.lines.0.real_quantity', '287560.000')
            ->assertJsonPath('partial_invoice.lines.0.real_unit_price', '0.1280')
            ->assertJsonPath('partial_invoice.lines.1.import_parent_invoice_line_id', $parent['lines'][1]['id'])
            ->assertJsonPath('partial_invoice.lines.1.quantity', '50000.000');

        $this->getJson("/api/importations/{$operationId}/overview")
            ->assertOk()
            ->assertJsonPath('balances.0.partials.0.agent_percentage', '10.0000')
            ->assertJsonPath('balances.0.partials.0.bank_exchange_rate_dzd', '140.0000')
            ->assertJsonPath('balances.0.partials.0.settlement.agent_amount', 3680.768)
            ->assertJsonPath('balances.0.partials.0.settlement.agent_amount_dzd', 515307.52)
            ->assertJsonPath('balances.0.partials.0.settlement.bank_amount_dzd', 5250000)
            ->assertJsonPath('balances.0.partials.0.settlement.customs_amount_dzd', 292653.76)
            ->assertJsonPath('balances.0.partials.0.settlement.transitaire_amount_dzd', 100000)
            ->assertJsonPath('balances.0.partials.0.settlement.transport_amount_dzd', 140000)
            ->assertJsonPath('balances.0.partials.0.settlement.transport_customs_amount_dzd', 7000)
            ->assertJsonPath('balances.0.partials.0.settlement.transport_total_dzd', 147000)
            ->assertJsonPath('balances.0.partials.0.settlement.total_required_dzd', 6304961.28);
    }

    private function createPartial(
        int $operationId,
        int $parentId,
        string $number,
        string $idempotencyKey,
        int $lineId,
        string $quantity
    ): array {
        return $this->postJson("/api/importations/{$operationId}/parent-invoices/{$parentId}/partials", [
            'number' => $number,
            'container_allocation_count' => 1,
            'freight_amount' => '1000.0000',
            'idempotency_key' => $idempotencyKey,
            'lines' => [[
                'parent_line_id' => $lineId,
                'quantity' => $quantity,
            ]],
        ])->assertCreated()->json('partial_invoice');
    }
}
