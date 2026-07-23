<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('seeds article, hero, gallery, ppdb, and statistic content in three languages', function (): void {
    $this->seed();

    $filled = static fn (mixed $value): bool => is_string($value) && trim($value) !== '';
    $articles = DB::table('articles')->whereNull('deleted_at')->get();
    $heroes = DB::table('hero_slides')->where('is_active', true)->get();
    $gallery = DB::table('gallery_items')->whereNull('deleted_at')->get();
    $ppdb = DB::table('ppdb_showcase_items')->whereNull('deleted_at')->get();
    $statistics = DB::table('site_statistics')->whereNull('deleted_at')->get();

    expect($articles)->toHaveCount(10)
        ->and($articles->every(fn (object $article): bool => $filled($article->title_id)
            && $filled($article->title_en)
            && $filled($article->title_ar)
            && $filled($article->description_id)
            && $filled($article->description_en)
            && $filled($article->description_ar)
            && $filled($article->content_id)
            && $filled($article->content_en)
            && $filled($article->content_ar)))->toBeTrue()
        ->and($heroes)->toHaveCount(5)
        ->and($heroes->every(fn (object $hero): bool => $filled($hero->title_id)
            && $filled($hero->title_en)
            && $filled($hero->title_ar)
            && ! str_contains(strtolower((string) $hero->media_url), 'youtu')))->toBeTrue()
        ->and($gallery)->toHaveCount(6)
        ->and($gallery->every(fn (object $item): bool => $filled($item->title_id)
            && $filled($item->title_en)
            && $filled($item->title_ar)
            && $filled($item->category_id)
            && $filled($item->category_en)
            && $filled($item->category_ar)
            && $filled($item->caption_id)
            && $filled($item->caption_en)
            && $filled($item->caption_ar)
            && ! str_contains(strtolower((string) $item->media_url), 'youtu')))->toBeTrue()
        ->and($ppdb)->toHaveCount(6)
        ->and($ppdb->every(fn (object $item): bool => $filled($item->title_id)
            && $filled($item->title_en)
            && $filled($item->title_ar)
            && $filled($item->description_id)
            && $filled($item->description_en)
            && $filled($item->description_ar)))->toBeTrue()
        ->and($statistics)->toHaveCount(4)
        ->and($statistics->every(fn (object $item): bool => $filled($item->value)
            && $filled($item->value_en)
            && $filled($item->value_ar)
            && $filled($item->label)
            && $filled($item->label_en)
            && $filled($item->label_ar)))->toBeTrue();
});
