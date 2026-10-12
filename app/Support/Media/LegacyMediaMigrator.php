<?php

namespace App\Support\Media;

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\TestimonialMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class LegacyMediaMigrator
{
    /** @var array<string, array<class-string<Model>, array<string, string>>> */
    private const OWNERS = [
        'gallery' => [GalleryItem::class => ['media_url' => 'gallery/media', 'thumbnail_url' => 'gallery/thumbnails']],
        'testimonials' => [TestimonialMedia::class => ['media_url' => 'testimonials/media']],
        'articles' => [Article::class => ['cover_url' => 'articles/covers', 'cover_thumbnail_url' => 'articles/thumbnails']],
    ];

    public function __construct(private readonly R2MediaStorage $mediaStorage) {}

    /**
     * @return array{scanned: int, eligible: int, migrated: int, skipped: int, failed: int, errors: array<int, string>}
     */
    public function migrate(?string $owner = null, bool $dryRun = false): array
    {
        $owners = $owner === null ? array_keys(self::OWNERS) : [$owner];

        if ($owner !== null && ! isset(self::OWNERS[$owner])) {
            throw new InvalidArgumentException('Unknown media owner: '.$owner);
        }

        $stats = ['scanned' => 0, 'eligible' => 0, 'migrated' => 0, 'skipped' => 0, 'failed' => 0, 'errors' => []];

        foreach ($owners as $ownerName) {
            foreach (self::OWNERS[$ownerName] as $modelClass => $fields) {
                foreach ($modelClass::withoutGlobalScopes()->cursor() as $model) {
                    foreach ($fields as $field => $namespace) {
                        $this->migrateField($model, $field, $namespace, $dryRun, $stats);
                    }

                }
            }
        }

        return $stats;
    }

    /** @param array<string, mixed> $stats */
    private function migrateField(Model $model, string $field, string $namespace, bool $dryRun, array &$stats): void
    {
        $url = $model->getAttribute($field);
        $stats['scanned']++;

        if (! $this->isLocalUrl($url)) {
            $stats['skipped']++;

            return;
        }

        $stats['eligible']++;

        if ($dryRun) {
            return;
        }

        try {
            $stored = $this->upload((string) $url, $namespace, $model->getKey());
            $model->setAttribute($field, $stored['url']);

            try {
                $model->saveQuietly();
            } catch (Throwable $exception) {
                $this->mediaStorage->deleteKey($stored['key']);
                throw $exception;
            }

            $stats['migrated']++;
        } catch (Throwable $exception) {
            $stats['failed']++;
            $stats['errors'][] = $model::class.'#'.$model->getKey().'.'.$field.': '.$exception->getMessage();
        }
    }

    /** @return array{key: string, url: string} */
    private function upload(string $url, string $owner, int|string $scope): array
    {
        $path = ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($path === '' || str_contains($path, '..') || str_contains($path, '\\')) {
            throw new RuntimeException('Legacy media path is invalid.');
        }

        if (str_starts_with($url, '/storage/')) {
            $path = substr($path, strlen('storage/'));
            $disk = Storage::disk('public');

            if (! $disk->exists($path)) {
                throw new RuntimeException('Legacy public-disk object is missing.');
            }

            $stream = $disk->readStream($path);
            $contentType = $disk->mimeType($path) ?: 'application/octet-stream';
        } else {
            $absolutePath = public_path($path);

            if (! is_file($absolutePath)) {
                throw new RuntimeException('Legacy repository media is missing.');
            }

            $stream = fopen($absolutePath, 'rb');
            $contentType = mime_content_type($absolutePath) ?: 'application/octet-stream';
        }

        if (! is_resource($stream)) {
            throw new RuntimeException('Legacy media could not be opened.');
        }

        try {
            return $this->mediaStorage->storeStream(
                $stream,
                pathinfo($path, PATHINFO_EXTENSION),
                $contentType,
                $owner,
                $scope,
            );
        } finally {
            fclose($stream);
        }
    }

    private function isLocalUrl(mixed $url): bool
    {
        if (! is_string($url)) {
            return false;
        }

        return str_starts_with($url, '/storage/')
            || str_starts_with($url, '/media/')
            || str_starts_with($url, 'media/');
    }
}
