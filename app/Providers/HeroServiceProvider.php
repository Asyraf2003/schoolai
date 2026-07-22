<?php

namespace App\Providers;

use App\Http\Controllers\Admin\HeroSlideAdminController;
use App\Models\HeroSlide;
use App\Models\PpdbSetting;
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
        if (! Schema::hasTable('hero_slides')) {
            return;
        }

        $slides = HeroSlide::query()->activeOrdered()->get();

        if ($slides->isEmpty()) {
            return;
        }

        $hero = $view->getData()['hero'] ?? [];
        $hero = is_array($hero) ? $hero : [];
        $fallbackImageUrl = is_string($hero['fallback_image_url'] ?? null)
            ? $hero['fallback_image_url']
            : null;
        $locale = app()->getLocale();
        $ppdbSetting = null;
        $normalizedSlides = [];

        foreach ($slides as $slideModel) {
            $slide = $slideModel->toHeroArray($locale);
            $type = in_array(($slide['type'] ?? null), ['image', 'video'], true)
                ? $slide['type']
                : 'image';
            $mediaUrl = $this->publicAssetUrl($slide['media'] ?? null);
            $posterUrl = $this->publicAssetUrl($slide['poster'] ?? null);
            $renderType = $type === 'video' && $mediaUrl !== null ? 'video' : 'image';

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
