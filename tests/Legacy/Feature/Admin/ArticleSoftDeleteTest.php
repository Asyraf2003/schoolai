<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');

    $admin = User::query()->forceCreate([
        'name' => 'Admin Artikel Test',
        'email' => 'admin-artikel@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin);

    Storage::disk('public')->put(
        'articles/thumbnails/soft-delete.jpg',
        'thumbnail-test'
    );

    $this->article = Article::query()->create([
        'title_id' => 'Artikel soft delete test',
        'title_en' => 'Soft delete article test',
        'description_id' => 'Deskripsi artikel untuk pengujian soft delete.',
        'description_en' => 'Article description for soft-delete testing.',
        'thumbnail_url' => '/storage/articles/thumbnails/soft-delete.jpg',
        'link_id' => 'https://example.com/artikel-soft-delete',
        'link_en' => 'https://example.com/soft-delete-article',
        'author' => 'Admin Test',
        'published_at' => now(),
    ]);
});

it('soft deletes an article without deleting its thumbnail file', function (): void {
    $response = $this->delete(
        route('admin.artikel.destroy', $this->article)
    );

    $response
        ->assertRedirect(route('admin.artikel'))
        ->assertSessionHas('success', 'Artikel dipindahkan ke arsip dan dapat dipulihkan.');

    $this->assertSoftDeleted('articles', [
        'id' => $this->article->id,
    ]);

    expect(
        Storage::disk('public')->exists('articles/thumbnails/soft-delete.jpg')
    )->toBeTrue();
});

it('hides deleted articles publicly but keeps them muted in the admin list', function (): void {
    $this->article->delete();

    $this->get(route('artikel'))
        ->assertOk()
        ->assertDontSee('Artikel soft delete test');

    $this->get(route('admin.artikel'))
        ->assertOk()
        ->assertSee('Artikel soft delete test')
        ->assertSee('Dihapus')
        ->assertSee('Pulihkan')
        ->assertSee('is-deleted', false);

    $this->get(route('admin.artikel.edit', $this->article->id))
        ->assertNotFound();
});

it('restores an archived article and makes it public again', function (): void {
    $this->article->delete();

    $response = $this->patch(
        route('admin.artikel.restore', $this->article->id)
    );

    $response
        ->assertRedirect(route('admin.artikel'))
        ->assertSessionHas('success', 'Artikel berhasil dipulihkan.');

    $this->assertDatabaseHas('articles', [
        'id' => $this->article->id,
        'deleted_at' => null,
    ]);

    expect(
        Storage::disk('public')->exists('articles/thumbnails/soft-delete.jpg')
    )->toBeTrue();

    $this->get(route('artikel'))
        ->assertOk()
        ->assertSee('Artikel soft delete test');
});

it('normalizes equivalent article links for replacement matching', function (): void {
    $first = Article::normalizedLinkIdentity(
        'HTTPS://Example.COM:443/artikel-sama/?b=2&a=1#bagian'
    );

    $second = Article::normalizedLinkIdentity(
        'https://example.com/artikel-sama?a=1&b=2'
    );

    expect($first)
        ->toBe('https://example.com/artikel-sama?a=1&b=2')
        ->and($second)
        ->toBe($first);
});

it('offers restore and replace only for an active article with an identical normalized link', function (): void {
    $this->article->update([
        'link_id' => 'HTTPS://Example.COM:443/artikel-sama/?b=2&a=1#arsip',
    ]);
    $this->article->delete();

    $identicalActive = Article::query()->create([
        'title_id' => 'Artikel aktif identik',
        'thumbnail_url' => '/storage/articles/thumbnails/identical.jpg',
        'link_id' => 'https://example.com/artikel-sama?a=1&b=2',
        'author' => 'Admin Test',
        'published_at' => now()->addMinute(),
    ]);

    $unrelatedActive = Article::query()->create([
        'title_id' => 'Artikel aktif berbeda',
        'thumbnail_url' => '/storage/articles/thumbnails/unrelated.jpg',
        'link_id' => 'https://example.com/artikel-berbeda',
        'author' => 'Admin Test',
        'published_at' => now()->addMinutes(2),
    ]);

    $this->get(route('admin.artikel'))
        ->assertOk()
        ->assertSee('Pulihkan &amp; Gantikan', false)
        ->assertSee('Gantikan ID '.$identicalActive->id)
        ->assertDontSee('Gantikan ID '.$unrelatedActive->id);
});

it('restores an archived article and archives the identical active replacement atomically', function (): void {
    Storage::disk('public')->put(
        'articles/thumbnails/replacement.jpg',
        'replacement-thumbnail'
    );

    $this->article->update([
        'link_id' => 'HTTPS://Example.COM:443/artikel-sama/?b=2&a=1#arsip',
    ]);
    $this->article->delete();

    $replacement = Article::query()->create([
        'title_id' => 'Artikel pengganti aktif',
        'thumbnail_url' => '/storage/articles/thumbnails/replacement.jpg',
        'link_id' => 'https://example.com/artikel-sama?a=1&b=2',
        'author' => 'Admin Test',
        'published_at' => now()->addMinute(),
    ]);

    $response = $this->patch(
        route('admin.artikel.restore', $this->article->id),
        ['replacement_article_id' => $replacement->id]
    );

    $response
        ->assertRedirect(route('admin.artikel'))
        ->assertSessionHas(
            'success',
            'Artikel lama dipulihkan dan artikel aktif pengganti dipindahkan ke arsip.'
        );

    $this->assertDatabaseHas('articles', [
        'id' => $this->article->id,
        'deleted_at' => null,
    ]);
    $this->assertSoftDeleted('articles', [
        'id' => $replacement->id,
    ]);

    expect(Storage::disk('public')->exists('articles/thumbnails/soft-delete.jpg'))
        ->toBeTrue()
        ->and(Storage::disk('public')->exists('articles/thumbnails/replacement.jpg'))
        ->toBeTrue();

    $this->get(route('artikel'))
        ->assertOk()
        ->assertSee('Artikel soft delete test')
        ->assertDontSee('Artikel pengganti aktif');
});

it('rejects replacing an unrelated active article without changing either status', function (): void {
    $this->article->delete();

    $unrelatedActive = Article::query()->create([
        'title_id' => 'Artikel aktif tidak berkaitan',
        'thumbnail_url' => '/storage/articles/thumbnails/unrelated.jpg',
        'link_id' => 'https://example.com/artikel-tidak-berkaitan',
        'author' => 'Admin Test',
        'published_at' => now()->addMinute(),
    ]);

    $response = $this->from(route('admin.artikel'))->patch(
        route('admin.artikel.restore', $this->article->id),
        ['replacement_article_id' => $unrelatedActive->id]
    );

    $response
        ->assertRedirect(route('admin.artikel'))
        ->assertSessionHasErrors('replacement_article_id');

    $this->assertSoftDeleted('articles', [
        'id' => $this->article->id,
    ]);
    $this->assertDatabaseHas('articles', [
        'id' => $unrelatedActive->id,
        'deleted_at' => null,
    ]);
});

it('does not allow the restore endpoint to act on an active article', function (): void {
    $this->patch(route('admin.artikel.restore', $this->article->id))
        ->assertNotFound();
});
