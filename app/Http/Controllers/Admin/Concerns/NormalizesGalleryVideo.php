<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Support\Media\MediaUrlResolver;
use Illuminate\Validation\ValidationException;

trait NormalizesGalleryVideo
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
                'media_url' => 'URL video tidak valid.',
            ]);
        }

        $ownedKey = app(MediaUrlResolver::class)->ownedKey($url);
        if (
            $ownedKey !== null &&
            str_starts_with($ownedKey, 'gallery/media/') &&
            str_ends_with(strtolower($ownedKey), '.mp4')
        ) {
            return $url;
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

        if ($this->hostMatches($host, 'tiktok.com') && preg_match('~(?:^|/)(?:video|player/v1|embed/v2)/(\d+)(?:/|$)~', $path, $match)) {
            return 'https://www.tiktok.com/player/v1/'.$match[1];
        }

        if ($this->hostMatches($host, 'instagram.com') && preg_match('~^(p|reel|tv)/([^/]+)~', $path, $match)) {
            return 'https://www.instagram.com/'.$match[1].'/'.rawurlencode($match[2]).'/embed';
        }

        if ($scheme === 'https' && $this->hostMatches($host, 'facebook.com')) {
            $facebookUrl = $path === 'plugins/video.php'
                ? trim((string) ($query['href'] ?? ''))
                : $url;
            $facebookVideoId = $this->facebookVideoId($facebookUrl);

            if ($facebookVideoId !== null) {
                return $this->facebookReelEmbedUrl($facebookVideoId);
            }
        }

        if ($this->hostMatches($host, 'vimeo.com') && preg_match('~^(?:video/)?(\d+)$~', $path, $match)) {
            return 'https://player.vimeo.com/video/'.$match[1];
        }

        throw ValidationException::withMessages([
            'media_url' => 'URL video belum didukung. Gunakan MP4 R2 milik Galeri, YouTube, TikTok, Instagram, Vimeo, atau Facebook Reel/Watch.',
        ]);
    }

    private function facebookVideoId(string $url): ?string
    {
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if ($scheme !== 'https' || ! $this->hostMatches($host, 'facebook.com')) {
            return null;
        }

        if (preg_match('~^reel/(\d+)$~', $path, $match)) {
            return $match[1];
        }

        $watchId = (string) ($query['v'] ?? '');

        return $path === 'watch' && preg_match('~^\d+$~', $watchId)
            ? $watchId
            : null;
    }

    private function facebookReelEmbedUrl(string $reelId): string
    {
        $reelUrl = 'https://www.facebook.com/reel/'.rawurlencode($reelId).'/';

        return 'https://www.facebook.com/plugins/video.php?'.http_build_query([
            'height' => 476,
            'href' => $reelUrl,
            'show_text' => 'false',
            'width' => 267,
            't' => 0,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    private function hostMatches(string $host, string $domain): bool
    {
        return $host === $domain || str_ends_with($host, '.'.$domain);
    }
}
