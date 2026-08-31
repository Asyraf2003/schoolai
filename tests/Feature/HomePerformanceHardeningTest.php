<?php

it('keeps homepage interaction styles out of the first paint path', function (): void {
    $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
    $head = file_get_contents(resource_path('views/partials/site-head-meta.blade.php'));
    $hero = file_get_contents(resource_path('css/pages/welcome-hero.css'));
    $vite = file_get_contents(base_path('vite.config.js'));

    expect($provider)
        ->toContain("'resources/css/pages/welcome-login-perspective.css'")
        ->toContain("'resources/css/pages/welcome-mega-menu.css'")
        ->and($hero)
        ->toContain('@import "./welcome-hero-visual.css";')
        ->and($head)
        ->not->toContain("'resources/css/pages/welcome-hero-visual.css'")
        ->and($vite)
        ->not->toContain("'resources/css/pages/welcome-hero-visual.css'");
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
