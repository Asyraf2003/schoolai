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

it('keeps the shared mega menu stylesheet owned by document heads', function (): void {
    $homeBlade = file_get_contents(resource_path('views/welcome.blade.php'));
    $publicLayout = file_get_contents(resource_path('views/layouts/public.blade.php'));
    $languageFlag = file_get_contents(resource_path('views/partials/language-flag.blade.php'));
    $heroEntry = "'resources/css/pages/welcome-home-hero.css'";
    $sharedHeroEntry = "'resources/css/pages/welcome-hero.css'";
    $megaEntry = "'resources/css/pages/welcome-mega-menu.css'";

    expect($homeBlade)
        ->toContain($megaEntry)
        ->toContain("document.querySelectorAll('link[data-home-deferred-style]')")
        ->and($publicLayout)
        ->toContain($megaEntry)
        ->and($languageFlag)
        ->not->toContain("@vite('resources/css/pages/welcome-mega-menu.css')");

    expect(strpos($homeBlade, $heroEntry))
        ->toBeLessThan(strpos($homeBlade, $megaEntry))
        ->and(strpos($publicLayout, $sharedHeroEntry))
        ->toBeLessThan(strpos($publicLayout, $megaEntry));
});
