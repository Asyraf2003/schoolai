<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one localized Program journey with seven full-screen frames', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);

        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="program"', false)
            ->assertSee('aria-labelledby="program-journey-title"', false)
            ->assertSee('data-program-intro-frame', false)
            ->assertSee('data-program-intro-guide', false)
            ->assertSee('data-program-title', false)
            ->assertSee('data-program-description', false)
            ->assertSee('data-program-sticky', false)
            ->assertSee('data-program-frames', false)
            ->assertSee('data-program-exit', false)
            ->assertDontSee('data-program-origin', false)
            ->assertDontSee('data-vision-program', false)
            ->assertDontSee('data-program-curtain', false)
            ->assertDontSee('<figcaption', false);

        $content = $response->getContent();
        preg_match('/<section[^>]+id="program".*?<\/section>/s', $content, $section);
        preg_match_all('/\sdata-program-frame(?:\s|>)/', $section[0] ?? '', $frames);
        preg_match_all('/class="program-journey__anchor"/', $section[0] ?? '', $anchors);
        preg_match_all('/\sdata-program-rail-item(?:\s|>)/', $section[0] ?? '', $railItems);

        expect(count($frames[0]))->toBe(6)
            ->and(count($anchors[0]))->toBe(6)
            ->and(count($railItems[0]))->toBe(6)
            ->and(substr_count($section[0] ?? '', 'images.unsplash.com'))->toBe(6)
            ->and(substr_count($section[0] ?? '', 'href="#program-scroll-'))->toBe(6)
            ->and(substr_count($content, 'id="program-journey-title"'))->toBe(1)
            ->and($section[0] ?? '')->not->toContain('00 / 00')
            ->and($section[0] ?? '')->not->toContain('<button');
    }
});

it('shares one desktop pin while keeping Program media and copy locally owned', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/program-journey/controller.js'));
    $integration = file_get_contents(resource_path('js/surfaces/home/program-journey/integration.js'));
    $motion = file_get_contents(resource_path('js/surfaces/home/program-journey/motion.js'));
    $geometry = file_get_contents(resource_path('js/surfaces/home/program-journey/geometry.js'));
    $base = file_get_contents(resource_path('css/pages/welcome/program-journey/base.css'));
    $hud = file_get_contents(resource_path('css/pages/welcome/program-journey/hud.css'));

    expect($controller)
        ->toContain('createProgramIntegration(root, reducedMotion)')
        ->toContain('programIndex(current, travelDirection)')
        ->toContain("addEventListener('vision:layout', onVisionLayout)")
        ->toContain("behavior: 'instant'")
        ->toContain('visualScroll.sync(geometry.localPosition(index))')
        ->not->toContain('handoffIn')
        ->not->toContain('handoffOut')
        ->and($integration)
        ->toContain('visionTrack.appendChild(root)')
        ->toContain('has-integrated-program')
        ->toContain("root.classList.add('is-integrated')")
        ->and($motion)
        ->toContain('lerp(current, target, .08)')
        ->toContain('current = target')
        ->not->toContain('snapTimer')
        ->not->toContain('projected')
        ->and($geometry)
        ->toContain('story.dataset.programStoryTravel')
        ->toContain('Math.floor(framePosition)')
        ->toContain('Math.ceil(framePosition)')
        ->toContain('documentPosition')
        ->and($base)
        ->toContain('.vision-paper.has-integrated-program')
        ->toContain('.program-journey.is-integrated')
        ->not->toContain('.program-journey__curtain')
        ->and($hud)
        ->toContain('position: absolute')
        ->not->toContain('position: fixed');
});
