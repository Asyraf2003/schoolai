<?php

use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    DB::table('hero_slides')->delete();

    $admin = User::query()->forceCreate([
        'name' => 'Admin Hero Test',
        'email' => 'admin-hero@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin);

    $this->heroArticle = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'hero-database-test',
        'title_id' => 'Hero Database',
        'title_en' => 'Database Hero',
        'title_ar' => 'واجهة قاعدة البيانات',
        'description_id' => 'Dikelola sebagai artikel dari admin.',
        'description_en' => 'Managed as an article from admin.',
        'thumbnail_url' => 'media/home/hero-school.png',
        'author' => 'Admin Hero Test',
        'published_at' => now(),
    ]);
});

it('creates and normalizes a youtube hero slide from admin', function (): void {
    $response = $this->post(route('admin.hero.store'), [
        'article_id' => $this->heroArticle->getKey(),
        'type' => 'video',
        'media_url' => 'https://youtu.be/kb1dXcf3QQs',
        'focal_position' => 'center center',
        'overlay_strength' => '0.40',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.hero'));

    $slide = HeroSlide::query()->firstOrFail();

    expect($slide->media_url)
        ->toContain('youtube-nocookie.com/embed/kb1dXcf3QQs')
        ->and($slide->is_active)->toBeTrue();

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('hero', fn (array $hero): bool => ($hero['slides'][0]['title'] ?? null) === 'Database Hero');
});

it('edits a seeded public image without forcing a replacement upload', function (): void {
    $slide = HeroSlide::query()->create([
        'article_id' => $this->heroArticle->getKey(),
        'type' => 'image',
        'media_url' => 'media/home/hero-school.png',
        'title_id' => 'Hero Lama',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $response = $this->put(route('admin.hero.update', $slide), [
        'article_id' => $this->heroArticle->getKey(),
        'type' => 'image',
        'media_url' => 'media/home/hero-school.png',
        'focal_position' => 'center center',
        'overlay_strength' => '0.46',
        'is_active' => '1',
    ]);

    $response
        ->assertRedirect(route('admin.hero'))
        ->assertSessionHasNoErrors();

    expect($slide->fresh()->title_id)->toBe('Hero Database')
        ->and($slide->fresh()->media_url)->toBe('media/home/hero-school.png');
});

it('reorders and toggles hero slides', function (): void {
    $first = HeroSlide::query()->create([
        'type' => 'image',
        'media_url' => 'media/home/hero-school.png',
        'title_id' => 'Pertama',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $second = HeroSlide::query()->create([
        'type' => 'image',
        'media_url' => 'media/home/hero-school.png',
        'title_id' => 'Kedua',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    $this->patch(route('admin.hero.move-up', $second))->assertRedirect();

    expect($second->fresh()->sort_order)->toBe(1)
        ->and($first->fresh()->sort_order)->toBe(2);

    $this->patch(route('admin.hero.toggle', $second))->assertRedirect();
    expect($second->fresh()->is_active)->toBeFalse();
});
