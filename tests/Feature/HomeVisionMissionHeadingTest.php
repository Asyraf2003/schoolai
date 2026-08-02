<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one effect25-only vision mission story for every locale', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="visi-misi"', false)
            ->assertSee('class="direction-story"', false)
            ->assertSee('data-story-kind="direction"', false)
            ->assertSee('data-story-effect="stretch"', false)
            ->assertSee('data-story-fragment', false)
            ->assertDontSee('data-story-effect="rise"', false)
            ->assertDontSee('data-story-effect="fan"', false)
            ->assertDontSee('data-story-effect="focus"', false)
            ->assertDontSee('id="tentang"', false)
            ->assertDontSee('home-about-scroll', false)
            ->assertDontSee('media/home/9.png', false)
            ->assertDontSee('media/home/10.png', false)
            ->assertDontSee('media/home/11.png', false)
            ->assertDontSee('media/home/12.png', false)
            ->assertDontSee('data-mission-card', false);

        $content = $response->getContent();

        expect(substr_count($content, 'data-story-effect="stretch"'))->toBe(7)
            ->and(substr_count($content, 'class="direction-story__scene"'))->toBe(4);
    }
});
