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
                $mediaUrls = array_column($slides, 'media_url');

                return count($slides) >= 4
                    && in_array('image', $declaredTypes, true)
                    && in_array('video', $declaredTypes, true)
                    && collect($slides)->every(
                        fn (array $slide): bool => ! empty($slide['media_url'])
                            && in_array($slide['render_type'] ?? null, ['image', 'video'], true)
                    )
                    && collect($mediaUrls)->contains(
                        fn (mixed $url): bool => is_string($url) && str_contains($url, 'youtube-nocookie.com/embed/lNzvxnnEpjs')
                    )
                    && collect($mediaUrls)->contains(
                        fn (mixed $url): bool => is_string($url) && str_contains($url, 'resources.finalsite.net')
                    );
            })
            ->assertSee('data-hero-slider', false)
            ->assertSee('data-media-type="video"', false)
            ->assertSee('lNzvxnnEpjs', false)
            ->assertSee('HSFModuleatNight.jpg', false)
            ->assertSee('data-hero-ornaments', false)
            ->assertSee('data-nav-mega', false)
            ->assertSee('nav-mega__media', false)
            ->assertSee('nav-language__flag--id', false)
            ->assertSee('nav-language__flag--en', false)
            ->assertSee('nav-language__flag--ar', false);
    }
});
