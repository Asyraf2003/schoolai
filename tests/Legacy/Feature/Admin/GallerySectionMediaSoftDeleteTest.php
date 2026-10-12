<?php

use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('gallery/media/prestasi.jpg', 'prestasi-image');

    $this->actingAs(User::query()->forceCreate([
        'name' => 'Admin Galeri Section Test',
        'email' => 'admin-gallery-section@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));

    $this->section = GalleryPageSection::query()->create([
        'title_id' => 'Prestasi Sekolah',
        'title_en' => 'School Achievements',
        'is_published' => true,
    ]);

    $this->otherSection = GalleryPageSection::query()->create([
        'title_id' => 'Kegiatan Sekolah',
        'title_en' => 'School Activities',
        'is_published' => true,
    ]);

    $this->media = GalleryItem::query()->create([
        'title' => 'Prestasi Siswa',
        'title_id' => 'Prestasi Siswa',
        'type' => 'photo',
        'category' => 'Prestasi',
        'category_id' => 'Prestasi',
        'media_url' => '/storage/gallery/media/prestasi.jpg',
        'sort_order' => 1,
        'is_published' => true,
        'show_on_homepage' => false,
        'show_on_gallery_page' => false,
        'published_at' => now(),
    ]);
});

it('places one canonical media item in multiple sections without duplicate media rows', function (): void {
    $this->put(route('admin.galeri.sections.media.update', $this->section), [
        'gallery_item_ids' => [$this->media->id],
    ])->assertRedirect();

    $this->put(route('admin.galeri.sections.media.update', $this->otherSection), [
        'gallery_item_ids' => [$this->media->id],
    ])->assertRedirect();

    expect(GalleryItem::query()->count())->toBe(1);
    $this->assertDatabaseCount('gallery_item_gallery_page_section', 2);
    $this->assertDatabaseHas('gallery_item_gallery_page_section', [
        'gallery_item_id' => $this->media->id,
        'gallery_page_section_id' => $this->section->id,
        'is_published' => true,
    ]);

    $this->get(route('galeri'))
        ->assertOk()
        ->assertSee('Prestasi Sekolah')
        ->assertSee('Kegiatan Sekolah');
});

it('removes one placement without deleting the media or another placement', function (): void {
    $this->media->sections()->attach([
        $this->section->id => ['sort_order' => 1, 'is_published' => true],
        $this->otherSection->id => ['sort_order' => 1, 'is_published' => true],
    ]);

    $this->put(route('admin.galeri.sections.media.update', $this->section), [
        'gallery_item_ids' => [],
    ])->assertRedirect();

    expect($this->media->fresh())->not->toBeNull();
    $this->assertDatabaseMissing('gallery_item_gallery_page_section', [
        'gallery_item_id' => $this->media->id,
        'gallery_page_section_id' => $this->section->id,
    ]);
    $this->assertDatabaseHas('gallery_item_gallery_page_section', [
        'gallery_item_id' => $this->media->id,
        'gallery_page_section_id' => $this->otherSection->id,
    ]);
    expect(Storage::disk('public')->exists('gallery/media/prestasi.jpg'))->toBeTrue();
});

it('archives and restores a section while preserving its canonical placements', function (): void {
    $this->media->sections()->attach($this->section->id, [
        'sort_order' => 1,
        'is_published' => true,
    ]);

    $this->delete(route('admin.galeri.sections.destroy', $this->section))
        ->assertRedirect(route('admin.galeri'));

    $this->assertSoftDeleted('gallery_page_sections', ['id' => $this->section->id]);
    $this->assertDatabaseHas('gallery_item_gallery_page_section', [
        'gallery_item_id' => $this->media->id,
        'gallery_page_section_id' => $this->section->id,
    ]);
    expect(Storage::disk('public')->exists('gallery/media/prestasi.jpg'))->toBeTrue();

    $this->get(route('galeri'))->assertOk()->assertDontSee('Prestasi Sekolah');

    $this->patch(route('admin.galeri.sections.restore', $this->section->id))
        ->assertRedirect(route('admin.galeri'));

    $this->get(route('galeri'))->assertOk()->assertSee('Prestasi Sekolah');
});

it('rejects placement of archived canonical media', function (): void {
    $this->media->delete();

    $this->from(route('admin.galeri.sections.show', $this->section))
        ->put(route('admin.galeri.sections.media.update', $this->section), [
            'gallery_item_ids' => [$this->media->id],
        ])
        ->assertRedirect(route('admin.galeri.sections.show', $this->section))
        ->assertSessionHasErrors('gallery_item_ids.0');

    $this->assertDatabaseCount('gallery_item_gallery_page_section', 0);
});

it('retires the duplicate per-section media CRUD routes', function (): void {
    expect(Route::has('admin.galeri.section-media.create'))->toBeFalse()
        ->and(Route::has('admin.galeri.section-media.store'))->toBeFalse()
        ->and(Route::has('admin.galeri.section-media.update'))->toBeFalse()
        ->and(Route::has('admin.galeri.section-media.destroy'))->toBeFalse();
});
