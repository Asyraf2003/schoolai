<?php

use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    DB::table('ppdb_showcase_items')->delete();
    PpdbSetting::query()->updateOrCreate(
        ['id' => 1],
        ['registration_url' => 'https://apply.example.test/archive-proof', 'is_active' => true],
    );
    $this->actingAs(User::query()->forceCreate([
        'name' => 'Admin Arsip Konten Test',
        'email' => 'admin-arsip-konten@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));
});

afterEach(fn () => app()->setLocale('id'));

it('archives a PPDB showcase item without deleting its file and hides it publicly', function (): void {
    Storage::disk('public')->put('ppdb/showcase/arsip.jpg', 'arsip');
    $item = makePpdbArchiveItem([
        'title_id' => 'Item PPDB Arsip Unik',
        'media_url' => '/storage/ppdb/showcase/arsip.jpg',
    ]);

    $this->delete(route('admin.ppdb.showcase.destroy', $item))
        ->assertRedirect(route('admin.ppdb').'#ppdb-showcase-admin')
        ->assertSessionHas('success');

    $this->assertSoftDeleted('ppdb_showcase_items', ['id' => $item->id]);
    expect(Storage::disk('public')->exists('ppdb/showcase/arsip.jpg'))->toBeTrue();
    $this->get(route('ppdb'))->assertOk()->assertDontSee('Item PPDB Arsip Unik');
    $this->get(route('admin.ppdb'))->assertOk()->assertSee('Item PPDB Arsip Unik')->assertSee('Pulihkan');
    $this->get(route('admin.ppdb.showcase.edit', $item->id))->assertNotFound();
});

it('restores a PPDB showcase item into the next position of its audience', function (): void {
    makePpdbArchiveItem(['title_id' => 'Item PPDB Aktif', 'sort_order' => 1]);
    $archived = makePpdbArchiveItem(['title_id' => 'Item PPDB Lama', 'sort_order' => 2]);
    $archived->delete();

    $this->patch(route('admin.ppdb.showcase.restore', $archived->id))->assertSessionHas('success');

    expect($archived->fresh())->not->toBeNull()
        ->and($archived->fresh()->sort_order)->toBe(2);
});

it('atomically swaps PPDB items with identical audience and normalized Indonesian title', function (): void {
    $archived = makePpdbArchiveItem(['title_id' => '  Observasi   Anak  ']);
    $archived->delete();
    $replacement = makePpdbArchiveItem(['title_id' => 'observasi anak']);

    $this->patch(route('admin.ppdb.showcase.restore', $archived->id), [
        'replacement_ppdb_showcase_item_id' => $replacement->id,
    ])->assertSessionHas('success');

    expect($archived->fresh())->not->toBeNull()
        ->and(PpdbShowcaseItem::withTrashed()->findOrFail($replacement->id)->trashed())->toBeTrue();
});

it('rejects an unrelated or cross-audience PPDB replacement', function (): void {
    $archived = makePpdbArchiveItem([
        'title_id' => 'Konsultasi Keluarga',
        'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,
    ]);
    $archived->delete();
    $replacement = makePpdbArchiveItem([
        'title_id' => 'Konsultasi Keluarga',
        'audience' => PpdbShowcaseItem::AUDIENCE_SCHOOL,
    ]);

    $this->patch(route('admin.ppdb.showcase.restore', $archived->id), [
        'replacement_ppdb_showcase_item_id' => $replacement->id,
    ])->assertSessionHasErrors('replacement_ppdb_showcase_item_id');

    expect(PpdbShowcaseItem::withTrashed()->findOrFail($archived->id)->trashed())->toBeTrue()
        ->and($replacement->fresh())->not->toBeNull();
});

it('keeps a PPDB photo still referenced by an archived item during replacement', function (): void {
    Storage::disk('public')->put('ppdb/showcase/shared.jpg', 'shared');
    $archived = makePpdbArchiveItem(['media_url' => '/storage/ppdb/showcase/shared.jpg']);
    $archived->delete();
    $active = makePpdbArchiveItem(['media_url' => '/storage/ppdb/showcase/shared.jpg']);

    $this->put(route('admin.ppdb.showcase.update', $active), [
        'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,
        'title_id' => 'Aktif pemilik foto bersama',
        'title_en' => 'Active shared photo owner',
        'description_id' => 'Deskripsi aktif.',
        'description_en' => 'Active description.',
        'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
        'media_file' => UploadedFile::fake()->image('baru.jpg'),
    ])->assertSessionHas('success');

    expect(Storage::disk('public')->exists('ppdb/showcase/shared.jpg'))->toBeTrue();
});

it('does not allow the PPDB restore endpoint to act on an active item', function (): void {
    $active = makePpdbArchiveItem();

    $this->patch(route('admin.ppdb.showcase.restore', $active->id))->assertNotFound();
});

function makePpdbArchiveItem(array $overrides = []): PpdbShowcaseItem
{
    return PpdbShowcaseItem::query()->create([
        'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,
        'title_id' => 'Item PPDB Test',
        'title_en' => 'Admission Test Item',
        'description_id' => 'Deskripsi item PPDB untuk test.',
        'description_en' => 'Admission item description for testing.',
        'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
        'media_url' => '/storage/ppdb/showcase/default.jpg',
        'sort_order' => 1,
        ...$overrides,
    ]);
}
