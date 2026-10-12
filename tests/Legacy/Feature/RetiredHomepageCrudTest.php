<?php

use App\Models\SiteStatistic;
use App\Models\TestimonialMedia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::query()->forceCreate([
        'name' => 'Retired CRUD Admin',
        'email' => 'retired-crud@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));
});

it('retires testimonial and statistic HTTP CRUD while retaining their inventory tables', function (): void {
    expect(Route::has('testimoni.media'))->toBeFalse()
        ->and(Route::has('admin.testimoni.index'))->toBeFalse()
        ->and(Route::has('admin.testimoni.store'))->toBeFalse()
        ->and(Route::has('admin.stats.edit'))->toBeFalse()
        ->and(Route::has('admin.stats.store'))->toBeFalse()
        ->and(Schema::hasTable('testimonial_media'))->toBeTrue()
        ->and(Schema::hasTable('site_statistics'))->toBeTrue();

    $this->get('/testimoni/media')->assertNotFound();
    $this->get('/admin/testimoni')->assertNotFound();
    $this->get('/admin/stats')->assertNotFound();
});

it('does not query retired tables from the homepage or admin dashboard', function (): void {
    $queries = collect();
    DB::listen(function ($query) use (&$queries): void {
        $queries->push(strtolower($query->sql));
    });

    $this->get(route('home'))->assertOk();
    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertDontSee('Testimoni aktif')
        ->assertDontSee('Statistik homepage')
        ->assertDontSee('Atur Statistik');

    expect($queries->contains(fn (string $sql): bool => str_contains($sql, 'testimonial_media')))->toBeFalse()
        ->and($queries->contains(fn (string $sql): bool => str_contains($sql, 'site_statistics')))->toBeFalse()
        ->and($queries->contains(fn (string $sql): bool => str_contains($sql, 'gallery_page_media_items')))->toBeFalse();
});

it('preserves retained rows and testimonial objects without exposing a mutation path', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('testimonials/photos/legacy/retained.jpg', 'retained');

    $testimonial = TestimonialMedia::query()->create([
        'type' => 'photo',
        'source' => 'upload',
        'media_url' => '/storage/testimonials/photos/legacy/retained.jpg',
        'sort_order' => 1,
        'is_published' => true,
    ]);
    $statistic = SiteStatistic::query()->create([
        'value' => '320+',
        'value_en' => '320+',
        'value_ar' => '+320',
        'label' => 'Siswa',
        'label_en' => 'Students',
        'label_ar' => 'طالبًا',
        'sort_order' => 1,
    ]);

    $this->get(route('home'))->assertOk();
    $this->get(route('admin.dashboard'))->assertOk();

    expect($testimonial->fresh())->not->toBeNull()
        ->and($statistic->fresh())->not->toBeNull();
    Storage::disk('public')->assertExists('testimonials/photos/legacy/retained.jpg');
});

it('keeps static statistic localization and removes testimonial build entries', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        $items = trans('home.stats.items', [], $locale);

        expect($items)->toBeArray()->toHaveCount(4)
            ->and(collect($items)->every(fn (array $item): bool => is_numeric($item['count'] ?? null)
                && trim((string) ($item['label'] ?? '')) !== ''))->toBeTrue();
    }

    $vite = file_get_contents(base_path('vite.config.js'));
    $app = file_get_contents(resource_path('js/app.js'));

    expect($vite)->not->toContain('welcome-testimonial')
        ->and($app)->not->toContain('testimonial');
});
