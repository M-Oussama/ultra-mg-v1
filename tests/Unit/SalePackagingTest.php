<?php

namespace Tests\Unit;

use App\Http\Controllers\PDFController;
use App\Http\Controllers\POSController;
use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use ReflectionMethod;
use Tests\TestCase;

class SalePackagingTest extends TestCase
{
    private function saleItem(): SaleItem
    {
        $item = new SaleItem();
        $item->forceFill([
            'id' => 2479,
            'sale_id' => 922,
            'product_id' => 1,
            'quantity' => 10100,
            'package_type' => 'Carton',
            'units_per_package' => 1600,
            'price' => 0,
            'total_price' => 0,
            'price_active' => true,
        ]);
        $item->setRelation('product', new Product([
            'name' => 'Bouchon 50G Gold',
            'package_type' => 'Carton',
            'units_per_package' => 1600,
        ]));

        return $item;
    }

    private function allocations(string $controllerClass): array
    {
        $queues = ['1' => [
            ['reference' => '260205', 'remaining_quantity' => 7562, 'units_per_package' => 1600],
            ['reference' => 'BG-03', 'remaining_quantity' => 2538, 'units_per_package' => 1680],
        ]];
        $allocations = [];
        $method = new ReflectionMethod($controllerClass, 'allocateStockReferencesForSaleItem');
        $method->setAccessible(true);
        $method->invokeArgs(new $controllerClass(), [&$queues, $this->saleItem(), 922, &$allocations]);

        return $allocations;
    }

    public function test_receipt_api_keeps_supply_batch_size_and_loose_remainder(): void
    {
        $lines = $this->allocations(POSController::class)[2479];
        $this->assertSame(1600, $lines['260205']['units_per_package']);
        $this->assertSame(1680, $lines['BG-03']['units_per_package']);
        $this->assertSame('4 Carton (1600) + 1162 pcs isolées', $lines['260205']['carton_breakdown']);
        $this->assertSame('1 Carton (1680) + 858 pcs isolées', $lines['BG-03']['carton_breakdown']);
        $this->assertEquals(10100, array_sum(array_column($lines, 'quantity_value')));
    }

    public function test_pdf_allocations_keep_each_remainder_with_its_own_size(): void
    {
        $lines = $this->allocations(PDFController::class)[2479];
        $this->assertSame(1600, $lines['260205']['units_per_package']);
        $this->assertSame(1680, $lines['BG-03']['units_per_package']);
        $this->assertSame('4 Carton (1600) + 1162 pcs', $lines['260205']['carton_breakdown']);
        $this->assertSame('1 Carton (1680) + 858 pcs', $lines['BG-03']['carton_breakdown']);
        $this->assertEquals(10100, array_sum(array_column($lines, 'quantity_value')));
    }

    public function test_receipt_and_preparation_templates_render_both_batch_breakdowns(): void
    {
        $sale = new Sale(['sale_date' => '2026-10-06', 'total_amount' => 0, 'paid_amount' => 0]);
        $sale->id = 922;
        $sale->setRelation('saleItems', collect([$this->saleItem()]));
        $sale->setRelation('client', new Client(['name' => 'Packaging test client']));
        $data = [
            'sale' => $sale,
            'company' => new Company(['name' => 'Packaging test company']),
            'amountLetter' => 'Zero Dinar',
            'showCompanyInfo' => false,
            'saleItemStockReferences' => $this->allocations(PDFController::class),
        ];

        foreach (['sale_delivery_pdf', 'sale_preparation_pdf', 'sale_pdf'] as $template) {
            $html = view($template, $data)->render();
            $this->assertStringContainsString('4 Carton (1600) + 1162 pcs', $html, $template);
            $this->assertStringContainsString('1 Carton (1680) + 858 pcs', $html, $template);
            $this->assertStringNotContainsString('6 Carton (1600)', $html, $template);
            $this->assertStringNotContainsString('2020 pieces', $html, $template);
            $this->assertStringNotContainsString('938 pcs', $html, $template);

            $bytes = Pdf::loadHTML($html)->setPaper('a4', 'portrait')->output();
            $this->assertStringStartsWith('%PDF-', $bytes, $template);
        }
    }
}
