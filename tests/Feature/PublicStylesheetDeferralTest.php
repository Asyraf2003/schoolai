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

it('keeps mega menu final cascade owned by the navbar after its inline layout styles', function (): void {
    $navbar = file_get_contents(resource_path('views/partials/site-navbar.blade.php'));
    $homeBlade = file_get_contents(resource_path('views/welcome.blade.php'));
    $publicLayout = file_get_contents(resource_path('views/layouts/public.blade.php'));
    $languageFlag = file_get_contents(resource_path('views/partials/language-flag.blade.php'));
    $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
    $desktopLayout = "@include('partials.site-navbar.styles.desktop-mega-layout')";
    $megaEntry = 'resources/css/pages/welcome-mega-menu.css';
    $megaInline = "Vite::content('{$megaEntry}')";
    $headerInclude = "@include('partials.site-navbar.header')";

    expect($navbar)
        ->toContain($megaInline)
        ->toContain("@vite('{$megaEntry}')")
        ->toContain('data-site-navbar-style="mega"')
        ->and($homeBlade)
        ->not->toContain($megaEntry)
        ->and($publicLayout)
        ->not->toContain($megaEntry)
        ->and($languageFlag)
        ->not->toContain($megaEntry)
        ->and($provider)
        ->not->toContain($megaEntry);

    expect(strpos($navbar, $desktopLayout))
        ->toBeLessThan(strpos($navbar, $megaInline))
        ->and(strpos($navbar, $megaInline))
        ->toBeLessThan(strpos($navbar, $headerInclude));
});
