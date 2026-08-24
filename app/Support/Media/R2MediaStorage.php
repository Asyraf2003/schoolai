<?php

namespace App\Support\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

final class R2MediaStorage
{
    public function __construct(private readonly MediaUrlResolver $urlResolver) {}

    /** @return array{key: string, url: string} */
    public function store(UploadedFile $file, string $owner, int|string|null $scope = null): array
    {
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $extension = preg_match('/^[a-z0-9]+$/', $extension) === 1 ? $extension : 'bin';
        $key = $this->objectKey($owner, $scope, $extension);
        $directory = dirname($key);
        $filename = basename($key);
        $disk = Storage::disk($this->disk());

        $storedKey = $disk->putFileAs($directory, $file, $filename, [
            'CacheControl' => (string) config('media.cache_control'),
            'ContentType' => $file->getMimeType() ?: 'application/octet-stream',
        ]);

        if ($storedKey !== $key || ! $disk->exists($key)) {
            if (is_string($storedKey) && $storedKey !== '') {
                $disk->delete($storedKey);
            }

            throw new RuntimeException('R2 media upload could not be verified.');
        }

        return [
            'key' => $key,
            'url' => $this->urlResolver->publicUrl($key),
        ];
    }

    /**
     * @param  resource  $stream
     * @return array{key: string, url: string}
     */
    public function storeStream($stream, string $extension, string $contentType, string $owner, int|string|null $scope = null): array
    {
        if (! is_resource($stream)) {
            throw new RuntimeException('Legacy media stream is invalid.');
        }

        $extension = strtolower($extension);
        $extension = preg_match('/^[a-z0-9]+$/', $extension) === 1 ? $extension : 'bin';
        $key = $this->objectKey($owner, $scope, $extension);
        $disk = Storage::disk($this->disk());
        $stored = $disk->put($key, $stream, [
            'CacheControl' => (string) config('media.cache_control'),
            'ContentType' => $contentType,
        ]);

        if (! $stored || ! $disk->exists($key)) {
            $disk->delete($key);
            throw new RuntimeException('Legacy R2 media upload could not be verified.');
        }

        return ['key' => $key, 'url' => $this->urlResolver->publicUrl($key)];
    }

    public function deleteKey(?string $key): bool
    {
        if (! is_string($key) || $key === '') {
            return false;
        }

        return Storage::disk($this->disk())->delete($key);
    }

    public function deleteOwnedUrl(?string $url): bool
    {
        $key = $this->urlResolver->ownedKey($url);

        return $key !== null && $this->deleteKey($key);
    }

    private function disk(): string
    {
        return (string) config('media.disk');
    }

    private function objectKey(string $owner, int|string|null $scope, string $extension): string
    {
        return $this->directory($owner, $scope).'/'.Str::uuid()->toString().'.'.$extension;
    }

    private function directory(string $owner, int|string|null $scope): string
    {
        $owner = trim($owner, '/');

        if (preg_match('/^[a-z0-9]+(?:[\/-][a-z0-9]+)*$/', $owner) !== 1) {
            throw new RuntimeException('R2 media owner namespace is invalid.');
        }

        $scope = $scope === null ? 'new' : (string) $scope;

        if (preg_match('/^[A-Za-z0-9_-]+$/', $scope) !== 1) {
            throw new RuntimeException('R2 media owner scope is invalid.');
        }

        return $owner.'/'.$scope;
    }
}
