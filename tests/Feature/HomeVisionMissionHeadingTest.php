<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders localized scene backgrounds and real Set 2 effects for every locale', function (): void {
    $labels = [
        'id' => 'Visi Pendidikan',
        'en' => 'Education Vision',
        'ar' => 'الرؤية التربوية',
    ];

    foreach ($labels as $locale => $label) {
        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="visi-misi"', false)
            ->assertSee('data-story-kind="direction"', false)
            ->assertSee('data-story-art-motion="drift"', false)
            ->assertSee('data-story-art-motion="orbit"', false)
            ->assertSee('data-story-art-motion="sweep"', false)
            ->assertSee('data-story-art-motion="zoom"', false)
            ->assertSee('data-story-art-motion="fold"', false)
            ->assertSee('data-story-effect="effect25"', false)
            ->assertSee('data-story-effect="effect22"', false)
            ->assertSee('data-story-effect="effect23"', false)
            ->assertSee('data-story-effect="effect27"', false)
            ->assertSee('data-story-effect="effect28"', false)
            ->assertSee('data-story-color="#061d4f"', false)
            ->assertSee('data-story-color="#075e62"', false)
            ->assertSee('data-story-color="#7a3828"', false)
            ->assertSee('data-story-color="#4d2c75"', false)
            ->assertSee('data-story-color="#175b45"', false)
            ->assertSee($label)
            ->assertDontSee('data-story-effect="fan"', false)
            ->assertDontSee('data-story-effect="perspective"', false)
            ->assertDontSee('data-story-effect="focus"', false)
            ->assertDontSee('data-story-effect="wave"', false)
            ->assertDontSee('direction-story__index', false)
            ->assertDontSee('id="tentang"', false)
            ->assertDontSee('home-about-scroll', false)
            ->assertDontSee('data-mission-card', false);

        $content = $response->getContent();

        expect(substr_count($content, 'data-story-effect="effect25"'))->toBe(2)
            ->and(substr_count($content, 'data-story-scene-art'))->toBe(8)
            ->and(substr_count($content, '<li class="direction-story__scene direction-story__scene--mission"'))->toBe(4);
    }
});
