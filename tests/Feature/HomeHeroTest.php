<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the stabilized mixed-media hero for every public locale', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);

        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertViewHas('hero', function (array $hero): bool {
                $slides = $hero['slides'] ?? [];
                $declaredTypes = array_column($slides, 'type');
                $firstSlide = $slides[0] ?? [];
                $firstMediaUrl = $firstSlide['media_url'] ?? null;

                return count($slides) >= 4
                    && ($firstSlide['type'] ?? null) === 'video'
                    && ($firstSlide['render_type'] ?? null) === 'video'
                    && is_string($firstMediaUrl)
                    && str_ends_with(parse_url($firstMediaUrl, PHP_URL_PATH) ?: '', '/flower.mp4')
                    && in_array('image', $declaredTypes, true)
                    && in_array('video', $declaredTypes, true)
                    && collect($slides)->every(
                        fn (array $slide): bool => ! empty($slide['media_url'])
                            && ! empty($slide['poster_url'])
                            && in_array($slide['render_type'] ?? null, ['image', 'video'], true)
                    );
            })
            ->assertSee('data-hero-slider', false)
            ->assertSee('data-media-type="video"', false)
            ->assertSee('data-hero-video', false)
            ->assertSee('data-hero-poster', false)
            ->assertSee('class="hero-cinema__arrow hero-cinema__arrow--previous"', false)
            ->assertSee('class="hero-cinema__arrow hero-cinema__arrow--next"', false)
            ->assertDontSee('data-hero-progress', false)
            ->assertDontSee('data-hero-dot', false)
            ->assertDontSee('data-hero-playback', false)
            ->assertSee('role="region"', false)
            ->assertSee('flower.mp4', false)
            ->assertDontSee('youtube', false)
            ->assertDontSee('<iframe', false)
            ->assertSee('HSFModuleatNight.jpg', false)
            ->assertDontSee('data-hero-ornaments', false)
            ->assertSee('viewBox="0 0 128 72"', false)
            ->assertSee('data-nav-mega', false)
            ->assertSee('nav-language__flag--id', false)
            ->assertSee('nav-language__flag--en', false)
            ->assertSee('nav-language__flag--ar', false);
    }
});
