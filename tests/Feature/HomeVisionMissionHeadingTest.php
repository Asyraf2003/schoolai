<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders localized scene backgrounds, positions, and real Set 2 effects', function (): void {
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
            ->assertSee('data-story-position="start"', false)
            ->assertSee('data-story-position="center"', false)
            ->assertSee('data-story-position="end"', false)
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
            ->assertDontSee('direction-story__scene--position-left', false)
            ->assertDontSee('direction-story__scene--position-right', false)
            ->assertDontSee('id="tentang"', false)
            ->assertDontSee('home-about-scroll', false)
            ->assertDontSee('data-mission-card', false);

        $content = $response->getContent();

        expect(substr_count($content, 'data-story-effect="effect25"'))->toBe(2)
            ->and(substr_count($content, 'data-story-scene-art'))->toBe(8)
            ->and(substr_count($content, 'data-story-position="center"'))->toBe(4)
            ->and(substr_count($content, 'data-story-position="start"'))->toBe(1)
            ->and(substr_count($content, 'data-story-position="end"'))->toBe(1)
            ->and(substr_count($content, 'direction-story__scene--mission direction-story__scene--position-'))->toBe(4);

        if ($locale === 'ar') {
            $storyStart = strpos($content, 'class="direction-story"');
            $storyEnd = strpos($content, '</section>', $storyStart);
            $story = substr($content, $storyStart, $storyEnd - $storyStart);

            expect($story)
                ->toContain('data-story-honorific')
                ->toContain('صلى الله عليه وسلم')
                ->not->toContain('ﷺ');
        }
    }
});
