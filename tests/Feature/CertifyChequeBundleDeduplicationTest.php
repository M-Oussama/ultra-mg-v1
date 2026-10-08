<?php

namespace Tests\Feature;

use App\Http\Controllers\CertifyInvoiceController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use ZipArchive;

class CertifyChequeBundleDeduplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_bundle_reuses_logically_identical_cheque_and_remaps_invoice(): void
    {
        if (! class_exists(ZipArchive::class)) {
            $this->markTestSkipped('PHP ZIP extension is unavailable.');
        }

        $cityId = DB::table('cities')->insertGetId([
            'code' => 16,
            'name' => 'Algiers',
            'country' => 'Algeria',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('certify_clients')->insert([
            'id' => 150,
            'name' => 'BOUZAHZAH',
            'surname' => 'FARES',
            'full_name' => 'BOUZAHZAH FARES',
            'city_id' => $cityId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('cheques')->insert([
            'id' => 21,
            'cheque_date' => '2026-05-04',
            'cheque_number' => '3756854',
            'client_id' => 150,
            'amount' => 7500000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $zipPath = tempnam(sys_get_temp_dir(), 'certify-cheque-').'.zip';
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        $zip->addFromString('clients.csv', "id,name,surname,city_id\n150,BOUZAHZAH,FARES,{$cityId}\n");
        $zip->addFromString(
            'cheques.csv',
            "id,date,number,amount,bank,pdf_url,pdf_name,client_id,company_id,notes\n"
            ."22,2026-05-04,3756854,7500000,,,,150,,\n",
        );
        $zip->addFromString(
            'commands.csv',
            "id,fac_id,date,client_id,amount_ttc,amount,payment_type,cheque_id,tva\n"
            ."501,9001,2026-05-05,150,7499975,7499975,2,22,0\n",
        );
        $zip->close();

        try {
            $request = Request::create('/api/certifyInvoices/import-bundle', 'POST');
            $request->files->set(
                'bundle',
                new UploadedFile($zipPath, 'certify.zip', 'application/zip', null, true),
            );
            $response = app(CertifyInvoiceController::class)->importBundle($request);

            $this->assertSame(200, $response->status());
            $this->assertSame(1, DB::table('cheques')->whereNull('deleted_at')->count());
            $this->assertDatabaseMissing('cheques', ['id' => 22]);
            $this->assertDatabaseHas('certify_invoices', [
                'id' => 501,
                'cheque_id' => 21,
            ]);
        } finally {
            @unlink($zipPath);
        }
    }
}
