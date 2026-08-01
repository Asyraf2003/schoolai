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
