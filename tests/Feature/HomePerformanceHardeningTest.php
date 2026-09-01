<?php

it('keeps homepage interaction styles out of the first paint path', function (): void {
    $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
    $head = file_get_contents(resource_path('views/partials/site-head-meta.blade.php'));
    $navbar = file_get_contents(resource_path('views/partials/site-navbar.blade.php'));
    $sharedHero = file_get_contents(resource_path('css/pages/welcome-hero.css'));
    $homeHero = file_get_contents(resource_path('css/pages/welcome-home-hero.css'));
    $latinType = file_get_contents(resource_path('css/pages/welcome-home-type-latin.css'));
    $latinAdapter = file_get_contents(resource_path('css/public-latin-inter.css'));
    $arabicType = file_get_contents(resource_path('css/pages/welcome-home-type-arabic.css'));
    $blade = file_get_contents(resource_path('views/welcome.blade.php'));
    $vite = file_get_contents(base_path('vite.config.js'));

    expect($provider)
        ->toContain("'resources/css/pages/welcome-login-perspective.css'")
        ->not->toContain("'resources/css/pages/welcome-mega-menu.css'")
        ->and($navbar)
        ->toContain("Vite::content('resources/css/pages/welcome-mega-menu.css')")
        ->and($sharedHero)
        ->not->toContain('@import "./welcome-hero-visual.css";')
        ->and($homeHero)
        ->toContain('@import "./welcome-hero.css";')
        ->toContain('@import "./welcome-hero-visual.css";')
        ->and($latinType)
        ->toContain('@import "../text-system.css";')
        ->toContain('@import "../public-latin-inter.css";')
        ->and($latinAdapter)
        ->toContain('font-display: optional;')
        ->not->toContain('font-display: swap;')
        ->and($arabicType)
        ->toContain('@import "../text-system.css";')
        ->toContain('@import "../arabic-typography.css";')
        ->and($blade)
        ->toContain("'resources/css/pages/welcome-home-hero.css'")
        ->toContain("'resources/css/pages/welcome-home-type-latin.css'")
        ->toContain("'resources/css/pages/welcome-home-type-arabic.css'")
        ->not->toContain("'resources/css/pages/welcome-hero.css'")
        ->not->toContain("'resources/css/text-system.css'")
        ->not->toContain("'resources/css/public-latin-inter.css'")
        ->not->toContain("'resources/css/arabic-typography.css'")
        ->and($head)
        ->not->toContain("'resources/css/pages/welcome-hero-visual.css'")
        ->and($vite)
        ->toContain("'resources/css/pages/welcome-home-hero.css'")
        ->toContain("'resources/css/pages/welcome-home-type-latin.css'")
        ->toContain("'resources/css/pages/welcome-home-type-arabic.css'")
        ->not->toContain("'resources/css/pages/welcome-hero-visual.css'");
});

it('inlines asset-safe homepage critical and locale typography styles without changing cascade order', function (): void {
    $blade = file_get_contents(resource_path('views/welcome.blade.php'));
    $foundationInline = "Vite::content('resources/css/pages/welcome-critical.css')";
    $legacyEntry = "'resources/css/pages/welcome.css'";
    $heroInline = "Vite::content('resources/css/pages/welcome-home-hero.css')";
    $visionEntry = "'resources/css/pages/welcome-vision-waapi.css'";
    $latinTypeInline = "Vite::content('resources/css/pages/welcome-home-type-latin.css')";
    $arabicTypeInline = "Vite::content('resources/css/pages/welcome-home-type-arabic.css')";
    $latinTypeDevEntry = "@vite('resources/css/pages/welcome-home-type-latin.css')";
    $arabicTypeDevEntry = "@vite('resources/css/pages/welcome-home-type-arabic.css')";
    $editorialEntry = "'resources/css/pages/welcome-editorial-headings.css'";

    expect($blade)
        ->not->toContain("Vite::asset('resources/fonts/inter/inter-latin-variable.woff2')")
        ->not->toContain('data-home-critical-font="inter"')
        ->toContain("app()->environment('production')")
        ->toContain('data-home-critical-style="foundation"')
        ->toContain($foundationInline)
        ->toContain('data-home-critical-style="hero"')
        ->toContain($heroInline)
        ->toContain('data-home-critical-style="type"')
        ->toContain($latinTypeInline)
        ->toContain($arabicTypeInline)
        ->toContain($latinTypeDevEntry)
        ->toContain($arabicTypeDevEntry);

    expect(strpos($blade, $foundationInline))
        ->toBeLessThan(strpos($blade, $legacyEntry))
        ->and(strpos($blade, $legacyEntry))
        ->toBeLessThan(strpos($blade, $heroInline))
        ->and(strpos($blade, $heroInline))
        ->toBeLessThan(strpos($blade, $visionEntry))
        ->and(strpos($blade, $visionEntry))
        ->toBeLessThan(strpos($blade, $arabicTypeInline))
        ->and(strpos($blade, $visionEntry))
        ->toBeLessThan(strpos($blade, $latinTypeInline))
        ->and(strpos($blade, $arabicTypeInline))
        ->toBeLessThan(strpos($blade, $editorialEntry))
        ->and(strpos($blade, $latinTypeInline))
        ->toBeLessThan(strpos($blade, $editorialEntry));
});

