<?php

use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');

    $admin = User::query()->forceCreate([
        'name' => 'Admin Galeri Section Test',
        'email' => 'admin-gallery-section@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin);

    $this->makeSection = function (array $overrides = []): GalleryPageSection {
        return GalleryPageSection::query()->forceCreate(array_merge([
            'title_id' => 'Prestasi Sekolah',
            'title_en' => 'School Achievements',
            'description_id' => 'Dokumentasi prestasi siswa.',
            'description_en' => 'Student achievement documentation.',
            'is_published' => true,
        ], $overrides));
    };

    $this->makeMedia = function (GalleryPageSection $section, array $overrides = []): GalleryPageMediaItem {
        return GalleryPageMediaItem::query()->forceCreate(array_merge([
            'gallery_page_section_id' => $section->id,
            'type' => 'photo',
            'media_url' => '/storage/gallery/page/prestasi.jpg',
            'is_published' => true,
            'published_at' => now(),
            'title_id' => null,
            'title_en' => null,
            'description_id' => null,
            'description_en' => null,
        ], $overrides));
    };

    Storage::disk('public')->put('gallery/page/prestasi.jpg', 'prestasi-image');

    $this->section = ($this->makeSection)();
    $this->media = ($this->makeMedia)($this->section);
});

it('archives a gallery section without deleting its media record or file', function (): void {
    $response = $this->delete(route('admin.galeri.sections.destroy', $this->section));

    $response
        ->assertRedirect(route('admin.galeri'))
        ->assertSessionHas('success', 'Bagian galeri dipindahkan ke arsip. Semua media tetap tersimpan.');

    $this->assertSoftDeleted('gallery_page_sections', ['id' => $this->section->id]);
    $this->assertDatabaseHas('gallery_page_media_items', [
        'id' => $this->media->id,
        'deleted_at' => null,
    ]);

    expect(Storage::disk('public')->exists('gallery/page/prestasi.jpg'))->toBeTrue();

    $this->get(route('galeri'))
        ->assertOk()
        ->assertDontSee('Prestasi Sekolah');

    $this->get(route('admin.galeri'))
        ->assertOk()
        ->assertSee('Prestasi Sekolah')
        ->assertSee('Dihapus')
        ->assertSee('Pulihkan')
        ->assertSee('admin-section-card is-deleted', false);
});

it('restores a gallery section with all of its untouched media', function (): void {
    $this->section->delete();

    $response = $this->patch(route('admin.galeri.sections.restore', $this->section->id));

    $response
        ->assertRedirect(route('admin.galeri'))
        ->assertSessionHas('success', 'Bagian galeri berhasil dipulihkan beserta seluruh medianya.');

    $this->assertDatabaseHas('gallery_page_sections', [
        'id' => $this->section->id,
        'deleted_at' => null,
    ]);
    $this->assertDatabaseHas('gallery_page_media_items', [
        'id' => $this->media->id,
        'deleted_at' => null,
    ]);

    $this->get(route('galeri'))
        ->assertOk()
        ->assertSee('Prestasi Sekolah');
});

it('atomically swaps identical gallery sections while preserving both child collections', function (): void {
    Storage::disk('public')->put('gallery/page/replacement.jpg', 'replacement-image');

    $replacement = ($this->makeSection)([
        'title_id' => '  PRESTASI   SEKOLAH ',
        'title_en' => 'Replacement achievements',
    ]);
    $replacementMedia = ($this->makeMedia)($replacement, [
        'media_url' => '/storage/gallery/page/replacement.jpg',
    ]);

    $this->section->delete();

    $response = $this->patch(route('admin.galeri.sections.restore', $this->section->id), [
        'replacement_gallery_page_section_id' => $replacement->id,
    ]);

    $response
        ->assertRedirect(route('admin.galeri'))
        ->assertSessionHas('success', 'Bagian lama dipulihkan dan bagian aktif pengganti dipindahkan ke arsip. Semua media tetap tersimpan.');

    $this->assertDatabaseHas('gallery_page_sections', [
        'id' => $this->section->id,
        'deleted_at' => null,
    ]);
    $this->assertSoftDeleted('gallery_page_sections', ['id' => $replacement->id]);
    $this->assertDatabaseHas('gallery_page_media_items', [
        'id' => $this->media->id,
        'deleted_at' => null,
    ]);
    $this->assertDatabaseHas('gallery_page_media_items', [
        'id' => $replacementMedia->id,
        'deleted_at' => null,
    ]);

    expect(Storage::disk('public')->exists('gallery/page/prestasi.jpg'))->toBeTrue()
        ->and(Storage::disk('public')->exists('gallery/page/replacement.jpg'))->toBeTrue();
});

it('rejects replacing a gallery section with an unrelated active section', function (): void {
    $replacement = ($this->makeSection)(['title_id' => 'Fasilitas Sekolah']);
    $this->section->delete();

    $response = $this->from(route('admin.galeri'))->patch(
        route('admin.galeri.sections.restore', $this->section->id),
        ['replacement_gallery_page_section_id' => $replacement->id]
    );

    $response
        ->assertRedirect(route('admin.galeri'))
        ->assertSessionHasErrors('replacement_gallery_page_section_id');

    $this->assertSoftDeleted('gallery_page_sections', ['id' => $this->section->id]);
    $this->assertDatabaseHas('gallery_page_sections', [
        'id' => $replacement->id,
        'deleted_at' => null,
    ]);
});

