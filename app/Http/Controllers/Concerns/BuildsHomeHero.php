<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\PpdbSetting;
use App\Models\SiteStatistic;
use App\Support\HomeHeroPresentation;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

trait BuildsHomeHero
{
    /**
     * Normalizes the locale-backed hero data into one presentation contract.
     *
     * The slides currently come from translation fallback data. A future
     * database query only needs to provide the same keys before this boundary.
     */
    private function heroData(): array
    {
        $hero = $this->homeSection('hero');
        $slides = $hero['slides'] ?? [];
        $fallbackImageUrl = $this->publicAssetUrl($hero['fallback_image'] ?? null);
        $normalizedSlides = [];
        $ppdbSetting = $this->currentPpdbSetting();

        if (! is_array($slides)) {
            $slides = [];
        }

        foreach ($slides as $slide) {
            if (! is_array($slide)) {
                continue;
            }

            $type = in_array(($slide['type'] ?? null), ['image', 'video'], true)
                ? $slide['type']
                : 'image';
            $mediaUrl = $this->publicAssetUrl($slide['media'] ?? null);
            $posterUrl = $this->publicAssetUrl($slide['poster'] ?? null);

            if (HeroVideoUrl::isYoutubeAsset($mediaUrl)) {
                $mediaUrl = null;
            }

            if (HeroVideoUrl::isYoutubeAsset($posterUrl)) {
                $posterUrl = null;
            }

            $renderType = $type === 'video'
                && $mediaUrl !== null
                && HeroVideoUrl::isDirectVideo($mediaUrl)
                    ? 'video'
                    : 'image';

            if ($renderType === 'image') {
                $mediaUrl = $type === 'image'
                    ? ($mediaUrl ?: $posterUrl ?: $fallbackImageUrl)
                    : ($posterUrl ?: $fallbackImageUrl);
            }

            if ($mediaUrl === null) {
                continue;
            }

            $cta = isset($slide['cta']) && is_array($slide['cta'])
                ? $slide['cta']
                : [];

            if (($cta['action'] ?? null) === 'admission') {
                $cta['href'] = $ppdbSetting->isRegistrationOpen()
                    ? $ppdbSetting->publicRegistrationUrl()
                    : route('ppdb');
            } else {
                $cta['href'] = $this->heroLinkUrl($cta['href'] ?? null);
            }

            $normalizedSlides[] = array_replace($slide, [
                'type' => $type,
                'render_type' => $renderType,
                'media_url' => $mediaUrl,
                'poster_url' => $posterUrl ?: $fallbackImageUrl,
                'is_media_fallback' => $type !== $renderType,
                'focal_position' => $this->heroFocalPosition(
                    $slide['focal_position'] ?? null
                ),
                'overlay_strength' => $this->heroOverlayStrength(
                    $slide['overlay_strength'] ?? null
                ),
                'video_mime_type' => $this->heroVideoMimeType($mediaUrl),
                'cta' => $cta,
            ]);
        }

        if ($normalizedSlides === [] && $fallbackImageUrl !== null) {
            $normalizedSlides[] = [
                'type' => 'image',
                'render_type' => 'image',
                'media_url' => $fallbackImageUrl,
                'poster_url' => $fallbackImageUrl,
                'media_alt' => $hero['fallback_image_alt'] ?? 'Al Mustaqbal School',
                'eyebrow' => 'Al Mustaqbal School',
                'title' => $hero['fallback_title'] ?? 'Al Mustaqbal School',
                'description' => $hero['fallback_description'] ?? '',
                'focal_position' => 'center center',
                'overlay_strength' => 0.58,
                'video_mime_type' => 'video/mp4',
                'is_media_fallback' => true,
                'cta' => [],
            ];
        }

        $hero['slides'] = HomeHeroPresentation::decorate(
            $normalizedSlides,
            $ppdbSetting,
            __('runtime.home.ppdb_cta_label'),
        );
        $hero['fallback_image_url'] = $fallbackImageUrl;
        $hero['autoplay_interval'] = min(
            15000,
            max(4000, (int) ($hero['autoplay_interval'] ?? 7000))
        );

        return $hero;
    }

    private function heroLinkUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (preg_match('/^#[A-Za-z][A-Za-z0-9_-]*$/', $url) === 1) {
            return $url;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private function heroFocalPosition(mixed $position): string
    {
        if (! is_string($position)) {
            return 'center center';
        }

        $position = trim($position);

        return preg_match('/^[a-z0-9%.\s-]{1,60}$/i', $position) === 1
            ? $position
            : 'center center';
    }

    private function heroOverlayStrength(mixed $strength): float
    {
        $strength = is_numeric($strength) ? (float) $strength : 0.58;

        return round(min(0.88, max(0.28, $strength)), 2);
    }

    private function heroVideoMimeType(?string $url): string
    {
        $extension = strtolower(pathinfo(
            (string) parse_url((string) $url, PHP_URL_PATH),
            PATHINFO_EXTENSION
        ));

        return match ($extension) {
            'webm' => 'video/webm',
            'ogv', 'ogg' => 'video/ogg',
            default => 'video/mp4',
        };
    }
}
