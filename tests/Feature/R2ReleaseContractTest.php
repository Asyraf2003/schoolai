<?php

use App\Models\Article;
use App\Models\GalleryItem;
use App\Support\Media\MediaUrlResolver;
use App\Support\Media\R2MediaConfiguration;
use App\Support\Media\R2MediaStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function validReleaseR2Configuration(): void
{
    config()->set('media.disk', 's3');
    config()->set('media.public_url', 'https://media.almustaqbal.sch.id');
    config()->set('filesystems.disks.s3', [
        'driver' => 's3', 'key' => 'test-key', 'secret' => 'test-secret', 'bucket' => 'test-bucket',
        'url' => 'https://media.almustaqbal.sch.id', 'region' => 'auto',
        'endpoint' => 'https://test-account.r2.cloudflarestorage.com',
    ]);
}

it('accepts R2 public media without changing private application storage', function (): void {
    validReleaseR2Configuration();
    app(R2MediaConfiguration::class)->assertValid();
    Storage::fake('s3');
    app()->detectEnvironment(fn (): string => 'production');
    try {
        $stored = app(R2MediaStorage::class)->store(UploadedFile::fake()->image('photo.jpg'), 'gallery/media');
        expect($stored['url'])->toStartWith('https://media.almustaqbal.sch.id/gallery/media/');
        Storage::disk('s3')->assertExists($stored['key']);
        expect(config('filesystems.default'))->toBe('local');
    } finally {
        app()->detectEnvironment(fn (): string => 'testing');
    }
});

it('refuses invalid production media configuration before an upload reaches storage', function (string $key, mixed $value): void {
    validReleaseR2Configuration();
    config()->set($key, $value);
    Storage::fake('s3');
    Storage::fake('public');
    app()->detectEnvironment(fn (): string => 'production');
    try {
        expect(fn () => app(R2MediaStorage::class)->store(UploadedFile::fake()->image('photo.jpg'), 'gallery/media'))
            ->toThrow(LogicException::class);
        expect(Storage::disk('s3')->allFiles())->toBeEmpty()
            ->and(Storage::disk('public')->allFiles())->toBeEmpty();
    } finally {
        app()->detectEnvironment(fn (): string => 'testing');
    }
})->with([
    ['media.disk', 'public'], ['filesystems.disks.s3.driver', 'local'],
    ['media.public_url', 'https://example.test'], ['filesystems.disks.s3.url', 'http://localhost/storage'],
    ['filesystems.disks.s3.endpoint', 'https://s3.amazonaws.com'],
    ['filesystems.disks.s3.endpoint', 'https://test-account.r2.cloudflarestorage.com?token=test'],
    ['filesystems.disks.s3.region', 'us-east-1'], ['filesystems.disks.s3.key', ''],
    ['filesystems.disks.s3.secret', ''], ['filesystems.disks.s3.bucket', ''],
]);

it('rejects noncanonical URL components rather than embedding them into media URLs', function (string $base): void {
    config()->set('media.public_url', $base);
    expect(fn () => app(MediaUrlResolver::class)->publicUrl('gallery/media/photo.webp'))->toThrow(LogicException::class);
})->with([
    'https://user@media.almustaqbal.sch.id', 'https://media.almustaqbal.sch.id?token=test',
    'https://media.almustaqbal.sch.id#fragment',
]);

it('never treats credential-bearing URLs as owned media', function (): void {
    $resolver = app(MediaUrlResolver::class);
    expect($resolver->ownedKey('https://user@media.almustaqbal.sch.id/gallery/media/photo.webp'))->toBeNull()
        ->and($resolver->ownedKey('https://user:password@media.almustaqbal.sch.id/gallery/media/photo.webp'))->toBeNull();
});

it('renders first-party public media from R2 while keeping Vite output on the application origin', function (): void {
    Article::create(['content_id' => '<p>Isi</p>', 'cover_url' => config('media.static.school_life.labit')]);
    GalleryItem::create(['media_url' => config('media.static.school_life.labit')]);
    foreach (['home', 'galeri', 'artikel'] as $route) {
        $response = $this->get(route($route))->assertOk();
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new DOMXPath($document);
        foreach ($xpath->query('//img[@src] | //video[@src] | //source[@src] | //*[@data-footer-preview-src]') as $node) {
            $url = $node->getAttribute('src') ?: $node->getAttribute('data-footer-preview-src');
            expect($url)->toStartWith('https://media.almustaqbal.sch.id/');
        }
        $response->assertDontSee('/storage/', false)->assertDontSee('/media/footer/school-map.webp', false)
            ->assertSee('/build/assets/', false);
    }
});

it('migrates a legacy video poster without deleting its local original', function (): void {
    Storage::fake('public');
    Storage::fake('s3');
    config()->set('media.disk', 's3');
    Storage::disk('public')->put('poster.webp', 'legacy poster');
    $article = Article::create(['content_id' => '<p>Isi</p>', 'cover_thumbnail_url' => '/storage/poster.webp']);
    $this->artisan('media:migrate-r2', ['owner' => 'articles'])->assertSuccessful();
    expect($article->fresh()->cover_thumbnail_url)->toStartWith(config('media.public_url').'/articles/thumbnails/');
    Storage::disk('public')->assertExists('poster.webp');
    $key = app(MediaUrlResolver::class)->ownedKey($article->fresh()->cover_thumbnail_url);
    Storage::disk('s3')->assertExists($key);
});
