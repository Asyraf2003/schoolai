<?php

use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use App\Models\User;
use App\Support\Media\MediaUrlResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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
        'show_on_homepage' => '1',
        'show_on_gallery_page' => '1',
        'published_at' => now()->format('Y-m-d H:i:s'),
    ])->assertRedirect();

    $item = GalleryItem::query()->where('title_id', 'Galeri R2')->firstOrFail();
    $oldKey = app(MediaUrlResolver::class)->ownedKey($item->media_url);

    expect($oldKey)->toStartWith('gallery/media/new/');
    Storage::disk('public')->assertExists($oldKey);

    $this->put(route('admin.galeri.update', $item), [
        'title_id' => 'Galeri R2 Baru',
        'type' => 'photo',
        'category_id' => 'Kegiatan',
        'media_file' => UploadedFile::fake()->image('gallery-new.jpg'),
        'is_published' => '1',
        'show_on_homepage' => '1',
        'show_on_gallery_page' => '1',
        'published_at' => now()->format('Y-m-d H:i:s'),
    ])->assertRedirect(route('admin.galeri.show', $item));

    $newKey = app(MediaUrlResolver::class)->ownedKey($item->fresh()->media_url);
    expect($newKey)->toStartWith('gallery/media/'.$item->getKey().'/');
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

it('uploads one canonical media object and places it in multiple gallery sections', function (): void {
    $firstSection = GalleryPageSection::query()->create([
        'title_id' => 'Prestasi',
        'is_published' => true,
    ]);
    $secondSection = GalleryPageSection::query()->create([
        'title_id' => 'Kegiatan',
        'is_published' => true,
    ]);

    $this->post(route('admin.galeri.store'), [
        'title_id' => 'Satu Upload Dua Bagian',
        'type' => 'photo',
        'category_id' => 'Kegiatan',
        'media_file' => UploadedFile::fake()->image('one.jpg'),
        'is_published' => '1',
        'section_ids' => [$firstSection->id, $secondSection->id],
    ])->assertRedirect();

    $item = GalleryItem::query()->where('title_id', 'Satu Upload Dua Bagian')->firstOrFail();
    $key = app(MediaUrlResolver::class)->ownedKey($item->media_url);

    expect(GalleryItem::query()->count())->toBe(1)
        ->and($item->sections()->count())->toBe(2)
        ->and($key)->toStartWith('gallery/media/new/');
    Storage::disk('public')->assertExists($key);
    $this->assertDatabaseCount('gallery_item_gallery_page_section', 2);
});

it('keeps an old object when the retained legacy inventory still references it', function (): void {
    $section = GalleryPageSection::query()->create([
        'title_id' => 'Inventory Legacy',
        'is_published' => true,
    ]);
    Storage::disk('public')->put('gallery/page-media/shared.jpg', 'legacy-shared');

    $item = GalleryItem::query()->create([
        'title' => 'Media Shared',
        'title_id' => 'Media Shared',
        'type' => 'photo',
        'category' => 'Galeri',
        'category_id' => 'Galeri',
        'media_url' => '/storage/gallery/page-media/shared.jpg',
        'sort_order' => 1,
        'is_published' => true,
        'show_on_homepage' => false,
        'show_on_gallery_page' => false,
    ]);
    DB::table('gallery_page_media_items')->insert([
        'gallery_page_section_id' => $section->id,
        'type' => 'photo',
        'media_url' => $item->media_url,
        'is_published' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->put(route('admin.galeri.update', $item), [
        'title_id' => 'Media Shared Baru',
        'type' => 'photo',
        'category_id' => 'Galeri',
        'media_file' => UploadedFile::fake()->image('replacement.jpg'),
        'is_published' => '1',
    ])->assertRedirect(route('admin.galeri.show', $item));

    Storage::disk('public')->assertExists('gallery/page-media/shared.jpg');
});
