<?php

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use App\Models\SiteStatistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Carbon::setTestNow(
        Carbon::create(2026, 7, 13, 6, 10, 0, 'Asia/Jakarta')
    );
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('uses WIB application and MySQL configuration', function (): void {
    expect(config('app.timezone'))->toBe('Asia/Jakarta')
        ->and(config('database.connections.mysql.timezone'))->toBe('+07:00')
        ->and(now()->format('Y-m-d H:i:s P'))
        ->toBe('2026-07-13 06:10:00 +07:00');
});

it('keeps article publication time and derived date consistent in WIB', function (): void {
    $article = Article::query()->create([
        'title_id' => 'Artikel Uji WIB',
        'thumbnail_url' => 'https://example.com/article-wib.webp',
        'link_id' => 'https://example.com/article-wib',
        'author' => 'Admin',
        'published_at' => '2026-07-13 06:15:00',
    ]);

    $article->refresh();

    expect($article->published_date?->toDateString())
        ->toBe('2026-07-13')
        ->and($article->published_at?->format('Y-m-d H:i:s P'))
        ->toBe('2026-07-13 06:15:00 +07:00')
        ->and($article->created_at?->format('Y-m-d H:i:s P'))
        ->toBe('2026-07-13 06:10:00 +07:00');

    $createdRaw = DB::table('articles')
        ->where('id', $article->id)
        ->first(['published_date', 'published_at']);

    expect(Carbon::parse((string) $createdRaw->published_date)->toDateString())
        ->toBe('2026-07-13')
        ->and((string) $createdRaw->published_at)
        ->toBe('2026-07-13 06:15:00');

    $article->update([
        'published_at' => '2026-07-14 19:45:00',
    ]);

    $article->refresh();

    expect($article->published_date?->toDateString())
        ->toBe('2026-07-14')
        ->and($article->published_at?->format('Y-m-d H:i:s P'))
        ->toBe('2026-07-14 19:45:00 +07:00');

    $updatedRaw = DB::table('articles')
        ->where('id', $article->id)
        ->first(['published_date', 'published_at']);

    expect(Carbon::parse((string) $updatedRaw->published_date)->toDateString())
        ->toBe('2026-07-14')
        ->and((string) $updatedRaw->published_at)
        ->toBe('2026-07-14 19:45:00');
});

it('keeps gallery publication times in WIB', function (): void {
    $gallery = GalleryItem::query()->create([
        'title' => 'Galeri Uji WIB',
        'title_id' => 'Galeri Uji WIB',
        'type' => 'photo',
        'category' => 'Umum',
        'category_id' => 'Umum',
        'media_url' => '/storage/gallery/timezone-probe.webp',
        'is_published' => true,
        'published_at' => '2026-07-13 06:30:00',
    ]);

    $section = GalleryPageSection::query()->create([
        'title_id' => 'Section Uji WIB',
        'is_published' => true,
    ]);

    $pageMedia = GalleryPageMediaItem::query()->create([
        'gallery_page_section_id' => $section->id,
        'type' => 'photo',
        'media_url' => '/storage/gallery/page-timezone-probe.webp',
        'is_published' => true,
        'published_at' => '2026-07-13 07:45:00',
    ]);

    expect($gallery->fresh()->published_at?->format('Y-m-d H:i:s P'))
        ->toBe('2026-07-13 06:30:00 +07:00')
        ->and($pageMedia->fresh()->published_at?->format('Y-m-d H:i:s P'))
        ->toBe('2026-07-13 07:45:00 +07:00');

    $this->assertDatabaseHas('gallery_items', [
        'id' => $gallery->id,
        'published_at' => '2026-07-13 06:30:00',
    ]);

    $this->assertDatabaseHas('gallery_page_media_items', [
        'id' => $pageMedia->id,
        'published_at' => '2026-07-13 07:45:00',
    ]);
});

it('uses WIB for standard CRUD timestamps', function (): void {
    $models = [
        PpdbSetting::query()->create([
            'registration_url' => 'https://example.com/ppdb',
            'is_active' => true,
        ]),

        PpdbShowcaseItem::query()->create([
            'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,
            'title_id' => 'PPDB Uji WIB',
            'description_id' => 'Deskripsi pengujian.',
            'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
            'sort_order' => 99,
        ]),

        SiteStatistic::query()->create([
            'value' => '99',
            'label' => 'Statistik Uji WIB',
            'sort_order' => 99,
        ]),

        GalleryPageSection::query()->create([
            'title_id' => 'Section Timestamp WIB',
            'is_published' => true,
        ]),
    ];

    foreach ($models as $model) {
        $model->refresh();

        expect($model->created_at?->format('Y-m-d H:i:s P'))
            ->toBe('2026-07-13 06:10:00 +07:00')
            ->and($model->updated_at?->format('Y-m-d H:i:s P'))
            ->toBe('2026-07-13 06:10:00 +07:00');
    }
});
