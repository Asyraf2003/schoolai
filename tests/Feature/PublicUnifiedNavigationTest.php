<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps the same education gallery and article mega menus on every public page', function (): void {
    foreach (['home', 'ppdb', 'artikel', 'galeri'] as $routeName) {
        $response = $this
            ->withSession(['locale' => 'id'])
            ->get(route($routeName));

        $response
            ->assertOk()
            ->assertSee('nav-shell', false)
            ->assertSee('data-nav-mega', false)
            ->assertSee('data-nav-roll="main"', false)
            ->assertSee('data-nav-roll="sub"', false)
            ->assertDontSee('data-nav-mega-video', false)
            ->assertDontSee('youtube-nocookie.com', false)
            ->assertSee('Pendidikan')
            ->assertSee('Galeri')
            ->assertSee('Artikel')
            ->assertSee('Kegiatan')
            ->assertSee('Prestasi')
            ->assertSee('Program')
            ->assertSee('Pendidikan');

        $content = $response->getContent();

        expect(substr_count($content, 'data-nav-mega'))->toBeGreaterThanOrEqual(3)
            ->and(substr_count($content, 'data-nav-roll="main"'))->toBeGreaterThanOrEqual(12)
            ->and(substr_count($content, 'data-nav-roll="sub"'))->toBeGreaterThanOrEqual(24);
    }
});

it('mounts the mobile navigation layer after the fixed header', function (): void {
    $response = $this
        ->withSession(['locale' => 'id'])
        ->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('id="desktopNavMenu"', false)
        ->assertSee('data-mobile-navigation-layer', false)
        ->assertDontSee('id="navOverlay"', false);

    $content = $response->getContent();
    $headerEnd = strpos($content, '</header>');
    $mobileLayer = strpos($content, 'data-mobile-navigation-layer');

    expect(substr_count($content, 'id="navMenu"'))->toBe(1)
        ->and($headerEnd)->not->toBeFalse()
        ->and($mobileLayer)->not->toBeFalse()
        ->and($mobileLayer)->toBeGreaterThan($headerEnd);
});

it('keeps Arabic labels plain while preserving glyph room and smooth header state', function (): void {
    $rollStyles = file_get_contents(resource_path(
        'views/partials/site-navbar/styles/mega-roll.blade.php'
    ));
    $rollScript = file_get_contents(resource_path(
        'views/partials/site-navbar/mega-roll-script.blade.php'
    ));
    $heroStyles = file_get_contents(resource_path(
        'css/pages/welcome-hero/009-welcome-hero-cascade-009.css'
    ));
    $navigationState = file_get_contents(resource_path(
        'js/pages/welcome/navigation-state.js'
    ));

    expect($rollStyles)
        ->toContain('html[dir="rtl"] .nav-shell [data-nav-roll="main"]')
        ->toContain('line-height: 1.32')
        ->toContain('padding-block-end: 0.08em')
        ->not->toContain('.nav-roll--arabic')
        ->not->toContain('clip-path: inset(0 0 0 100%)')
        ->and($rollScript)
        ->toContain("root.dataset.navRollDisabled = 'arabic'")
        ->toContain('if (arabic)')
        ->not->toContain('function playArabicSweep')
        ->not->toContain("label.classList.add('nav-roll--arabic')")
        ->not->toContain('webkitClipPath')
        ->and($heroStyles)
        ->toContain('.nav-shell .navbar::before')
        ->toContain('opacity 420ms cubic-bezier(0.22, 1, 0.36, 1)')
        ->toContain('box-shadow 420ms cubic-bezier(0.22, 1, 0.36, 1)')
        ->and($navigationState)
        ->toContain('navbarScrolled ? 24 : 48')
        ->toContain('window.requestAnimationFrame(runNavigationUpdate)')
        ->toContain("window.addEventListener('scroll', requestNavigationUpdate, { passive: true })");
});
