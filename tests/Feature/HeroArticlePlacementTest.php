<?php

use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses latest published articles automatically and lets hero placements curate their order', function (): void {
    HeroSlide::query()->delete();
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);

    $older = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'artikel-lama-test',
        'title_id' => 'Artikel Lama',
        'title_en' => 'Older Article',
        'description_id' => 'Tetap tersedia di halaman artikel.',
        'description_en' => 'Still available on the article page.',
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'link_id' => url('/artikel/artikel-lama-test'),
        'author' => 'Admin Test',
        'published_at' => now()->subDay(),
    ]);

    $latest = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'artikel-terbaru-test',
        'title_id' => 'Artikel Terbaru',
        'title_en' => 'Latest Article',
        'description_id' => 'Otomatis masuk hero ketika belum dikurasi.',
        'description_en' => 'Automatically enters the hero before curation.',
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'link_id' => url('/artikel/artikel-terbaru-test'),
        'author' => 'Admin Test',
        'published_at' => now(),
    ]);

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 2
            && ($hero['slides'][0]['is_primary_slide'] ?? false) === true
            && ($hero['slides'][0]['title'] ?? null) === 'Latest Article'
            && ($hero['slides'][1]['title'] ?? null) === 'Older Article');

    HeroSlide::query()->create([
        'article_id' => $older->getKey(),
        'type' => 'image',
        'media_url' => Article::PLACEHOLDER_THUMBNAIL,
        'poster_url' => Article::PLACEHOLDER_THUMBNAIL,
        'title_id' => $older->title_id,
        'title_en' => $older->title_en,
        'description_id' => $older->description_id,
        'description_en' => $older->description_en,
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1
            && ($hero['slides'][0]['is_primary_slide'] ?? false) === true
            && ($hero['slides'][0]['title'] ?? null) === 'Older Article'
            && ($hero['slides'][0]['article_id'] ?? null) === $older->getKey());

    expect($latest->fresh()->isPubliclyVisibleNow())->toBeTrue();

    $older->delete();

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1
            && ($hero['slides'][0]['is_primary_slide'] ?? false) === true
            && ($hero['slides'][0]['title'] ?? null) === 'Latest Article');
});
