<?php

use App\Models\Article;
use App\Models\HeroSetting;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('keeps the disabled homepage Article surface out of the rendered DOM', function (): void {
    app()->setLocale('id');

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertDontSee('data-article-story', false)
        ->assertDontSee('Belum ada artikel terbaru.')
        ->assertDontSee('Children’s Learning Rhythm: Calm, Directed, and Not Rushed');

    expect($response->original->getData())->not->toHaveKey('articlesSection');
});

it('does not query disabled homepage data owners', function (): void {
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
        ->assertDontSee('data-article-story', false)
        ->assertDontSee('data-article-journey', false)
        ->assertDontSee('data-article-final-cta', false)
        ->assertDontSee('Children’s Learning Rhythm: Calm, Directed, and Not Rushed');

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
