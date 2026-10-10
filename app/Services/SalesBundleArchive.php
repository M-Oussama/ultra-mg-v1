<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use ZipArchive;

/** Adapts the staged ZIP to the existing transactional CSV sales importer. */
class SalesBundleArchive
{
    public const FILES = [
        'cities', 'clients', 'sale_statuses', 'categories', 'drivers', 'products',
        'sales', 'sale_items', 'payments', 'partial_payments',
        'product_returns', 'product_return_lists',
    ];

    public function attachCsvFiles(string $archivePath, string $directory, Request $request): void
    {
        File::ensureDirectoryExists($directory);
        $zip = new ZipArchive;
        if ($zip->open($archivePath) !== true) {
            throw new \RuntimeException('Unable to open the sales ZIP archive.');
        }
        $seen = [];
        try {
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $entry = $zip->statIndex($index);
                $name = strtolower(basename(str_replace('\\', '/', $entry['name'])));
                $key = pathinfo($name, PATHINFO_FILENAME);
                if (! in_array($key, self::FILES, true) || $name !== $key.'.csv') {
                    continue;
                }
                if (isset($seen[$key])) {
                    throw new \RuntimeException('The sales ZIP contains duplicate '.$name.' files.');
                }
                if ($entry['size'] > 20 * 1024 * 1024) {
                    throw new \RuntimeException($name.' exceeds the 20 MB CSV import limit.');
                }
                $seen[$key] = true;
                // Never extract user-controlled paths. Only known CSV basenames.
                $input = $zip->getStream($entry['name']);
                $target = $directory.DIRECTORY_SEPARATOR.$name;
                $output = fopen($target, 'wb');
                if ($input === false || $output === false) {
                    if (is_resource($input)) {
                        fclose($input);
                    }
                    if (is_resource($output)) {
                        fclose($output);
                    }
                    throw new \RuntimeException('Unable to stage '.$name.'.');
                }
                try {
                    $copied = stream_copy_to_stream($input, $output, 20 * 1024 * 1024 + 1);
                    if ($copied === false || $copied !== $entry['size']) {
                        throw new \RuntimeException('Unable to read the complete '.$name.'.');
                    }
                } finally {
                    fclose($input);
                    fclose($output);
                }
                $request->files->set($key, new UploadedFile($target, $name, 'text/csv', null, true));
            }
        } finally {
            $zip->close();
        }
    }
}
