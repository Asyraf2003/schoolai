<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\PpdbShowcaseItem;
use App\Rules\SafeImageUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

trait ManagesPpdbShowcaseMedia
{
    private function applyMedia(Request $request, array $data, ?PpdbShowcaseItem $currentItem = null): array
    {
        if ($data['media_type'] === PpdbShowcaseItem::MEDIA_VIDEO) {
            $data['media_url'] = $this->normalizeVideoUrl((string) ($data['media_url'] ?? ''));

            return [$data, null, $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO];
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('ppdb/showcase', 'public');

            if (! is_string($path) || $path === '') {
                throw ValidationException::withMessages([
                    'media_file' => 'Foto gagal disimpan. Silakan coba lagi.',
                ]);
            }

            $data['media_url'] = Storage::url($path);

            return [$data, $path, $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO];
        }

        $data['media_url'] = $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO
            ? $currentItem->media_url
            : null;

        return [$data, null, false];
    }

    private function deleteStoredPublicPath(?string $path): void
    {
        if ($path !== null && $path !== '') {
            Storage::disk('public')->delete($path);
        }
    }

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
            throw ValidationException::withMessages(['media_url' => 'URL tidak valid.']);
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
}
