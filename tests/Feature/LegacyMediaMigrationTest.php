<?php

use App\Models\Article;
use App\Models\GalleryItem;
use App\Support\Media\MediaUrlResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('media.disk', 'r2');
    Storage::fake('public');
    Storage::fake('r2');
});

it('dry-runs then migrates article thumbnails and embedded content idempotently', function (): void {
    Storage::disk('public')->put('articles/thumbnails/legacy.jpg', 'thumbnail');
    Storage::disk('public')->put('articles/content/legacy.webp', 'content-image');
    $article = Article::query()->create([
        'title_id' => 'Legacy R2 article',
        'thumbnail_url' => '/storage/articles/thumbnails/legacy.jpg',
        'content_id' => '<p>Isi.</p><img src="/storage/articles/content/legacy.webp" alt="Proof">',
        'link_id' => 'https://example.test/legacy-r2',
        'author' => 'Migration Test',
        'published_at' => now(),
    ]);

    $this->artisan('media:migrate-r2', ['owner' => 'articles', '--dry-run' => true])
        ->assertExitCode(0);

    expect($article->fresh()->thumbnail_url)->toBe('/storage/articles/thumbnails/legacy.jpg');
    expect(Storage::disk('r2')->allFiles())->toHaveCount(0);

    $this->artisan('media:migrate-r2', ['owner' => 'articles'])->assertExitCode(0);
    $article->refresh();
    $thumbnailKey = app(MediaUrlResolver::class)->ownedKey($article->thumbnail_url);
    preg_match('~https://media\.almustaqbal\.sch\.id/(?<key>articles/content/[^"\']+)~', (string) $article->content_id, $match);

    expect($thumbnailKey)->toStartWith('articles/thumbnails/'.$article->getKey().'/')
        ->and($match['key'] ?? null)->toStartWith('articles/content/'.$article->getKey().'/');
    Storage::disk('r2')->assertExists($thumbnailKey);
    Storage::disk('r2')->assertExists($match['key']);
    Storage::disk('public')->assertExists('articles/thumbnails/legacy.jpg');
    Storage::disk('public')->assertExists('articles/content/legacy.webp');

    $this->artisan('media:migrate-r2', ['owner' => 'articles'])->assertExitCode(0);
    expect(Storage::disk('r2')->allFiles())->toHaveCount(2);
});

it('migrates archived owner records but never rewrites external provider URLs', function (): void {
    Storage::disk('public')->put('gallery/photos/archived.jpg', 'gallery');
    $archived = GalleryItem::query()->create([
        'title' => 'Archived',
        'title_id' => 'Archived',
        'type' => 'photo',
        'category' => 'Kegiatan',
        'category_id' => 'Kegiatan',
        'media_url' => '/storage/gallery/photos/archived.jpg',
        'sort_order' => 1,
        'is_published' => true,
    ]);
    $archived->delete();
    $external = GalleryItem::query()->create([
        'title' => 'External',
        'title_id' => 'External',
        'type' => 'video',
        'category' => 'Kegiatan',
        'category_id' => 'Kegiatan',
        'media_url' => 'https://www.youtube.com/embed/proof',
        'sort_order' => 1,
        'is_published' => true,
    ]);

    $this->artisan('media:migrate-r2', ['owner' => 'gallery-homepage'])->assertExitCode(0);

    expect($archived->fresh()->trashed())->toBeTrue()
        ->and($archived->fresh()->media_url)->toStartWith('https://media.almustaqbal.sch.id/gallery/homepage/')
        ->and($external->fresh()->media_url)->toBe('https://www.youtube.com/embed/proof');
    expect(Storage::disk('r2')->allFiles())->toHaveCount(1);
});

it('fails safely when a referenced legacy binary is missing', function (): void {
    $article = Article::query()->create([
        'title_id' => 'Missing legacy media',
        'thumbnail_url' => '/storage/articles/thumbnails/missing.jpg',
        'link_id' => 'https://example.test/missing-r2',
        'author' => 'Migration Test',
        'published_at' => now(),
    ]);

    $this->artisan('media:migrate-r2', ['owner' => 'articles'])->assertExitCode(1);

    expect($article->fresh()->thumbnail_url)->toBe('/storage/articles/thumbnails/missing.jpg');
    expect(Storage::disk('r2')->allFiles())->toHaveCount(0);
});
