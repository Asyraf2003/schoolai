<?php

use App\Models\GalleryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    DB::table('gallery_items')->delete();

    $admin = User::query()->forceCreate([
        'name' => 'Admin Galeri Test',
        'email' => 'admin-galeri@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin);
});

it('soft deletes a gallery item without deleting its media and hides it publicly', function (): void {
    Storage::disk('public')->put('gallery/photos/arsip.jpg', 'arsip');

    $item = galleryItem([
        'title_id' => 'Galeri untuk diarsipkan',
        'media_url' => '/storage/gallery/photos/arsip.jpg',
        'sort_order' => 1,
    ]);
    $survivor = galleryItem([
        'title_id' => 'Galeri yang tetap aktif',
        'media_url' => '/storage/gallery/photos/survivor.jpg',
        'sort_order' => 2,
    ]);

    $response = $this->delete(route('admin.galeri.destroy', $item));

    $response
        ->assertRedirect(route('admin.galeri'))
        ->assertSessionHas('success', 'Item galeri dipindahkan ke arsip dan dapat dipulihkan.');

    $this->assertSoftDeleted('gallery_items', ['id' => $item->id]);
    expect(Storage::disk('public')->exists('gallery/photos/arsip.jpg'))->toBeTrue();
    expect($survivor->fresh()->sort_order)->toBe(1);

    $this->get(route('galeri'))
        ->assertOk()
        ->assertDontSee('Galeri untuk diarsipkan');

    $this->get(route('admin.galeri'))
        ->assertOk()
        ->assertSee('Galeri untuk diarsipkan')
        ->assertSee('Dihapus')
        ->assertSee('Pulihkan')
        ->assertSee('is-deleted', false);

    $this->get(route('admin.galeri.edit', $item->id))->assertNotFound();
});

it('restores an archived gallery item into the next active sort position', function (): void {
    $first = galleryItem([
        'title_id' => 'Galeri aktif',
        'media_url' => '/storage/gallery/photos/active.jpg',
        'sort_order' => 1,
    ]);
    $archived = galleryItem([
        'title_id' => 'Galeri lama',
        'media_url' => '/storage/gallery/photos/old.jpg',
        'sort_order' => 2,
    ]);
    $archived->delete();

    $response = $this->patch(route('admin.galeri.restore', $archived->id));

    $response
        ->assertRedirect(route('admin.galeri'))
        ->assertSessionHas('success', 'Item galeri berhasil dipulihkan.');

    expect($first->fresh()->sort_order)->toBe(1)
        ->and($archived->fresh()->deleted_at)->toBeNull()
        ->and($archived->fresh()->sort_order)->toBe(2);

    $this->get(route('galeri'))
        ->assertOk()
        ->assertSee('Galeri lama');
});

it('matches replacement candidates only when type and normalized media are identical', function (): void {
    $archived = galleryItem([
        'title_id' => 'Video lama',
        'type' => 'video',
        'media_url' => 'HTTPS://WWW.YOUTUBE.COM:443/embed/abc123/?b=2&a=1#preview',
        'sort_order' => 1,
    ]);
    $archived->delete();

    $identical = galleryItem([
        'title_id' => 'Video pengganti identik',
        'type' => 'video',
        'media_url' => 'https://www.youtube.com/embed/abc123?a=1&b=2',
        'sort_order' => 1,
    ]);
    $unrelated = galleryItem([
        'title_id' => 'Video berbeda',
        'type' => 'video',
        'media_url' => 'https://www.youtube.com/embed/different',
        'sort_order' => 2,
    ]);

    expect($archived->replacementIdentity())->toBe($identical->replacementIdentity())
        ->and($archived->replacementIdentity())->not->toBe($unrelated->replacementIdentity());

    $this->get(route('admin.galeri'))
        ->assertOk()
        ->assertSee('Pulihkan &amp; Gantikan #'.$identical->id, false)
        ->assertDontSee('Pulihkan &amp; Gantikan #'.$unrelated->id, false);
});

it('atomically restores an archived gallery item and archives its identical active replacement', function (): void {
    $archived = galleryItem([
        'title_id' => 'Galeri lama identik',
        'type' => 'photo',
        'media_url' => '/storage/gallery/photos/same-photo.jpg',
        'sort_order' => 1,
        'is_published' => true,
    ]);
    $archived->delete();

    $replacement = galleryItem([
        'title_id' => 'Galeri aktif pengganti',
        'type' => 'photo',
        'media_url' => '/storage/gallery/photos/same-photo.jpg/',
        'sort_order' => 1,
        'is_published' => true,
    ]);

    $response = $this->patch(route('admin.galeri.restore', $archived->id), [
        'replacement_gallery_item_id' => $replacement->id,
    ]);

    $response
        ->assertRedirect(route('admin.galeri'))
        ->assertSessionHas('success', 'Item galeri lama dipulihkan dan item aktif pengganti dipindahkan ke arsip.');

    expect($archived->fresh()->deleted_at)->toBeNull()
        ->and($archived->fresh()->sort_order)->toBe(1);
    $this->assertSoftDeleted('gallery_items', ['id' => $replacement->id]);

    $this->get(route('galeri'))
        ->assertOk()
        ->assertSee('Galeri lama identik')
        ->assertDontSee('Galeri aktif pengganti');
});

