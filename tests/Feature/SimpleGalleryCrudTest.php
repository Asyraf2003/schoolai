<?php

use App\Models\GalleryItem;
use App\Models\User;
use App\Support\Media\MediaUrlResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('creates edits deletes and restores a gallery photo using R2', function (): void {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->post(route('admin.galeri.store'), ['media_file' => UploadedFile::fake()->image('photo.jpg'), 'alt_text' => 'Foto kelas'])
        ->assertRedirect(route('admin.galeri'))->assertSessionHasNoErrors();
    $item = GalleryItem::sole();
    $key = app(MediaUrlResolver::class)->ownedKey($item->media_url);
    Storage::disk('public')->assertExists($key);
    expect($item->type)->toBe('photo');
    $this->get(route('admin.galeri.edit', $item))->assertOk()->assertSee('Foto kelas');
    $this->put(route('admin.galeri.update', $item), ['alt_text' => 'Alt baru'])->assertSessionHasNoErrors();
    expect($item->fresh()->alt_text)->toBe('Alt baru')->and($item->fresh()->media_url)->toBe($item->media_url);
    $this->get(route('home'))->assertOk()->assertSee('Alt baru');
    $this->delete(route('admin.galeri.destroy', $item))->assertRedirect(route('admin.galeri'));
    $this->assertSoftDeleted($item);
    $this->get(route('home'))->assertDontSee('Alt baru');
    Storage::disk('public')->assertExists($key);
    $this->patch(route('admin.galeri.restore', $item))->assertRedirect(route('admin.galeri'));
    $this->get(route('home'))->assertSee('Alt baru');
});

it('uploads video and thumbnail and exposes new videos on V2 rather than a hardcoded catalog', function (): void {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $video = UploadedFile::fake()->createWithContent('video.mp4', hex2bin('000000206674797069736f6d0000020069736f6d69736f32617663316d703431'));
    $this->post(route('admin.galeri.store'), ['media_file' => $video, 'thumbnail_file' => UploadedFile::fake()->image('poster.jpg'), 'alt_text' => 'Video baru'])
        ->assertSessionHasNoErrors();
    $item = GalleryItem::sole();
    expect($item->type)->toBe('video')->and($item->thumbnail_url)->toStartWith(config('media.public_url').'/gallery/thumbnails/');
    $this->get(route('home'))->assertSee($item->media_url, false)->assertSee($item->thumbnail_url, false)->assertSee('Video baru');
    Storage::disk('public')->assertExists(app(MediaUrlResolver::class)->ownedKey($item->thumbnail_url));
});

it('replaces media and thumbnail without deleting previously referenced files', function (): void {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->post(route('admin.galeri.store'), ['media_file' => UploadedFile::fake()->image('first.jpg')])->assertSessionHasNoErrors();
    $item = GalleryItem::sole();
    $old = $item->media_url;
    $this->put(route('admin.galeri.update', $item), ['media_file' => UploadedFile::fake()->image('second.jpg'), 'thumbnail_file' => UploadedFile::fake()->image('poster.jpg')])->assertSessionHasNoErrors();
    expect($item->fresh()->media_url)->not->toBe($old);
    Storage::disk('public')->assertExists(app(MediaUrlResolver::class)->ownedKey($old));
    $this->put(route('admin.galeri.update', $item), ['remove_thumbnail' => true])->assertSessionHasNoErrors();
    expect($item->fresh()->thumbnail_url)->toBeNull();
});

it('rejects missing invalid and active media payloads before storing files', function (): void {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->post(route('admin.galeri.store'), [])->assertSessionHasErrors('media_file');
    $this->post(route('admin.galeri.store'), ['media_file' => UploadedFile::fake()->createWithContent('evil.jpg', '<?php echo "evil";')])->assertSessionHasErrors('media_file');
    $this->post(route('admin.galeri.store'), ['media_file' => UploadedFile::fake()->image('safe.jpg'), 'thumbnail_file' => UploadedFile::fake()->createWithContent('evil.svg', '<svg onload="evil()"></svg>')])->assertSessionHasErrors('thumbnail_file');
    expect(GalleryItem::count())->toBe(0)->and(Storage::disk('public')->allFiles())->toBeEmpty();
});
