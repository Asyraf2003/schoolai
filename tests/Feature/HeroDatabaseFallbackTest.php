<?php

use App\Models\HeroSlide;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses active database hero slides and falls back to locale slides when none are active', function (): void {
    HeroSlide::query()->delete();

    HeroSlide::query()->create([
        'type' => 'image',
        'media_url' => 'media/home/hero-school.png',
        'title_id' => 'Hero dari Database',
        'title_en' => 'Hero from Database',
        'title_ar' => 'واجهة من قاعدة البيانات',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee('hero-cinema__title-link', false)
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1
            && ($hero['slides'][0]['is_primary_slide'] ?? false) === true
            && ($hero['slides'][0]['title'] ?? null) === 'Hero from Database');

    HeroSlide::query()->update(['is_active' => false]);

    $this->withSession(['locale' => 'id'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) >= 4
            && ($hero['slides'][0]['title'] ?? null) !== 'Hero dari Database');
});

it('never renders legacy youtube media or thumbnails in the hero', function (): void {
    HeroSlide::query()->delete();

    HeroSlide::query()->create([
        'type' => 'video',
        'media_url' => 'https://www.youtube-nocookie.com/embed/legacy123',
        'poster_url' => 'https://i.ytimg.com/vi/legacy123/hqdefault.jpg',
        'title_id' => 'Hero Lama',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('youtube', false)
        ->assertDontSee('ytimg', false)
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1
            && ($hero['slides'][0]['render_type'] ?? null) === 'image'
            && ! str_contains(strtolower((string) ($hero['slides'][0]['media_url'] ?? '')), 'youtu'));
});
