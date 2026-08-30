<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses in-view About and Mission video previews with the shared modal player', function (): void {
    $aboutUrl = (string) config('media.homepage_about_video_url');
    $missionUrl = (string) config('media.homepage_mission_video_url');
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('data-vision-video-open', false)
        ->assertSee('data-vision-video-preview', false)
        ->assertSee('data-about-video-modal', false)
        ->assertSee('data-about-video-shell', false)
        ->assertSee('data-about-video-player', false)
        ->assertSee('data-about-video-toggle', false)
        ->assertSee('data-about-video-seek', false)
        ->assertSee('data-about-video-volume', false)
        ->assertSee('data-about-video-fullscreen', false)
        ->assertDontSee('data-about-video-poster', false)
        ->assertDontSee('data-mission-video-cue', false)
        ->assertDontSee('vision-arch__video-cue--pending', false)
        ->assertSee('preload="none"', false)
        ->assertSee('muted', false)
        ->assertSee('loop', false)
        ->assertSee('playsinline', false)
        ->assertSee($aboutUrl, false)
        ->assertSee($missionUrl, false);

    $section = file_get_contents(resource_path('views/home/sections/vision-mission.blade.php'));
    $composer = file_get_contents(app_path('View/Composers/HomeVisionMissionComposer.php'));
    $modal = file_get_contents(resource_path('js/pages/welcome/about-video-modal.js'));
    $timeline = file_get_contents(resource_path('js/surfaces/home/vision-story/timeline.js'));
    $css = file_get_contents(resource_path('css/pages/welcome-vision-waapi/about-video.css'));
    $baseCss = file_get_contents(resource_path('css/pages/welcome-vision-waapi/base.css'));
    $enhancedCss = file_get_contents(resource_path('css/pages/welcome-vision-waapi/enhanced.css'));

    expect($section)
        ->toContain('data-vision-video-open')
        ->toContain('data-vision-video-preview')
        ->toContain('$visionVideoSources[$loop->index]')
        ->toContain("config('media.homepage_about_video_url')")
        ->not->toContain("config('media.homepage_mission_video_url')")
        ->toContain('muted')
        ->toContain('loop')
        ->toContain('playsinline')
        ->toContain('preload="none"')
        ->toContain('data-about-video-shell')
        ->toContain('data-about-video-toggle')
        ->toContain('data-about-video-seek')
        ->toContain('data-about-video-volume')
        ->toContain('data-about-video-fullscreen')
        ->not->toContain('data-about-video-poster')
        ->not->toContain('data-mission-video-cue')
        ->not->toContain('vision-arch__video-cue--pending')
        ->and($composer)
        ->toContain("config('media.homepage_about_video_url')")
        ->toContain("config('media.homepage_mission_video_url')")
        ->toContain("'visionVideoSources' => [")
        ->and($modal)
        ->toContain("querySelectorAll('[data-vision-video-preview]')")
        ->toContain("querySelectorAll('[data-vision-video-open]')")
        ->toContain('new IntersectionObserver')
        ->toContain("rootMargin: '180px 0px'")
        ->toContain("preview.src = source")
        ->toContain('preview.pause()')
        ->toContain('player.src = source')
        ->toContain('hydratePlayer(activeSource)')
        ->toContain('previewController.pause()')
        ->toContain('previewController.resume()')
        ->toContain('modal.showModal()')
        ->toContain('requestFullscreen')
        ->toContain('document.fullscreenElement')
        ->toContain('player.currentTime')
        ->toContain('player.volume')
        ->and($css)
        ->toContain('.vision-arch__video-preview')
        ->toContain('.vision-arch__video-preview.is-ready')
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
