<?php

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the empty message instead of language dummy articles when no article exists', function (): void {
    app()->setLocale('id');

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertViewHas(
            'articlesSection',
            fn (array $section): bool => ($section['items'] ?? null) === []
        )
        ->assertSee('Belum ada artikel terbaru.')
        ->assertDontSee('Children’s Learning Rhythm: Calm, Directed, and Not Rushed');
});

it('shows the latest database article first on the homepage', function (): void {
    app()->setLocale('id');

    Article::query()->create([
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

    Article::query()->create([
        'title_id' => 'Artikel Terbaru Homepage',
        'title_en' => 'Latest Homepage Article',
        'description_id' => 'Artikel yang paling baru.',
        'description_en' => 'The latest article.',
        'thumbnail_url' => '/storage/articles/thumbnails/latest.jpg',
        'link_id' => 'https://example.com/artikel-terbaru-homepage',
        'link_en' => 'https://example.com/latest-homepage-article',
        'author' => 'Admin Test',
        'published_at' => now(),
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertViewHas('articlesSection', function (array $section): bool {
            $items = $section['items'] ?? [];

            return count($items) === 2
                && ($items[0]['title'] ?? null) === 'Artikel Terbaru Homepage'
                && ($items[1]['title'] ?? null) === 'Artikel Lama Homepage';
        })
        ->assertSee('Artikel Terbaru Homepage')
        ->assertSee('Artikel Lama Homepage')
        ->assertDontSee('Children’s Learning Rhythm: Calm, Directed, and Not Rushed');
});
