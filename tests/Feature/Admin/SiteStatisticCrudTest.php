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
    ]);

    $this->actingAs($user);
});

it('renders the statistic manager inside the admin panel', function (): void {
    SiteStatistic::query()->create([
        'value' => '100+',
        'label' => 'Siswa aktif',
        'sort_order' => 1,
    ]);

    $response = $this->get(route('admin.stats.edit'));

    $response
        ->assertOk()
        ->assertSee('Statistik Homepage')
        ->assertSee('Tambah Statistik')
        ->assertSee('100+')
        ->assertSee('Siswa aktif');
});

it('creates and updates a homepage statistic', function (): void {
    $createResponse = $this->post(route('admin.stats.store'), [
        'value' => '250+',
        'label' => 'Siswa dan alumni',
    ]);

    $createResponse
        ->assertRedirect(route('admin.stats.edit'))
        ->assertSessionHas('success');

    $statistic = SiteStatistic::query()->firstOrFail();

    expect($statistic->value)->toBe('250+')
        ->and($statistic->label)->toBe('Siswa dan alumni')
        ->and($statistic->sort_order)->toBe(1);

    $updateResponse = $this->put(
        route('admin.stats.update', $statistic),
        [
            'value' => '300+',
            'label' => 'Siswa aktif dan alumni',
        ]
    );

    $updateResponse
        ->assertRedirect(route('admin.stats.edit'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('site_statistics', [
        'id' => $statistic->id,
        'value' => '300+',
        'label' => 'Siswa aktif dan alumni',
        'sort_order' => 1,
    ]);
});

it('prevents creating more than four statistics', function (): void {
    foreach (range(1, SiteStatistic::MAX_ITEMS) as $position) {
        SiteStatistic::query()->create([
            'value' => (string) ($position * 100),
            'label' => "Statistik {$position}",
            'sort_order' => $position,
        ]);
    }

    $response = $this->post(route('admin.stats.store'), [
        'value' => '500',
        'label' => 'Statistik kelima',
    ]);

    $response->assertSessionHasErrors('value');

    expect(SiteStatistic::query()->count())
        ->toBe(SiteStatistic::MAX_ITEMS);

    $this->assertDatabaseMissing('site_statistics', [
        'label' => 'Statistik kelima',
    ]);
});

it('moves statistics up and down while keeping sequential order', function (): void {
    $first = SiteStatistic::query()->create([
        'value' => 'A',
        'label' => 'Pertama',
        'sort_order' => 1,
    ]);

    $second = SiteStatistic::query()->create([
        'value' => 'B',
        'label' => 'Kedua',
        'sort_order' => 2,
    ]);

    $third = SiteStatistic::query()->create([
        'value' => 'C',
        'label' => 'Ketiga',
        'sort_order' => 3,
    ]);

    $this->patch(route('admin.stats.move-up', $third))
        ->assertRedirect(route('admin.stats.edit'));

    expect($first->fresh()->sort_order)->toBe(1)
        ->and($third->fresh()->sort_order)->toBe(2)
        ->and($second->fresh()->sort_order)->toBe(3);

    $this->patch(route('admin.stats.move-down', $first))
        ->assertRedirect(route('admin.stats.edit'));

    expect($third->fresh()->sort_order)->toBe(1)
        ->and($first->fresh()->sort_order)->toBe(2)
        ->and($second->fresh()->sort_order)->toBe(3);
});

it('prevents deleting the final statistic and normalizes after deletion', function (): void {
    $first = SiteStatistic::query()->create([
        'value' => '1',
        'label' => 'Statistik wajib',
        'sort_order' => 1,
    ]);

    $blockedResponse = $this->delete(
        route('admin.stats.destroy', $first)
    );

    $blockedResponse->assertSessionHasErrors('delete');

    $this->assertDatabaseHas('site_statistics', [
        'id' => $first->id,
    ]);

    $second = SiteStatistic::query()->create([
        'value' => '2',
        'label' => 'Statistik kedua',
        'sort_order' => 2,
    ]);

    $deleteResponse = $this->delete(
        route('admin.stats.destroy', $first)
    );

    $deleteResponse
        ->assertRedirect(route('admin.stats.edit'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('site_statistics', [
        'id' => $first->id,
    ]);

    expect($second->fresh()->sort_order)->toBe(1);
});
