<?php

namespace App\Support\Media;

use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

final class StaticPublicMediaPublisher
{
    /**
     * @return array{
     *   scanned: int,
     *   eligible: int,
     *   uploaded: int,
     *   existing: int,
     *   failed: int,
     *   bytes: int,
     *   errors: array<int, string>
     * }
     */
    public function publish(bool $dryRun = false): array
    {
        $entries = config('media.static_publish', []);
        $entries = is_array($entries) ? $entries : [];
        $stats = [
            'scanned' => 0,
            'eligible' => 0,
            'uploaded' => 0,
            'existing' => 0,
            'failed' => 0,
            'bytes' => 0,
            'errors' => [],
        ];
        $disk = Storage::disk((string) config('media.disk'));

        foreach ($entries as $entry) {
            $stats['scanned']++;

            try {
                [$source, $key] = $this->validateEntry($entry);
                $absolutePath = public_path($source);

                if (! is_file($absolutePath)) {
                    throw new RuntimeException('Static source is missing: '.$source);
                }

                $size = filesize($absolutePath);
                if (! is_int($size)) {
                    throw new RuntimeException('Static source size is unreadable: '.$source);
                }

                $stats['bytes'] += $size;

                if ($disk->exists($key)) {
                    if ((int) $disk->size($key) !== $size) {
                        throw new RuntimeException(
                            'Versioned R2 object differs from local source: '.$key
                            .'. Bump the versioned key instead of overwriting immutable media.'
                        );
                    }

                    $stats['existing']++;
                    continue;
                }

                $stats['eligible']++;
                if ($dryRun) {
                    continue;
                }

                $stream = fopen($absolutePath, 'rb');
                if (! is_resource($stream)) {
                    throw new RuntimeException('Static source could not be opened: '.$source);
                }

                try {
                    $stored = $disk->put($key, $stream, [
                        'CacheControl' => (string) config('media.cache_control'),
                        'ContentType' => mime_content_type($absolutePath)
                            ?: 'application/octet-stream',
                    ]);
                } finally {
                    fclose($stream);
                }

                if (! $stored || ! $disk->exists($key)) {
                    $disk->delete($key);
                    throw new RuntimeException('Static R2 upload could not be verified: '.$key);
                }

                if ((int) $disk->size($key) !== $size) {
                    $disk->delete($key);
                    throw new RuntimeException('Static R2 upload size mismatch: '.$key);
                }

                $stats['uploaded']++;
            } catch (Throwable $exception) {
                $stats['failed']++;
                $stats['errors'][] = $exception->getMessage();
            }
        }

        return $stats;
    }

    /** @return array{string, string} */
    private function validateEntry(mixed $entry): array
    {
        if (! is_array($entry)) {
            throw new RuntimeException('Static media manifest entry is invalid.');
        }

        $source = trim((string) ($entry['source'] ?? ''));
        $key = trim((string) ($entry['key'] ?? ''));

        if (
            $source === ''
            || str_starts_with($source, '/')
            || str_contains($source, '..')
            || str_contains($source, '\\')
        ) {
            throw new RuntimeException('Static media source path is invalid: '.$source);
        }

        if (
            ! str_starts_with($key, 'site/')
            || str_contains($key, '..')
            || str_contains($key, '\\')
        ) {
            throw new RuntimeException('Static media R2 key is invalid: '.$key);
        }

        return [$source, $key];
    }
}
