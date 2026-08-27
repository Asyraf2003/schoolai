<?php

use App\Models\Article;
use App\Models\HeroSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('keeps Opening first and derives only explicitly promoted published Articles', function (): void {
    HeroSetting::query()->firstOrFail()->update([
        'title_id' => 'Opening Sekolah',
        'title_en' => 'School Opening',
        'title_ar' => 'افتتاحية المدرسة',
        'cta_url' => null,
    ]);

    $older = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'artikel-lama-test',
        'title_id' => 'Artikel Lama',
        'title_en' => 'Older Article',
        'description_en' => 'Older excerpt.',
        'content_en' => '<p>Heavy older Canvas body.</p>',
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'link_id' => url('/artikel/artikel-lama-test'),
        'published_at' => now()->subDay(),
    ]);

    $latest = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'artikel-terbaru-test',
        'title_id' => 'Artikel Terbaru',
        'title_en' => 'Latest Article',
        'description_en' => 'Latest excerpt.',
        'content_en' => '<p>Heavy latest Canvas body.</p>',
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'link_id' => url('/artikel/artikel-terbaru-test'),
        'published_at' => now(),
    ]);

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1
            && ($hero['slides'][0]['title'] ?? null) === 'School Opening'
            && ! isset($hero['slides'][0]['article_id']));

    $latest->update(['hero_position' => 1]);
    $older->update(['hero_position' => 2]);
    $queries = collect();
    DB::listen(function ($query) use ($queries): void {
        $queries->push($query->sql);
    });

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 3
            && ($hero['slides'][0]['title'] ?? null) === 'School Opening'
            && ($hero['slides'][1]['title'] ?? null) === 'Latest Article'
            && ($hero['slides'][1]['description'] ?? null) === 'Latest excerpt.'
            && ($hero['slides'][2]['title'] ?? null) === 'Older Article');

    $heroQuery = $queries->first(
        fn (string $sql): bool => str_contains($sql, 'hero_position')
            && str_contains($sql, 'is not null'),
    );

    expect($heroQuery)
        ->toBeString()
        ->not->toContain('content_id')
        ->not->toContain('content_en')
        ->not->toContain('content_ar')
        ->not->toContain('select *');

    $latest->update([
        'title_en' => 'Updated Article Title',
        'description_en' => 'Updated excerpt.',
    ]);

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertViewHas('hero', fn (array $hero): bool => ($hero['slides'][1]['title'] ?? null) === 'Updated Article Title'
            && ($hero['slides'][1]['description'] ?? null) === 'Updated excerpt.');

    $latest->update(['article_status' => Article::STATUS_DRAFT]);
    $older->delete();

    $this->withSession(['locale' => 'id'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1
            && ($hero['slides'][0]['title'] ?? null) === 'Opening Sekolah');
});
