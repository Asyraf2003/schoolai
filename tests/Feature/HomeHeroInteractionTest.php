<?php

use App\Models\Article;
use App\Models\HeroSetting;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function promotedHeroArticle(): Article
{
    return Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'hero-linked-story',
        'title_id' => 'Cerita Hero Tertaut',
        'title_en' => 'Linked Hero Story',
        'description_id' => 'Deskripsi artikel hero.',
        'description_en' => 'Hero article description.',
        'content_en' => '<p>Canvas body must not become Hero copy.</p>',
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'hero_position' => 1,
        'link_id' => url('/artikel/hero-linked-story'),
        'published_at' => now(),
    ]);
}

function heroSlideHtml(string $content, int $index): string
{
    $start = strpos($content, 'data-slide-index="'.$index.'"');
    $end = strpos($content, '</article>', $start);

    return substr($content, $start, $end - $start);
}

it('keeps Opening as the sole h1 and links a promoted Article as a later slide', function (): void {
    $article = promotedHeroArticle();
    HeroSetting::query()->firstOrFail()->update([
        'title_en' => 'Independent School Opening',
        'description_en' => 'Independent opening copy.',
        'cta_label_en' => 'Admissions',
        'cta_url' => '/ppdb',
    ]);

    $response = $this->withSession(['locale' => 'en'])->get(route('home'));
    $content = $response->getContent();
    $opening = heroSlideHtml($content, 0);
    $promoted = heroSlideHtml($content, 1);
    $articleUrl = route('artikel.native', $article->slug, false);

    expect(substr_count($content, '<h1'))->toBe(1)
        ->and($opening)->toContain('Independent School Opening')
        ->toContain('Independent opening copy.')
        ->toContain('href="/ppdb"')
        ->not->toContain('hero-cinema__title-link')
        ->and($promoted)->toContain('<h2')
        ->toContain('Linked Hero Story')
        ->toContain('Hero article description.')
        ->toContain('href="'.e($articleUrl).'"')
        ->not->toContain('Canvas body must not become Hero copy.');
});

it('does not let PPDB status replace Opening copy', function (): void {
    HeroSetting::query()->firstOrFail()->update([
        'title_id' => 'Copy Opening Milik Sekolah',
        'cta_url' => null,
    ]);
    PpdbSetting::query()->firstOrFail()->update(['is_active' => true]);

    $opening = heroSlideHtml($this->get(route('home'))->getContent(), 0);

    expect($opening)
        ->toContain('Copy Opening Milik Sekolah')
        ->not->toContain(__('runtime.home.ppdb_campaign_title'));
});

it('keeps audio user-gesture ownership and no hero glow runtime', function (): void {
    $entry = file_get_contents(resource_path('js/pages/welcome-hero.js'));
    $media = file_get_contents(resource_path('js/pages/welcome-hero/slider-media.js'));
    $title = file_get_contents(resource_path('views/home/partials/hero-title.blade.php'));

    expect($entry)
        ->not->toContain('initHeroTitleGlow')
        ->toContain('audioEnabled: false')
        ->toContain("audioButton.addEventListener('click'")
        ->and($media)
        ->toContain('video.muted = !state.audioEnabled')
        ->toContain('video.muted = true')
        ->and($title)
        ->not->toContain('data-hero-title-glow')
        ->not->toContain('data-hero-title-base');
});
