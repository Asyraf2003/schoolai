<?php

use App\Models\Article;
use App\Models\HeroSetting;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('renders four latest published Articles in one Head plus three Rail order after Testimonial', function (): void {
    app()->setLocale('id');

    foreach ([
        ['title' => 'Artikel Masa Depan', 'slug' => 'masa-depan', 'published_at' => now()->addDay(), 'tag' => 'Nanti'],
        ['title' => 'Artikel Terbaru Satu', 'slug' => 'terbaru-satu', 'published_at' => now()->subMinute(), 'tag' => 'Sekolah'],
        ['title' => 'Artikel Terbaru Dua', 'slug' => 'terbaru-dua', 'published_at' => now()->subHour(), 'tag' => 'Karya'],
        ['title' => 'Artikel Terbaru Tiga', 'slug' => 'terbaru-tiga', 'published_at' => now()->subHours(2), 'tag' => 'Karakter'],
        ['title' => 'Artikel Terbaru Empat', 'slug' => 'terbaru-empat', 'published_at' => now()->subHours(3), 'tag' => 'Prestasi'],
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
        ->assertSee('Artikel Terbaru Empat')
        ->assertDontSee('Artikel Masa Depan')
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
        ->toBe(4)
        ->and(strpos($html, 'Artikel Terbaru Satu'))
        ->toBeLessThan(strpos($html, 'Artikel Terbaru Dua'))
        ->and(strpos($html, 'Artikel Terbaru Dua'))
        ->toBeLessThan(strpos($html, 'Artikel Terbaru Tiga'))
        ->and(strpos($html, 'Artikel Terbaru Tiga'))
        ->toBeLessThan(strpos($html, 'Artikel Terbaru Empat'));
});

it('keeps Article heading reveal clipping separate from its horizontal shift', function (): void {
    $blade = file_get_contents(resource_path('views/home/sections/articles.blade.php'));
    $typography = file_get_contents(resource_path('css/surfaces/home/article-showcase/typography.css'));

    expect($blade)
        ->toContain('article-showcase__title-line--{{ $loop->first ? \'top\' : \'bottom\' }}')
        ->and($typography)
        ->toContain('.article-showcase__title-line--bottom')
        ->toContain('article-showcase-heading-drop')
        ->toContain('article-showcase-heading-shift')
        ->toContain('transform: translate3d(var(--editorial-shift), 0, 0)')
        ->not->toContain(".article-showcase__title-line {\n    width: 100%;");
});

it('prioritizes homepage pins and fills remaining slots with latest published Articles', function (): void {
    app()->setLocale('id');

    $pinned = Article::query()->create([
        'article_source' => Article::SOURCE_EXTERNAL,
        'article_status' => Article::STATUS_PUBLISHED,
        'title_id' => 'Prestasi Lama Tetap Head',
        'description_id' => 'Artikel lama yang sengaja dipin.',
        'thumbnail_url' => 'https://media.almustaqbal.sch.id/articles/thumbnails/test/pinned.webp',
        'link_id' => 'https://example.com/pinned',
        'published_at' => now()->subMonth(),
        'homepage_position' => 1,
    ]);

    foreach ([1, 2, 3, 4] as $index) {
        Article::query()->create([
            'article_source' => Article::SOURCE_EXTERNAL,
            'article_status' => Article::STATUS_PUBLISHED,
            'title_id' => 'Latest Auto '.$index,
            'description_id' => 'Auto '.$index,
            'thumbnail_url' => 'https://media.almustaqbal.sch.id/articles/thumbnails/test/auto-'.$index.'.webp',
            'link_id' => 'https://example.com/auto-'.$index,
            'published_at' => now()->subMinutes($index),
        ]);
    }

    $html = $this->get(route('home'))
        ->assertOk()
        ->assertSee($pinned->title_id)
        ->assertSee('Latest Auto 1')
        ->assertSee('Latest Auto 2')
        ->assertSee('Latest Auto 3')
        ->assertDontSee('Latest Auto 4')
        ->getContent();

    expect(strpos($html, 'Prestasi Lama Tetap Head'))
        ->toBeLessThan(strpos($html, 'Latest Auto 1'));
});

it('uses a lean pinned-first Article query for the homepage showcase', function (): void {
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
            && str_contains($sql, 'homepage_position')
            && str_contains($sql, 'published_at')
            && str_contains($sql, 'limit 4'),
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
        ))->toBeTrue();
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
