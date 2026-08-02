<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one localized cinematic direction story for every locale', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="visi-misi"', false)
            ->assertSee('class="direction-story"', false)
            ->assertSee('data-story-kind="direction"', false)
            ->assertSee('data-story-effect="rise"', false)
            ->assertSee('data-story-effect="fan"', false)
            ->assertSee('data-story-effect="focus"', false)
            ->assertSee('data-story-effect="stretch"', false)
            ->assertDontSee('vision-mission-heading', false)
            ->assertDontSee('data-mission-card', false)
            ->assertDontSee('aria-pressed=', false);

        $content = $response->getContent();
        expect(substr_count($content, 'class="direction-story__mission"'))->toBe(4);
    }
});
