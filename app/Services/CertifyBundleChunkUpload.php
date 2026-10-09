<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class CertifyBundleChunkUpload
{
    public const CHUNK_BYTES = 262144;

    public const MAX_ARCHIVE_BYTES = 536870912;

    public function directory(string $operationId): string
    {
        if (! \Illuminate\Support\Str::isUuid($operationId)) {
            throw new \InvalidArgumentException('Invalid import operation ID.');
        }

        return storage_path('app/certify-import-chunks/'.$operationId);
    }

    public function receive(string $operationId, array $upload, int $index, UploadedFile $file, string $checksum): array
    {
        $count = (int) ceil($upload['archive_size_bytes'] / $upload['chunk_size_bytes']);
        if ($index >= $count) {
            throw ValidationException::withMessages(['chunk_index' => 'This upload part is outside the archive.']);
        }
        $expectedSize = min($upload['chunk_size_bytes'], $upload['archive_size_bytes'] - $index * $upload['chunk_size_bytes']);
        if (! $file->isValid() || $file->getSize() !== $expectedSize) {
            throw ValidationException::withMessages(['chunk' => 'The upload part is incomplete or has the wrong size.']);
        }
        if (! hash_equals($checksum, hash_file('sha256', $file->getRealPath()))) {
            throw ValidationException::withMessages(['chunk_sha256' => 'The upload part checksum does not match.']);
        }
        $previous = $upload['received_chunks'][$index] ?? null;
        if ($previous !== null && ! hash_equals($previous, $checksum)) {
            throw ValidationException::withMessages(['chunk_sha256' => 'This part was already uploaded with different data.']);
        }
        $directory = $this->directory($operationId);
        File::ensureDirectoryExists($directory);
        // A repeated part replaces only this operation's temporary fragment.
        $file->move($directory, $index.'.part');
        $upload['received_chunks'][$index] = $checksum;

        return $upload;
    }

    public function receivedIndices(string $operationId, array $upload): array
    {
        $directory = $this->directory($operationId);
        $received = [];
        foreach ($upload['received_chunks'] as $index => $checksum) {
            $path = $directory.'/'.$index.'.part';
            if (is_file($path) && hash_equals($checksum, hash_file('sha256', $path))) {
                $received[] = (int) $index;
            }
        }

        return $received;
    }

    public function assertComplete(string $operationId, array $upload): void
    {
        $count = (int) ceil($upload['archive_size_bytes'] / $upload['chunk_size_bytes']);
        $directory = $this->directory($operationId);
        for ($index = 0; $index < $count; $index++) {
            $path = $directory.'/'.$index.'.part';
            $expectedSize = min($upload['chunk_size_bytes'], $upload['archive_size_bytes'] - $index * $upload['chunk_size_bytes']);
            if (! isset($upload['received_chunks'][$index]) || ! is_file($path) || filesize($path) !== $expectedSize) {
                throw ValidationException::withMessages(['chunk' => 'Archive part '.$index.' is missing. Retry upload to resume.']);
            }
        }
    }

    public function assemble(string $operationId, array $upload, string $destination): void
    {
        $this->assertComplete($operationId, $upload);
        $count = (int) ceil($upload['archive_size_bytes'] / $upload['chunk_size_bytes']);
        $directory = $this->directory($operationId);
        File::ensureDirectoryExists(dirname($destination));
        $output = fopen($destination, 'wb');
        if ($output === false) {
            throw new \RuntimeException('The server cannot assemble the uploaded ZIP. Check storage permissions and free disk space.');
        }
        try {
            for ($index = 0; $index < $count; $index++) {
                $input = fopen($directory.'/'.$index.'.part', 'rb');
                if ($input === false) {
                    throw new \RuntimeException('The server cannot read archive part '.$index.'.');
                }
                try {
                    if (stream_copy_to_stream($input, $output) === false) {
                        throw new \RuntimeException('The server cannot write the assembled ZIP.');
                    }
                } finally {
                    fclose($input);
                }
            }
        } finally {
            fclose($output);
        }
        if (filesize($destination) !== $upload['archive_size_bytes']
            || ! hash_equals($upload['archive_sha256'], hash_file('sha256', $destination))) {
            throw new \RuntimeException('The assembled ZIP checksum does not match. No data was imported.');
        }
    }

    public function cleanup(string $operationId): void
    {
        // directory() enforces UUIDs and an exact child of our staging root.
        File::deleteDirectory($this->directory($operationId));
    }

    public function pruneExpired(): int
    {
        $root = storage_path('app/certify-import-chunks');
        if (! is_dir($root)) {
            return 0;
        }
        $store = app(CertifyBundleImportStateStore::class);
        $removed = 0;
        foreach (File::directories($root) as $directory) {
            $id = basename($directory);
            if (\Illuminate\Support\Str::isUuid($id)
                && File::lastModified($directory) < now()->subDay()->timestamp
                && $store->get($id) === null) {
                $this->cleanup($id);
                $removed++;
            }
        }

        return $removed;
    }
}
