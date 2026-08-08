<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one localized Program journey with seven full-screen frames', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);
        $response = $this->withSession(['locale' => $locale])->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="program"', false)
            ->assertSee('aria-labelledby="program-journey-title"', false)
            ->assertSee('data-program-intro-frame', false)
            ->assertSee('data-program-title', false)
            ->assertSee('data-program-description', false)
            ->assertSee('data-program-sticky', false)
            ->assertSee('data-program-frames', false)
            ->assertSee('data-program-exit', false)
            ->assertDontSee('data-program-origin', false)
            ->assertDontSee('data-vision-program', false)
            ->assertDontSee('data-program-curtain', false);

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
            ->and(substr_count($content, 'id="program-journey-title"'))->toBe(1);
    }
});

it('keeps Program independent below the About Vision Mission surface', function (): void {
    $integration = file_get_contents(resource_path('js/surfaces/home/program-journey/integration.js'));
    $geometry = file_get_contents(resource_path('js/surfaces/home/program-journey/geometry.js'));
    $base = file_get_contents(resource_path('css/pages/welcome/program-journey/base.css'));
    $welcome = file_get_contents(resource_path('views/welcome.blade.php'));

    expect($integration)
        ->toContain("root.classList.remove('is-integrated')")
        ->not->toContain('appendChild')
        ->not->toContain('visionTrack')
        ->and($geometry)
        ->not->toContain('programStoryTravel')
        ->not->toContain('track.scrollWidth - window.innerWidth')
        ->and($base)
        ->not->toContain('has-integrated-program')
        ->not->toContain('.program-journey.is-integrated')
        ->and(strpos($welcome, "@include('home.sections.vision-mission')"))
        ->toBeLessThan(strpos($welcome, "@include('home.sections.featured-programs')"));
});
