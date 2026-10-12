<?php

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('uses WIB application and MySQL configuration', function (): void {
    $this->travelTo(Carbon::create(2026, 7, 13, 6, 10, 0, 'Asia/Jakarta'));
    expect(config('app.timezone'))->toBe('Asia/Jakarta')
        ->and(config('database.connections.mysql.timezone'))->toBe('+07:00')
        ->and(now()->format('Y-m-d H:i:s P'))->toBe('2026-07-13 06:10:00 +07:00');
});

it('keeps optional article dates independent of creation and publication schedules', function (): void {
    $this->travelTo(Carbon::create(2026, 7, 13, 6, 10, 0, 'Asia/Jakarta'));
    $article = Article::create(['content_id' => '<p>Isi</p>', 'published_date' => '2026-07-14']);
    expect($article->fresh()->published_date->toDateString())->toBe('2026-07-14')
        ->and($article->fresh()->created_at->format('Y-m-d H:i:s P'))->toBe('2026-07-13 06:10:00 +07:00');
    $article->update(['published_date' => null]);
    expect($article->fresh()->published_date)->toBeNull();
});

it('uses WIB for current gallery and PPDB CRUD timestamps', function (): void {
    $this->travelTo(Carbon::create(2026, 7, 13, 6, 10, 0, 'Asia/Jakarta'));
    $gallery = GalleryItem::create(['type' => 'photo', 'media_url' => 'https://example.com/photo.jpg']);
    $ppdb = PpdbSetting::query()->firstOrFail();
    $ppdb->update(['registration_url' => 'https://forms.gle/TimezoneForm']);
    foreach ([$gallery, $ppdb] as $model) {
        expect($model->fresh()->updated_at->format('Y-m-d H:i:s P'))->toBe('2026-07-13 06:10:00 +07:00');
    }
});
