<?php

namespace App\Providers;

use App\Http\Controllers\Admin\HeroSlideAdminController;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\PpdbSetting;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

final class HeroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->routesAreCached()) {
            $this->registerAdminRoutes();
        }

        ViewFacade::composer('welcome', function (View $view): void {
            $this->injectDatabaseHero($view);
        });
    }

    private function registerAdminRoutes(): void
    {
        Route::middleware([
            'web',
            'auth',
            'active.account',
            'admin',
            'admin.locale',
        ])->group(function (): void {
            Route::get('/admin/hero', [HeroSlideAdminController::class, 'index'])
                ->name('admin.hero');

            Route::get('/admin/hero/create', [HeroSlideAdminController::class, 'create'])
                ->name('admin.hero.create');

            Route::post('/admin/hero', [HeroSlideAdminController::class, 'store'])
                ->name('admin.hero.store');

            Route::get('/admin/hero/{heroSlide}/edit', [HeroSlideAdminController::class, 'edit'])
                ->name('admin.hero.edit');

            Route::put('/admin/hero/{heroSlide}', [HeroSlideAdminController::class, 'update'])
                ->name('admin.hero.update');

            Route::delete('/admin/hero/{heroSlide}', [HeroSlideAdminController::class, 'destroy'])
                ->name('admin.hero.destroy');

            Route::patch('/admin/hero/{heroSlide}/toggle', [HeroSlideAdminController::class, 'toggle'])
                ->name('admin.hero.toggle');

            Route::patch('/admin/hero/{heroSlide}/move-up', [HeroSlideAdminController::class, 'moveUp'])
                ->name('admin.hero.move-up');

            Route::patch('/admin/hero/{heroSlide}/move-down', [HeroSlideAdminController::class, 'moveDown'])
                ->name('admin.hero.move-down');
        });
    }

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

        $hero['slides'] = $normalizedSlides;
        $view->with('hero', $hero);
    }

    /** @return array<int, array<string, mixed>> */
    private function articleHeroSlides(string $locale): array
    {
        if (! Schema::hasTable('articles')) {
            return [];
        }

        if (
            Schema::hasTable('hero_slides')
            && Schema::hasColumn('hero_slides', 'article_id')
        ) {
            $placements = HeroSlide::query()
                ->with('article')
                ->activeOrdered()
                ->whereNotNull('article_id')
                ->get()
                ->filter(fn (HeroSlide $slide): bool => $slide->article?->isPubliclyVisibleNow() === true)
                ->map(fn (HeroSlide $slide): array => $slide->toHeroArray($locale))
                ->values()
                ->all();

            if ($placements !== []) {
                return $placements;
            }
        }

        return Article::query()
            ->latestPublished()
            ->limit(4)
            ->get()
            ->map(fn (Article $article): array => $this->articleToHeroArray($article, $locale))
            ->all();
    }

    /** @return array<string, mixed> */
    private function articleToHeroArray(Article $article, string $locale): array
    {
        $tag = collect($article->tags ?? [])
            ->first(fn (mixed $value): bool => is_string($value) && trim($value) !== '');

        return [
            'type' => 'image',
            'media' => $article->thumbnail_url ?: Article::PLACEHOLDER_THUMBNAIL,
            'poster' => $article->thumbnail_url ?: Article::PLACEHOLDER_THUMBNAIL,
            'media_alt' => $article->titleForLocale($locale),
            'eyebrow' => is_string($tag) && trim($tag) !== ''
                ? trim($tag)
                : $this->articleEyebrow($locale),
            'title' => $article->titleForLocale($locale),
            'description' => $article->descriptionForLocale($locale),
            'cta' => [
                'label' => $this->articleCtaLabel($locale),
                'href' => $article->linkForLocale($locale),
                'action' => 'link',
            ],
            'focal_position' => 'center center',
            'overlay_strength' => 0.52,
            'article_id' => $article->getKey(),
        ];
    }

    private function articleEyebrow(string $locale): string
    {
        return match ($locale) {
            'ar' => 'أحدث المقالات',
            'en' => 'Latest story',
            default => 'Artikel Terbaru',
        };
    }

    private function articleCtaLabel(string $locale): string
    {
        return match ($locale) {
            'ar' => 'اقرأ المقال',
            'en' => 'Read article',
            default => 'Baca Artikel',
        };
    }

    private function publicAssetUrl(mixed $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return PublicUrl::normalize($path);
        }

        $relativePath = ltrim($path, '/');

        if (str_starts_with($relativePath, 'storage/')) {
            return asset($relativePath);
        }

        if (! file_exists(public_path($relativePath))) {
            return null;
        }

        return asset($relativePath);
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
            PATHINFO_EXTENSION,
        ));

        return match ($extension) {
            'webm' => 'video/webm',
            'ogv', 'ogg' => 'video/ogg',
            default => 'video/mp4',
        };
    }

    private function currentPpdbSetting(): PpdbSetting
    {
        if (! Schema::hasTable('ppdb_settings')) {
            return new PpdbSetting([
                'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
                'is_active' => true,
            ]);
        }

        return PpdbSetting::query()->first() ?? new PpdbSetting([
            'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
            'is_active' => true,
        ]);
    }
}
