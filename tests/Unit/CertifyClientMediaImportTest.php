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

    public function test_extract_zip_safely_normalizes_windows_style_entry_paths(): void
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'test-win-zip-');
        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE));
        $zip->addFromString('media-files\\10\\file.pdf', '%PDF-1.4 sample content');
        $zip->close();

        $destDir = storage_path('app/test-extract-'.uniqid());
        \Illuminate\Support\Facades\File::makeDirectory($destDir, 0755, true);

        try {
            $zip = new \ZipArchive();
            $this->assertTrue($zip->open($zipPath));

            $method = new ReflectionMethod(CertifyInvoiceController::class, 'extractZipSafely');
            $method->setAccessible(true);
            $controller = new CertifyInvoiceController();
            $result = $method->invoke($controller, $zip, $destDir);
            $zip->close();

            $this->assertTrue($result);
            $expectedFilePath = $destDir.DIRECTORY_SEPARATOR.'media-files'.DIRECTORY_SEPARATOR.'10'.DIRECTORY_SEPARATOR.'file.pdf';
            $this->assertFileExists($expectedFilePath);
            $this->assertSame('%PDF-1.4 sample content', file_get_contents($expectedFilePath));

            $pathResolver = new ReflectionMethod(CertifyInvoiceController::class, 'bundleMediaPath');
            $pathResolver->setAccessible(true);

            // Forward slash path reference (like from media.csv)
            $resolvedForward = $pathResolver->invoke($controller, $destDir, 'media-files/10/file.pdf');
            $this->assertNotNull($resolvedForward);
            $this->assertSame(realpath($expectedFilePath), realpath($resolvedForward));

            // Windows-style path reference
            $resolvedBackslash = $pathResolver->invoke($controller, $destDir, 'media-files\\10\\file.pdf');
            $this->assertNotNull($resolvedBackslash);
            $this->assertSame(realpath($expectedFilePath), realpath($resolvedBackslash));
        } finally {
            \Illuminate\Support\Facades\File::deleteDirectory($destDir);
            @unlink($zipPath);
        }
    }

    public function test_extract_zip_safely_rejects_path_traversal(): void
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'test-traversal-');
        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE));
        $zip->addFromString('..\\evil.txt', 'malicious content');
        $zip->close();

        $destDir = storage_path('app/test-extract-'.uniqid());
        \Illuminate\Support\Facades\File::makeDirectory($destDir, 0755, true);

        try {
            $zip = new \ZipArchive();
            $this->assertTrue($zip->open($zipPath));

            $method = new ReflectionMethod(CertifyInvoiceController::class, 'extractZipSafely');
            $method->setAccessible(true);
            $controller = new CertifyInvoiceController();
            $result = $method->invoke($controller, $zip, $destDir);
            $zip->close();

            $this->assertFalse($result);
        } finally {
            \Illuminate\Support\Facades\File::deleteDirectory($destDir);
            @unlink($zipPath);
        }
    }
}
