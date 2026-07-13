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
