<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses a static about poster and intent-loads the R2 modal video', function (): void {
    $url = (string) config('media.homepage_about_video_url');
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('data-about-video-open', false)
        ->assertSee('data-about-video-poster', false)
        ->assertSee('data-about-video-modal', false)
        ->assertSee('data-about-video-player', false)
        ->assertDontSee('data-about-video-preview', false)
        ->assertDontSee('data-about-preview-start=', false)
        ->assertDontSee('data-about-preview-end=', false)
        ->assertSee('preload="none"', false)
        ->assertSee('playsinline', false)
        ->assertSee($url, false);

    $section = file_get_contents(resource_path('views/home/sections/vision-mission.blade.php'));
    $modal = file_get_contents(resource_path('js/pages/welcome/about-video-modal.js'));
    $timeline = file_get_contents(resource_path('js/surfaces/home/vision-story/timeline.js'));
    $css = file_get_contents(resource_path('css/pages/welcome-vision-waapi/about-video.css'));

    expect($section)
        ->toContain('data-about-video-poster')
        ->toContain('data-about-video-src="{{ config(\'media.homepage_about_video_url\') }}"')
        ->toContain('preload="none"')
        ->not->toContain('data-about-video-preview')
        ->not->toContain('data-about-preview-start')
        ->not->toContain('data-about-preview-end')
        ->and($modal)
        ->toContain('player.src = playerSource')
        ->toContain('player.load()')
        ->toContain('hydratePlayer();')
        ->toContain('modal.showModal()')
        ->toContain('player.muted = false')
        ->not->toContain('IntersectionObserver')
        ->not->toContain('currentTime')
        ->not->toContain('preview.')
        ->and($css)
        ->toContain('inset-block-start: 50%')
        ->toContain('inset-inline-start: 50%')
        ->and($timeline)
        ->toContain("visual.querySelector('[data-vision-art]')")
        ->not->toContain("visual.querySelector('img')");
});
