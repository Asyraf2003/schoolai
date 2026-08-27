<?php

namespace App\Providers\Concerns;

use App\Models\HeroSetting;
use App\Models\PpdbSetting;
use App\Support\HeroVideoUrl;
use App\Support\HomeHeroPresentation;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

trait InjectsDatabaseHero
{
    private function injectDatabaseHero(View $view): void
    {
        $hero = $view->getData()['hero'] ?? [];
        $hero = is_array($hero) ? $hero : [];
        $locale = app()->getLocale();
        $slides = [
            $this->openingHeroSlide($hero, $locale),
            ...$this->promotedArticleHeroSlides($locale),
        ];

        $hero['slides'] = HomeHeroPresentation::decorate(
            array_values(array_filter($slides)),
        );
        $view->with('hero', $hero);
    }

    /** @param array<string, mixed> $hero
     * @return array<string, mixed>|null
     */
    private function openingHeroSlide(array $hero, string $locale): ?array
    {
        $fallback = collect($hero['slides'] ?? [])->first();
        $fallback = is_array($fallback) ? $fallback : [];
        $setting = Schema::hasTable('hero_settings')
            ? HeroSetting::query()->first()
            : null;
        $mediaUrl = $this->publicAssetUrl(config('media.homepage_hero_video_url'));
        $fallbackImage = $this->publicAssetUrl($hero['fallback_image_url'] ?? null);
        $posterUrl = $this->publicAssetUrl($fallback['poster_url'] ?? null) ?: $fallbackImage;
        $renderType = $mediaUrl !== null && HeroVideoUrl::isDirectVideo($mediaUrl)
            ? 'video'
            : 'image';
        $renderUrl = $renderType === 'video' ? $mediaUrl : $posterUrl;

        if ($renderUrl === null) {
            return null;
        }

        $ctaUrl = $this->heroLinkUrl($setting?->cta_url ?? data_get($fallback, 'cta.href'));

        if ($this->isClosedPpdbLink($ctaUrl)) {
            $ctaUrl = null;
        }

        return [
            'type' => 'video',
            'render_type' => $renderType,
            'media_url' => $renderUrl,
            'poster_url' => $posterUrl,
            'media_alt' => $fallback['media_alt'] ?? 'Al Mustaqbal School',
            'eyebrow' => $setting?->eyebrowForLocale($locale) ?? ($fallback['eyebrow'] ?? ''),
            'title' => $setting?->titleForLocale($locale) ?? ($fallback['title'] ?? 'Al Mustaqbal School'),
            'description' => $setting?->descriptionForLocale($locale) ?? ($fallback['description'] ?? ''),
            'cta' => [
                'label' => $ctaUrl !== null ? $setting?->ctaLabelForLocale($locale) : null,
                'href' => $ctaUrl,
                'action' => $ctaUrl !== null ? 'link' : null,
            ],
            'focal_position' => 'center center',
            'overlay_strength' => 0.34,
            'video_mime_type' => $this->heroVideoMimeType($mediaUrl),
            'is_media_fallback' => $renderType !== 'video',
            'is_opening' => true,
        ];
    }

    private function isClosedPpdbLink(?string $url): bool
    {
        if ($url === null || '/'.ltrim((string) parse_url($url, PHP_URL_PATH), '/') !== '/ppdb') {
            return false;
        }

        if (! Schema::hasTable('ppdb_settings')) {
            return true;
        }

        return PpdbSetting::query()->first()?->isRegistrationOpen() !== true;
    }
}
