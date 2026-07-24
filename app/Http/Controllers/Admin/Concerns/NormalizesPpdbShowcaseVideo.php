<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use App\Rules\SafeImageUpload;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Throwable;

trait NormalizesPpdbShowcaseVideo
{
    private function normalizeVideoUrl(string $url): string
    {
        $url = trim($url);
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (
            $url === '' ||
            $host === '' ||
            ! in_array($scheme, ['http', 'https'], true) ||
            ! filter_var($url, FILTER_VALIDATE_URL)
        ) {
            throw ValidationException::withMessages([
                'media_url' => 'URL tidak valid.',
            ]);
        }

        if ($this->hostMatches($host, 'youtu.be') && $path !== '') {
            $parts = explode('/', $path);

            return 'https://www.youtube.com/embed/' . rawurlencode((string) $parts[0]);
        }

        if ($this->hostMatches($host, 'youtube.com')) {
            if (! empty($query['v'])) {
                return 'https://www.youtube.com/embed/' . rawurlencode((string) $query['v']);
            }

            if (preg_match('~(?:^|/)(?:shorts|embed)/([^/?#]+)~', $path, $match)) {
                return 'https://www.youtube.com/embed/' . rawurlencode($match[1]);
            }
        }

        if ($this->hostMatches($host, 'tiktok.com') && preg_match('~(?:^|/)video/(\d+)(?:/|$)~', $path, $match)) {
            return 'https://www.tiktok.com/embed/v2/' . $match[1];
        }

        if ($this->hostMatches($host, 'instagram.com') && preg_match('~^(p|reel|tv)/([^/]+)~', $path, $match)) {
            return 'https://www.instagram.com/' . $match[1] . '/' . rawurlencode($match[2]) . '/embed';
        }

        if ($this->hostMatches($host, 'vimeo.com') && preg_match('~^(?:video/)?(\d+)$~', $path, $match)) {
            return 'https://player.vimeo.com/video/' . $match[1];
        }

        throw ValidationException::withMessages([
            'media_url' => 'URL belum didukung. Gunakan YouTube, TikTok, Instagram, atau Vimeo.',
        ]);
    }

    private function hostMatches(string $host, string $domain): bool
    {
        return $host === $domain || str_ends_with($host, '.' . $domain);
    }

    private function deleteStoredPublicFile(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        if (PpdbShowcaseItem::withTrashed()->where('media_url', $url)->exists()) {
            return;
        }

        $path = substr($url, strlen('/storage/'));

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/') || str_contains($path, '\\')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
