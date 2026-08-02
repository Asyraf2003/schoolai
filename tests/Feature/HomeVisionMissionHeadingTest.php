<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the compact effect25 vision mission story for every locale', function (): void {
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
            ->assertSee('class="direction-story"', false)
            ->assertSee('data-story-kind="direction"', false)
            ->assertSee('data-story-effect="stretch"', false)
            ->assertSee('data-story-bg-layer', false)
            ->assertSee('media/home/9.png', false)
            ->assertSee('media/home/10.png', false)
            ->assertSee('media/home/11.png', false)
            ->assertSee('media/home/12.png', false)
            ->assertSee($label)
            ->assertDontSee('direction-story__index', false)
            ->assertDontSee('direction-story__scene--opening', false)
            ->assertDontSee('data-story-effect="rise"', false)
            ->assertDontSee('data-story-effect="fan"', false)
            ->assertDontSee('data-story-effect="focus"', false)
            ->assertDontSee('id="tentang"', false)
            ->assertDontSee('home-about-scroll', false)
            ->assertDontSee('data-mission-card', false);

        $content = $response->getContent();

        expect(substr_count($content, 'data-story-effect="stretch"'))->toBe(6)
            ->and(substr_count($content, 'data-story-bg-layer'))->toBe(4)
            ->and(substr_count($content, '<li class="direction-story__scene"'))->toBe(4);
    }
});
