<?php

namespace App\Support\Media;

use LogicException;

final class MediaUrlResolver
{
    public function publicUrl(string $key): string
    {
        $this->assertSafeKey($key);

        return $this->baseUrl().'/'.$key;
    }

    public function ownedKey(?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '' || str_contains($url, '%')) {
            return null;
        }

        $parts = parse_url(trim($url));

        if (! is_array($parts) || isset($parts['user'], $parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) {
            return null;
        }

        $base = parse_url($this->baseUrl());

        if (
            strtolower((string) ($parts['scheme'] ?? '')) !== strtolower((string) ($base['scheme'] ?? ''))
            || strtolower((string) ($parts['host'] ?? '')) !== strtolower((string) ($base['host'] ?? ''))
            || ($parts['port'] ?? null) !== ($base['port'] ?? null)
        ) {
            return null;
        }

        $basePath = rtrim((string) ($base['path'] ?? ''), '/');
        $path = (string) ($parts['path'] ?? '');
        $prefix = $basePath.'/';

        if (! str_starts_with($path, $prefix)) {
            return null;
        }

        $key = substr($path, strlen($prefix));

        return $this->isSafeKey($key) ? $key : null;
    }

    private function baseUrl(): string
    {
        $url = rtrim((string) config('media.public_url'), '/');
        $parts = parse_url($url);

        if (
            ! is_array($parts)
            || strtolower((string) ($parts['scheme'] ?? '')) !== 'https'
            || empty($parts['host'])
            || isset($parts['user'], $parts['pass'], $parts['query'], $parts['fragment'])
        ) {
            throw new LogicException('MEDIA_PUBLIC_URL must be a canonical HTTPS origin or base URL.');
        }

        return $url;
    }

    private function assertSafeKey(string $key): void
    {
        if (! $this->isSafeKey($key)) {
            throw new LogicException('R2 media object key is invalid.');
        }
    }

    private function isSafeKey(string $key): bool
    {
        return $key !== ''
            && ! str_contains($key, '..')
            && ! str_contains($key, '\\')
            && preg_match('/^[a-z0-9][a-z0-9\/_\-.]*$/', $key) === 1;
    }
}
