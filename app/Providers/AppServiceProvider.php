<?php

namespace App\Providers;

use App\View\Composers\AdminArticleFormComposer;
use App\View\Composers\AdminArticleIndexComposer;
use App\View\Composers\AdminGalleryFormComposer;
use App\View\Composers\AdminGalleryIndexComposer;
use App\View\Composers\AdminGallerySectionFormComposer;
use App\View\Composers\AdminGalleryShowComposer;
use App\View\Composers\AdminLayoutComposer;
use App\View\Composers\AdminPlaceholderComposer;
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
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
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
        $this->configureDeferredHomeStyles();

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

        View::composer('admin.articles.form', AdminArticleFormComposer::class);
        View::composer('admin.articles.index', AdminArticleIndexComposer::class);
        View::composer('admin.gallery.form', AdminGalleryFormComposer::class);
        View::composer('admin.gallery.index', AdminGalleryIndexComposer::class);
        View::composer('admin.gallery.page-sections.form', AdminGallerySectionFormComposer::class);
        View::composer('admin.gallery.show', AdminGalleryShowComposer::class);
        View::composer('admin.placeholder', AdminPlaceholderComposer::class);
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

    private function configureDeferredHomeStyles(): void
    {
        $deferredHomeStyles = [
            'resources/css/pages/welcome.css',
            'resources/css/pages/welcome-login-perspective.css',
            'resources/css/pages/welcome-mega-menu.css',
            'resources/css/pages/welcome-scroll-reveal.css',
            'resources/css/pages/welcome-vision-waapi.css',
            'resources/css/pages/welcome-values-story.css',
            'resources/css/pages/welcome-depth-gallery.css',
            'resources/css/pages/welcome-article-showcase.css',
            'resources/css/pages/welcome-editorial-headings.css',
            'resources/css/pages/welcome-editorial-description-desktop.css',
        ];

        Vite::useStyleTagAttributes(
            static function (
                ?string $src,
                string $url,
                ?array $chunk,
                ?array $manifest,
            ) use ($deferredHomeStyles): array {
                $deferred = request()->routeIs('home')
                    && in_array($src, $deferredHomeStyles, true);

                return [
                    'media' => $deferred ? 'print' : false,
                    'data-home-deferred-style' => $deferred,
                ];
            }
        );
    }

}
