<?php

namespace App\Http\Controllers\Concerns;

use App\Support\Media\MediaUrlResolver;
use App\Support\PublicUrl;

trait NormalizesHomeGallery
{
    private function normalizeGalleryItem(array $item): array
    {
        $allowedTypes = ['photo', 'video'];
        $allowedVariants = ['normal', 'wide', 'tall', 'feature'];

        $type = $item['type'] ?? 'photo';
        $variant = $item['variant'] ?? 'normal';

        if (! in_array($type, $allowedTypes, true)) {
            $type = 'photo';
        }

        if (! in_array($variant, $allowedVariants, true)) {
            $variant = 'normal';
        }

        $rawMedia = $item['media_url'] ?? $item['thumbnail'] ?? null;
        $directVideoUrl = $type === 'video'
            ? $this->trustedGalleryDirectVideoUrl($rawMedia)
            : null;
        $mediaUrl = $type === 'video'
            ? ($directVideoUrl ?? $this->trustedVideoEmbedUrl($rawMedia))
            : $this->publicAssetUrl($rawMedia);
        $isDirectVideo = $type === 'video' && $directVideoUrl !== null;

        if ($mediaUrl === null) {
            $fallbackImages = [
                'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=82',
                'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1600&q=82',
                'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1600&q=82',
                'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1600&q=82',
                'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1600&q=82',
                'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1600&q=82',
            ];
            $fallbackKey = trim((string) ($item['title'] ?? $item['caption'] ?? 'Al Mustaqbal School'));
            $fallbackIndex = ((int) sprintf('%u', crc32($fallbackKey))) % count($fallbackImages);

            $mediaUrl = $fallbackImages[$fallbackIndex];
            $type = 'photo';
            $isDirectVideo = false;
        }

        $videoProvider = $type === 'video' && ! $isDirectVideo
            ? $this->videoProvider($mediaUrl)
            : ($isDirectVideo ? 'direct' : null);
        $caption = trim((string) ($item['caption'] ?? ''));

        if ($this->isDummyGalleryCaption($caption)) {
            $caption = trim((string) __('home.galeri.section_subtitle'));
        }

        $item['type'] = $type;
        $item['type_label'] = (string) ($item['type_label'] ?? (
            $type === 'video' ? __('runtime.home.video') : __('runtime.home.photo')
        ));
        $item['is_video'] = $type === 'video';
        $item['is_direct_video'] = $isDirectVideo;
        $item['variant'] = $variant;
        $item['instagram_url'] = $this->instagramUrl($item['instagram_url'] ?? null);
        $item['media_url'] = $mediaUrl;
        $item['thumbnail_url'] = $type === 'photo'
            ? $mediaUrl
            : ($isDirectVideo ? null : $this->videoThumbnailUrl($mediaUrl));
        $item['video_provider'] = $videoProvider;
        $item['video_provider_logo_url'] = $isDirectVideo
            ? null
            : $this->videoProviderLogoUrl($videoProvider);
        $item['video_provider_label'] = match ($videoProvider) {
            'youtube' => 'YouTube',
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'tiktok' => 'TikTok',
            'vimeo' => 'Vimeo',
            default => __('runtime.home.video'),
        };
        $item['published_at'] = (string) ($item['published_at'] ?? $item['date'] ?? '');
        $item['date'] = (string) ($item['date'] ?? $item['published_at']);
        $item['caption'] = $caption;
        $item['category'] = (string) ($item['category'] ?? '');
        $item['accent'] = $type === 'video' ? '#f97316' : '#19aee6';
        $item['fallback_icon'] = $type === 'video' ? '▶' : '📸';

        return $item;
    }

    private function isDummyGalleryCaption(string $caption): bool
    {
        return in_array($caption, [
            'Dokumentasi dummy untuk pratinjau galeri sekolah.',
            'Sample documentation for the school gallery preview.',
            'محتوى تجريبي لمعاينة معرض المدرسة.',
        ], true);
    }

    private function trustedGalleryDirectVideoUrl(mixed $url): ?string
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

    private function trustedVideoEmbedUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (! PublicUrl::isSafe($url)) {
            return null;
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

        if ($host === 'www.facebook.com' && $path === 'plugins/video.php'
            && $this->isTrustedFacebookEmbedUrl($url)) {
            return $url;
        }

        return null;
    }
}
