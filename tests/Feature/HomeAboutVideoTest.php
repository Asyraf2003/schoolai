<?php

it('streams the R2 about video lazily as a 30-50 second preview and intent-loaded modal', function (): void {
    $url = (string) config('media.homepage_about_video_url');
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('data-about-video-open', false)
        ->assertSee('data-about-video-preview', false)
        ->assertSee('data-about-video-modal', false)
        ->assertSee('data-about-video-player', false)
        ->assertSee('data-about-preview-start="30"', false)
        ->assertSee('data-about-preview-end="50"', false)
        ->assertSee('preload="none"', false)
        ->assertSee('autoplay', false)
        ->assertSee('muted', false)
        ->assertSee('playsinline', false)
        ->assertSee($url, false);

    $section = file_get_contents(resource_path('views/home/sections/vision-mission.blade.php'));
    $modal = file_get_contents(resource_path('js/pages/welcome/about-video-modal.js'));
    $timeline = file_get_contents(resource_path('js/surfaces/home/vision-story/timeline.js'));

    expect($section)
        ->toContain('data-about-video-src="{{ config(\'media.homepage_about_video_url\') }}"')
        ->toContain('data-about-preview-start="30"')
        ->toContain('data-about-preview-end="50"')
        ->toContain('preload="none"')
        ->not->toContain('<source src="{{ config(\'media.homepage_about_video_url\') }}"')
        ->and($modal)
        ->toContain('new IntersectionObserver')
        ->toContain('preview.src = previewSource')
        ->toContain('player.src = playerSource')
        ->toContain('hydratePlayer();')
        ->toContain('preview.currentTime >= end - 0.05')
        ->toContain('modal.showModal()')
        ->toContain('player.muted = false')
        ->and($timeline)
        ->toContain("visual.querySelector('[data-vision-art]')")
        ->not->toContain("visual.querySelector('img')");
});
