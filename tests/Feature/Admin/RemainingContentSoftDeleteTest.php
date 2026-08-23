<?php

use App\Http\Controllers\HomeController;
use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use App\Models\SiteStatistic;
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
    DB::table('site_statistics')->delete();
    PpdbSetting::query()->updateOrCreate(
        ['id' => 1],
        [
            'registration_url' => 'https://apply.example.test/archive-proof',
            'is_active' => true,
        ],
    );

    $admin = User::query()->forceCreate([
        'name' => 'Admin Arsip Konten Test',
        'email' => 'admin-arsip-konten@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin);
});

afterEach(function (): void {
    app()->setLocale('id');
});

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

    $this->get(route('ppdb'))
        ->assertOk()
        ->assertDontSee('Item PPDB Arsip Unik');

    $this->get(route('admin.ppdb'))
        ->assertOk()
        ->assertSee('Item PPDB Arsip Unik')
        ->assertSee('Dihapus')
        ->assertSee('Pulihkan')
        ->assertSee('is-deleted', false);

    $this->get(route('admin.ppdb.showcase.edit', $item->id))->assertNotFound();
});

it('restores a PPDB showcase item into the next position of its audience', function (): void {
    makePpdbArchiveItem([
        'title_id' => 'Item PPDB Aktif',
        'sort_order' => 1,
    ]);

    $archived = makePpdbArchiveItem([
        'title_id' => 'Item PPDB Lama',
        'sort_order' => 2,
    ]);
    $archived->delete();

    $this->patch(route('admin.ppdb.showcase.restore', $archived->id))
        ->assertRedirect(route('admin.ppdb').'#ppdb-showcase-admin')
        ->assertSessionHas('success');

    $restored = $archived->fresh();

    expect($restored)->not->toBeNull();
    expect($restored->sort_order)->toBe(2);
});

it('atomically swaps PPDB items with identical audience and normalized Indonesian title', function (): void {
    $archived = makePpdbArchiveItem([
        'title_id' => '  Observasi   Anak  ',
        'description_id' => 'Versi lama yang akan dipulihkan.',
        'sort_order' => 1,
    ]);
    $archived->delete();

    $replacement = makePpdbArchiveItem([
        'title_id' => 'observasi anak',
        'description_id' => 'Versi aktif pengganti.',
        'sort_order' => 1,
    ]);

    $this->patch(route('admin.ppdb.showcase.restore', $archived->id), [
        'replacement_ppdb_showcase_item_id' => $replacement->id,
    ])->assertSessionHas('success');

    $restored = $archived->fresh();

    expect($restored)->not->toBeNull();
    expect($restored->sort_order)->toBe(1);
    expect(PpdbShowcaseItem::withTrashed()->findOrFail($replacement->id)->trashed())->toBeTrue();
});

it('rejects an unrelated or cross-audience PPDB replacement without changing statuses', function (): void {
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

    expect(PpdbShowcaseItem::withTrashed()->findOrFail($archived->id)->trashed())->toBeTrue();
    expect($replacement->fresh())->not->toBeNull();
});

