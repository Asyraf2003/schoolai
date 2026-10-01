<?php

namespace App\Http\Controllers\Concerns;

use App\Models\GalleryItem;
use App\Support\Media\MediaUrlResolver;
use App\Support\PublicUrl;

trait PresentsGalleryMedia
{
    private function presentItem(GalleryItem $item, string $locale, bool $sectionItem = false): array
    {
        $type = $item->type === 'video' ? 'video' : 'photo';
        $directVideoUrl = $type === 'video'
            ? $this->trustedGalleryDirectVideoUrl($item->media_url)
            : null;
        $mediaUrl = $directVideoUrl ?? $this->trustedMediaUrl($type, $item->media_url);

        return [
            'title' => $item->titleForLocale($locale),
            'label' => $sectionItem ? $item->categoryForLocale($locale) : null,
            'type' => $type,
            'media_url' => $mediaUrl,
            'thumbnail_url' => $type === 'video' && $directVideoUrl === null
                ? $this->videoThumbnailUrl($mediaUrl)
                : ($type === 'photo' ? $mediaUrl : null),
            'is_direct_video' => $directVideoUrl !== null,
            'emoji' => $type === 'video' ? '▶️' : '📸',
            'badge' => $item->typeLabelForLocale($locale),
        ];
    }

    private function trustedGalleryDirectVideoUrl(?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);
        $key = app(MediaUrlResolver::class)->ownedKey($url);

        return $key !== null
            && str_starts_with($key, 'gallery/media/')
            && str_ends_with(strtolower($key), '.mp4')
                ? $url
                : null;
    }

    private function trustedMediaUrl(string $type, ?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if ($type === 'photo' && str_starts_with($url, '/storage/')) {
            return $url;
        }

        if (! PublicUrl::isSafe($url)) {
            return null;
        }

        if ($type === 'photo') {
            return $url;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($scheme !== 'https' || $host === '') {
            return null;
        }

        if ($host === 'www.youtube.com' && preg_match('~^embed/[^/?#]+$~', $path)) {
            return $url;
        }

        if ($host === 'www.tiktok.com' && preg_match('~^(?:player/v1|embed/v2)/\d+$~', $path)) {
            return $url;
        }

        if ($host === 'www.instagram.com' && preg_match('~^(p|reel|tv)/[^/]+/embed$~', $path)) {
            return $url;
        }

        if ($host === 'player.vimeo.com' && preg_match('~^video/\d+$~', $path)) {
            return $url;
        }

        if ($host === 'www.facebook.com' && $path === 'plugins/video.php' && $this->isTrustedFacebookEmbedUrl($url)) {
            return $url;
        }

        return null;
    }

    private function isTrustedFacebookEmbedUrl(string $url): bool
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $facebookUrl = trim((string) ($query['href'] ?? ''));

        if ($facebookUrl === '' || ! PublicUrl::isSafe($facebookUrl)) {
            return false;
        }

        $facebookScheme = strtolower((string) parse_url($facebookUrl, PHP_URL_SCHEME));
        $facebookHost = strtolower((string) parse_url($facebookUrl, PHP_URL_HOST));
        $facebookPath = trim((string) parse_url($facebookUrl, PHP_URL_PATH), '/');
        parse_str((string) parse_url($facebookUrl, PHP_URL_QUERY), $facebookQuery);

        if (
            $facebookScheme !== 'https' ||
            ($facebookHost !== 'facebook.com' && ! str_ends_with($facebookHost, '.facebook.com'))
        ) {
            return false;
        }

        if (preg_match('~^reel/\d+$~', $facebookPath)) {
            return true;
        }

        return $facebookPath === 'watch' && preg_match('~^\d+$~', (string) ($facebookQuery['v'] ?? '')) === 1;
    }

    private function videoThumbnailUrl(?string $embedUrl): ?string
    {
        if (! is_string($embedUrl) || trim($embedUrl) === '') {
            return null;
        }

        $embedUrl = trim($embedUrl);
        $host = strtolower((string) parse_url($embedUrl, PHP_URL_HOST));
        $path = trim((string) parse_url($embedUrl, PHP_URL_PATH), '/');

        if ($host === 'www.youtube.com' && preg_match('~^embed/([^/?#]+)$~', $path, $match)) {
            return 'https://i.ytimg.com/vi/'.rawurlencode($match[1]).'/hqdefault.jpg';
        }

        return null;
    }
}
