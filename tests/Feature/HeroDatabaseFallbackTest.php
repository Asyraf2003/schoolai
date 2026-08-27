<?php

use App\Models\HeroSetting;
use App\Models\HeroSlide;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses localized singleton Opening copy with the fixed configured video', function (): void {
    HeroSetting::query()->firstOrFail()->update([
        'title_id' => 'Opening Indonesia',
        'title_en' => 'Opening English',
        'title_ar' => 'الافتتاحية العربية',
        'cta_label_en' => 'Learn more',
        'cta_url' => '#program',
    ]);

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Opening English')
        ->assertSee('href="#program"', false)
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1
            && ($hero['slides'][0]['media_url'] ?? null) === config('media.homepage_hero_video_url')
            && ($hero['slides'][0]['is_opening'] ?? false) === true);
});

it('ignores legacy HeroSlide rows for runtime presentation', function (): void {
    HeroSlide::query()->create([
        'type' => 'video',
        'media_url' => 'https://www.youtube-nocookie.com/embed/legacy123',
        'title_id' => 'Legacy Hero Copy',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Legacy Hero Copy')
        ->assertDontSee('youtube', false)
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1);
});