it('rejects replacing an unrelated active gallery item without changing either status', function (): void {
    $archived = galleryItem([
        'title_id' => 'Galeri arsip',
        'type' => 'video',
        'media_url' => 'https://www.youtube.com/embed/archive',
        'sort_order' => 1,
    ]);
    $archived->delete();

    $unrelated = galleryItem([
        'title_id' => 'Galeri tidak berkaitan',
        'type' => 'video',
        'media_url' => 'https://www.youtube.com/embed/unrelated',
        'sort_order' => 1,
    ]);

    $response = $this->from(route('admin.galeri'))->patch(route('admin.galeri.restore', $archived->id), [
        'replacement_gallery_item_id' => $unrelated->id,
    ]);

    $response
        ->assertRedirect(route('admin.galeri'))
        ->assertSessionHasErrors('replacement_gallery_item_id');

    expect($archived->fresh()->trashed())->toBeTrue()
        ->and($unrelated->fresh()->trashed())->toBeFalse();
});

it('restores a canonical item without exceeding the six homepage placements', function (): void {
    foreach (range(1, GalleryItem::MAX_ITEMS) as $position) {
        galleryItem([
            'title_id' => 'Galeri aktif '.$position,
            'media_url' => '/storage/gallery/photos/active-'.$position.'.jpg',
            'sort_order' => $position,
        ]);
    }

    $archived = galleryItem([
        'title_id' => 'Galeri ketujuh yang diarsipkan',
        'media_url' => '/storage/gallery/photos/seventh.jpg',
        'sort_order' => 7,
    ]);
    $archived->delete();

    $response = $this->from(route('admin.galeri'))->patch(route('admin.galeri.restore', $archived->id));

    $response
        ->assertRedirect(route('admin.galeri'))
        ->assertSessionHasNoErrors();

    expect($archived->fresh()->trashed())->toBeFalse()
        ->and($archived->fresh()->show_on_homepage)->toBeFalse()
        ->and(GalleryItem::query()->homepage()->count())->toBe(GalleryItem::MAX_ITEMS)
        ->and(GalleryItem::query()->count())->toBe(GalleryItem::MAX_ITEMS + 1);
});

it('allows the canonical collection to have no published item', function (): void {
    $onlyPublished = galleryItem([
        'title_id' => 'Satu-satunya galeri terbit',
        'type' => 'video',
        'media_url' => 'https://www.youtube.com/embed/only-published',
        'sort_order' => 1,
        'is_published' => true,
    ]);

    $response = $this->patch(route('admin.galeri.toggle', $onlyPublished));

    $response->assertRedirect();

    expect($onlyPublished->fresh()->is_published)->toBeFalse();
});

it('does not allow the restore endpoint to act on an active gallery item', function (): void {
    $active = galleryItem([
        'title_id' => 'Galeri aktif biasa',
        'media_url' => '/storage/gallery/photos/ordinary.jpg',
    ]);

    $this->patch(route('admin.galeri.restore', $active->id))->assertNotFound();
});

it('keeps a shared archived photo when an active item replaces its upload', function (): void {
    Storage::disk('public')->put('gallery/photos/shared.jpg', 'shared-photo');

    $archived = galleryItem([
        'title_id' => 'Arsip dengan foto bersama',
        'media_url' => '/storage/gallery/photos/shared.jpg',
        'sort_order' => 1,
    ]);
    $archived->delete();

    $active = galleryItem([
        'title_id' => 'Aktif dengan foto bersama',
        'media_url' => '/storage/gallery/photos/shared.jpg',
        'sort_order' => 1,
    ]);

    $response = $this->put(route('admin.galeri.update', $active), [
        'title_id' => 'Aktif dengan foto baru',
        'title_en' => null,
        'type' => 'photo',
        'category_id' => 'Kegiatan',
        'category_en' => null,
        'caption_id' => null,
        'caption_en' => null,
        'media_file' => UploadedFile::fake()->image('new-photo.jpg'),
        'is_published' => '1',
        'published_at' => now()->format('Y-m-d H:i:s'),
    ]);

    $response->assertRedirect(route('admin.galeri.show', $active));

    expect(Storage::disk('public')->exists('gallery/photos/shared.jpg'))->toBeTrue()
        ->and($archived->fresh()->trashed())->toBeTrue();
});

function galleryItem(array $overrides = []): GalleryItem
{
    $attributes = array_merge([
        'title' => 'Galeri Test',
        'title_id' => 'Galeri Test',
        'title_en' => 'Gallery Test',
        'type' => 'photo',
        'category' => 'Kegiatan',
        'category_id' => 'Kegiatan',
        'category_en' => 'Activities',
        'caption' => null,
        'caption_id' => null,
        'caption_en' => null,
        'media_url' => '/storage/gallery/photos/default.jpg',
        'sort_order' => 1,
        'is_published' => true,
        'show_on_homepage' => true,
        'show_on_gallery_page' => true,
        'published_at' => now(),
    ], $overrides);

    $sortOrder = (int) $attributes['sort_order'];
    unset($attributes['sort_order']);

    $item = new GalleryItem($attributes);
    $item->forceFill(['sort_order' => $sortOrder])->save();

    return $item;
}