it('archives one media item without deleting its file and hides it publicly', function (): void {
    $response = $this->delete(route('admin.galeri.section-media.destroy', $this->media));

    $response
        ->assertRedirect(route('admin.galeri.sections.show', $this->section))
        ->assertSessionHas('success', 'Media halaman galeri dipindahkan ke arsip dan dapat dipulihkan.');

    $this->assertSoftDeleted('gallery_page_media_items', ['id' => $this->media->id]);
    expect(Storage::disk('public')->exists('gallery/page/prestasi.jpg'))->toBeTrue();

    $this->get(route('galeri'))
        ->assertOk()
        ->assertDontSee('Prestasi Sekolah');

    $this->get(route('admin.galeri.sections.show', $this->section))
        ->assertOk()
        ->assertSee('Dihapus')
        ->assertSee('Pulihkan')
        ->assertSee('admin-media-card is-deleted', false);
});

it('restores an archived media item in its active section', function (): void {
    $this->media->delete();

    $response = $this->patch(route('admin.galeri.section-media.restore', $this->media->id));

    $response
        ->assertRedirect(route('admin.galeri.sections.show', $this->section))
        ->assertSessionHas('success', 'Media halaman galeri berhasil dipulihkan.');

    $this->assertDatabaseHas('gallery_page_media_items', [
        'id' => $this->media->id,
        'deleted_at' => null,
    ]);

    $this->get(route('galeri'))
        ->assertOk()
        ->assertSee('Prestasi Sekolah');
});

it('atomically swaps identical media only inside the same gallery section', function (): void {
    $replacement = ($this->makeMedia)($this->section, [
        'media_url' => '/storage/gallery/page/prestasi.jpg/',
    ]);
    $this->media->delete();

    $response = $this->patch(route('admin.galeri.section-media.restore', $this->media->id), [
        'replacement_gallery_page_media_item_id' => $replacement->id,
    ]);

    $response
        ->assertRedirect(route('admin.galeri.sections.show', $this->section))
        ->assertSessionHas('success', 'Media lama dipulihkan dan media aktif pengganti dipindahkan ke arsip.');

    $this->assertDatabaseHas('gallery_page_media_items', [
        'id' => $this->media->id,
        'deleted_at' => null,
    ]);
    $this->assertSoftDeleted('gallery_page_media_items', ['id' => $replacement->id]);
    expect(Storage::disk('public')->exists('gallery/page/prestasi.jpg'))->toBeTrue();
});

it('rejects media replacement from another section without changing either status', function (): void {
    $otherSection = ($this->makeSection)(['title_id' => 'Bagian Lain']);
    $replacement = ($this->makeMedia)($otherSection);
    $this->media->delete();

    $response = $this->from(route('admin.galeri.sections.show', $this->section))->patch(
        route('admin.galeri.section-media.restore', $this->media->id),
        ['replacement_gallery_page_media_item_id' => $replacement->id]
    );

    $response
        ->assertRedirect(route('admin.galeri.sections.show', $this->section))
        ->assertSessionHasErrors('replacement_gallery_page_media_item_id');

    $this->assertSoftDeleted('gallery_page_media_items', ['id' => $this->media->id]);
    $this->assertDatabaseHas('gallery_page_media_items', [
        'id' => $replacement->id,
        'deleted_at' => null,
    ]);
});

it('blocks direct media management while its parent section is archived', function (): void {
    $this->section->delete();

    $this->get(route('admin.galeri.section-media.show', $this->media))->assertNotFound();
    $this->get(route('admin.galeri.section-media.edit', $this->media))->assertNotFound();
    $this->patch(route('admin.galeri.section-media.toggle', $this->media))->assertNotFound();
    $this->delete(route('admin.galeri.section-media.destroy', $this->media))->assertNotFound();

    $this->assertDatabaseHas('gallery_page_media_items', [
        'id' => $this->media->id,
        'deleted_at' => null,
    ]);
});

it('keeps a shared archived photo when an active media item replaces its upload', function (): void {
    $archived = ($this->makeMedia)($this->section);
    $archived->delete();

    $response = $this->put(route('admin.galeri.section-media.update', $this->media), [
        'type' => 'photo',
        'media_file' => UploadedFile::fake()->image('replacement.jpg'),
        'is_published' => '1',
        'published_at' => now()->format('Y-m-d H:i:s'),
    ]);

    $response->assertRedirect(route('admin.galeri.section-media.show', $this->media));

    expect(Storage::disk('public')->exists('gallery/page/prestasi.jpg'))->toBeTrue();
});

it('does not allow restore endpoints to act on active records', function (): void {
    $this->patch(route('admin.galeri.sections.restore', $this->section->id))->assertNotFound();
    $this->patch(route('admin.galeri.section-media.restore', $this->media->id))->assertNotFound();
});
