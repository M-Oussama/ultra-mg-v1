<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Product;
use App\Models\SalesSupplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ImportProformaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_proforma_is_saved_reopened_and_updated_without_changing_stock_or_catalog_name(): void
    {
        $user = User::factory()->create(['email' => 'admin@gmail.com']);
        Sanctum::actingAs($user);

        $department = Department::create([
            'name' => 'Packaging Resell',
            'department_type' => 'resell',
        ]);
        $cityId = DB::table('cities')->insertGetId([
            'code' => 16,
            'name' => 'Algiers',
            'country' => 'Algeria',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $supplier = SalesSupplier::create([
            'name' => 'Shida Lafeil International',
            'city_id' => $cityId,
            'departement_id' => $department->id,
        ]);
        $product = Product::create([
            'name' => 'POT 50 Sablé',
            'price' => 58.50,
            'tax_rate' => 0,
            'department_id' => $department->id,
        ]);

        $stockBefore = DB::table('product_stocks')
            ->where('product_id', $product->id)
            ->value('quantity');

        $create = $this->postJson('/api/import-proformas/store', [
            'data' => [
                'number' => 'IMP-20261008-001',
                'proforma_date' => '2026-10-08',
                'supplier_id' => $supplier->id,
                'departement_id' => $department->id,
                'currency' => 'USD',
                'notes' => 'Quote CIF Algiers.',
                'items' => [[
                    'product_id' => $product->id,
                    'product_name' => 'POT 50 Sablé — export box',
                    'reference' => 'POT-50-EXP',
                    'quantity' => 20,
                    'unit_price' => 58.50,
                ]],
            ],
        ]);

        $create->assertCreated()
            ->assertJsonPath('proforma.items.0.product_name', 'POT 50 Sablé — export box')
            ->assertJsonPath('proforma.total_quantity', 20)
            ->assertJsonPath('proforma.total_amount', 1170);

        $id = (int) $create->json('proforma.id');

        $this->getJson("/api/import-proformas/show/{$id}")
            ->assertOk()
            ->assertJsonPath('proforma.items.0.product_name', 'POT 50 Sablé — export box');

        $update = $this->postJson("/api/import-proformas/update/{$id}", [
            'data' => [
                'number' => 'IMP-20261008-001',
                'proforma_date' => '2026-10-08',
                'supplier_id' => $supplier->id,
                'departement_id' => $department->id,
                'currency' => 'USD',
                'items' => [[
                    'product_id' => $product->id,
                    'product_name' => 'POT 50 Sablé — revised supplier name',
                    'reference' => 'POT-50-EXP',
                    'quantity' => 25,
                    'unit_price' => 57.25,
                ]],
            ],
        ]);

        $update->assertOk()
            ->assertJsonPath('proforma.items.0.product_name', 'POT 50 Sablé — revised supplier name')
            ->assertJsonPath('proforma.total_quantity', 25)
            ->assertJsonPath('proforma.total_amount', 1431.25);

        $this->getJson('/api/import-proformas/list?departement_id='.$department->id)
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $id);

        $this->assertDatabaseHas('import_proforma_items', [
            'import_proforma_id' => $id,
            'product_name' => 'POT 50 Sablé — revised supplier name',
            'quantity' => 25,
        ]);
        $this->assertSame('POT 50 Sablé', $product->fresh()->name);
        $this->assertEquals(
            $stockBefore,
            DB::table('product_stocks')
                ->where('product_id', $product->id)
                ->value('quantity')
        );
    }
}
