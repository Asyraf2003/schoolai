<?php

use App\Models\GalleryItem;
use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use App\Models\User;
use App\Support\Media\MediaUrlResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');

    $this->actingAs(User::query()->forceCreate([
        'name' => 'Gallery R2 Admin',
        'email' => 'gallery-r2@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));
});

it('keeps homepage gallery replacement and soft-delete lifecycles R2-owned', function (): void {
    $this->post(route('admin.galeri.store'), [
        'title_id' => 'Galeri R2',
        'type' => 'photo',
        'category_id' => 'Kegiatan',
        'media_file' => UploadedFile::fake()->image('gallery.jpg'),
        'is_published' => '1',
        'published_at' => now()->format('Y-m-d H:i:s'),
    ])->assertRedirect();

    $item = GalleryItem::query()->where('title_id', 'Galeri R2')->firstOrFail();
    $oldKey = app(MediaUrlResolver::class)->ownedKey($item->media_url);

    expect($oldKey)->toStartWith('gallery/homepage/new/');
    Storage::disk('public')->assertExists($oldKey);

    $this->put(route('admin.galeri.update', $item), [
        'title_id' => 'Galeri R2 Baru',
        'type' => 'photo',
        'category_id' => 'Kegiatan',
        'media_file' => UploadedFile::fake()->image('gallery-new.jpg'),
        'is_published' => '1',
        'published_at' => now()->format('Y-m-d H:i:s'),
    ])->assertRedirect(route('admin.galeri.show', $item));

    $newKey = app(MediaUrlResolver::class)->ownedKey($item->fresh()->media_url);
    expect($newKey)->toStartWith('gallery/homepage/'.$item->getKey().'/');
    Storage::disk('public')->assertMissing($oldKey);
    Storage::disk('public')->assertExists($newKey);

    GalleryItem::query()->create([
        'title' => 'Survivor',
        'title_id' => 'Survivor',
        'type' => 'video',
        'category' => 'Kegiatan',
        'category_id' => 'Kegiatan',
        'media_url' => 'https://www.youtube.com/watch?v=proof',
        'sort_order' => 2,
        'is_published' => true,
    ]);

    $this->delete(route('admin.galeri.destroy', $item))->assertRedirect(route('admin.galeri'));
    Storage::disk('public')->assertExists($newKey);
    $this->patch(route('admin.galeri.restore', $item->getKey()))->assertRedirect(route('admin.galeri'));
    Storage::disk('public')->assertExists($newKey);
});

it('stores gallery page batches and replacements under their bounded owner', function (): void {
    $section = GalleryPageSection::query()->create([
        'title_id' => 'Prestasi',
        'is_published' => true,
    ]);

    $this->post(route('admin.galeri.section-media.store', $section), [
        'type' => 'photo',
        'media_files' => [
            UploadedFile::fake()->image('one.jpg'),
            UploadedFile::fake()->image('two.webp'),
        ],
        'is_published' => '1',
    ])->assertRedirect(route('admin.galeri.sections.show', $section));

    $items = GalleryPageMediaItem::query()->orderBy('id')->get();
    expect($items)->toHaveCount(2);

    foreach ($items as $item) {
        $key = app(MediaUrlResolver::class)->ownedKey($item->media_url);
        expect($key)->toStartWith('gallery/page-media/'.$section->getKey().'/');
        Storage::disk('public')->assertExists($key);
    }

    $item = $items->firstOrFail();
    $oldKey = app(MediaUrlResolver::class)->ownedKey($item->media_url);
    $this->put(route('admin.galeri.section-media.update', $item), [
        'type' => 'photo',
        'media_file' => UploadedFile::fake()->image('replacement.jpg'),
        'is_published' => '1',
    ])->assertRedirect(route('admin.galeri.section-media.show', $item));

    $newKey = app(MediaUrlResolver::class)->ownedKey($item->fresh()->media_url);
    expect($newKey)->toStartWith('gallery/page-media/'.$item->getKey().'/');
    Storage::disk('public')->assertMissing($oldKey);
    Storage::disk('public')->assertExists($newKey);

    $this->delete(route('admin.galeri.section-media.destroy', $item))->assertRedirect();
    Storage::disk('public')->assertExists($newKey);
    $this->patch(route('admin.galeri.section-media.restore', $item->getKey()))->assertRedirect();
    Storage::disk('public')->assertExists($newKey);
});
