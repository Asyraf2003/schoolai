<?php

use App\Models\Article;
use App\Models\User;
use App\Support\Media\MediaUrlResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('creates one text article without any optional metadata and preserves locale fallback', function (): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->post(route('admin.artikel.store'), ['content_id' => '<p>Teks <strong>tebal</strong> <a href="https://example.com/read">tautan</a></p>'])
        ->assertRedirect(route('admin.artikel'))->assertSessionHasNoErrors();
    $article = Article::sole();
    expect($article->title_id)->toBeNull()->and($article->description_id)->toBeNull()
        ->and($article->cover_url)->toBeNull()->and($article->published_date)->toBeNull()
        ->and($article->content_id)->toContain('<strong>tebal</strong>')->toContain('href="https://example.com/read"');
    $this->get(route('admin.artikel.edit', $article))->assertOk()->assertSee('Isi artikel');
    foreach (['id', 'en', 'ar'] as $locale) {
        $this->withSession(['locale' => $locale])->get($article->linkForLocale($locale))->assertOk()->assertSee('<strong>tebal</strong>', false);
    }
});

it('updates the same record with optional metadata and a future display date without scheduling', function (): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $article = Article::create(['content_id' => '<p>Lama</p>']);
    $slug = $article->slug;
    $this->put(route('admin.artikel.update', $article), [
        'title_id' => 'Indonesia', 'title_en' => 'English', 'title_ar' => 'العربية',
        'description_id' => 'Ringkasan', 'content_ar' => '<p>نص جديد</p>', 'published_date' => '2099-01-01',
        'article_status' => 'draft', 'hero_position' => 2,
    ])->assertRedirect(route('admin.artikel'))->assertSessionHasNoErrors();
    expect(Article::count())->toBe(1)->and($article->fresh()->slug)->toBe($slug)
        ->and($article->fresh()->published_date->toDateString())->toBe('2099-01-01');
    $this->withSession(['locale' => 'ar'])->get($article->linkForLocale('ar'))->assertOk()->assertSee('نص جديد');
    $this->get(route('home'))->assertOk()->assertSee($article->linkForLocale('ar'), false);
});

it('removes images video embeds event handlers and script links from article text', function (): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->post(route('admin.artikel.store'), [
        'content_id' => '<p onclick="evil()">Aman <em>miring</em><a href="javascript:evil()">link</a></p><script>evil()</script><img src="https://example.com/image.jpg"><video src="x"></video><iframe src="https://youtube.com/embed/a"></iframe>',
    ])->assertSessionHasNoErrors();
    $article = Article::sole();
    expect($article->content_id)->toContain('<em>miring</em>')->not->toContain('onclick')->not->toContain('javascript:')
        ->not->toContain('<img')->not->toContain('<video')->not->toContain('<iframe')->not->toContain('<script');
});

it('rejects empty or media-only content and invalid optional metadata', function (array $payload, string $error): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->post(route('admin.artikel.store'), $payload)->assertSessionHasErrors($error);
    expect(Article::count())->toBe(0);
})->with([
    [[], 'content_id'], [['content_id' => '<img src="https://example.com/a.jpg">'], 'content_id'],
    [['content_id' => '<p>Isi</p>', 'published_date' => 'besok'], 'published_date'],
    [['content_id' => '<p>Isi</p>', 'title_id' => str_repeat('a', 201)], 'title_id'],
]);

it('stores optional cover on R2 and retains files when deleting or restoring an article', function (): void {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->post(route('admin.artikel.store'), ['content_id' => '<p>Isi</p>', 'cover_file' => UploadedFile::fake()->image('cover.jpg')])
        ->assertSessionHasNoErrors();
    $article = Article::sole();
    $key = app(MediaUrlResolver::class)->ownedKey($article->cover_url);
    Storage::disk('public')->assertExists($key);
    expect($article->cover_url)->toStartWith(config('media.public_url').'/articles/covers/');
    $this->delete(route('admin.artikel.destroy', $article))->assertRedirect(route('admin.artikel'));
    $this->assertSoftDeleted($article);
    $this->get($article->linkForLocale('id'))->assertNotFound();
    Storage::disk('public')->assertExists($key);
    $this->patch(route('admin.artikel.restore', $article))->assertRedirect(route('admin.artikel'));
    $this->get($article->linkForLocale('id'))->assertOk();
    $this->put(route('admin.artikel.update', $article), ['content_id' => '<p>Isi baru</p>', 'remove_cover' => true])->assertSessionHasNoErrors();
    expect($article->fresh()->cover_url)->toBeNull();
});

it('keeps an Arabic-only article readable in every locale with the correct content language', function (): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->post(route('admin.artikel.store'), ['content_ar' => '<p>محتوى عربي فقط</p>'])->assertSessionHasNoErrors();
    $article = Article::sole();
    foreach (['id', 'en', 'ar'] as $locale) {
        $this->withSession(['locale' => $locale])->get($article->linkForLocale($locale))
            ->assertOk()->assertSee('محتوى عربي فقط')->assertSee('class="article-content" lang="ar" dir="auto"', false);
    }
});
