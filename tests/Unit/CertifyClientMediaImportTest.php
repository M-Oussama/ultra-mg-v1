<?php

namespace Tests\Unit;

use App\Http\Controllers\CertifyInvoiceController;
use App\Models\CertifyClient;
use ReflectionMethod;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileUnacceptableForCollection;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class CertifyClientMediaImportTest extends TestCase
{
    private function validateAttachment(string $collection, string $mimeType): void
    {
        $client = new CertifyClient();
        $client->id = 158;
        $media = new Media();
        $media->forceFill([
            'collection_name' => $collection,
            'file_name' => 'CNRC.pdf',
            'size' => 162216,
            'mime_type' => $mimeType,
        ]);

        $adder = app(FileAdder::class)->setSubject($client);
        $guard = new ReflectionMethod(FileAdder::class, 'guardAgainstDisallowedFileAdditions');
        $guard->setAccessible(true);
        $guard->invoke($adder, $media);
    }

    public function test_cnrc_and_nif_accept_pdf_and_existing_image_formats(): void
    {
        foreach (['cnrc_file', 'nif_file'] as $collection) {
            foreach (['application/pdf', 'image/jpeg', 'image/png'] as $mimeType) {
                $this->validateAttachment($collection, $mimeType);
            }
        }

        $this->addToAssertionCount(6);
    }

    public function test_document_collection_still_rejects_unrelated_files(): void
    {
        $this->expectException(FileUnacceptableForCollection::class);
        $this->validateAttachment('cnrc_file', 'application/zip');
    }

    public function test_import_aliases_resolve_to_collections_registered_by_the_model(): void
    {
        $client = new CertifyClient();
        $client->registerMediaCollections();
        $registeredNames = array_column($client->mediaCollections, 'name');
        $resolver = new ReflectionMethod(CertifyInvoiceController::class, 'bundleMediaCollection');
        $resolver->setAccessible(true);
        $controller = new CertifyInvoiceController();

        foreach ([
            'documents' => 'pdf_file',
            'pdf_file' => 'pdf_file',
            'cnrc' => 'cnrc_file',
            'cnrc_file' => 'cnrc_file',
            'nif' => 'nif_file',
            'nif_file' => 'nif_file',
        ] as $source => $expected) {
            $resolved = $resolver->invoke($controller, 'clients', $source);
            $this->assertSame($expected, $resolved);
            $this->assertContains($resolved, $registeredNames);
        }

        $this->assertNull($resolver->invoke($controller, 'clients', 'cheques'));
        $this->assertNull($resolver->invoke($controller, 'cheques', 'cnrc_file'));
    }
}
