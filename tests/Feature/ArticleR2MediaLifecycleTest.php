<?php

use App\Models\Article;
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
        'name' => 'Article R2 Admin',
        'email' => 'article-r2@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));
});

it('publishes replaces archives and restores article thumbnails through R2', function (): void {
    $this->post(route('admin.artikel.store'), [
        'title_id' => 'Artikel R2',
        'thumbnail_file' => UploadedFile::fake()->image('article.jpg'),
        'link_id' => 'https://example.test/article-r2',
        'published_at' => now()->format('Y-m-d H:i:s'),
    ])->assertRedirect(route('admin.artikel'));

    $article = Article::query()->where('title_id', 'Artikel R2')->firstOrFail();
    $oldKey = app(MediaUrlResolver::class)->ownedKey($article->thumbnail_url);

    expect($oldKey)->toStartWith('articles/thumbnails/new/');
    Storage::disk('public')->assertExists($oldKey);

    $this->put(route('admin.artikel.update', $article), [
        'title_id' => 'Artikel R2 Baru',
        'thumbnail_file' => UploadedFile::fake()->image('article-new.webp'),
        'link_id' => 'https://example.test/article-r2',
        'published_at' => now()->format('Y-m-d H:i:s'),
    ])->assertRedirect(route('admin.artikel'));

    $newKey = app(MediaUrlResolver::class)->ownedKey($article->fresh()->thumbnail_url);

    expect($newKey)->toStartWith('articles/thumbnails/'.$article->getKey().'/');
    Storage::disk('public')->assertMissing($oldKey);
    Storage::disk('public')->assertExists($newKey);

    $this->delete(route('admin.artikel.destroy', $article))->assertRedirect(route('admin.artikel'));
    Storage::disk('public')->assertExists($newKey);
    $this->patch(route('admin.artikel.restore', $article->getKey()))->assertRedirect(route('admin.artikel'));
    Storage::disk('public')->assertExists($newKey);
});

it('keeps R2 canvas content images through sanitization and public rendering', function (): void {
    $this->post(route('admin.artikel.canvas.start'))->assertRedirect();
    $article = Article::query()->where('article_source', Article::SOURCE_NATIVE)->firstOrFail();

    $upload = $this->post(route('admin.artikel.canvas.image', $article), [
        'purpose' => 'content',
        'image' => UploadedFile::fake()->image('canvas.jpg', 1200, 675),
    ])->assertCreated();

    $url = $upload->json('url');
    $key = app(MediaUrlResolver::class)->ownedKey($url);

    expect($key)->toStartWith('articles/content/'.$article->getKey().'/');
    Storage::disk('public')->assertExists($key);

    $this->patchJson(route('admin.artikel.canvas.autosave', $article), [
        'title_id' => 'Canvas R2',
        'content_id' => '<p>Konten dengan gambar.</p><figure><img src="'.$url.'" alt="Kelas"></figure>',
    ])->assertOk();

    expect($article->fresh()->content_id)->toContain($url);
});

it('never lets thumbnail replacement delete an article content object', function (): void {
    $this->post(route('admin.artikel.canvas.start'))->assertRedirect();
    $article = Article::query()->where('article_source', Article::SOURCE_NATIVE)->firstOrFail();

    $upload = $this->post(route('admin.artikel.canvas.image', $article), [
        'purpose' => 'content',
        'image' => UploadedFile::fake()->image('content-cover.jpg', 1200, 675),
    ])->assertCreated();

    $contentUrl = $upload->json('url');
    $contentKey = app(MediaUrlResolver::class)->ownedKey($contentUrl);

    $article->update([
        'thumbnail_url' => $contentUrl,
        'content_id' => '<img src="'.$contentUrl.'" alt="Indonesia">',
        'content_en' => '<img src="'.$contentUrl.'" alt="English">',
        'content_ar' => '<img src="'.$contentUrl.'" alt="العربية">',
    ]);

    $this->post(route('admin.artikel.canvas.image', $article), [
        'purpose' => 'thumbnail',
        'image' => UploadedFile::fake()->image('replacement.jpg', 1200, 675),
    ])->assertCreated();

    Storage::disk('public')->assertExists($contentKey);
});

it('keeps a replaced thumbnail object while localized article content references it', function (): void {
    $this->post(route('admin.artikel.canvas.start'))->assertRedirect();
    $article = Article::query()->where('article_source', Article::SOURCE_NATIVE)->firstOrFail();

    $firstUpload = $this->post(route('admin.artikel.canvas.image', $article), [
        'purpose' => 'thumbnail',
        'image' => UploadedFile::fake()->image('shared-thumbnail.jpg', 1200, 675),
    ])->assertCreated();

    $sharedUrl = $firstUpload->json('url');
    $sharedKey = app(MediaUrlResolver::class)->ownedKey($sharedUrl);

    $article->update([
        'content_id' => '<img src="'.$sharedUrl.'" alt="Indonesia">',
        'content_en' => '<img src="'.$sharedUrl.'" alt="English">',
        'content_ar' => '<img src="'.$sharedUrl.'" alt="العربية">',
    ]);

    $this->post(route('admin.artikel.canvas.image', $article), [
        'purpose' => 'thumbnail',
        'image' => UploadedFile::fake()->image('new-thumbnail.webp', 1200, 675),
    ])->assertCreated();

    Storage::disk('public')->assertExists($sharedKey);
});
