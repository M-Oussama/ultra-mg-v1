<?php

namespace Tests\Unit;

use App\Http\Controllers\CertifyInvoiceController;
use App\Http\Controllers\ChequeController;
use App\Models\CertifyInvoices;
use App\Models\Cheque;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class CertifyInvoiceTtcTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        foreach (['certify_invoices', 'sub_certify_invoices'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('client_id')->nullable();
                $table->double('amount')->default(0);
                $table->double('ht_amount')->nullable();
                $table->double('tva_rate')->nullable();
                $table->double('tva_amount')->nullable();
                $table->double('timbre_rate')->nullable();
                $table->double('timbre_amount')->nullable();
                $table->integer('payment_type')->nullable();
                $table->integer('cheque_id')->nullable();
                $table->string('cheque_number')->nullable();
                $table->softDeletes();
            });
        }
    }

    public function test_ttc_handles_legacy_ht_missing_vat_recorded_taxes_and_existing_ttc(): void
    {
        $cases = [
            [['amount' => 100, 'ht_amount' => 100, 'tva_rate' => 19, 'tva_amount' => 0], 119],
            [['amount' => 119, 'ht_amount' => 100, 'tva_rate' => 19, 'tva_amount' => 19], 119],
            [['amount' => 100, 'ht_amount' => 100, 'tva_rate' => 9, 'tva_amount' => null], 109],
            [['amount' => 100, 'ht_amount' => 100, 'tva_rate' => 0, 'tva_amount' => 0], 100],
            [['amount' => 100, 'ht_amount' => 100, 'tva_rate' => 19, 'tva_amount' => 9], 109],
            [['amount' => 100, 'ht_amount' => 100, 'tva_rate' => 19, 'payment_type' => 1, 'timbre_rate' => 2], 121.38],
            [['amount' => 121, 'ht_amount' => 100, 'tva_amount' => 19, 'timbre_amount' => 2, 'payment_type' => 1], 121],
            [['amount' => 100, 'ht_amount' => null, 'tva_rate' => 19], 119],
            [['amount' => 1680667.5, 'ht_amount' => 1680667.5, 'tva_rate' => 19, 'tva_amount' => 0], 1999994.33],
        ];

        foreach ($cases as [$fields, $expected]) {
            $id = DB::table('certify_invoices')->insertGetId($fields);
            $invoice = CertifyInvoices::without(['client', 'certifyInvoiceProducts', 'cheque', 'user'])->find($id);
            $sqlAmount = DB::table('certify_invoices')->where('id', $id)
                ->selectRaw(CertifyInvoices::amountTtcExpression().' as total_ttc')
                ->value('total_ttc');
            $this->assertEqualsWithDelta($expected, $invoice->amount_ttc, 0.001);
            $this->assertEqualsWithDelta($expected, $sqlAmount, 0.001);
        }
    }

    public function test_dashboard_summary_and_payment_breakdown_sum_ttc_without_double_taxing(): void
    {
        DB::table('certify_invoices')->insert([
            ['client_id' => 1, 'amount' => 100, 'ht_amount' => 100, 'tva_rate' => 19, 'tva_amount' => 0, 'payment_type' => 2],
            ['client_id' => 1, 'amount' => 119, 'ht_amount' => 100, 'tva_rate' => 19, 'tva_amount' => 19, 'payment_type' => 2],
            ['client_id' => 2, 'amount' => 200, 'ht_amount' => 200, 'tva_rate' => 9, 'tva_amount' => 0, 'payment_type' => 3],
        ]);

        $summary = (new CertifyInvoiceController())->getSummary(Request::create('/summary'))->getData(true);
        $this->assertEqualsWithDelta(456, $summary['total_amount_ttc'], 0.001);
        $this->assertSame(3, $summary['invoice_count']);
        $this->assertEqualsWithDelta(238, $summary['payment_type_breakdown'][0]['total_amount_ttc'], 0.001);
        $this->assertEqualsWithDelta(218, $summary['payment_type_breakdown'][1]['total_amount_ttc'], 0.001);
    }

    public function test_cheque_usage_counts_ttc_once_when_id_and_number_both_match(): void
    {
        DB::table('certify_invoices')->insert([
            'amount' => 100, 'ht_amount' => 100, 'tva_rate' => 19, 'tva_amount' => 0,
            'cheque_id' => 7, 'cheque_number' => 'C-7', 'payment_type' => 2,
        ]);
        $cheque = new Cheque();
        $cheque->forceFill(['id' => 7, 'cheque_number' => 'C-7', 'amount' => 200]);
        $method = new ReflectionMethod(ChequeController::class, 'attachUsageStats');
        $method->setAccessible(true);
        $result = $method->invoke(new ChequeController(), collect([$cheque]), false)->first();

        $this->assertEqualsWithDelta(119, $result->used_amount, 0.001);
        $this->assertEqualsWithDelta(81, $result->remaining_balance, 0.001);
    }

    public function test_cheque_status_uses_ttc_and_preserves_invoice_exclusions(): void
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cheque_number');
            $table->double('amount');
            $table->softDeletes();
        });
        DB::table('cheques')->insert(['id' => 7, 'cheque_number' => 'C-7', 'amount' => 500]);
        foreach (['certify_invoices', 'sub_certify_invoices'] as $table) {
            DB::table($table)->insert([
                'id' => 1, 'amount' => 100, 'ht_amount' => 100, 'tva_rate' => 19,
                'tva_amount' => 0, 'cheque_id' => 7, 'payment_type' => 2,
            ]);
        }
        $controller = new ChequeController();
        $status = $controller->getStatusExcluding(Request::create('/status'), 7)->getData(true);
        $this->assertEqualsWithDelta(238, $status['used_amount'], 0.001);
        $this->assertEqualsWithDelta(262, $status['remaining_balance'], 0.001);

        $status = $controller->getStatusExcluding(Request::create('/status?type=certify'), 7, 1)->getData(true);
        $this->assertEqualsWithDelta(119, $status['used_amount'], 0.001);
    }
}
