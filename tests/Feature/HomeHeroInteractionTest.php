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
    $opening = file_get_contents(resource_path('js/pages/welcome-hero/opening.js'));
    $carousel = file_get_contents(resource_path('js/pages/welcome-hero/carousel.js'));
    $media = file_get_contents(resource_path('js/pages/welcome-hero/slider-media.js'));
    $title = file_get_contents(resource_path('views/home/partials/hero-title.blade.php'));

    expect($entry)
        ->not->toContain('initHeroTitleGlow')
        ->toContain("import('./welcome-hero/carousel.js')")
        ->not->toContain("from './welcome-hero/slider-media.js'")
        ->not->toContain("from './welcome-hero/slider-playback.js'")
        ->and($opening)
        ->toContain('var audioEnabled = false')
        ->toContain("audioButton?.addEventListener('click'")
        ->toContain("video.setAttribute('loop', '')")
        ->not->toContain('data-hero-playback')
        ->not->toContain('userPaused')
        ->not->toContain('setTimeout')
        ->not->toContain("addEventListener('keydown'")
        ->and($carousel)
        ->toContain("import '../../../css/pages/welcome-hero-carousel.css'")
        ->toContain("from './slider-media.js'")
        ->toContain("from './slider-playback.js'")
        ->and($media)
        ->toContain('video.muted = !state.audioEnabled')
        ->toContain('video.muted = true')
        ->and($title)
        ->not->toContain('data-hero-title-glow')
        ->not->toContain('data-hero-title-base');
});

it('renders only the agreed carousel arrows when a published Article is promoted', function (): void {
    promotedHeroArticle();

    $response = $this->withSession(['locale' => 'en'])->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('data-hero-mode="carousel"', false)
        ->assertSee('data-hero-previous', false)
        ->assertSee('data-hero-next', false)
        ->assertDontSee('data-hero-playback', false)
        ->assertDontSee('data-hero-dot', false)
        ->assertDontSee('data-hero-progress', false)
        ->assertDontSee('data-hero-current', false);
});
