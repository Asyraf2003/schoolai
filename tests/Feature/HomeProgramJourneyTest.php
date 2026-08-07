<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one localized Program journey with a visual handoff and six background frames', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);

        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="program"', false)
            ->assertSee('aria-labelledby="program-journey-title"', false)
            ->assertSee('data-program-handoff', false)
            ->assertSee('data-program-handoff-mission', false)
            ->assertSee('data-program-handoff-main', false)
            ->assertSee('data-program-handoff-thumb-one', false)
            ->assertSee('data-program-handoff-thumb-two', false)
            ->assertSee('data-program-backgrounds', false)
            ->assertSee('data-program-hud-title', false)
            ->assertSee('data-program-hud-description', false)
            ->assertSee('data-program-sticky', false)
            ->assertDontSee('class="program-journey__anchor"', false)
            ->assertDontSee('href="#program-scroll-', false)
            ->assertDontSee('data-program-exit', false)
            ->assertDontSee('data-program-curtain', false)
            ->assertDontSee('<figcaption', false);

        $content = $response->getContent();
        preg_match('/<section[^>]+id="program".*?<\/section>/s', $content, $section);
        preg_match_all('/\sdata-program-frame(?:\s|>)/', $section[0] ?? '', $frames);
        preg_match_all('/\sdata-program-rail-item(?:\s|>)/', $section[0] ?? '', $railItems);

        expect(count($frames[0]))->toBe(6)
            ->and(count($railItems[0]))->toBe(6)
            ->and(substr_count($section[0] ?? '', 'images.unsplash.com'))->toBe(6)
            ->and(substr_count($content, 'id="program-journey-title"'))->toBe(1)
            ->and($section[0] ?? '')->not->toContain('00 / 00')
            ->and($section[0] ?? '')->not->toContain('<button');
    }
});

it('keeps Program geometry local and native-scroll driven', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/program-journey/controller.js'));
    $motion = file_get_contents(resource_path('js/surfaces/home/program-journey/motion.js'));
    $geometry = file_get_contents(resource_path('js/surfaces/home/program-journey/geometry.js'));
    $visionController = file_get_contents(resource_path('js/surfaces/home/vision-story/controller.js'));
    $base = file_get_contents(resource_path('css/pages/welcome/program-journey/base.css'));

    expect($controller)
        ->not->toContain('createProgramIntegration')
        ->not->toContain('window.scrollTo')
        ->not->toContain('history.pushState')
        ->not->toContain("addEventListener('click'")
        ->not->toContain('vision:layout')
        ->not->toContain('program:layout')
        ->and(file_exists(resource_path('js/surfaces/home/program-journey/integration.js')))->toBeFalse()
        ->and($motion)
        ->toContain('lerp(current, target, .08)')
        ->toContain('current = target')
        ->not->toContain('snapTimer')
        ->not->toContain('projected')
        ->and($geometry)
        ->toContain('[.45, { x: 0, y: 0, w: 1920, h: 965 }]')
        ->toContain('[.60, { x: 320, y: 300, w: 1280, h: 350 }]')
        ->toContain("const loop = clamp((progress - .75) / .25)")
        ->not->toContain('documentPosition')
        ->not->toContain('programStoryTravel')
        ->and($visionController)
        ->not->toContain('programStoryTravel')
        ->not->toContain('program:layout')
        ->and($base)
        ->not->toContain('has-integrated-program')
        ->not->toContain('.program-journey.is-integrated');
});
