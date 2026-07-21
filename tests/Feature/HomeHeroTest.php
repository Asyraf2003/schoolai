<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the mixed-media homepage hero contract for every public locale', function (): void {
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

                return count($slides) >= 3
                    && in_array('image', $declaredTypes, true)
                    && in_array('video', $declaredTypes, true)
                    && collect($slides)->every(
                        fn (array $slide): bool => ! empty($slide['media_url'])
                            && in_array($slide['render_type'] ?? null, ['image', 'video'], true)
                    );
            })
            ->assertSee('data-hero-slider', false)
            ->assertSee('data-media-type="video"', false)
            ->assertSee('data-nav-mega', false);
    }
});
