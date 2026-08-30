<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses framed vision media with corner video cues and intent-loads the R2 about modal', function (): void {
    $url = (string) config('media.homepage_about_video_url');
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('data-about-video-open', false)
        ->assertSee('data-about-video-poster', false)
        ->assertSee('data-mission-video-cue', false)
        ->assertSee('data-about-video-modal', false)
        ->assertSee('data-about-video-shell', false)
        ->assertSee('data-about-video-player', false)
        ->assertSee('data-about-video-toggle', false)
        ->assertSee('data-about-video-seek', false)
        ->assertSee('data-about-video-volume', false)
        ->assertSee('data-about-video-fullscreen', false)
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
    $baseCss = file_get_contents(resource_path('css/pages/welcome-vision-waapi/base.css'));
    $enhancedCss = file_get_contents(resource_path('css/pages/welcome-vision-waapi/enhanced.css'));

    expect($section)
        ->toContain('data-about-video-poster')
        ->toContain('data-mission-video-cue')
        ->toContain('vision-arch__video-cue--pending')
        ->toContain('data-about-video-shell')
        ->toContain('data-about-video-toggle')
        ->toContain('data-about-video-seek')
        ->toContain('data-about-video-volume')
        ->toContain('data-about-video-fullscreen')
        ->toContain('data-about-video-src="{{ config(\'media.homepage_about_video_url\') }}"')
        ->toContain('preload="none"')
        ->not->toContain("\n        controls\n")
        ->not->toContain('data-about-video-preview')
        ->not->toContain('data-about-preview-start')
        ->not->toContain('data-about-preview-end')
        ->and($modal)
        ->toContain('player.src = playerSource')
        ->toContain('player.load()')
        ->toContain('hydratePlayer();')
        ->toContain('modal.showModal()')
        ->toContain('requestFullscreen')
        ->toContain('document.fullscreenElement')
        ->toContain('player.currentTime')
        ->toContain('player.volume')
        ->not->toContain('IntersectionObserver')
        ->not->toContain('preview.')
        ->and($css)
        ->toContain('inset-block-end:')
        ->toContain('inset-inline-end:')
        ->not->toContain('inset-block-start: 50%')
        ->not->toContain('inset-inline-start: 50%')
        ->toContain('.vision-video-modal__surface:fullscreen')
        ->toContain('.vision-video-modal__controls')
        ->and($baseCss)
        ->toContain('border-radius: 0')
        ->toContain('border-image: linear-gradient(')
        ->toContain('--vision-media-shadow-x')
        ->toContain('html[dir="rtl"] .vision-arch__visual')
        ->and($enhancedCss)
        ->toContain('width: min(96vw, 1600px)')
        ->toContain('grid-template-columns: minmax(0, .72fr) minmax(0, 1.28fr)')
        ->and($timeline)
        ->toContain("visual.querySelector('[data-vision-art]')")
        ->not->toContain("visual.querySelector('img')");
});

it('opts only public site surfaces into the character cursor and follows fullscreen top layers', function (): void {
    $home = file_get_contents(resource_path('views/welcome.blade.php'));
    $publicLayout = file_get_contents(resource_path('views/layouts/public.blade.php'));
    $cursor = file_get_contents(resource_path('js/pages/welcome/cursor.js'));
    $cursorCss = file_get_contents(resource_path('css/pages/welcome/048-custom-cursor.css'));

    expect($home)
        ->toContain('home-page site-cursor-page nav-shell')
        ->and($publicLayout)
        ->toContain('public-page-body public-content-page site-cursor-page nav-shell')
        ->and($cursor)
        ->toContain("classList.contains('site-cursor-page')")
        ->toContain('document.fullscreenElement')
        ->toContain('activeCursorLayerHost()')
        ->toContain("'input[type=\"range\"]'")
        ->and($cursorCss)
        ->toContain('body.site-cursor-page[data-cursor-character]')
        ->not->toContain('body.home-page[data-cursor-character]');
});
