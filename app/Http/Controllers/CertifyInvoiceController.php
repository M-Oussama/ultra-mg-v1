<?php

namespace App\Http\Controllers;

use App\Http\Helpers\NumberToLetter;
use App\Models\CertifyInvoiceProducts;
use App\Models\CertifyInvoices;
use App\Models\CertifyClient;
use App\Models\Payment;
use App\Models\CertifyProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use ZipArchive;

class CertifyInvoiceController extends Controller
{

    /**
     * Import client and cheque media without importing the reference tables.
     *
     * clients.csv and cheques.csv are deliberately read only for validation.
     * The relationship for every attachment comes from owner_table + owner_id
     * in media.csv, and the physical file is attached through Spatie Media
     * Library so the media table and storage path stay consistent.
     */
    public function importMediaBundle(Request $request): JsonResponse
    {
        $file = $request->file('bundle') ?? $request->file('file');
        if (! $file || strtolower($file->getClientOriginalExtension()) !== 'zip') {
            return response()->json(['message' => 'A ZIP media bundle file is required.'], 422);
        }
        if (! class_exists(ZipArchive::class)) {
            return response()->json(['message' => 'The PHP ZIP extension is required.'], 422);
        }

        $directory = storage_path('app/import-media-bundle-'.uniqid('', true));
        File::makeDirectory($directory, 0755, true);

        try {
            $zip = new ZipArchive();
            if ($zip->open($file->getRealPath()) !== true || ! $zip->extractTo($directory)) {
                return response()->json(['message' => 'Unable to extract the ZIP media bundle.'], 422);
            }
            $zip->close();

            $requiredFiles = ['manifest.json', 'clients.csv', 'cheques.csv', 'media.csv'];
            $missingFiles = array_values(array_filter(
                $requiredFiles,
                fn (string $name): bool => ! is_file($directory.'/'.$name),
            ));
            if ($missingFiles !== []) {
                return response()->json([
                    'message' => 'The media ZIP is missing required files.',
                    'missing_files' => $missingFiles,
                ], 422);
            }

            $manifest = json_decode((string) file_get_contents($directory.'/manifest.json'), true);
            if (! is_array($manifest)) {
                return response()->json(['message' => 'manifest.json is invalid JSON.'], 422);
            }
            $relatedMedia = $manifest['related_media'] ?? null;
            if (! is_array($relatedMedia)
                || ($relatedMedia['metadata_file'] ?? null) !== 'media.csv'
                || ($relatedMedia['files_directory'] ?? null) !== 'media-files'
            ) {
                return response()->json([
                    'message' => 'manifest.json does not describe the expected media export structure.',
                ], 422);
            }

            $referenceCounts = [
                'clients' => count($this->bundleCsv($directory, 'clients.csv')),
                'cheques' => count($this->bundleCsv($directory, 'cheques.csv')),
            ];
            $counts = [
                'media_rows' => 0,
                'attached' => 0,
                'already_attached' => 0,
                'not_exported' => 0,
                'missing_files' => 0,
            ];
            $skipped = [];
            $chequeIdMap = [];

            DB::transaction(function () use ($directory, &$counts, &$skipped, &$chequeIdMap) {
                foreach ($this->bundleCsv($directory, 'media.csv') as $row) {
                    $counts['media_rows']++;
                    $mediaId = $this->nullableBundleValue($row['id'] ?? null);
                    $ownerTable = strtolower((string) ($row['owner_table'] ?? ''));
                    $ownerId = $this->nullableBundleValue($row['owner_id'] ?? null);
                    $sourceCollection = strtolower(trim((string) ($row['collection_name'] ?? '')));
                    $relativePath = $this->nullableBundleValue($row['relative_path'] ?? null);
                    $fileExported = strtolower(trim((string) ($row['file_exported'] ?? '')));

                    if (in_array($fileExported, ['', 'no', 'n', 'false', '0'], true)) {
                        $counts['not_exported']++;
                        $skipped[] = [
                            'file' => 'media.csv',
                            'id' => $mediaId,
                            'owner_table' => $ownerTable,
                            'owner_id' => $ownerId,
                            'reason' => 'file_exported=no; the physical file is not in the ZIP',
                        ];
                        continue;
                    }

                    $collection = $this->bundleMediaCollection($ownerTable, $sourceCollection);
                    $model = match ($ownerTable) {
                        'clients' => $ownerId === null ? null : CertifyClient::find($ownerId),
                        'cheques' => $ownerId === null ? null : \App\Models\Cheque::find($ownerId),
                        default => null,
                    };

                    if ($mediaId === null || $ownerId === null || $collection === null || $model === null) {
                        $skipped[] = [
                            'file' => 'media.csv',
                            'id' => $mediaId,
                            'owner_table' => $ownerTable,
                            'owner_id' => $ownerId,
                            'reason' => $model === null
                                ? 'No existing '.$ownerTable.' record with this database id'
                                : 'Unsupported owner_table or collection_name',
                        ];
                        continue;
                    }

                    $path = $relativePath === null
                        ? null
                        : $this->bundleMediaPath($directory, $relativePath);
                    if ($path === null || ! is_file($path)) {
                        $counts['missing_files']++;
                        $skipped[] = [
                            'file' => 'media.csv',
                            'id' => $mediaId,
                            'owner_table' => $ownerTable,
                            'owner_id' => $ownerId,
                            'reason' => 'Referenced physical file is missing from the ZIP',
                        ];
                        continue;
                    }

                    $sourceHash = strtolower((string) ($this->nullableBundleValue($row['sha256'] ?? null) ?? ''));
                    if ($sourceHash !== '' && hash_file('sha256', $path) !== $sourceHash) {
                        $counts['missing_files']++;
                        $skipped[] = [
                            'file' => 'media.csv',
                            'id' => $mediaId,
                            'owner_table' => $ownerTable,
                            'owner_id' => $ownerId,
                            'reason' => 'Physical file checksum does not match sha256',
                        ];
                        continue;
                    }

                    $existingQuery = $model->media()->where('collection_name', $collection);
                    $alreadyAttached = false;
                    if ($sourceHash !== '') {
                        $alreadyAttached = (clone $existingQuery)
                            ->where('custom_properties->source_media_sha256', $sourceHash)
                            ->exists();
                    }
                    if (! $alreadyAttached && $mediaId !== null) {
                        $alreadyAttached = (clone $existingQuery)
                            ->where('custom_properties->source_media_id', (string) $mediaId)
                            ->exists();
                    }
                    if (! $alreadyAttached) {
                        $alreadyAttached = (clone $existingQuery)
                            ->where('file_name', (string) ($row['file_name'] ?? basename($path)))
                            ->where('size', filesize($path))
                            ->exists();
                    }
                    if ($alreadyAttached) {
                        $counts['already_attached']++;
                        $this->activateCertifyClientMediaFlag($model, $collection);
                        continue;
                    }

                    $customProperties = json_decode((string) ($row['custom_properties'] ?? ''), true);
                    if (! is_array($customProperties)) {
                        $customProperties = [];
                    }
                    $customProperties['source_media_id'] = (string) $mediaId;
                    $customProperties['source_media_sha256'] = $sourceHash !== ''
                        ? $sourceHash
                        : hash_file('sha256', $path);
                    $customProperties['source_owner_table'] = $ownerTable;
                    $customProperties['source_owner_id'] = (string) $ownerId;
                    $customProperties['source_collection_name'] = $sourceCollection;

                    $model->addMedia($path)
                        ->usingName((string) ($row['name'] ?? pathinfo($path, PATHINFO_FILENAME)))
                        ->usingFileName((string) ($row['file_name'] ?? basename($path)))
                        ->withCustomProperties($customProperties)
                        ->toMediaCollection($collection);
                    $this->activateCertifyClientMediaFlag($model, $collection);
                    $counts['attached']++;
                }
            });

            return response()->json([
                'message' => 'Client and cheque media imported successfully.',
                'reference_counts' => $referenceCounts,
                'counts' => $counts,
                'skipped' => $skipped,
            ]);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'Media bundle import failed: '.$exception->getMessage()], 422);
        } finally {
            File::deleteDirectory($directory);
        }
    }

    private function bundleMediaCollection(string $ownerTable, string $sourceCollection): ?string
    {
        return match ($ownerTable.':'.strtolower(trim($sourceCollection))) {
            'clients:documents', 'clients:pdf_file' => 'pdf_file',
            'clients:cnrc', 'clients:cnrc_file' => 'cnrc_file',
            'clients:nif', 'clients:nif_file' => 'nif_file',
            'cheques:cheques' => 'cheques',
            default => null,
        };
    }

    private function activateCertifyClientMediaFlag($model, string $collection): void
    {
        if (! $model instanceof CertifyClient) {
            return;
        }

        $column = match ($collection) {
            'cnrc_file' => 'is_cnrc_active',
            'nif_file' => 'is_nif_active',
            default => null,
        };
        if ($column === null || $model->{$column}) {
            return;
        }

        $model->forceFill([$column => true])->save();
    }

    /**
     * Import the command-related ZIP exported by Ultra.
     *
     * The source and destination applications use different table names, so
     * the bundle is mapped into the certify_* tables while preserving IDs.
     */
    public function importBundle(Request $request): JsonResponse
    {
        $file = $request->file('bundle') ?? $request->file('file');
        if (! $file || strtolower($file->getClientOriginalExtension()) !== 'zip') {
            return response()->json(['message' => 'A ZIP bundle file is required.'], 422);
        }
        if (! class_exists(ZipArchive::class)) {
            return response()->json(['message' => 'The PHP ZIP extension is required.'], 422);
        }

        $directory = storage_path('app/import-bundle-'.uniqid('', true));
        File::makeDirectory($directory, 0755, true);

        try {
            $zip = new ZipArchive();
            if ($zip->open($file->getRealPath()) !== true || ! $zip->extractTo($directory)) {
                return response()->json(['message' => 'Unable to extract the ZIP bundle.'], 422);
            }
            $zip->close();

            $counts = [
                'clients' => 0,
                'products' => 0,
                'cheques' => 0,
                'media' => 0,
                'invoices' => 0,
                'invoice_products' => 0,
            ];
            $skipped = [];

            DB::transaction(function () use ($directory, &$counts, &$skipped) {
                foreach ($this->bundleCsv($directory, 'clients.csv') as $row) {
                    $id = $this->nullableBundleValue($row['id'] ?? null);
                    if ($id === null) {
                        $skipped[] = ['file' => 'clients.csv', 'reason' => 'Missing id'];
                        continue;
                    }
                    $cityId = $this->destinationCityId(
                        $row['city_id'] ?? null,
                        $row['city_name'] ?? $row['city'] ?? $row['wilaya'] ?? null,
                    );
                    $this->upsertBundle('certify_clients', $id, [
                        'name' => $row['name'] ?? '',
                        'surname' => $this->nullableBundleValue($row['surname'] ?? null),
                        'full_name' => trim(($row['name'] ?? '').' '.($row['surname'] ?? '')),
                        'address' => $this->nullableBundleValue($row['address'] ?? null),
                        'email' => $this->nullableBundleValue($row['email'] ?? null),
                        'phone' => $this->nullableBundleValue($row['phone'] ?? null),
                        // bundleCsv normalizes headers to lowercase.
                        'NRC' => $this->nullableBundleValue($row['nrc'] ?? null),
                        'NIF' => $this->nullableBundleValue($row['nif'] ?? null),
                        'NART' => $this->nullableBundleValue($row['nart'] ?? null),
                        'NIS' => $this->nullableBundleValue($row['nis'] ?? null),
                        'city_id' => $cityId,
                    ]);
                    $counts['clients']++;
                }

                foreach ($this->bundleCsv($directory, 'products.csv') as $row) {
                    $id = $this->nullableBundleValue($row['id'] ?? null);
                    if ($id === null) {
                        $skipped[] = ['file' => 'products.csv', 'reason' => 'Missing id'];
                        continue;
                    }
                    $this->upsertBundle('certify_products', $id, [
                        'name' => $row['name'] ?? '',
                        'product_code' => $this->nullableBundleValue($row['barcode'] ?? null),
                        'price' => (float) ($row['price'] ?? 0),
                        'tax_rate' => 0,
                        'stockable' => true,
                        'weight' => 0,
                        'min_stock_level' => 0,
                    ]);
                    $counts['products']++;
                }

                foreach ($this->bundleCsv($directory, 'cheques.csv') as $row) {
                    $id = $this->nullableBundleValue($row['id'] ?? null);
                    $clientId = $this->existingId('certify_clients', $row['client_id'] ?? null);
                    $reasons = [];
                    if ($id === null) {
                        $reasons[] = 'Missing id';
                    }
                    if ($clientId === null) {
                        $reasons[] = 'Missing client or client was not imported';
                    }
                    if ($reasons !== []) {
                        $skipped[] = [
                            'file' => 'cheques.csv',
                            'id' => $id,
                            'reason' => implode('; ', $reasons),
                        ];
                        continue;
                    }
                    $values = [
                        'cheque_date' => $this->nullableBundleValue($row['date'] ?? $row['cheque_date'] ?? null)
                            ?? now()->toDateString(),
                        'cheque_number' => $this->nullableBundleValue($row['number'] ?? $row['cheque_number'] ?? null) ?? '',
                        'client_id' => $clientId,
                        'amount' => $this->nullableBundleValue($row['amount'] ?? null) ?? '0',
                        'banque' => $this->nullableBundleValue($row['bank'] ?? $row['banque'] ?? null),
                        'file_path' => $this->nullableBundleValue($row['pdf_url'] ?? null),
                        'pdf_name' => $this->nullableBundleValue($row['pdf_name'] ?? null),
                        'company_id' => $this->nullableBundleValue($row['company_id'] ?? null),
                        'notes' => $this->nullableBundleValue($row['notes'] ?? null),
                    ];
                    $destinationId = $this->bundleChequeDestinationId((int) $id, $values);
                    $chequeIdMap[(string) $id] = $destinationId;
                    $this->upsertBundle('cheques', $destinationId, $values);
                    $counts['cheques']++;
                }

                $this->importBundleMedia($directory, $counts, $skipped, $chequeIdMap);

                foreach ($this->bundleCsv($directory, 'commands.csv') as $row) {
                    $id = $this->nullableBundleValue($row['id'] ?? null);
                    if ($id === null) {
                        $skipped[] = ['file' => 'commands.csv', 'reason' => 'Missing id'];
                        continue;
                    }
                    $this->upsertBundle('certify_invoices', $id, [
                        'fac_id' => (int) ($row['fac_id'] ?? 0),
                        'date' => $row['date'] ?? now()->toDateString(),
                        'client_id' => $this->existingId('certify_clients', $row['client_id'] ?? null),
                        'amount' => (float) (($row['amount_ttc'] ?? null) ?: ($row['amount'] ?? 0)),
                        'payment_type' => $this->nullableBundleValue($row['payment_type'] ?? null),
                        'cheque_id' => $this->mappedBundleChequeId(
                            $row['cheque_id'] ?? null,
                            $chequeIdMap,
                        ),
                        'tva_amount' => (float) ($row['tva'] ?? 0),
                        'ht_amount' => (float) ($row['amount'] ?? 0),
                    ]);
                    $counts['invoices']++;
                }

                foreach ($this->bundleCsv($directory, 'command_products.csv') as $row) {
                    $id = $this->nullableBundleValue($row['id'] ?? null);
                    $invoiceId = $this->existingId('certify_invoices', $row['command_id'] ?? null);
                    $productId = $this->existingId('certify_products', $row['product_id'] ?? null);
                    if ($id === null || $invoiceId === null || $productId === null) {
                        $skipped[] = ['file' => 'command_products.csv', 'id' => $id, 'reason' => 'Missing invoice or product'];
                        continue;
                    }
                    $this->upsertBundle('certify_invoice_products', $id, [
                        'certify_invoice_id' => $invoiceId,
                        'product_id' => $productId,
                        'price' => (int) ($row['price'] ?? 0),
                        'quantity' => (int) ($row['quantity'] ?? 0),
                        'total' => (int) ($row['amount'] ?? 0),
                    ]);
                    $counts['invoice_products']++;
                }
            });

            $unsupported = [];
            foreach (['avoir_invoices.csv', 'avoir_invoice_items.csv', 'payment_types.csv'] as $name) {
                if (is_file($directory.'/'.$name)) {
                    $unsupported[] = $name;
                }
            }

            return response()->json([
                'message' => 'Command-related bundle imported successfully.',
                'counts' => $counts,
                'skipped' => $skipped,
                'unsupported_files' => $unsupported,
            ]);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'Bundle import failed: '.$exception->getMessage()], 422);
        } finally {
            File::deleteDirectory($directory);
        }
    }

    private function bundleCsv(string $directory, string $filename): array
    {
        $path = $directory.'/'.$filename;
        if (! is_file($path)) {
            return [];
        }
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return [];
        }
        $header[0] = preg_replace('/^\\xEF\\xBB\\xBF/', '', (string) $header[0]);
        $rows = [];
        while (($values = fgetcsv($handle)) !== false) {
            if (count(array_filter($values, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }
            $row = [];
            foreach ($header as $index => $column) {
                $value = $values[$index] ?? null;
                $row[strtolower(trim($column))] = $value === '\\N' ? null : $value;
            }
            $rows[] = $row;
        }
        fclose($handle);
        return $rows;
    }

    private function upsertBundle(string $table, $id, array $values): void
    {
        $values['id'] = $id;
        $values['created_at'] = $values['created_at'] ?? now();
        $values['updated_at'] = $values['updated_at'] ?? now();
        $columns = array_flip(Schema::getColumnListing($table));
        // Cheque deletion is a Laravel soft delete. Re-importing the same
        // source row must make it visible again instead of only updating the
        // hidden row that still has deleted_at set.
        if (isset($columns['deleted_at'])) {
            $values['deleted_at'] = null;
        }
        $values = array_intersect_key($values, $columns);
        DB::table($table)->updateOrInsert(['id' => $id], $values);
    }

    private function bundleChequeDestinationId(int $sourceId, array $values): int
    {
        $activeSource = DB::table('cheques')
            ->where('id', $sourceId)
            ->whereNull('deleted_at')
            ->value('id');
        if ($activeSource !== null) {
            return (int) $activeSource;
        }

        $matchingId = DB::table('cheques')
            ->whereNull('deleted_at')
            ->where('cheque_number', trim((string) ($values['cheque_number'] ?? '')))
            ->where('client_id', (int) ($values['client_id'] ?? 0))
            ->whereDate('cheque_date', (string) ($values['cheque_date'] ?? ''))
            ->where('amount', (float) ($values['amount'] ?? 0))
            ->orderBy('id')
            ->value('id');

        return $matchingId === null ? $sourceId : (int) $matchingId;
    }

    private function mappedBundleChequeId($sourceId, array $chequeIdMap): ?int
    {
        $sourceId = $this->nullableBundleValue($sourceId);
        if ($sourceId === null) {
            return null;
        }

        return $chequeIdMap[(string) $sourceId]
            ?? $this->existingId('cheques', $sourceId);
    }

    private function existingId(string $table, $id): ?int
    {
        $id = $this->nullableBundleValue($id);
        return $id !== null && DB::table($table)->where('id', $id)->exists() ? (int) $id : null;
    }

    private function destinationCityId($sourceCityId, $sourceCityName = null): ?int
    {
        if (! Schema::hasTable('cities')) {
            return null;
        }
        $sourceCityId = $this->nullableBundleValue($sourceCityId);
        if ($sourceCityId !== null && DB::table('cities')->where('id', $sourceCityId)->exists()) {
            return (int) $sourceCityId;
        }

        $sourceCityName = $this->nullableBundleValue($sourceCityName);
        if ($sourceCityName !== null) {
            $normalize = static function (string $value): string {
                $value = trim(mb_strtolower($value));
                $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
                return preg_replace('/[^a-z0-9]+/', '', $ascii !== false ? $ascii : $value) ?? '';
            };
            $normalizedSource = $normalize($sourceCityName);
            foreach (DB::table('cities')->select(['id', 'name'])->get() as $city) {
                if ($normalize((string) $city->name) === $normalizedSource) {
                    return (int) $city->id;
                }
            }
        }

        // Never silently turn an unknown source city into the first city
        // (which is Adrar in this database). Preserve an unknown value as
        // null so the import report/data can be corrected explicitly.
        return null;
    }

    private function nullableBundleValue($value)
    {
        return $value === null || trim((string) $value) === '' || $value === '\\N' ? null : trim((string) $value);
    }

    private function bundleMediaPath(string $directory, string $relativePath): ?string
    {
        $root = realpath($directory);
        if ($root === false || str_contains($relativePath, "\0")) {
            return null;
        }

        $candidate = realpath($directory.DIRECTORY_SEPARATOR.str_replace(
            ['/', '\\'],
            DIRECTORY_SEPARATOR,
            ltrim($relativePath, '/\\'),
        ));
        if ($candidate === false) {
            return null;
        }

        $rootPrefix = rtrim($root, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        return str_starts_with(strtolower($candidate), strtolower($rootPrefix)) ? $candidate : null;
    }

    private function importBundleMedia(
        string $directory,
        array &$counts,
        array &$skipped,
        array $chequeIdMap = [],
    ): void
    {
        foreach ($this->bundleCsv($directory, 'media.csv') as $row) {
            $mediaId = $this->nullableBundleValue($row['id'] ?? null);
            $modelId = $this->nullableBundleValue($row['model_id'] ?? null);
            $sourceType = (string) ($row['model_type'] ?? '');
            $relativePath = $this->nullableBundleValue($row['relative_path'] ?? null);
            $isCheque = $sourceType === \App\Models\Cheque::class || $sourceType === 'App\\Models\\Cheque';
            if ($isCheque && $modelId !== null) {
                $modelId = $chequeIdMap[(string) $modelId] ?? $modelId;
            }

            $model = match ($sourceType) {
                \App\Models\Client::class, 'App\\Models\\Client' => $modelId === null ? null : CertifyClient::find($modelId),
                \App\Models\Cheque::class, 'App\\Models\\Cheque' => $modelId === null ? null : \App\Models\Cheque::find($modelId),
                default => null,
            };

            $ownerTable = match ($sourceType) {
                \App\Models\Client::class, 'App\\Models\\Client' => 'clients',
                \App\Models\Cheque::class, 'App\\Models\\Cheque' => 'cheques',
                default => null,
            };
            $collection = $ownerTable === null ? null : $this->bundleMediaCollection(
                $ownerTable,
                (string) ($row['collection_name'] ?? ''),
            );

            $path = $relativePath === null ? null : $directory.'/'.$relativePath;
            if ($mediaId === null || $model === null || $collection === null || $path === null || ! is_file($path)) {
                $skipped[] = [
                    'file' => 'media.csv',
                    'id' => $mediaId,
                    'reason' => 'Missing target record, unsupported collection, or media file',
                ];
                continue;
            }

            if ($model->media()
                ->where('collection_name', $collection)
                ->where('custom_properties->source_media_id', (string) $mediaId)
                ->exists()) {
                continue;
            }

            $customProperties = json_decode((string) ($row['custom_properties'] ?? ''), true);
            if (! is_array($customProperties)) {
                $customProperties = [];
            }
            $customProperties['source_media_id'] = (string) $mediaId;

            $model->addMedia($path)
                ->usingName((string) ($row['name'] ?? pathinfo($path, PATHINFO_FILENAME)))
                ->usingFileName((string) ($row['file_name'] ?? basename($path)))
                ->withCustomProperties($customProperties)
                ->toMediaCollection($collection);
            $counts['media']++;
        }
    }

    /**
     * get List Of All Invoices
     *
     * @param Request $request
     * @return JsonResponse
     */

    #[OA\Get(
        path: "/api/certifyInvoices/list",
        operationId: "getInvoices",
        description: "Returns the list of invoices",
        tags: ["certifyInvoice"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/ICertifyInvoice",
        type: 'object'
    )])]
    public function getInvoices(Request $request): JsonResponse
    {
        $searchValue = trim((string) $request->input('searchValue', ''));
        $clientId = $request->input('client_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $modifiedOnly = filter_var($request->input('modified_only', false), FILTER_VALIDATE_BOOLEAN);
        $compact = filter_var($request->input('compact', false), FILTER_VALIDATE_BOOLEAN);
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
        $invoices = CertifyInvoices::orderBy('date', 'desc');

        if ($compact) {
            $invoices->without(['client', 'certifyInvoiceProducts', 'cheque', 'user'])
                ->with(['client' => fn ($query) => $query->without('city')]);
        }

        if ($searchValue !== '') {
            $invoices->where(function ($query) use ($searchValue) {
                $query->where('fac_id', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('payment_type', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('amount', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('custom_cheque_number', 'LIKE', '%' . $searchValue . '%')
                    ->orWhereHas('client', function ($clientQuery) use ($searchValue) {
                        $clientQuery->where('name', 'LIKE', '%' . $searchValue . '%')
                            ->orWhere('surname', 'LIKE', '%' . $searchValue . '%')
                            ->orWhereRaw(
                                "CONCAT(COALESCE(name, ''), ' ', COALESCE(surname, '')) LIKE ?",
                                ['%' . $searchValue . '%']
                            );
                    });
            });
        }

        if (!empty($clientId) && $clientId !== 'all') {
            $invoices->where('client_id', $clientId);
        }

        if (!empty($startDate)) {
            $invoices->whereDate('date', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $invoices->whereDate('date', '<=', $endDate);
        }

        if ($modifiedOnly) {
            $invoices->whereColumn('updated_at', '>', 'created_at');
        }

        $this->applyInvoiceVisibility($invoices);

        $invoices = $invoices->paginate($perPage, ['*'], 'page', $currentPage);

        if ($compact) {
            $invoices->getCollection()->each(function ($invoice) {
                $client = $invoice->getRelation('client');
                if ($client) {
                    $client->setAppends([]);
                }
            });
        }

        $totalInvoices = $invoices->total(); // Total number of invoices matching the query
        $totalPage = ceil($totalInvoices / $perPage); // Calculate total pages

        return response()->json(["invoices" => $invoices, "totalPage" => $totalPage, "totalInvoices"=>$totalInvoices]);
    }

    /**
     * Return aggregate invoice statistics without pagination.
     *
     * The list endpoint intentionally returns one page. Dashboard counters
     * must use this endpoint so they are calculated from the complete set the
     * authenticated user is allowed to see.
     */
    public function getSummary(Request $request): JsonResponse
    {
        $invoices = CertifyInvoices::query();
        $this->applyInvoiceVisibility($invoices);
        $ttcExpression = CertifyInvoices::amountTtcExpression();

        $summary = (clone $invoices)
            ->selectRaw("COALESCE(SUM($ttcExpression), 0) as total_amount")
            ->selectRaw('COUNT(*) as invoice_count')
            ->selectRaw('COUNT(DISTINCT client_id) as client_count')
            ->toBase()
            ->first();

        $invoiceCount = (int) ($summary?->invoice_count ?? 0);
        $paymentTypeBreakdown = (clone $invoices)
            ->select('payment_type')
            ->selectRaw('COUNT(*) as invoice_count')
            ->selectRaw("COALESCE(SUM($ttcExpression), 0) as total_amount")
            ->groupBy('payment_type')
            ->orderBy('payment_type')
            ->toBase()
            ->get()
            ->map(function ($row) {
                $rawType = $row->payment_type;
                $type = $rawType === null || trim((string) $rawType) === ''
                    ? 'unknown'
                    : (string) $rawType;

                return [
                    'payment_type' => $type,
                    'label' => match ($type) {
                        '1' => 'Espece',
                        '2' => 'Cheque',
                        '3' => 'Virement Bancaire',
                        '4' => 'Versement Espece',
                        default => $type === 'unknown' ? 'Unknown' : $type,
                    },
                    'invoice_count' => (int) $row->invoice_count,
                    'total_amount' => (float) $row->total_amount,
                    'total_amount_ttc' => (float) $row->total_amount,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'total_amount' => (float) ($summary?->total_amount ?? 0),
            'total_amount_ttc' => (float) ($summary?->total_amount ?? 0),
            'invoice_count' => $invoiceCount,
            'paid_invoices' => 0,
            'draft_invoices' => $invoiceCount,
            'client_count' => (int) ($summary?->client_count ?? 0),
            'payment_type_breakdown' => $paymentTypeBreakdown,
        ]);
    }

    private function applyInvoiceVisibility($query)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user || $user->isGlobalAdmin()) {
            return $query;
        }

        if ($user->isDepartmentManager()) {
            $deptIds = $user->departments->pluck('id')->toArray();
            return $query->whereHas('client', function ($clientQuery) use ($deptIds) {
                $clientQuery->whereIn('department_id', $deptIds);
            });
        }

        return $query->where('user_id', $user->id);
    }

    public function getInvoice($id): JsonResponse
    {
        $invoice = CertifyInvoices::find($id);

        $invoice->amount_letter = $this->convertAmoutToLetter(($invoice->amount*1.19));

        $clients = CertifyClient::all();

        $products = CertifyProduct::getAllProductsFormatted();

        return response()->json(["invoice" => $invoice, "clients"=>$clients, "products"=>$products, "unSelectedProducts"=>$products]);
    }

    /**
     * Create a new Certify Invoice
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/certifyInvoices/store",
        operationId: "store",
        description: "Create a new Certify Invoice",
        tags: ["certifyInvoice"],
    )]
    #[OA\RequestBody(
        required: true,
        content: [
            new OA\JsonContent(
                required: ["client_id", "amount", "date"],
                properties: [
                    new OA\Property(property: "client_id", type: "integer", example: "1"),
                    new OA\Property(property: "amount", type: "number", format: "float", example: "123"),
                    new OA\Property(property: "date",  type: "string", format: "date-time", example: "2023-08-13"),
                    new OA\Property(property: "payment_type", type: "integer", example: "1"),
                    new OA\Property(property: "products", type: "array",
                        items: new OA\Items(
                            properties: [  // Example object of type object
                            new OA\Property(property: "product_id", type: "integer", example: "1"),
                            new OA\Property(property: "quantity", type: "number", example: "2"),
                            new OA\Property(property: "price", type: "number", example: "2"),
                            new OA\Property(property: "total", type: "number", example: "2"),
                            ],

                        )),

                ]
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: [
            new OA\JsonContent(
                ref: "#/components/schemas/ICertifyInvoice",
                type: 'object'
            )
        ]
    )]

    public function store(Request $request): JsonResponse
    {

        $invoiceData = $request->input('invoiceData');
        $client = $invoiceData['client'];

        $fac_id = $invoiceData['fac_id'];

         $invoice = CertifyInvoices::create([
            'fac_id' => $fac_id,
            'date' => $invoiceData['date'],
            'client_id' => $client['id'],
            'amount' => $invoiceData['total'] ?? $invoiceData['amount'] ?? 0,
            'payment_type' => $invoiceData['payment_type'],
            'tva_rate' => $invoiceData['tva_rate'] ?? null,
            'tva_amount' => $invoiceData['tva_amount'] ?? null,
            'ht_amount' => $invoiceData['ht_amount'] ?? null,
            'timbre_rate' => $invoiceData['timbre_rate'] ?? null,
            'timbre_amount' => $invoiceData['timbre_amount'] ?? null,
            'cheque_number' => $invoiceData['cheque_number'] ?? null,
            'cheque_id' => $invoiceData['cheque_id'] ?? null,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        $products = $invoiceData['certify_invoice_products'];

        foreach ($products as $product) {

            CertifyInvoiceProducts::create([
               'product_id' => $product['product']['id'],
               'price' => $product['price'],
               'quantity' => $product['quantity'],
               'total' => $product['quantity'] * $product['price'],
               'certify_invoice_id' => $invoice->id,
            ]);
        }



        return response()->json(['message' => 'Product created successfully', "id"=>$invoice->id]);

    }

    /**
     * get List Of Clients and Products
     *
     * @return JsonResponse
     */

    #[OA\Get(
        path: "/api/certifyInvoices/getInvoiceData",
        operationId: "getInvoiceData",
        description: "Returns the list of clients and products",
        tags: ["certifyInvoice"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        type: 'object'
    )])]
    public function getInvoiceData(): JsonResponse {
        $clients = CertifyClient::all();
        $products = CertifyProduct::getAllProductsFormatted();
        $date = date('Y-m-d');
        $id = $this->getLastIDPerYear(date('Y-m-d'));

        return response()->json(["clients" => $clients, "products" => $products, "id"=>$id, "date"=>$date]);
    }

    public function getLastID(Request $request){
        $date = $request->input('date');

        $last_id = $this->getLastIDPerYear($date);

        return response()->json(["id" => $last_id]);
    }

    public function getLastIDPerYear($date){

        $year = date("Y",strtotime($date));

        $max_fac_id = CertifyInvoices::whereYear('date',$year)->max('fac_id');

        return ($max_fac_id ?? 0) + 1;
    }



    public function update(Request $request): JsonResponse
    {
        $invoiceData = $request->input('invoiceData');

        $client = $invoiceData['client'];

       $fac_id = $invoiceData['fac_id'];

       $invoice = CertifyInvoices::find($invoiceData['id']);

         $invoice->update([
             'fac_id' => $fac_id,
             'date' => $invoiceData['date'],
             'client_id' => $client['id'],
             'amount' => $invoiceData['total'] ?? $invoiceData['amount'] ?? 0,
             'payment_type' => $invoiceData['payment_type'],
             'tva_rate' => $invoiceData['tva_rate'] ?? null,
             'tva_amount' => $invoiceData['tva_amount'] ?? null,
             'ht_amount' => $invoiceData['ht_amount'] ?? null,
             'timbre_rate' => $invoiceData['timbre_rate'] ?? null,
             'timbre_amount' => $invoiceData['timbre_amount'] ?? null,
             'cheque_number' => $invoiceData['cheque_number'] ?? null,
             'cheque_id' => $invoiceData['cheque_id'] ?? null,
         ]);


        foreach ($invoice->certifyInvoiceProducts as $product) {
            $product->delete();
        }

        $products = $invoiceData['certify_invoice_products'];

         foreach ($products as $product) {

             CertifyInvoiceProducts::create([
                 'product_id' => $product['product']['id'],
                 'price' => $product['price'],
                 'quantity' => $product['quantity'],
                 'total' => $product['price'] * $product['quantity'],
                 'certify_invoice_id' => $invoiceData['id'],
             ]);
         }

        return response()->json(["message"=>"Invoice Updated Successfully"]);

    }

    /**
     * Delete a certify invoice
     *
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Delete(
        path: "/api/certifyInvoices/delete/{id}",
        operationId: "deleteCertifyInvoice",
        description: "Delete a certify invoice",
        tags: ["certifyInvoice"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: "Successfully deleted")]
    public function delete(int $id): JsonResponse
    {
        $invoice = CertifyInvoices::findOrFail($id);
        
        // Delete associated products
        $invoice->certifyInvoiceProducts()->delete();
        
        $invoice->delete();
        
        return response()->json(["message" => "Certify Invoice deleted successfully"]);
    }

    /**
     * Import certify invoices from CSV using fiscal mapping rules.
     *
     * Required headers:
     * id, fac_id, date, client_id, amount, custom_cheque_number, cheque_id, payment_type
     */
    public function importCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);

        foreach (['id', 'fac_id', 'date', 'client_id', 'amount', 'custom_cheque_number', 'cheque_id', 'payment_type'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $baseAmount = isset($rowData['amount']) ? (float) $rowData['amount'] : 0;
            $paymentType = isset($rowData['payment_type']) ? (int) $rowData['payment_type'] : null;
            $tvaAmount = round($baseAmount * 0.19, 2);
            $rawChequeNumber = trim((string) ($rowData['custom_cheque_number'] ?? ''));
            $chequeNumber = ($rawChequeNumber === '' || strtolower($rawChequeNumber) === 'null')
                ? null
                : $rawChequeNumber;
            $clientId = isset($rowData['client_id']) ? (int) $rowData['client_id'] : null;
            $rawChequeId = trim((string) ($rowData['cheque_id'] ?? ''));
            $chequeId = ($rawChequeId === '' || strtolower($rawChequeId) === 'null')
                ? null
                : (int) $rawChequeId;

            if ($paymentType === 1) {
                $timbreRate = 2;
                $timbreAmount = round($baseAmount * 0.02, 2);
                $finalAmount = round($baseAmount * 1.21, 2);
            } else {
                $timbreRate = null;
                $timbreAmount = null;
                $finalAmount = round($baseAmount * 1.19, 2);
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'fac_id' => isset($rowData['fac_id']) ? (int) $rowData['fac_id'] : null,
                'date' => $rowData['date'] ?? null,
                'client_id' => $clientId,
                'ht_amount' => $baseAmount,
                'tva_amount' => $tvaAmount,
                'cheque_number' => $chequeNumber,
                'cheque_id' => $chequeId,
                'payment_type' => $paymentType,
                'timbre_rate' => $timbreRate,
                'timbre_amount' => $timbreAmount,
                'amount' => $finalAmount,
                'tva_rate' => 19,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'fac_id' => 'required|integer|min:0',
                'date' => 'required|date',
                'client_id' => 'required|integer|exists:certify_clients,id',
                'ht_amount' => 'required|numeric|min:0',
                'tva_amount' => 'required|numeric|min:0',
                'cheque_number' => 'nullable|string|max:255',
                'cheque_id' => 'nullable|integer|min:1',
                'payment_type' => 'required|integer|in:1,2,3,4',
                'timbre_rate' => 'nullable|numeric|min:0',
                'timbre_amount' => 'nullable|numeric|min:0',
                'amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            if (DB::table('certify_invoices')->where('id', $payload['id'])->exists()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => ['Invoice id already exists in database.'],
                ];
                continue;
            }

            DB::table('certify_invoices')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
        ]);
    }

    /**
     * Import certify invoice products from CSV.
     *
     * Required headers:
     * id, command_id, product_id, price, quantity, amount
     */
    public function importProductsCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);

        foreach (['id', 'command_id', 'product_id', 'price', 'quantity', 'amount'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'certify_invoice_id' => isset($rowData['command_id']) ? (int) $rowData['command_id'] : null,
                'product_id' => isset($rowData['product_id']) ? (int) $rowData['product_id'] : null,
                'price' => isset($rowData['price']) ? (float) $rowData['price'] : null,
                'quantity' => isset($rowData['quantity']) ? (int) $rowData['quantity'] : null,
                'total' => isset($rowData['amount']) ? (float) $rowData['amount'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'certify_invoice_id' => 'required|integer|exists:certify_invoices,id',
                'product_id' => 'required|integer|exists:certify_products,id',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'total' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            if (DB::table('certify_invoice_products')->where('id', $payload['id'])->exists()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => ['Certify invoice product id already exists in database.'],
                ];
                continue;
            }

            DB::table('certify_invoice_products')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
        ]);
    }

}
