<?php

use App\Models\SiteStatistic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $user = User::query()->forceCreate([
        'name' => 'Admin Statistik Test',
        'email' => 'admin-statistik@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($user);
});

afterEach(function (): void {
    app()->setLocale('id');
});

it('renders bilingual statistics inside the admin panel', function (): void {
    SiteStatistic::query()->create([
        'value' => '100+',
        'value_en' => '100+',
        'label' => 'Siswa aktif',
        'label_en' => 'Active students',
        'sort_order' => 1,
    ]);

    $response = $this->get(route('admin.stats.edit'));

    $response
        ->assertOk()
        ->assertSee('Statistik Homepage')
        ->assertSee('Tambah Statistik')
        ->assertSee('Siswa aktif')
        ->assertSee('Active students')
        ->assertDontSee('Naik')
        ->assertDontSee('Turun');
});

it('creates and updates a bilingual homepage statistic', function (): void {
    $createResponse = $this->post(route('admin.stats.store'), [
        'value' => '250+',
        'value_en' => '250+',
        'label' => 'Siswa dan alumni',
        'label_en' => 'Students and alumni',
    ]);

    $createResponse
        ->assertRedirect(route('admin.stats.edit'))
        ->assertSessionHas('success');

    $statistic = SiteStatistic::query()->firstOrFail();

    expect($statistic->value)->toBe('250+')
        ->and($statistic->value_en)->toBe('250+')
        ->and($statistic->label)->toBe('Siswa dan alumni')
        ->and($statistic->label_en)->toBe('Students and alumni')
        ->and($statistic->sort_order)->toBe(1);

    $updateResponse = $this->put(
        route('admin.stats.update', $statistic),
        [
            'value' => '300+',
            'value_en' => '300+',
            'label' => 'Siswa aktif dan alumni',
            'label_en' => 'Active students and alumni',
        ]
    );

    $updateResponse
        ->assertRedirect(route('admin.stats.edit'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('site_statistics', [
        'id' => $statistic->id,
        'value' => '300+',
        'value_en' => '300+',
        'label' => 'Siswa aktif dan alumni',
        'label_en' => 'Active students and alumni',
        'sort_order' => 1,
    ]);
});

it('does not expose admin statistics to the disabled homepage surface', function (): void {
    SiteStatistic::query()->create([
        'value' => '100+',
        'value_en' => '100+',
        'label' => 'Siswa aktif',
        'label_en' => 'Active students',
        'sort_order' => 1,
    ]);

    $response = $this->get(route('home'))->assertOk();

    expect($response->original->getData())->not->toHaveKey('stats');
});

it('prevents creating more than four statistics', function (): void {
    foreach (range(1, SiteStatistic::MAX_ITEMS) as $position) {
        SiteStatistic::query()->create([
            'value' => (string) ($position * 100),
            'value_en' => (string) ($position * 100),
            'label' => "Statistik {$position}",
            'label_en' => "Statistic {$position}",
            'sort_order' => $position,
        ]);
    }

    $response = $this->post(route('admin.stats.store'), [
        'value' => '500',
        'value_en' => '500',
        'label' => 'Statistik kelima',
        'label_en' => 'Fifth statistic',
    ]);

    $response->assertSessionHasErrors('value');

    expect(SiteStatistic::query()->count())
        ->toBe(SiteStatistic::MAX_ITEMS);

    $this->assertDatabaseMissing('site_statistics', [
        'label' => 'Statistik kelima',
    ]);
});

it('prevents deleting the final statistic and normalizes after deletion', function (): void {
    $first = SiteStatistic::query()->create([
        'value' => '1',
        'value_en' => '1',
        'label' => 'Statistik wajib',
        'label_en' => 'Required statistic',
        'sort_order' => 1,
    ]);

    $blockedResponse = $this->delete(
        route('admin.stats.destroy', $first)
    );

    $blockedResponse->assertSessionHasErrors('delete');

    $this->assertDatabaseHas('site_statistics', [
        'id' => $first->id,
        'deleted_at' => null,
    ]);

    $second = SiteStatistic::query()->create([
        'value' => '2',
        'value_en' => '2',
        'label' => 'Statistik kedua',
        'label_en' => 'Second statistic',
        'sort_order' => 2,
    ]);

    $deleteResponse = $this->delete(
        route('admin.stats.destroy', $first)
    );

    $deleteResponse
        ->assertRedirect(route('admin.stats.edit'))
        ->assertSessionHas('success');

    $this->assertSoftDeleted('site_statistics', [
        'id' => $first->id,
    ]);

    expect($second->fresh()->sort_order)->toBe(1);
});
