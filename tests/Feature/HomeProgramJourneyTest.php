<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one localized six-frame Program scroll journey', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);

        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="program"', false)
            ->assertSee('aria-labelledby="vision-program-title"', false)
            ->assertSee('data-program-journey', false)
            ->assertSee('data-program-hud', false)
            ->assertSee('data-program-frames', false)
            ->assertSee('data-program-exit', false)
            ->assertSee('data-program-active-title', false)
            ->assertSee('data-program-active-description', false)
            ->assertSee('data-program-active-link', false)
            ->assertDontSee('data-featured-program-card', false)
            ->assertDontSee('program-spotlight', false);

        $content = $response->getContent();
        preg_match('/<section\b[^>]*\bdata-program-journey\b[^>]*>.*?<\/section>/s', $content, $section);
        preg_match_all('/\sdata-program-frame(?:\s|>)/', $section[0] ?? '', $frames);
        preg_match_all('/\sdata-program-rail-item(?:\s|>)/', $section[0] ?? '', $railItems);

        expect($section)->not->toBeEmpty()
            ->and(count($frames[0]))->toBe(6)
            ->and(count($railItems[0]))->toBe(6)
            ->and(substr_count($section[0], 'images.unsplash.com'))->toBe(6)
            ->and(substr_count($section[0], 'href="'.route('portal.login').'"'))->toBe(7)
            ->and($section[0])->not->toContain('href="/ppdb"')
            ->and(substr_count($content, 'id="vision-program-title"'))->toBe(1);
    }
});