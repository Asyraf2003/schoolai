<?php

use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('backfills legacy section media into canonical placements without duplicating an existing asset', function (): void {
    $firstSection = GalleryPageSection::query()->create([
        'title_id' => 'Bagian Pertama',
        'is_published' => true,
    ]);
    $secondSection = GalleryPageSection::query()->create([
        'title_id' => 'Bagian Kedua',
        'is_published' => true,
    ]);
    $item = GalleryItem::query()->create([
        'title' => 'Media Existing',
        'title_id' => 'Media Existing',
        'type' => 'photo',
        'category' => 'Galeri',
        'category_id' => 'Galeri',
        'media_url' => '/storage/gallery/page-media/shared.jpg',
        'sort_order' => 1,
        'is_published' => true,
        'show_on_homepage' => false,
        'show_on_gallery_page' => false,
    ]);

    $legacyAttributes = [
        'type' => 'photo',
        'media_url' => $item->media_url,
        'is_published' => true,
        'published_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
        'deleted_at' => null,
    ];

    DB::table('gallery_page_media_items')->insert([
        array_merge($legacyAttributes, ['gallery_page_section_id' => $firstSection->id]),
        array_merge($legacyAttributes, [
            'gallery_page_section_id' => $secondSection->id,
            'deleted_at' => now(),
        ]),
    ]);

    $migration = require database_path('migrations/2026_08_28_010100_backfill_canonical_gallery_media.php');
    $migration->up();

    expect(GalleryItem::query()->count())->toBe(1);
    $this->assertDatabaseCount('gallery_item_gallery_page_section', 2);
    $this->assertDatabaseHas('gallery_item_gallery_page_section', [
        'gallery_item_id' => $item->id,
        'gallery_page_section_id' => $firstSection->id,
        'is_published' => true,
    ]);
    $this->assertDatabaseHas('gallery_item_gallery_page_section', [
        'gallery_item_id' => $item->id,
        'gallery_page_section_id' => $secondSection->id,
        'is_published' => false,
    ]);
});

it('imports a legacy-only object as one rollback-identifiable canonical asset', function (): void {
    $section = GalleryPageSection::query()->create([
        'title_id' => 'Bagian Legacy',
        'is_published' => true,
    ]);
    $legacyId = DB::table('gallery_page_media_items')->insertGetId([
        'gallery_page_section_id' => $section->id,
        'type' => 'photo',
        'media_url' => '/storage/gallery/page-media/legacy-only.jpg',
        'is_published' => true,
        'published_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $migration = require database_path('migrations/2026_08_28_010100_backfill_canonical_gallery_media.php');
    $migration->up();

    $item = GalleryItem::query()
        ->where('legacy_gallery_page_media_item_id', $legacyId)
        ->firstOrFail();

    expect($item->show_on_homepage)->toBeFalse()
        ->and($item->show_on_gallery_page)->toBeFalse();
    $this->assertDatabaseHas('gallery_item_gallery_page_section', [
        'gallery_item_id' => $item->id,
        'gallery_page_section_id' => $section->id,
        'is_published' => true,
    ]);
});
