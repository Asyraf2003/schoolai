<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('defers shared welcome styles only on the homepage', function (): void {
    $home = $this->get(route('home'))->assertOk()->getContent();

    expect($home)
        ->toContain('data-home-deferred-style')
        ->toContain('media="print"');

    foreach (['ppdb', 'artikel', 'galeri'] as $routeName) {
        $content = $this->get(route($routeName))->assertOk()->getContent();

        expect($content)
            ->not->toContain('data-home-deferred-style')
            ->not->toContain('media="print"');
    }
});

it('keeps mega menu final cascade owned by the navbar through the proven Vite stylesheet path', function (): void {
    $navbar = file_get_contents(resource_path('views/partials/site-navbar.blade.php'));
    $homeBlade = file_get_contents(resource_path('views/welcome.blade.php'));
    $publicLayout = file_get_contents(resource_path('views/layouts/public.blade.php'));
    $languageFlag = file_get_contents(resource_path('views/partials/language-flag.blade.php'));
    $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
    $desktopLayout = "@include('partials.site-navbar.styles.desktop-mega-layout')";
    $megaEntry = 'resources/css/pages/welcome-mega-menu.css';
    $megaLoad = "@vite('{$megaEntry}')";
    $megaInline = "Vite::content('{$megaEntry}')";
    $headerInclude = "@include('partials.site-navbar.header')";

    expect($navbar)
        ->toContain('@once')
        ->toContain($megaLoad)
        ->not->toContain($megaInline)
        ->not->toContain('data-site-navbar-style="mega"')
        ->and($homeBlade)
        ->not->toContain($megaEntry)
        ->and($publicLayout)
        ->not->toContain($megaEntry)
        ->and($languageFlag)
        ->not->toContain($megaEntry)
        ->and($provider)
        ->not->toContain($megaEntry);

    expect(strpos($navbar, $desktopLayout))
        ->toBeLessThan(strpos($navbar, $megaLoad))
        ->and(strpos($navbar, $megaLoad))
        ->toBeLessThan(strpos($navbar, $headerInclude));
});

it('locks the approved full-viewport desktop mega menu geometry independently of asset timing', function (): void {
    $desktop = file_get_contents(resource_path(
        'views/partials/site-navbar/styles/desktop-mega-layout.blade.php'
    ));
    $canonical = file_get_contents(resource_path(
        'css/pages/welcome-mega-menu/001-homepage-mega-navigation-polish-desktop-full-width-u.css'
    ));

    foreach ([
        'position: fixed;',
        'inset-block-start: 77px;',
        'inset-inline: 0;',
        'width: 100vw;',
        'min-height: clamp(400px, 50vh, 540px);',
        'border-radius: 0;',
        'height: clamp(280px, 34vh, 360px);',
        'grid-template-columns: repeat(2, minmax(0, 1fr));',
    ] as $contract) {
        expect($desktop)->toContain($contract)
            ->and($canonical)->toContain($contract);
    }

    expect($desktop)
        ->toContain('.nav-shell .nav-login .nav-mega__panel')
        ->toContain('width: min(420px, calc(100vw - 48px));')
        ->toContain('.nav-shell .nav-login .nav-mega__links')
        ->toContain('grid-template-columns: 1fr;');
});