it('keeps a PPDB photo that is still referenced by an archived item during active replacement upload', function (): void {
    Storage::disk('public')->put('ppdb/showcase/shared.jpg', 'shared');

    $archived = makePpdbArchiveItem([
        'title_id' => 'Arsip pemilik foto bersama',
        'media_url' => '/storage/ppdb/showcase/shared.jpg',
    ]);
    $archived->delete();

    $active = makePpdbArchiveItem([
        'title_id' => 'Aktif pemilik foto bersama',
        'media_url' => '/storage/ppdb/showcase/shared.jpg',
    ]);

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

it('soft deletes a statistic, hides it publicly, and protects the final active statistic', function (): void {
    $archived = makeStatisticArchiveItem([
        'value' => '111',
        'value_en' => '111',
        'label' => 'Label statistik arsip unik',
        'label_en' => 'Unique archived statistic label',
        'sort_order' => 1,
    ]);

    $survivor = makeStatisticArchiveItem([
        'value' => '222',
        'value_en' => '222',
        'label' => 'Label statistik aktif unik',
        'label_en' => 'Unique active statistic label',
        'sort_order' => 2,
    ]);

    $this->delete(route('admin.stats.destroy', $archived))
        ->assertRedirect(route('admin.stats.edit'))
        ->assertSessionHas('success');

    $this->assertSoftDeleted('site_statistics', ['id' => $archived->id]);
    expect($survivor->fresh()->sort_order)->toBe(1);

    $homepage = app(HomeController::class)();
    $labels = collect($homepage->getData()['stats'])->pluck('label');
    expect($labels)->not->toContain('Label statistik arsip unik');

    $this->get(route('admin.stats.edit'))
        ->assertOk()
        ->assertSee('Label statistik arsip unik')
        ->assertSee('Arsip')
        ->assertSee('Pulihkan');

    $this->delete(route('admin.stats.destroy', $survivor))
        ->assertSessionHasErrors('delete');
});

it('restores an archived statistic under the active limit and normalizes its position', function (): void {
    makeStatisticArchiveItem([
        'label' => 'Statistik aktif',
        'label_en' => 'Active statistic',
        'sort_order' => 1,
    ]);

    $archived = makeStatisticArchiveItem([
        'label' => 'Statistik lama',
        'label_en' => 'Old statistic',
        'sort_order' => 2,
    ]);
    $archived->delete();

    $this->patch(route('admin.stats.restore', $archived->id))
        ->assertSessionHas('success');

    $restored = $archived->fresh();

    expect($restored)->not->toBeNull();
    expect($restored->sort_order)->toBe(2);
});

it('requires statistic replacement when all four active slots are occupied', function (): void {
    foreach (range(1, SiteStatistic::MAX_ITEMS) as $position) {
        makeStatisticArchiveItem([
            'label' => "Statistik aktif {$position}",
            'label_en' => "Active statistic {$position}",
            'sort_order' => $position,
        ]);
    }

    $archived = makeStatisticArchiveItem([
        'label' => 'Statistik arsip tanpa pasangan',
        'label_en' => 'Archived statistic without pair',
        'sort_order' => 5,
    ]);
    $archived->delete();

    $this->patch(route('admin.stats.restore', $archived->id))
        ->assertSessionHasErrors('replacement_site_statistic_id');

    expect(SiteStatistic::query()->count())->toBe(SiteStatistic::MAX_ITEMS);
    expect(SiteStatistic::withTrashed()->findOrFail($archived->id)->trashed())->toBeTrue();
});

it('atomically swaps statistics with identical normalized bilingual labels', function (): void {
    $archived = makeStatisticArchiveItem([
        'value' => '100',
        'value_en' => '100',
        'label' => '  Siswa   Aktif ',
        'label_en' => ' Active   Students ',
        'sort_order' => 1,
    ]);
    $archived->delete();

    $replacement = makeStatisticArchiveItem([
        'value' => '200',
        'value_en' => '200',
        'label' => 'siswa aktif',
        'label_en' => 'active students',
        'sort_order' => 1,
    ]);

    $this->patch(route('admin.stats.restore', $archived->id), [
        'replacement_site_statistic_id' => $replacement->id,
    ])->assertSessionHas('success');

    $restored = $archived->fresh();

    expect($restored)->not->toBeNull();
    expect($restored->value)->toBe('100');
    expect($restored->sort_order)->toBe(1);
    expect(SiteStatistic::withTrashed()->findOrFail($replacement->id)->trashed())->toBeTrue();
});

it('rejects an unrelated statistic replacement and active restore endpoint use', function (): void {
    $archived = makeStatisticArchiveItem([
        'label' => 'Siswa aktif',
        'label_en' => 'Active students',
    ]);
    $archived->delete();

    $replacement = makeStatisticArchiveItem([
        'label' => 'Guru aktif',
        'label_en' => 'Active teachers',
    ]);

    $this->patch(route('admin.stats.restore', $archived->id), [
        'replacement_site_statistic_id' => $replacement->id,
    ])->assertSessionHasErrors('replacement_site_statistic_id');

    expect(SiteStatistic::withTrashed()->findOrFail($archived->id)->trashed())->toBeTrue();
    expect($replacement->fresh())->not->toBeNull();

    $this->patch(route('admin.stats.restore', $replacement->id))->assertNotFound();
});

it('does not reseed default statistics when only archived statistics remain', function (): void {
    $archived = makeStatisticArchiveItem([
        'label' => 'Satu-satunya statistik arsip',
        'label_en' => 'Only archived statistic',
    ]);
    $archived->delete();

    $this->get(route('admin.stats.edit'))
        ->assertOk()
        ->assertSee('Satu-satunya statistik arsip');

    expect(SiteStatistic::query()->count())->toBe(0);
    expect(SiteStatistic::withTrashed()->count())->toBe(1);
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

function makeStatisticArchiveItem(array $overrides = []): SiteStatistic
{
    return SiteStatistic::query()->create([
        'value' => '100+',
        'value_en' => '100+',
        'label' => 'Statistik test',
        'label_en' => 'Test statistic',
        'sort_order' => 1,
        ...$overrides,
    ]);
}
