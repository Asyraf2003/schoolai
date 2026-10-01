<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the fixed R2 video as a poster-first Opening slide when no Article is promoted', function (): void {
    $heroUrl = (string) config('media.homepage_hero_video_url');
    $posterUrl = (string) config('media.static.hero_school');

    foreach (['id', 'en', 'ar'] as $locale) {
        $response = $this->withSession(['locale' => $locale])->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('data-hero-slider', false)
            ->assertSee('data-hero-mode="opening"', false)
            ->assertSee('data-media-type="video"', false)
            ->assertSee('data-hero-video', false)
            ->assertSee('data-src="'.e($heroUrl).'"', false)
            ->assertDontSee('<source src="'.e($heroUrl).'"', false)
            ->assertSee('poster="'.e($posterUrl).'"', false)
            ->assertSee('preload="none"', false)
            ->assertSee('autoplay', false)
            ->assertSee('loop', false)
            ->assertSee('muted', false)
            ->assertSee('playsinline', false)
            ->assertSee('data-hero-audio', false)
            ->assertDontSee('data-hero-playback', false)
            ->assertDontSee('data-hero-previous', false)
            ->assertDontSee('data-hero-next', false)
            ->assertDontSee('data-hero-dot', false)
            ->assertDontSee('data-hero-progress', false)
            ->assertDontSee('data-hero-current', false)
            ->assertDontSee('welcome-hero-carousel', false)
            ->assertDontSee('interactive-examples.mdn.mozilla.net', false)
            ->assertDontSee('images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=2400&q=82', false)
            ->assertSee('hero-cinema__eyebrow', false)
            ->assertSee('hero-cinema__description', false)
            ->assertDontSee('<iframe', false);

        expect(substr_count($response->getContent(), 'data-slide-index='))->toBe(1);
    }

    $opening = file_get_contents(resource_path('js/pages/welcome-hero/opening.js'));
    $carousel = file_get_contents(resource_path('js/pages/welcome-hero/carousel.js'));
    $media = file_get_contents(resource_path('js/pages/welcome-hero/slider-media.js'));

    expect($opening)
        ->toContain('source[data-src]')
        ->toContain("video.setAttribute('data-hydrated', 'true')")
        ->not->toContain("addEventListener('pointermove'")
        ->not->toContain("addEventListener('scroll'")
        ->not->toContain("window.matchMedia('(max-width: 767px)')")
        ->not->toContain("addEventListener('keydown'")
        ->not->toContain("'requestIdleCallback' in window")
        ->not->toContain('startDeferredVideo')
        ->and($carousel)
        ->toContain('videoHydrationReady: false')
        ->toContain('HERO_READY_EVENT, startDeferredVideo')
        ->toContain("root.dataset.heroReady === 'true'")
        ->and($media)
        ->toContain('state.videoHydrationReady !== true')
        ->toContain('source[data-src]');
});

it('keeps the desktop hero audio label on the shared navigation typography contract', function (): void {
    $header = file_get_contents(resource_path('views/partials/site-navbar/header.blade.php'));
    $heroVisual = readOwnedSource(resource_path('css/pages/welcome-hero-visual.css'), ['resources/css/pages/welcome-hero-appearance.css', 'resources/css/pages/welcome-hero-audio.css']);
    $arabicTypography = file_get_contents(resource_path('css/arabic-typography.css'));

    expect($header)
        ->toContain('class="nav-link nav-hero-audio__text"')
        ->toContain('data-hero-audio')
        ->toContain('data-text-role="action"')
        ->and($heroVisual)
        ->toContain('.nav-hero-audio__text')
        ->not->toContain('font: inherit')
        ->and($arabicTypography)
        ->toContain('[data-text-role="action"]');
});
