<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\PpdbShowcaseItem;
use App\Support\Media\R2MediaStorage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
            $stored = app(R2MediaStorage::class)->store(
                $request->file('media_file'),
                'ppdb/showcase',
                $currentItem?->getKey(),
            );
            $data['media_url'] = $stored['url'];

            return [$data, $stored['key'], $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO];
        }

        $data['media_url'] = $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO
            ? $currentItem->media_url
            : null;

        return [$data, null, false];
    }

    private function deleteStoredPublicPath(?string $path): void
    {
        if ($path !== null && $path !== '') {
            app(R2MediaStorage::class)->deleteKey($path);
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

            return 'https://www.youtube.com/embed/'.rawurlencode((string) $parts[0]);
        }

        if ($this->hostMatches($host, 'youtube.com')) {
            if (! empty($query['v'])) {
                return 'https://www.youtube.com/embed/'.rawurlencode((string) $query['v']);
            }

            if (preg_match('~(?:^|/)(?:shorts|embed)/([^/?#]+)~', $path, $match)) {
                return 'https://www.youtube.com/embed/'.rawurlencode($match[1]);
            }
        }

        if ($this->hostMatches($host, 'tiktok.com') && preg_match('~(?:^|/)video/(\d+)(?:/|$)~', $path, $match)) {
            return 'https://www.tiktok.com/embed/v2/'.$match[1];
        }

        if ($this->hostMatches($host, 'instagram.com') && preg_match('~^(p|reel|tv)/([^/]+)~', $path, $match)) {
            return 'https://www.instagram.com/'.$match[1].'/'.rawurlencode($match[2]).'/embed';
        }

        if ($this->hostMatches($host, 'vimeo.com') && preg_match('~^(?:video/)?(\d+)$~', $path, $match)) {
            return 'https://player.vimeo.com/video/'.$match[1];
        }

        throw ValidationException::withMessages([
            'media_url' => 'URL belum didukung. Gunakan YouTube, TikTok, Instagram, atau Vimeo.',
        ]);
    }
}
