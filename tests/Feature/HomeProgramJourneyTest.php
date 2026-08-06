<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one localized six-frame Program scroll journey', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);

        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="program"', false)
            ->assertSee('aria-labelledby="vision-program-title"', false)
            ->assertSee('data-program-origin-title', false)
            ->assertSee('data-program-origin-description', false)
            ->assertSee('data-program-sticky', false)
            ->assertSee('data-program-viewport', false)
            ->assertSee('data-program-frames', false)
            ->assertSee('data-program-exit', false)
            ->assertDontSee('data-featured-program-card', false)
            ->assertDontSee('program-spotlight', false);

        $content = $response->getContent();
        preg_match('/<section[^>]+id="program".*?<\/section>/s', $content, $section);
        preg_match_all('/\sdata-program-frame(?:\s|>)/', $section[0] ?? '', $frames);
        preg_match_all('/\sdata-program-rail-item(?:\s|>)/', $section[0] ?? '', $railItems);

        expect(count($frames[0]))->toBe(6)
            ->and(count($railItems[0]))->toBe(6)
            ->and(substr_count($section[0] ?? '', 'images.unsplash.com'))->toBe(6)
            ->and(substr_count($content, 'id="vision-program-title"'))->toBe(1)
            ->and(substr_count($content, 'data-program-origin-title'))->toBe(1)
            ->and(substr_count($content, 'data-program-origin-description'))->toBe(1)
            ->and($section[0] ?? '')->not->toContain('<button');
        expect($content)->not->toContain('href="/ppdb"');
    }
});

it('keeps Program native-target visual smoothing isolated', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/program-journey/controller.js'));
    $motion = file_get_contents(resource_path('js/surfaces/home/program-journey/motion.js'));
    $geometry = file_get_contents(resource_path('js/surfaces/home/program-journey/geometry.js'));
    $base = file_get_contents(resource_path('css/pages/welcome/program-journey/base.css'));
    $rail = file_get_contents(resource_path('css/pages/welcome/program-journey/rail.css'));

    expect($controller)
        ->toContain('createVisualScrollEngine')
        ->toContain('geometry.mediaPosition(current)')
        ->toContain('descriptionSlot.appendChild(description)')
        ->not->toContain('moveWithFlip(description')
        ->not->toContain('snapNow')
        ->and($motion)
        ->toContain('lerp(current, target, .08)')
        ->not->toContain('snapTimer')
        ->not->toContain('window.scrollTo')
        ->not->toContain('projected')
        ->and($geometry)
        ->toContain('--program-scroll-distance')
        ->toContain('mediaPosition')
        ->not->toContain('anchors')
        ->not->toContain('documentTarget')
        ->and($base)
        ->toContain('position: sticky')
        ->toContain('height: var(--program-track-height, 600dvh)')
        ->and($rail)
        ->toContain('.program-journey__rail:hover li span')
        ->toContain('.program-journey__rail:focus-within li span')
        ->not->toContain('cursor: pointer');
});
