<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the fixed R2 video as the sole Opening slide when no Article is promoted', function (): void {
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
            ->assertSee('src="'.e($heroUrl).'"', false)
            ->assertSee('poster="'.e($posterUrl).'"', false)
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
});

it('keeps the desktop hero audio label on the shared navigation typography contract', function (): void {
    $header = file_get_contents(resource_path('views/partials/site-navbar/header.blade.php'));
    $heroVisual = file_get_contents(resource_path('css/pages/welcome-hero-visual.css'));
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
