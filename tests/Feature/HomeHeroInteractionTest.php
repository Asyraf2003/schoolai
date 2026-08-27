<?php

use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createPrimaryHeroArticleVideo(): Article
{
    HeroSlide::query()->delete();
    $article = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'hero-linked-story',
        'title_id' => 'Cerita Hero Tertaut',
        'title_en' => 'Linked Hero Story',
        'title_ar' => 'قصة واجهة مرتبطة',
        'description_id' => 'Deskripsi artikel hero.',
        'description_en' => 'Hero article description.',
        'description_ar' => 'وصف مقال الواجهة.',
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'link_id' => url('/artikel/hero-linked-story'),
        'author' => 'Hero Test',
        'published_at' => now(),
    ]);
    HeroSlide::query()->create([
        'article_id' => $article->getKey(),
        'type' => 'video',
        'media_url' => 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4',
        'poster_url' => Article::PLACEHOLDER_THUMBNAIL,
        'title_id' => $article->title_id,
        'title_en' => $article->title_en,
        'title_ar' => $article->title_ar,
        'sort_order' => 1,
        'is_active' => true,
    ]);

    return $article;
}

function firstHeroSlideHtml(string $content): string
{
    $start = strpos($content, 'data-slide-index="0"');
    $end = strpos($content, '</article>', $start);

    return substr($content, $start, $end - $start);
}

it('keeps one h1 and links the primary title server side', function (): void {
    $article = createPrimaryHeroArticleVideo();
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);
    $response = $this->withSession(['locale' => 'en'])->get(route('home'));

    $content = $response->getContent();
    $heroStart = strpos($content, '<section');
    $heroEnd = strpos($content, '</section>', $heroStart);
    $heroHtml = substr($content, $heroStart, $heroEnd - $heroStart);
    $articleUrl = route('artikel.native', $article->slug, false);

    expect(substr_count($content, '<h1'))->toBe(1)
        ->and(substr_count($heroHtml, '<h1'))->toBe(1)
        ->and(substr_count($heroHtml, '<h2'))->toBe(0)
        ->and(substr_count($heroHtml, 'class="hero-cinema__title-link"'))->toBe(1)
        ->and($heroHtml)->toContain('href="'.e($articleUrl).'"')
        ->and($heroHtml)->not->toContain('data-hero-title-glow')
        ->and($heroHtml)->not->toContain('hero-cinema__description')
        ->and($heroHtml)->not->toContain('hero-cinema__cta');
});

it('renders PPDB campaign as title-only hero copy', function (): void {
    createPrimaryHeroArticleVideo();
    PpdbSetting::query()->firstOrFail()->update([
        'registration_url' => 'https://apply.example.org/al-mustaqbal',
        'is_active' => true,
    ]);

    foreach (['id', 'en', 'ar'] as $locale) {
        $response = $this->withSession(['locale' => $locale])->get(route('home'));
        $firstSlideHtml = firstHeroSlideHtml($response->getContent());

        expect($firstSlideHtml)
            ->toContain('hero-cinema__title')
            ->not->toContain('hero-cinema__eyebrow')
            ->not->toContain('hero-cinema__description')
            ->not->toContain('hero-cinema__cta')
            ->not->toContain('data-hero-ppdb-description-link')
            ->not->toContain('data-hero-title-glow');
    }
});

it('keeps the primary article title while PPDB is closed', function (): void {
    $article = createPrimaryHeroArticleVideo();
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);
    $response = $this->withSession(['locale' => 'id'])->get(route('home'));
    $firstSlideHtml = firstHeroSlideHtml($response->getContent());

    expect($firstSlideHtml)
        ->toContain(e($article->title_id))
        ->not->toContain(e($article->description_id))
        ->toContain('hero-cinema__title-link')
        ->not->toContain('data-hero-ppdb-description-link')
        ->not->toContain('data-hero-title-glow');
});

it('removes the hero glow runtime and keeps a single video looping', function (): void {
    $entry = file_get_contents(resource_path('js/pages/welcome-hero.js'));
    $media = file_get_contents(resource_path('js/pages/welcome-hero/slider-media.js'));
    $title = file_get_contents(resource_path('views/home/partials/hero-title.blade.php'));
    $visual = file_get_contents(resource_path('css/pages/welcome-hero-visual.css'));

    expect($entry)
        ->not->toContain('initHeroTitleGlow')
        ->not->toContain("./welcome-hero/title-glow.js")
        ->and($media)
        ->toContain('var singleSlide = slides.length === 1')
        ->toContain('video.loop = singleSlide')
        ->and($title)
        ->not->toContain('data-hero-title-glow')
        ->not->toContain('data-hero-title-base')
        ->and($visual)
        ->not->toContain('hero-title-glow__overlay');

    expect(file_exists(resource_path('js/pages/welcome-hero/title-glow.js')))->toBeFalse()
        ->and(file_exists(resource_path('css/pages/welcome-hero/text-interactions.css')))->toBeFalse();
});
