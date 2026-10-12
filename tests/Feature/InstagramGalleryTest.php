<?php

use App\Models\GalleryItem;
use App\Models\InstagramGalleryItem;
use App\Services\InstagramMediaMirror;
use App\Services\SyncInstagramGallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('renders existing school gallery records without requiring Instagram credentials', function (): void {
    config()->set('services.instagram.access_token', null);
    GalleryItem::create([
        'type' => 'photo',
        'media_url' => 'https://media.almustaqbal.sch.id/gallery/media/first.jpg',
        'alt_text' => 'Kegiatan kelas',
    ]);
    $this->get(route('galeri'))
        ->assertOk()
        ->assertSee('data-portal-page="gallery"', false)
        ->assertSee('Kegiatan kelas')
        ->assertSee('https://media.almustaqbal.sch.id/gallery/media/first.jpg', false);
});

it('syncs Instagram media into R2 URLs idempotently and renders the public feed', function (): void {
    config()->set('services.instagram.access_token', 'test-only-token');
    Http::fake(['graph.instagram.com/*' => Http::response(['data' => [[
        'id' => '17912345',
        'caption' => 'Hari kreatif',
        'media_type' => 'VIDEO',
        'media_url' => 'https://scontent.cdninstagram.com/a.mp4',
        'thumbnail_url' => 'https://scontent.cdninstagram.com/a.jpg',
        'permalink' => 'https://www.instagram.com/reel/abc/',
        'timestamp' => '2026-10-12T10:00:00+0000',
    ]]], 200)]);

    $mirror = Mockery::mock(InstagramMediaMirror::class);
    $mirror->shouldReceive('mirror')->once()->with('https://scontent.cdninstagram.com/a.mp4', '17912345', true)
        ->andReturn('https://media.almustaqbal.sch.id/gallery/instagram/17912345/video.mp4');
    $mirror->shouldReceive('mirror')->once()->with('https://scontent.cdninstagram.com/a.jpg', '17912345')
        ->andReturn('https://media.almustaqbal.sch.id/gallery/instagram/17912345/poster.jpg');
    app()->instance(InstagramMediaMirror::class, $mirror);

    expect(app(SyncInstagramGallery::class)->run())->toBe(['synced' => 1, 'skipped' => 0]);
    expect(app(SyncInstagramGallery::class)->run())->toBe(['synced' => 1, 'skipped' => 0]);
    expect(InstagramGalleryItem::count())->toBe(1);

    $this->get(route('galeri'))
        ->assertOk()->assertSee('Hari kreatif')
        ->assertSee('https://media.almustaqbal.sch.id/gallery/instagram/17912345/video.mp4', false)
        ->assertSee('https://www.instagram.com/reel/abc/', false);
});

it('keeps the public gallery working when the Instagram API is unavailable', function (): void {
    config()->set('services.instagram.access_token', 'test-only-token');
    InstagramGalleryItem::create([
        'instagram_id' => '17923456', 'media_type' => 'IMAGE',
        'caption' => 'Foto tetap tersedia',
        'media_url' => 'https://media.almustaqbal.sch.id/gallery/instagram/existing.jpg',
        'permalink' => 'https://www.instagram.com/p/abc/',
        'posted_at' => now(),
    ]);
    Http::fake(['graph.instagram.com/*' => Http::response(['error' => ['message' => 'Expired']], 401)]);
    $this->artisan('instagram:sync-gallery')->assertExitCode(1);
    $this->get(route('galeri'))->assertOk()->assertSee('Foto tetap tersedia');
    expect(InstagramGalleryItem::count())->toBe(1);
});

it('rejects media URLs outside trusted Meta CDN domains', function (): void {
    $mirror = app(InstagramMediaMirror::class);
    expect(fn () => $mirror->mirror('https://cdninstagram.com.evil.test/p.jpg', '17923456'))
        ->toThrow(RuntimeException::class, 'outside trusted Meta CDN');
    expect(fn () => $mirror->mirror('http://scontent.cdninstagram.com/p.jpg', '17923456'))
        ->toThrow(RuntimeException::class, 'not trusted');
});
