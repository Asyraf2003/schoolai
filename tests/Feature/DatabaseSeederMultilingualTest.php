<?php

use App\Models\GalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('seeds article, hero, gallery, and ppdb content in three languages', function (): void {
    $this->seed();

    $filled = static fn (mixed $value): bool => is_string($value) && trim($value) !== '';
    $articles = DB::table('articles')->whereNull('deleted_at')->get();
    $heroSettings = DB::table('hero_settings')->get();
    $promotedArticles = DB::table('articles')->whereNotNull('hero_position')->get();
    $gallery = DB::table('gallery_items')
        ->whereNull('deleted_at')
        ->where('show_on_homepage', true)
        ->get();
    $canonicalGallery = DB::table('gallery_items')->whereNull('deleted_at')->get();
    $galleryPlacements = DB::table('gallery_item_gallery_page_section')->get();
    $facilitySection = DB::table('gallery_page_sections')
        ->whereNull('deleted_at')
        ->where('title_id', 'Fasilitas')
        ->first();
    $facilityPlacements = $facilitySection === null
        ? collect()
        : $galleryPlacements->where('gallery_page_section_id', $facilitySection->id);
    $ppdb = DB::table('ppdb_showcase_items')->whereNull('deleted_at')->get();

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
        ->and($heroSettings)->toHaveCount(1)
        ->and($heroSettings->every(fn (object $hero): bool => $filled($hero->title_id)
            && $filled($hero->title_en)
            && $filled($hero->title_ar)))->toBeTrue()
        ->and($promotedArticles)->toHaveCount(5)
        ->and($gallery)->toHaveCount(GalleryItem::MAX_HOMEPAGE_ITEMS)
        ->and($canonicalGallery)->toHaveCount(27)
        ->and($galleryPlacements)->toHaveCount(21)
        ->and($facilitySection)->not->toBeNull()
        ->and($facilityPlacements)->toHaveCount(GalleryItem::MAX_HOMEPAGE_ITEMS)
        ->and($gallery->every(fn (object $item): bool => $filled($item->title_id)
            && $filled($item->title_en)
            && $filled($item->title_ar)
            && $filled($item->category_id)
            && $filled($item->category_en)
            && $filled($item->category_ar)
            && $filled($item->caption_id)
            && $filled($item->caption_en)
            && $filled($item->caption_ar)
            && $item->category_id === 'Fasilitas'
            && str_starts_with((string) $item->media_url, 'https://media.almustaqbal.sch.id/')
            && ! str_contains(strtolower((string) $item->media_url), 'youtu')))->toBeTrue()
        ->and($ppdb)->toHaveCount(6)
        ->and($ppdb->every(fn (object $item): bool => $filled($item->title_id)
            && $filled($item->title_en)
            && $filled($item->title_ar)
            && $filled($item->description_id)
            && $filled($item->description_en)
            && $filled($item->description_ar)))->toBeTrue();
});
