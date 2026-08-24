<?php

use App\Support\Media\MediaUrlResolver;
use App\Support\Media\R2MediaStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    config()->set('media.disk', 's3');
    config()->set('media.public_url', 'https://media.almustaqbal.sch.id');
    config()->set('media.cache_control', 'public, max-age=31536000, immutable');
    Storage::fake('s3');
});

it('stores immutable owner-scoped media behind the canonical domain', function (): void {
    $stored = app(R2MediaStorage::class)->store(
        UploadedFile::fake()->create('gallery.webp', 4, 'image/webp'),
        'gallery/homepage',
        42,
    );

    expect($stored['key'])
        ->toMatch('~^gallery/homepage/42/[0-9a-f-]+\.webp$~')
        ->and($stored['url'])->toBe('https://media.almustaqbal.sch.id/'.$stored['key']);

    Storage::disk('s3')->assertExists($stored['key']);
});

it('resolves and deletes only canonical R2-owned URLs', function (): void {
    $storage = app(R2MediaStorage::class);
    $resolver = app(MediaUrlResolver::class);
    $stored = $storage->store(
        UploadedFile::fake()->create('article.jpg', 4, 'image/jpeg'),
        'articles/thumbnails',
        'new',
    );

    expect($resolver->ownedKey($stored['url']))->toBe($stored['key'])
        ->and($resolver->ownedKey('https://example.test/'.$stored['key']))->toBeNull()
        ->and($resolver->ownedKey('https://media.almustaqbal.sch.id.evil.test/'.$stored['key']))->toBeNull()
        ->and($resolver->ownedKey($stored['url'].'?download=1'))->toBeNull()
        ->and($storage->deleteOwnedUrl('https://example.test/'.$stored['key']))->toBeFalse();

    Storage::disk('s3')->assertExists($stored['key']);

    expect($storage->deleteOwnedUrl($stored['url']))->toBeTrue();
    Storage::disk('s3')->assertMissing($stored['key']);
});
