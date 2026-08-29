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
        $ppdbSetting = Schema::hasTable('ppdb_settings')
            ? PpdbSetting::query()->first()
            : null;
        $ppdbOpen = $ppdbSetting?->isRegistrationOpen() === true;
        $mediaUrl = $this->publicAssetUrl(config('media.homepage_hero_video_url'));
        $fallbackImage = $this->publicAssetUrl($hero['fallback_image_url'] ?? null);
        $posterUrl = $fallbackImage
            ?: $this->publicAssetUrl($fallback['poster_url'] ?? null);
        $renderType = $mediaUrl !== null && HeroVideoUrl::isDirectVideo($mediaUrl)
            ? 'video'
            : 'image';
        $renderUrl = $renderType === 'video' ? $mediaUrl : $posterUrl;

        if ($renderUrl === null) {
            return null;
        }

        $ctaUrl = $this->heroLinkUrl($setting?->cta_url ?? data_get($fallback, 'cta.href'));

        if (! $ppdbOpen && $this->isPpdbLink($ctaUrl)) {
            $ctaUrl = null;
        }

        $ppdbUrl = $ppdbOpen ? route('ppdb', absolute: false) : null;
        $ppdbLinkLabel = $ppdbOpen
            ? __('runtime.home.ppdb_campaign_link_label')
            : null;

        return [
            'type' => 'video',
            'render_type' => $renderType,
            'media_url' => $renderUrl,
            'poster_url' => $posterUrl,
            'media_alt' => $fallback['media_alt'] ?? 'Al Mustaqbal School',
            'eyebrow' => $ppdbOpen
                ? __('runtime.home.ppdb_campaign_eyebrow')
                : ($setting?->eyebrowForLocale($locale) ?? ($fallback['eyebrow'] ?? '')),
            'title' => $ppdbOpen
                ? __('runtime.home.ppdb_campaign_title')
                : ($setting?->titleForLocale($locale) ?? ($fallback['title'] ?? 'Al Mustaqbal School')),
            'description' => $ppdbOpen
                ? __('runtime.home.ppdb_campaign_description')
                : ($setting?->descriptionForLocale($locale) ?? ($fallback['description'] ?? '')),
            'eyebrow_href' => $ppdbUrl,
            'title_href' => $ppdbUrl,
            'description_href' => $ppdbUrl,
            'campaign_link_label' => $ppdbLinkLabel,
            'is_ppdb_campaign' => $ppdbOpen,
            'cta' => [
                'label' => $ppdbOpen
                    ? $ppdbLinkLabel
                    : ($ctaUrl !== null ? $setting?->ctaLabelForLocale($locale) : null),
                'href' => $ppdbOpen ? $ppdbUrl : $ctaUrl,
                'action' => ($ppdbOpen || $ctaUrl !== null) ? 'link' : null,
            ],
            'focal_position' => 'center center',
            'overlay_strength' => 0.34,
            'video_mime_type' => $this->heroVideoMimeType($mediaUrl),
            'is_media_fallback' => $renderType !== 'video',
            'is_opening' => true,
        ];
    }

    private function isPpdbLink(?string $url): bool
    {
        return $url !== null
            && '/'.ltrim((string) parse_url($url, PHP_URL_PATH), '/') === '/ppdb';
    }
}
