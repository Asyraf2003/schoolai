<?php

use App\Models\Article;
use App\Models\HeroSetting;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('renders the static homepage Article Lead Rail after Testimonial', function (): void {
    app()->setLocale('id');

    $response = $this->get(route('home'));
    $html = $response->getContent();

    $response
        ->assertOk()
        ->assertSee('data-article-showcase', false)
        ->assertSee('CERITA', false)
        ->assertSee('&amp; WAWASAN', false)
        ->assertSee('Lihat selengkapnya')
        ->assertDontSee('data-article-variant', false)
        ->assertDontSee('data-article-story', false)
        ->assertDontSee('article-debug-mark', false)
        ->assertDontSee('tes1');

    expect(strpos($html, 'data-testimonial-wall'))
        ->toBeLessThan(strpos($html, 'data-article-showcase'))
        ->and(substr_count($html, 'article-showcase__card article-showcase__card--'))
        ->toBe(4)
        ->and($response->original->getData())->not->toHaveKey('articlesSection');
});

it('keeps the dummy homepage Article preview independent from Article queries', function (): void {
    app()->setLocale('id');
    HeroSetting::query()->firstOrFail()->update(['cta_url' => '#program']);

    Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'artikel-lama-homepage',
        'title_id' => 'Artikel Lama Homepage',
        'title_en' => 'Older Homepage Article',
        'description_id' => 'Artikel yang lebih lama.',
        'description_en' => 'The older article.',
        'thumbnail_url' => '/storage/articles/thumbnails/older.jpg',
        'link_id' => 'https://example.com/artikel-lama-homepage',
        'link_en' => 'https://example.com/older-homepage-article',
        'author' => 'Admin Test',
        'published_at' => now()->subDay(),
    ]);

    $queries = collect();
    DB::listen(function ($query) use ($queries): void {
        $queries->push(strtolower($query->sql));
    });

    $response = $this->get(route('home'));
    $articleQueries = $queries->filter(
        fn (string $sql): bool => str_contains($sql, 'from "articles"')
            || str_contains($sql, 'from `articles`')
    );

    $response
        ->assertOk()
        ->assertSee('data-article-showcase', false)
        ->assertDontSee('Artikel Lama Homepage');

    expect($response->original->getData())
        ->not->toHaveKeys(['articlesSection', 'stats', 'quickInfo', 'ppdb'])
        ->and($articleQueries)->toHaveCount(1)
        ->and($articleQueries->first())->toContain('hero_position')
        ->and($queries->contains(
            fn (string $sql): bool => str_contains($sql, 'site_statistics')
        ))->toBeFalse()
        ->and($queries->contains(
            fn (string $sql): bool => str_contains($sql, 'testimonial_media')
        ))->toBeFalse()
        ->and($queries->contains(
            fn (string $sql): bool => str_contains($sql, 'ppdb_settings')
        ))->toBeFalse()
        ->and($queries->contains(
            fn (string $sql): bool => str_contains($sql, 'gallery_items')
        ))->toBeTrue();
});

it('queries PPDB only when the optional Opening CTA targets PPDB', function (): void {
    HeroSetting::query()->firstOrFail()->update(['cta_url' => '/ppdb']);
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
