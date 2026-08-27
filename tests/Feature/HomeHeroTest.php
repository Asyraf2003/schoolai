<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the fixed R2 video as the sole Opening slide when no Article is promoted', function (): void {
    $heroUrl = (string) config('media.homepage_hero_video_url');

    foreach (['id', 'en', 'ar'] as $locale) {
        $response = $this->withSession(['locale' => $locale])->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('data-hero-slider', false)
            ->assertSee('data-media-type="video"', false)
            ->assertSee('data-hero-video', false)
            ->assertSee('src="'.e($heroUrl).'"', false)
            ->assertSee('autoplay', false)
            ->assertSee('muted', false)
            ->assertSee('playsinline', false)
            ->assertSee('data-hero-audio', false)
            ->assertDontSee('data-hero-previous', false)
            ->assertDontSee('data-hero-next', false)
            ->assertSee('hero-cinema__eyebrow', false)
            ->assertSee('hero-cinema__description', false)
            ->assertDontSee('<iframe', false);

        expect(substr_count($response->getContent(), 'data-slide-index='))->toBe(1);
    }
});