it('keeps the shared public foundation single-owned by the critical entry', function (): void {
    $critical = file_get_contents(resource_path('css/pages/welcome-critical.css'));
    $legacy = file_get_contents(resource_path('css/pages/welcome.css'));
    $publicLayout = file_get_contents(resource_path('views/layouts/public.blade.php'));
    $foundation = '001-sekolah-ceria-nusantara-stylesheet-struktur-file-1-r.css';

    expect($critical)
        ->toContain($foundation)
        ->and($legacy)
        ->not->toContain($foundation)
        ->and($publicLayout)
        ->toContain("'resources/css/pages/welcome-critical.css'")
        ->toContain("'resources/css/pages/welcome.css'");

    expect(strpos($publicLayout, "'resources/css/pages/welcome-critical.css'"))
        ->toBeLessThan(strpos($publicLayout, "'resources/css/pages/welcome.css'"));
});

it('keeps the structure audit explicit while npm lifecycle scripts stay disabled', function (): void {
    $package = file_get_contents(base_path('package.json'));
    $npmrc = file_get_contents(base_path('.npmrc'));

    expect($npmrc)
        ->toContain('ignore-scripts=true')
        ->and($package)
        ->toContain('"build": "vite build"')
        ->toContain('"check:structure": "node scripts/verify-source-structure.mjs"')
        ->not->toContain('"prebuild"');
});

it('avoids known synchronous layout restart patterns on the homepage', function (): void {
    $editorial = file_get_contents(resource_path(
        'js/pages/welcome-editorial-headings.js'
    ));
    $navigation = file_get_contents(resource_path(
        'js/pages/welcome/navigation-state.js'
    ));

    expect($editorial)
        ->not->toContain('offsetWidth')
        ->toContain('replayFrame')
        ->toContain('window.requestAnimationFrame(function ()')
        ->and($navigation)
        ->toContain('function resolveActiveNavLink()')
        ->toContain('if (handleNavbarScroll())')
        ->toContain('requestNavigationUpdate();');
});

it('keeps gallery body copy contrast hardened across rotating backgrounds', function (): void {
    $styles = file_get_contents(resource_path(
        'css/pages/welcome-depth-gallery/base.css'
    ));

    expect($styles)
        ->toContain('--gallery-story-muted: #1c281f;')
        ->not->toContain('--gallery-story-muted: rgba(16, 24, 18, 0.7);');
});

it('keeps the homepage request path free from repeat data rebuilds and schema probes', function (): void {
    $page = file_get_contents(app_path(
        'Http/Controllers/Concerns/BuildsHomePage.php'
    ));
    $gallery = file_get_contents(app_path(
        'Http/Controllers/Concerns/BuildsHomeArticlesAndGallery.php'
    ));

    expect($page)
        ->toContain('private array $resolvedHomeDataByLocale = [];')
        ->toContain('$locale = app()->getLocale();')
        ->toContain('if (isset($this->resolvedHomeDataByLocale[$locale]))')
        ->toContain('return $this->resolvedHomeDataByLocale[$locale] = $this->canonicalizeHomeMedia(')
        ->and($gallery)
        ->not->toContain('Schema::hasTable')
        ->not->toContain('Illuminate\\Support\\Facades\\Schema');
});
