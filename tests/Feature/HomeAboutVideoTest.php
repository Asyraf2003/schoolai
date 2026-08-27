<?php

it('uses the R2 about video as an autoplay preview with a sound modal', function (): void {
    $url = (string) config('media.homepage_about_video_url');
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('data-about-video-open', false)
        ->assertSee('data-about-video-preview', false)
        ->assertSee('data-about-video-modal', false)
        ->assertSee('data-about-video-player', false)
        ->assertSee('autoplay', false)
        ->assertSee('muted', false)
        ->assertSee('loop', false)
        ->assertSee('playsinline', false)
        ->assertSee($url, false);

    $modal = file_get_contents(resource_path('js/pages/welcome/about-video-modal.js'));
    $timeline = file_get_contents(resource_path('js/surfaces/home/vision-story/timeline.js'));

    expect($modal)
        ->toContain('modal.showModal()')
        ->toContain('player.muted = false')
        ->toContain('preview.play()')
        ->and($timeline)
        ->toContain("visual.querySelector('[data-vision-art]')")
        ->not->toContain("visual.querySelector('img')");
});
