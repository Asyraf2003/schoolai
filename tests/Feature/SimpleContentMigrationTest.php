<?php

use App\Models\GalleryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('migrates legacy articles without losing raw records or media and leaves auth untouched', function (): void {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $credentials = DB::table('users')->where('id', $admin->id)->first();
    DB::table('legacy_articles')->insert([
        'id' => 37, 'slug' => 'cerita-lama', 'article_source' => 'native', 'article_status' => 'published',
        'title_id' => 'Cerita', 'title_en' => 'Story', 'title_ar' => 'قصة',
        'content_id' => '<p><strong>Isi lama</strong><img src="https://example.com/old.jpg"></p>',
        'content_ar' => '<p>النص القديم</p>', 'tags' => '["Old"]', 'thumbnail_url' => 'https://example.com/cover.jpg',
        'link_id' => '/artikel/cerita-lama', 'published_date' => '2026-01-01', 'published_at' => '2026-01-01 00:00:00',
    ]);
    $original = DB::table('legacy_articles')->where('id', 37)->first();
    Schema::drop('articles');
    $migration = require database_path('migrations/2026_10_12_201548_simplify_school_content_tables.php');
    $migration->up();
    expect((array) DB::table('legacy_articles')->where('id', 37)->first())->toBe((array) $original);
    $article = DB::table('articles')->where('id', 37)->first();
    expect($article->slug)->toBe('cerita-lama')->and($article->content_id)->toContain('<strong>Isi lama</strong>')->not->toContain('<img')
        ->and($article->content_ar)->toContain('النص القديم')->and($article->cover_url)->toBe($original->thumbnail_url);
    expect((array) DB::table('users')->where('id', $admin->id)->first())->toBe((array) $credentials);
    DB::table('articles')->where('id', 37)->update(['title_id' => 'Edit baru']);
    $migration->up();
    expect(DB::table('articles')->where('id', 37)->value('title_id'))->toBe('Edit baru');
});

it('retains external links as text and archives unpublished records instead of exposing them', function (): void {
    foreach ([['id' => 41, 'article_source' => 'external', 'article_status' => 'published'],
        ['id' => 42, 'article_source' => 'native', 'article_status' => 'draft']] as $row) {
        DB::table('legacy_articles')->insert($row + ['title_id' => 'Lama', 'description_id' => 'Ringkasan',
            'thumbnail_url' => 'https://example.com/cover.jpg', 'link_id' => 'https://example.com/old',
            'published_date' => '2026-01-01', 'published_at' => '2026-01-01 00:00:00']);
    }
    Schema::drop('articles');
    (require database_path('migrations/2026_10_12_201548_simplify_school_content_tables.php'))->up();
    expect(DB::table('articles')->where('id', 41)->value('content_id'))->toContain('Ringkasan')->toContain('https://example.com/old');
    expect(DB::table('articles')->where('id', 42)->value('deleted_at'))->not->toBeNull();
    expect(DB::table('legacy_articles')->count())->toBe(2);
});

it('consolidates gallery media and retains the archived section and visibility data', function (): void {
    DB::table('legacy_gallery_items')->insert(['id' => 19, 'title' => 'Lama', 'title_id' => 'Alt lama',
        'type' => 'video', 'media_url' => 'https://media.example.com/gallery/media/it-v1.mp4', 'is_published' => true,
        'show_on_homepage' => true, 'show_on_gallery_page' => true]);
    DB::table('legacy_gallery_items')->insert(['id' => 20, 'title' => 'Tersembunyi', 'type' => 'photo',
        'media_url' => 'https://example.com/hidden.jpg', 'is_published' => false]);
    DB::table('legacy_gallery_page_sections')->insert(['id' => 11, 'title_id' => 'Bagian lama']);
    DB::table('legacy_gallery_page_media_items')->insert(['id' => 12, 'gallery_page_section_id' => 11,
        'type' => 'photo', 'media_url' => 'https://example.com/section.jpg', 'is_published' => true]);
    $original = (array) DB::table('legacy_gallery_items')->where('id', 19)->first();
    Schema::drop('gallery_items');
    $migration = require database_path('migrations/2026_10_12_201609_simplify_gallery_storage.php');
    $migration->up();
    expect(DB::table('gallery_items')->count())->toBe(3);
    $video = DB::table('gallery_items')->where('id', 19)->first();
    expect($video->alt_text)->toBe('Alt lama')->and($video->thumbnail_url)->toBe('https://media.example.com/site/school-life/labit-v1.webp');
    expect(DB::table('gallery_items')->where('id', 20)->value('deleted_at'))->not->toBeNull();
    expect((array) DB::table('legacy_gallery_items')->where('id', 19)->first())->toBe($original);
    expect(DB::table('legacy_gallery_page_sections')->value('title_id'))->toBe('Bagian lama');
    $migration->up();
    expect(DB::table('gallery_items')->count())->toBe(3);
});

it('keeps only one valid enabled Google Form URL and archives the complete old settings', function (string $url, bool $active, ?string $expected): void {
    DB::table('legacy_ppdb_settings')->delete();
    DB::table('legacy_ppdb_settings')->insert(['id' => 11, 'registration_url' => $url, 'information_url' => 'https://example.com/info', 'is_active' => $active]);
    Schema::drop('ppdb_settings');
    (require database_path('migrations/2026_10_12_201610_simplify_ppdb_link.php'))->up();
    expect(DB::table('ppdb_settings')->count())->toBe(1)->and(DB::table('ppdb_settings')->value('registration_url'))->toBe($expected);
    expect(DB::table('legacy_ppdb_settings')->value('information_url'))->toBe('https://example.com/info');
})->with([
    ['https://forms.gle/LegacyForm', true, 'https://forms.gle/LegacyForm'],
    ['https://forms.gle/LegacyForm', false, null], ['javascript:alert(1)', true, null],
]);

it('preserves article records when a legacy slug matches a generated migration slug', function (): void {
    foreach ([['id' => 41, 'slug' => null], ['id' => 42, 'slug' => 'artikel-lama-41']] as $row) {
        DB::table('legacy_articles')->insert($row + ['title_id' => 'Lama', 'thumbnail_url' => 'https://example.com/cover.jpg',
            'link_id' => 'https://example.com/old', 'published_date' => '2026-01-01']);
    }
    Schema::drop('articles');
    (require database_path('migrations/2026_10_12_201548_simplify_school_content_tables.php'))->up();
    expect(DB::table('articles')->count())->toBe(2)->and(DB::table('articles')->where('id', 42)->value('slug'))->toBe('artikel-lama-41');
});

it('retains empty legacy media and withholds media from archived sections', function (): void {
    DB::table('legacy_gallery_items')->insert(['id' => 40, 'title' => 'Kosong', 'media_url' => null]);
    DB::table('legacy_gallery_page_sections')->insert(['id' => 50, 'title_id' => 'Tutup', 'is_published' => false]);
    DB::table('legacy_gallery_page_media_items')->insert(['id' => 60, 'gallery_page_section_id' => 50,
        'media_url' => 'https://example.com/photo.jpg', 'is_published' => true]);
    Schema::drop('gallery_items');
    (require database_path('migrations/2026_10_12_201609_simplify_gallery_storage.php'))->up();
    expect(DB::table('gallery_items')->count())->toBe(2)->and(GalleryItem::count())->toBe(0);
    expect(DB::table('legacy_gallery_items')->where('id', 40)->value('media_url'))->toBeNull();
});
