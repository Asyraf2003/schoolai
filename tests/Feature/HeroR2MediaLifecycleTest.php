<?php

use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('retires Hero media mutation without deleting legacy R2 objects', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('hero/slides/11/legacy.mp4', 'legacy-video');

    $legacy = HeroSlide::query()->create([
        'type' => 'video',
        'media_url' => 'https://media.almustaqbal.sch.id/hero/slides/11/legacy.mp4',
        'title_id' => 'Legacy Hero',
        'sort_order' => 1,
        'is_active' => true,
    ]);
    $admin = User::query()->forceCreate([
        'name' => 'Hero R2 Admin',
        'email' => 'hero-r2@example.test',
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);
    $article = Article::query()->create([
        'title_id' => 'Promoted Article',
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'link_id' => 'https://example.test/promoted',
        'published_at' => now(),
    ]);

    $this->actingAs($admin)
        ->post(route('admin.hero.articles.promote'), ['article_id' => $article->getKey()])
        ->assertRedirect();

    expect(Route::has('admin.hero.store'))->toBeFalse()
        ->and(Route::has('admin.hero.destroy'))->toBeFalse()
        ->and($legacy->fresh())->not->toBeNull();
    Storage::disk('public')->assertExists('hero/slides/11/legacy.mp4');
});
