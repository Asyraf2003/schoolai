<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Admin Canvas',
        'email' => 'admin-canvas@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin);
});

it('starts a native draft without changing the external article flow', function (): void {
    $response = $this->post(route('admin.artikel.canvas.start'));
    $article = Article::query()->where('article_source', Article::SOURCE_NATIVE)->firstOrFail();

    $response->assertRedirect(route('admin.artikel.canvas.edit', $article));

    expect($article->article_status)
        ->toBe(Article::STATUS_DRAFT)
        ->and($article->link_id)->toContain('/artikel/draft-');

    $this->get(route('artikel'))->assertDontSee('Artikel tanpa judul');
});

it('autosaves sanitized canvas content and strips active payloads', function (): void {
    $this->post(route('admin.artikel.canvas.start'));
    $article = Article::query()->where('article_source', Article::SOURCE_NATIVE)->firstOrFail();

    $this->patchJson(route('admin.artikel.canvas.autosave', $article), [
        'title_id' => 'Belajar dengan Adab',
        'subtitle_id' => 'Cerita dari kelas Al Mustaqbal',
        'content_id' => '<p class="article-align-justify article-bg--yellow bogus" onclick="alert(1)">Isi <strong>aman</strong> <span class="article-color--green bad">berwarna</span>.</p><script>alert(2)</script><img src="x" onerror="alert(3)">',
        'content_en' => '',
    ])->assertOk()->assertJsonPath('saved_label', 'Draft · Tersimpan');

    $article->refresh();

    expect($article->content_id)
        ->toContain('<strong>aman</strong>')
        ->toContain('article-align-justify')
        ->toContain('article-bg--yellow')
        ->toContain('article-color--green')
        ->not->toContain('bogus')
        ->not->toContain(' bad')
        ->not->toContain('onclick')
        ->not->toContain('<script')
        ->not->toContain('onerror')
        ->not->toContain('<img');
});

it('stores a dedicated thumbnail without inserting it into article content', function (): void {
    Storage::fake('public');

    $this->post(route('admin.artikel.canvas.start'));
    $article = Article::query()->where('article_source', Article::SOURCE_NATIVE)->firstOrFail();

    $response = $this->post(route('admin.artikel.canvas.image', $article), [
        'purpose' => 'thumbnail',
        'image' => UploadedFile::fake()->image('cover.jpg', 1200, 675),
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('purpose', 'thumbnail');

    $article->refresh();

    expect($article->thumbnail_url)
        ->toStartWith('/storage/articles/thumbnails/')
        ->and($article->content_id)->toBeNull();
});

it('publishes a native article and renders the same semantic content internally', function (): void {
    $this->post(route('admin.artikel.canvas.start'));
    $article = Article::query()->where('article_source', Article::SOURCE_NATIVE)->firstOrFail();

    $this->patchJson(route('admin.artikel.canvas.autosave', $article), [
        'title_id' => 'Belajar dengan Adab',
        'subtitle_id' => 'Cerita dari kelas Al Mustaqbal',
        'content_id' => '<p class="has-drop-cap">Adab adalah awal ilmu.</p><blockquote class="article-pull-quote">Ilmu menjaga pemiliknya.</blockquote>',
        'content_en' => '',
    ])->assertOk();

    $publish = $this->postJson(route('admin.artikel.canvas.publish', $article), [
        'publish_mode' => 'now',
        'tags' => ['Adab', 'Sekolah'],
        'author' => 'Ustazah Hana',
        'published_at' => now()->subHour()->toIso8601String(),
    ])->assertOk()->assertJsonPath('status', Article::STATUS_PUBLISHED);

    $article->refresh();

    expect($article->slug)
        ->toBe('belajar-dengan-adab')
        ->and($article->tags)->toBe(['Adab', 'Sekolah'])
        ->and($article->author)->toBe('Ustazah Hana');

    $this->get($publish->json('redirect'))
        ->assertOk()
        ->assertSee('Belajar dengan Adab')
        ->assertSee('Adab adalah awal ilmu.')
        ->assertSee('article-pull-quote', false);

    $this->get(route('artikel'))
        ->assertOk()
        ->assertSee('Belajar dengan Adab')
        ->assertSee(route('artikel.native', ['article' => $article->slug]), false);
});

it('filters clickable categories and shows related native articles', function (): void {
    $first = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'adab-di-kelas',
        'title_id' => 'Adab di Kelas',
        'description_id' => 'Kebiasaan baik sebelum belajar.',
        'content_id' => '<p>Adab adalah awal ilmu.</p>',
        'tags' => ['Adab', 'Sekolah'],
        'word_count' => 220,
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'link_id' => '/artikel/adab-di-kelas',
        'published_at' => now()->subDay(),
    ]);

    Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'budaya-sekolah',
        'title_id' => 'Budaya Sekolah',
        'description_id' => 'Cerita lain dari sekolah.',
        'content_id' => '<p>Budaya baik tumbuh bersama.</p>',
        'tags' => ['Sekolah'],
        'word_count' => 440,
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'link_id' => '/artikel/budaya-sekolah',
        'published_at' => now(),
    ]);

    $this->get(route('artikel', ['kategori' => 'Adab']))
        ->assertOk()
        ->assertSee('Adab di Kelas')
        ->assertDontSee('Budaya Sekolah')
        ->assertSee(route('artikel', ['kategori' => 'Sekolah']), false);

    $this->get(route('artikel.native', ['article' => $first->slug]))
        ->assertOk()
        ->assertSee('Mungkin Anda juga suka')
        ->assertSee('Budaya Sekolah');
});

it('keeps a scheduled article private until its publication time', function (): void {
    $this->post(route('admin.artikel.canvas.start'));
    $article = Article::query()->where('article_source', Article::SOURCE_NATIVE)->firstOrFail();

    $this->patchJson(route('admin.artikel.canvas.autosave', $article), [
        'title_id' => 'Artikel Besok',
        'content_id' => '<p>Konten yang akan terbit besok.</p>',
    ])->assertOk();

    $scheduledAt = now()->addDay();

    $this->postJson(route('admin.artikel.canvas.publish', $article), [
        'publish_mode' => 'schedule',
        'scheduled_at' => $scheduledAt->toIso8601String(),
        'tags' => [],
    ])->assertOk()->assertJsonPath('status', Article::STATUS_SCHEDULED);

    $article->refresh();

    $this->get(route('artikel.native', ['article' => $article->slug]))->assertNotFound();
    $this->get(route('artikel'))->assertDontSee('Artikel Besok');

    $this->travelTo($scheduledAt->copy()->addMinute());

    $this->get(route('artikel.native', ['article' => $article->slug]))
        ->assertOk()
        ->assertSee('Artikel Besok');
});
