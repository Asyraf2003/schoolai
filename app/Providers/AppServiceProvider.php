<?php

namespace App\Providers;

use App\View\Composers\AdminGalleryIndexComposer;
use App\View\Composers\AdminLayoutComposer;
use App\View\Composers\AdminPpdbEditComposer;
use App\View\Composers\EditorialSectionHeadingComposer;
use App\View\Composers\GalleryWallCardComposer;
use App\View\Composers\HomeArticlesComposer;
use App\View\Composers\HomeGalleryComposer;
use App\View\Composers\HomeHeroComposer;
use App\View\Composers\HomePageComposer;
use App\View\Composers\HomeProgramComposer;
use App\View\Composers\HomeValuesComposer;
use App\View\Composers\HomeVisionMissionComposer;
use App\View\Composers\LanguageFlagComposer;
use App\View\Composers\PublicArticleComposer;
use App\View\Composers\PublicArticleDetailComposer;
use App\View\Composers\PublicGalleryComposer;
use App\View\Composers\PublicPpdbComposer;
use App\View\Composers\SiteFooterComposer;
use App\View\Composers\SiteHeadMetaComposer;
use App\View\Composers\SiteNavbarComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('google-oauth', function (Request $request): array {
            $sessionId = $request->session()->getId();
            $key = app('encrypter')->getKey();
            $sourceHash = hash_hmac('sha256', (string) $request->ip(), $key);
            $sessionHash = hash_hmac('sha256', $sessionId, $key);

            return [
                Limit::perMinute(20)
                    ->by('google-oauth:source:'.$sourceHash),
                Limit::perMinute(10)
                    ->by('google-oauth:session:'.$sessionHash),
            ];
        });

        Route::middleware('web')->group(base_path('routes/testimonials.php'));

        View::composer('admin.gallery.index', AdminGalleryIndexComposer::class);
        View::composer('admin.ppdb.edit', AdminPpdbEditComposer::class);
        View::composer('home.partials.editorial-section-heading', EditorialSectionHeadingComposer::class);
        View::composer('home.sections.articles', HomeArticlesComposer::class);
        View::composer([
            'home.sections.gallery',
            'home.sections.gallery-depth',
        ], HomeGalleryComposer::class);
        View::composer('home.sections.hero', HomeHeroComposer::class);
        View::composer('home.sections.featured-programs', HomeProgramComposer::class);
        View::composer('home.sections.school-values', HomeValuesComposer::class);
        View::composer('home.sections.vision-mission', HomeVisionMissionComposer::class);
        View::composer('layouts.admin', AdminLayoutComposer::class);
        View::composer('partials.language-flag', LanguageFlagComposer::class);
        View::composer('partials.site-footer', SiteFooterComposer::class);
        View::composer('partials.site-head-meta', SiteHeadMetaComposer::class);
        View::composer('partials.site-navbar', SiteNavbarComposer::class);
        View::composer('pages.artikel', PublicArticleComposer::class);
        View::composer('pages.artikel-detail', PublicArticleDetailComposer::class);
        View::composer('pages.galeri', PublicGalleryComposer::class);
        View::composer('pages.partials.gallery-wall-card', GalleryWallCardComposer::class);
        View::composer('pages.ppdb', PublicPpdbComposer::class);
        View::composer('welcome', HomePageComposer::class);
    }
}
