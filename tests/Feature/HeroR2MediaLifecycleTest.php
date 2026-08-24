<?php

use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\User;
use App\Support\Media\MediaUrlResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');

    $this->actingAs(User::query()->forceCreate([
        'name' => 'Hero R2 Admin',
        'email' => 'hero-r2@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));

    $this->article = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'hero-r2-proof',
        'title_id' => 'Hero R2 Proof',
        'thumbnail_url' => 'media/home/hero-school.png',
        'link_id' => '/artikel/hero-r2-proof',
        'author' => 'Hero R2 Admin',
        'published_at' => now(),
    ]);
});

it('replaces an owned hero object only after the database URL changes', function (): void {
    $oldKey = 'hero/slides/11/old-proof.mp4';
    Storage::disk('public')->put($oldKey, 'old-video');
    $slide = HeroSlide::query()->create([
        'article_id' => $this->article->getKey(),
        'type' => 'video',
        'media_url' => 'https://media.almustaqbal.sch.id/'.$oldKey,
        'title_id' => 'Hero lama',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $this->put(route('admin.hero.update', $slide), [
        'article_id' => $this->article->getKey(),
        'type' => 'video',
        'media_file' => UploadedFile::fake()->create('replacement.mp4', 32, 'video/mp4'),
        'focal_position' => 'center center',
        'overlay_strength' => '0.46',
        'is_active' => '1',
    ])->assertRedirect(route('admin.hero'));

    $newKey = app(MediaUrlResolver::class)->ownedKey($slide->fresh()->media_url);

    expect($newKey)->toStartWith('hero/slides/'.$slide->getKey().'/');
    Storage::disk('public')->assertExists($newKey);
    Storage::disk('public')->assertMissing($oldKey);
});

it('deletes owned hard-deleted media without touching an external lookalike', function (): void {
    $externalLookalikeKey = 'hero/slides/12/external.mp4';
    Storage::disk('public')->put($externalLookalikeKey, 'must-remain');
    $slide = HeroSlide::query()->create([
        'article_id' => $this->article->getKey(),
        'type' => 'video',
        'media_url' => 'https://cdn.example.test/'.$externalLookalikeKey,
        'title_id' => 'External hero',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $this->delete(route('admin.hero.destroy', $slide))->assertRedirect(route('admin.hero'));

    Storage::disk('public')->assertExists($externalLookalikeKey);

    $ownedKey = 'hero/slides/13/owned.mp4';
    Storage::disk('public')->put($ownedKey, 'delete-me');
    $ownedSlide = HeroSlide::query()->create([
        'type' => 'video',
        'media_url' => 'https://media.almustaqbal.sch.id/'.$ownedKey,
        'title_id' => 'Owned hero',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $this->delete(route('admin.hero.destroy', $ownedSlide))->assertRedirect(route('admin.hero'));
    Storage::disk('public')->assertMissing($ownedKey);
});
