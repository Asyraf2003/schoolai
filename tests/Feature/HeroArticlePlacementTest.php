<?php

use App\Models\Article;
use App\Models\HeroSetting;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('keeps V2 Hero video-only without reading promoted Articles', function (): void {
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);

    HeroSetting::query()->firstOrFail()->update([
        'title_id' => 'Opening Sekolah',
        'title_en' => 'School Opening',
        'title_ar' => 'افتتاحية المدرسة',
        'cta_url' => null,
    ]);

    $fallback = (string) config('media.static.seo.home_og');
    $older = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'artikel-lama-test',
        'title_id' => 'Artikel Lama',
        'title_en' => 'Older Article',
        'description_en' => 'Older excerpt.',
        'content_en' => '<p>Heavy older Canvas body.</p>',
        'thumbnail_url' => $fallback,
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
        'thumbnail_url' => $fallback,
        'link_id' => url('/artikel/artikel-terbaru-test'),
        'published_at' => now(),
    ]);

    $latest->update(['hero_position' => 1]);
    $older->update(['hero_position' => 2]);
    $queries = collect();
    DB::listen(function ($query) use ($queries): void {
        $queries->push($query->sql);
    });

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee('Latest Article')
        ->assertDontSee('Older Article')
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1
            && ($hero['slides'][0]['title'] ?? null) === 'School Opening'
            && ! isset($hero['slides'][0]['article_id']));

    $promotedRead = $queries->first(
        fn (string $sql): bool => str_contains($sql, 'hero_position')
            && str_contains($sql, 'is not null'),
    );
    expect($promotedRead)->toBeNull();

    $latest->update(['title_en' => 'Updated Article Title']);
    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertDontSee('Updated Article Title')
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1);

    $latest->update(['article_status' => Article::STATUS_DRAFT]);
    $older->delete();

    $this->withSession(['locale' => 'id'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides'] ?? []) === 1
            && ($hero['slides'][0]['title'] ?? null) === 'Opening Sekolah');
});