<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders canonical, social metadata, and School structured data on the landing page', function (): void {
    $this->get(route('home'))->assertOk()
        ->assertSee('rel="canonical" href="'.route('home').'"', false)
        ->assertSee('property="og:image"', false)
        ->assertSee('name="twitter:card" content="summary_large_image"', false)
        ->assertSee('type="application/ld+json"', false)
        ->assertSee('"@type":"School"', false);
});

it('renders SEO for article listings and detail pages', function (): void {
    $article = Article::create([
        'title_id' => 'Cerita Sekolah',
        'description_id' => 'Kabar terbaru dari sekolah',
        'content_id' => '<p>Berita kegiatan sekolah.</p>',
    ]);

    $this->get(route('artikel'))->assertOk()
        ->assertSee('rel="canonical" href="'.route('artikel').'"', false);

    $this->get(route('artikel.native', ['article' => $article->slug]))->assertOk()
        ->assertSee('property="og:type" content="article"', false)
        ->assertSee('content="Cerita Sekolah · Al Mustaqbal School"', false)
        ->assertSee('content="Kabar terbaru dari sekolah"', false)
        ->assertSee('"@type":"Article"', false);
});

it('does not advertise login or admin pages to search engines', function (): void {
    $this->get(route('portal.login'))->assertOk()
        ->assertSee('name="robots" content="noindex, nofollow"', false)
        ->assertDontSee('rel="canonical"', false);

    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))->get(route('admin.artikel'))->assertOk()
        ->assertSee('name="robots" content="noindex, nofollow"', false);
});

it('indexes the real gallery and preserves paginated canonical URLs without tracking parameters', function (string $name): void {
    $this->get(route($name, ['page' => 2, 'utm_source' => 'test']))->assertOk()
        ->assertSee('rel="canonical" href="'.route($name).'?page=2"', false)
        ->assertDontSee('name="robots" content="noindex', false);
})->with(['artikel', 'galeri']);

it('uses a video article poster for sharing and keeps structured data script-safe', function (): void {
    $article = Article::create([
        'title_id' => '</script><script>alert(1)</script>', 'content_id' => '<p>Isi</p>',
        'cover_url' => config('media.public_url').'/gallery/media/it-v1.mp4',
        'cover_thumbnail_url' => config('media.static.school_life.labit'),
    ]);
    $response = $this->withSession(['locale' => 'id'])->get($article->linkForLocale('id'))->assertOk()
        ->assertSee('property="og:image" content="'.$article->cover_thumbnail_url.'"', false)
        ->assertDontSee('</script><script>alert(1)</script>', false);
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $scripts = (new DOMXPath($document))->query('//script[@type="application/ld+json"]');
    expect($scripts->length)->toBe(1);
    $data = json_decode($scripts->item(0)->textContent, true, flags: JSON_THROW_ON_ERROR);
    expect($data['@graph'][2]['headline'])->toBe($article->title_id);
});

it('preserves RTL and localized Open Graph language', function (): void {
    $this->withSession(['locale' => 'ar'])->get(route('home'))->assertOk()
        ->assertSee('<html lang="ar" dir="rtl">', false)
        ->assertSee('property="og:locale" content="ar_AR"', false);
});
