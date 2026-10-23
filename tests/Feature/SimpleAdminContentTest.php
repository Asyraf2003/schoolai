<?php

use App\Models\User;
use App\Models\Article;
use App\Models\GalleryItem;
use App\Services\ActiveSessionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('renders exactly four admin functions with functioning simple forms', function (): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    foreach (['admin.accounts.index', 'admin.galeri', 'admin.artikel', 'admin.ppdb', 'admin.artikel.create', 'admin.galeri.create'] as $name) {
        $response = $this->get(route($name))->assertOk();
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new DOMXPath($document);
        expect($xpath->query('//nav[@aria-label="Menu admin"]/a')->length)->toBe(4);
        $response->assertSee('Akun')->assertSee('Galeri')->assertSee('Artikel')->assertSee('Link PPDB');
    }
    foreach (['admin.hero', 'admin.artikel.canvas.start', 'admin.artikel.homepage.pin', 'admin.galeri.sections.store', 'admin.ppdb.showcase.store'] as $name) {
        expect(Route::has($name))->toBeFalse();
    }
});

it('requires authentication and admin role for every content mutation', function (string $role): void {
    if ($role !== 'guest') {
        $this->actingAs(User::factory()->create(['role' => $role]));
    }
    foreach (['admin.artikel.store', 'admin.galeri.store'] as $name) {
        $response = $this->postJson(route($name), []);
        $role === 'guest' ? $response->assertUnauthorized() : $response->assertForbidden();
    }
    $response = $this->putJson(route('admin.ppdb.update'), ['registration_url' => 'https://forms.gle/a']);
    $role === 'guest' ? $response->assertUnauthorized() : $response->assertForbidden();
})->with(['guest', 'guru', 'murid']);

it('keeps content mutations behind CSRF and active-session checks', function (): void {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->actingAs($admin)->withSession([ActiveSessionManager::SESSION_KEY => 99]);
    $this->post(route('admin.artikel.store'), ['content_id' => '<p>Isi</p>'])->assertRedirect(route('login'));
    $this->actingAs($admin);
    app()->detectEnvironment(fn () => 'production');
    try {
        $this->put(route('admin.ppdb.update'), ['registration_url' => 'https://forms.gle/a'])->assertStatus(419);
    } finally {
        app()->detectEnvironment(fn () => 'testing');
    }
});

it('exposes content management entry points and shared admin components', function (): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->get(route('admin.artikel'))->assertOk()->assertSee('Tambah artikel')->assertSee('Belum ada artikel');
    $this->get(route('admin.galeri'))->assertOk()->assertSee('Tambah media')->assertSee('Galeri masih kosong');
    $this->get(route('admin.accounts.index'))->assertOk()->assertSee('Tambah akun Guru atau Murid');
    $this->get(route('admin.ppdb'))->assertOk()->assertSee('URL Google Form');
});

it('lists active and archived articles and media without losing restore actions', function (): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $article = Article::query()->create(['title_id' => 'Kabar Kegiatan', 'content_id' => '<p>Isi artikel.</p>']);
    $media = GalleryItem::query()->create([
        'type' => 'photo',
        'media_url' => 'https://media.almustaqbal.sch.id/gallery/test.webp',
        'alt_text' => 'Foto kegiatan',
    ]);
    $this->get(route('admin.artikel'))->assertOk()->assertSee('Kabar Kegiatan')->assertSee('Edit');
    $this->get(route('admin.galeri'))->assertOk()->assertSee('Foto kegiatan')->assertSee('Edit');
    $article->delete();
    $media->delete();
    $this->get(route('admin.artikel'))->assertOk()->assertSee('Kabar Kegiatan')->assertSee('Pulihkan');
    $this->get(route('admin.galeri'))->assertOk()->assertSee('Foto kegiatan')->assertSee('Pulihkan');
});
