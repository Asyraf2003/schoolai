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
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1
            && ($hero['slides'][0]['title'] ?? null) === 'Hero from Database');

    HeroSlide::query()->update(['is_active' => false]);

    $this->withSession(['locale' => 'id'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) >= 4
            && ($hero['slides'][0]['title'] ?? null) !== 'Hero dari Database');
});
