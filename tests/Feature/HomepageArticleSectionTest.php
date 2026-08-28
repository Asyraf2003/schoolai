<?php

use App\Models\Article;
use App\Models\HeroSetting;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('renders the three latest published Articles in Lead Rail order after Testimonial', function (): void {
    app()->setLocale('id');

    foreach ([
        ['title' => 'Artikel Terbaru Satu', 'slug' => 'terbaru-satu', 'published_at' => now()->subMinute(), 'tag' => 'Sekolah'],
        ['title' => 'Artikel Terbaru Dua', 'slug' => 'terbaru-dua', 'published_at' => now()->subHour(), 'tag' => 'Karya'],
        ['title' => 'Artikel Terbaru Tiga', 'slug' => 'terbaru-tiga', 'published_at' => now()->subHours(2), 'tag' => 'Karakter'],
        ['title' => 'Artikel Lama Tidak Masuk', 'slug' => 'artikel-lama', 'published_at' => now()->subDay(), 'tag' => 'Arsip'],
    ] as $data) {
        Article::query()->create([
            'article_source' => Article::SOURCE_EXTERNAL,
            'article_status' => Article::STATUS_PUBLISHED,
            'title_id' => $data['title'],
            'description_id' => 'Deskripsi '.$data['title'],
            'tags' => [$data['tag']],
            'thumbnail_url' => 'https://media.almustaqbal.sch.id/articles/thumbnails/test/'.$data['slug'].'.webp',
            'link_id' => 'https://example.com/'.$data['slug'],
            'author' => 'Admin Test',
            'published_at' => $data['published_at'],
        ]);
    }

    $response = $this->get(route('home'));
    $html = $response->getContent();

    $response
        ->assertOk()
        ->assertSee('data-article-showcase', false)
        ->assertSee('CERITA', false)
        ->assertSee('&amp; WAWASAN', false)
        ->assertSee('Artikel Terbaru Satu')
        ->assertSee('Artikel Terbaru Dua')
        ->assertSee('Artikel Terbaru Tiga')
        ->assertDontSee('Artikel Lama Tidak Masuk')
        ->assertDontSee('Artikel, kabar, dan catatan yang merekam proses belajar, karya, dan kehidupan di Al Mustaqbal.')
        ->assertDontSee('Baca artikel')
        ->assertDontSee('article-showcase__read', false)
        ->assertSee('Lihat selengkapnya')
        ->assertDontSee('data-article-variant', false)
        ->assertDontSee('data-article-story', false);

    expect(strpos($html, 'data-testimonial-wall'))
        ->toBeLessThan(strpos($html, 'data-article-showcase'))
        ->and(substr_count($html, 'article-showcase__card article-showcase__card--'))
        ->toBe(3)
        ->and(strpos($html, 'Artikel Terbaru Satu'))
        ->toBeLessThan(strpos($html, 'Artikel Terbaru Dua'))
        ->and(strpos($html, 'Artikel Terbaru Dua'))
        ->toBeLessThan(strpos($html, 'Artikel Terbaru Tiga'));
});

it('uses a lean latest-published Article query for the homepage showcase', function (): void {
    app()->setLocale('id');
    HeroSetting::query()->firstOrFail()->update(['cta_url' => '#program']);

    Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'artikel-homepage-db',
        'title_id' => 'Artikel Homepage Database',
        'description_id' => 'Ringkasan artikel homepage dari database.',
        'content_id' => '<p>Body Canvas berat yang tidak boleh ikut query homepage.</p>',
        'thumbnail_url' => 'https://media.almustaqbal.sch.id/articles/thumbnails/test/homepage-db.webp',
        'link_id' => url('/artikel/artikel-homepage-db'),
        'author' => 'Admin Test',
        'published_at' => now()->subMinute(),
    ]);

    $queries = collect();
    DB::listen(function ($query) use ($queries): void {
        $queries->push(strtolower($query->sql));
    });

    $response = $this->get(route('home'));
    $homepageArticleQuery = $queries->first(
        fn (string $sql): bool => (str_contains($sql, 'from "articles"') || str_contains($sql, 'from `articles`'))
            && str_contains($sql, 'order by')
            && str_contains($sql, 'published_at')
            && str_contains($sql, 'limit 3'),
    );

    $response
        ->assertOk()
        ->assertSee('Artikel Homepage Database')
        ->assertDontSee('Belajar dengan arah, tumbuh dengan adab');

    expect($homepageArticleQuery)
        ->toBeString()
        ->not->toContain('content_id')
        ->not->toContain('content_en')
        ->not->toContain('content_ar')
        ->not->toContain('select *')
        ->and($queries->contains(
            fn (string $sql): bool => str_contains($sql, 'site_statistics')
        ))->toBeFalse()
        ->and($queries->contains(
            fn (string $sql): bool => str_contains($sql, 'testimonial_media')
        ))->toBeFalse()
        ->and($queries->contains(
            fn (string $sql): bool => str_contains($sql, 'gallery_items')
        ))->toBeFalse();
});

it('queries PPDB exactly once to resolve the homepage campaign state', function (): void {
    HeroSetting::query()->firstOrFail()->update(['cta_url' => '#program']);
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);
    $queries = collect();
    DB::listen(function ($query) use ($queries): void {
        $queries->push(strtolower($query->sql));
    });

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('href="/ppdb"', false);

    expect($queries->filter(
        fn (string $sql): bool => str_contains($sql, 'from "ppdb_settings"')
            || str_contains($sql, 'from `ppdb_settings`')
    ))->toHaveCount(1);
});
