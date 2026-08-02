<?php

namespace App\Providers\Concerns;

use App\Http\Controllers\Admin\HeroSlideAdminController;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\PpdbSetting;
use App\Support\HomeHeroPresentation;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

trait InjectsDatabaseHero
{
    private function injectDatabaseHero(View $view): void
    {
        $hero = $view->getData()['hero'] ?? [];
        $hero = is_array($hero) ? $hero : [];
        $fallbackImageUrl = is_string($hero['fallback_image_url'] ?? null)
            ? $hero['fallback_image_url']
            : null;
        $locale = app()->getLocale();
        $ppdbSetting = null;
        $normalizedSlides = [];
        $primarySlide = collect($hero['slides'] ?? [])->first(
            fn (mixed $slide): bool => is_array($slide)
                && ($slide['is_primary_slide'] ?? false) === true
                && ($slide['render_type'] ?? null) === 'video'
        );
        $slides = $this->articleHeroSlides($locale);

        if ($slides === [] && Schema::hasTable('hero_slides')) {
            $legacySlides = HeroSlide::query()->activeOrdered();

            if (Schema::hasColumn('hero_slides', 'article_id')) {
                $legacySlides->whereNull('article_id');
            }

            $slides = $legacySlides
                ->get()
                ->map(fn (HeroSlide $slide): array => $slide->toHeroArray($locale))
                ->all();
        }

        if ($slides === []) {
            return;
        }

        foreach ($slides as $slide) {
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

            $cta = isset($slide['cta']) && is_array($slide['cta']) ? $slide['cta'] : [];

            if (($cta['action'] ?? null) === 'admission') {
                $ppdbSetting ??= $this->currentPpdbSetting();
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
                'focal_position' => $this->heroFocalPosition($slide['focal_position'] ?? null),
                'overlay_strength' => $this->heroOverlayStrength($slide['overlay_strength'] ?? null),
                'video_mime_type' => $this->heroVideoMimeType($mediaUrl),
                'cta' => $cta,
            ]);
        }

        if ($normalizedSlides === []) {
            return;
        }

        $combinedSlides = is_array($primarySlide)
            ? array_merge([$primarySlide], $normalizedSlides)
            : $normalizedSlides;
        $ppdbSetting ??= $this->currentPpdbSetting();
        $hero['slides'] = HomeHeroPresentation::decorate(
            $combinedSlides,
            $ppdbSetting,
            __('runtime.home.ppdb_cta_label'),
        );
        $view->with('hero', $hero);
    }
}
