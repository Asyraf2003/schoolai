<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the R2 video as the first homepage hero slide and keeps carousel navigation', function (): void {
    $heroUrl = (string) config('media.homepage_hero_video_url');

    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);

        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('data-hero-slider', false)
            ->assertSee('data-media-type="video"', false)
            ->assertSee('data-hero-video', false)
            ->assertSee('src="'.e($heroUrl).'"', false)
            ->assertSee('autoplay', false)
            ->assertSee('muted', false)
            ->assertSee('playsinline', false)
            ->assertSee('data-hero-previous', false)
            ->assertSee('data-hero-next', false)
            ->assertDontSee('hero-cinema__eyebrow', false)
            ->assertDontSee('hero-cinema__description', false)
            ->assertDontSee('hero-cinema__cta', false)
            ->assertDontSee('data-hero-title-glow', false)
            ->assertDontSee('hero-title-glow__overlay', false)
            ->assertDontSee('<iframe', false)
            ->assertSee('data-nav-mega', false)
            ->assertSee('nav-mega__media', false)
            ->assertSee('nav-language__flag--id', false)
            ->assertSee('nav-language__flag--en', false)
            ->assertSee('nav-language__flag--ar', false);

        expect(substr_count($response->getContent(), 'data-hero-slide'))
            ->toBeGreaterThan(1);
    }
});
