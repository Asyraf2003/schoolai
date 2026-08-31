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

it('activates deferred homepage styles that are emitted later in the body', function (): void {
    $blade = file_get_contents(resource_path('views/welcome.blade.php'));
    $languageFlag = file_get_contents(resource_path('views/partials/language-flag.blade.php'));

    expect($blade)
        ->toContain("document.querySelectorAll('link[data-home-deferred-style]')")
        ->toContain("document.addEventListener('DOMContentLoaded', activateDeferredStyles, { once: true });")
        ->and($languageFlag)
        ->toContain("@vite('resources/css/pages/welcome-mega-menu.css')");
});
