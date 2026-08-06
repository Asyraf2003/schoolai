<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one white intro frame and six full-screen Program media frames', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);

        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="program"', false)
            ->assertSee('data-program-intro-frame', false)
            ->assertSee('data-program-sticky', false)
            ->assertSee('data-program-frames', false)
            ->assertDontSee('data-program-curtain', false)
            ->assertDontSee('data-program-active-label', false)
            ->assertDontSee('data-program-active-count', false);

        $content = $response->getContent();
        preg_match('/<section[^>]+id="program".*?<\/section>/s', $content, $section);
        preg_match_all('/\sdata-program-frame(?:\s|>)/', $section[0] ?? '', $frames);
        preg_match_all('/\sdata-program-rail-item(?:\s|>)/', $section[0] ?? '', $railItems);
        preg_match_all('/\sid="program-scroll-[1-6]"/', $section[0] ?? '', $anchors);

        expect(count($frames[0]))->toBe(6)
            ->and(count($railItems[0]))->toBe(6)
            ->and(count($anchors[0]))->toBe(6)
            ->and(substr_count($section[0] ?? '', 'data-program-intro-frame'))->toBe(1)
            ->and(substr_count($section[0] ?? '', 'images.unsplash.com'))->toBe(6)
            ->and($section[0] ?? '')->not->toContain('program-journey__meta')
            ->and($section[0] ?? '')->not->toContain('data-program-label')
            ->and($section[0] ?? '')->toContain('--program-anchor-step: 2');
    }
});

it('uses one seven-frame track and exact rail anchor geometry', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/program-journey/controller.js'));
    $motion = file_get_contents(resource_path('js/surfaces/home/program-journey/motion.js'));
    $geometry = file_get_contents(resource_path('js/surfaces/home/program-journey/geometry.js'));
    $base = file_get_contents(resource_path('css/pages/welcome/program-journey/base.css'));
    $hud = file_get_contents(resource_path('css/pages/welcome/program-journey/hud.css'));

    expect($controller)
        ->toContain('geometry.trackPosition(current)')
        ->toContain('geometry.titleTransform(current)')
        ->not->toContain('curtain')
        ->not->toContain('data-program-active-count')
        ->not->toContain('padStart')
        ->not->toContain('window.scrollTo')
        ->and($motion)
        ->toContain('lerp(current, target, .08)')
        ->not->toContain('snapTimer')
        ->not->toContain('projected')
        ->and($geometry)
        ->toContain('(frameCount + 1) * step')
        ->toContain('frameCount * step')
        ->toContain('current < metrics.step * .98')
        ->not->toContain('entryProgress')
        ->and($base)
        ->toContain('program-frame--intro')
        ->toContain('calc(var(--program-anchor-step) * var(--program-step))')
        ->toContain('height: var(--program-track-height, 700dvh)')
        ->and($hud)
        ->not->toContain('program-journey__meta');
});
