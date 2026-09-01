<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps the unified education gallery and article mega menu contract on every public page', function (): void {
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
            ->assertSee('Program');

        $content = $response->getContent();
        $subRollLabelCount = substr_count($content, '<strong data-nav-roll="sub"');
        $megaLinkCount = substr_count($content, 'class="nav-mega__link"');

        expect(substr_count($content, 'data-nav-mega'))->toBeGreaterThanOrEqual(3)
            ->and(substr_count($content, 'data-nav-roll="main"'))->toBeGreaterThanOrEqual(12)
            ->and($subRollLabelCount)->toBeGreaterThan(0)
            ->and($subRollLabelCount)->toBe($megaLinkCount)
            ->and($content)->toContain('#galeri');
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

it('protects Arabic desktop glyph tails and smooths the hero header state', function (): void {
    $rollStyles = file_get_contents(resource_path(
        'views/partials/site-navbar/styles/mega-roll.blade.php'
    ));
    $heroStyles = file_get_contents(resource_path(
        'css/pages/welcome-hero/009-welcome-hero-cascade-009.css'
    ));
    $navigationState = file_get_contents(resource_path(
        'js/pages/welcome/navigation-state.js'
    ));

    expect($rollStyles)
        ->toContain('html[dir="rtl"] .nav-shell [data-nav-roll="main"]')
        ->toContain('line-height: 1.28')
        ->toContain('padding-block-end: 0.2em')
        ->and($heroStyles)
        ->toContain('.nav-shell .navbar::before')
        ->toContain('opacity 420ms cubic-bezier(0.22, 1, 0.36, 1)')
        ->toContain('box-shadow 420ms cubic-bezier(0.22, 1, 0.36, 1)')
        ->and($navigationState)
        ->toContain('navbarScrolled ? 24 : 48')
        ->toContain('window.requestAnimationFrame(runNavigationUpdate)')
        ->toContain("window.addEventListener('scroll', requestNavigationUpdate, { passive: true })");
});
