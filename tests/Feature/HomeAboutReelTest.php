<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the localized scroll typography about story without reel legacy', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="tentang"', false)
            ->assertSee('class="home-about-scroll"', false)
            ->assertSee('data-story-kind="about"', false)
            ->assertSee('data-story-effect="stretch"', false)
            ->assertSee('media/home/9.png', false)
            ->assertSee('media/home/10.png', false)
            ->assertSee('media/home/11.png', false)
            ->assertSee('media/home/12.png', false)
            ->assertDontSee('about-reel', false)
            ->assertDontSee('data-about-reel', false)
            ->assertDontSee('<canvas', false)
            ->assertDontSee('data-about-reel-video', false);
    }
});
