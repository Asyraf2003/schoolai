<?php

use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps one h1 and uses h2 plus server title links only for articles', function (): void {
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

    $response = $this->withSession(['locale' => 'en'])->get(route('home'));
    $response->assertOk()->assertViewHas('hero', function (array $hero) use ($article): bool {
        $slides = $hero['slides'] ?? [];
        return ($slides[0]['is_primary_slide'] ?? false) === true
            && ($slides[0]['render_type'] ?? null) === 'video'
            && ($slides[0]['title_href'] ?? null) === null
            && ($slides[1]['article_id'] ?? null) === $article->getKey()
            && ($slides[1]['title_href'] ?? null) === route('artikel.native', $article->slug, false);
    });

    $content = $response->getContent();
    $heroStart = strpos($content, '<section');
    $heroEnd = strpos($content, '</section>', $heroStart);
    $heroHtml = substr($content, $heroStart, $heroEnd - $heroStart);
    $slideCount = count($response->viewData('hero')['slides'] ?? []);
    $articleUrl = route('artikel.native', $article->slug, false);
    expect(substr_count($content, '<h1'))->toBe(1)
        ->and(substr_count($heroHtml, '<h1'))->toBe(1)
        ->and(substr_count($heroHtml, '<h2'))->toBe($slideCount - 1)
        ->and(substr_count($heroHtml, 'class="hero-cinema__title-link"'))->toBe(1)
        ->and($heroHtml)->toContain('href="'.e($articleUrl).'" class="hero-cinema__title-link"')
        ->and($heroHtml)->toContain('data-hero-title-glow');
});

it('replaces only the primary video description with localized PPDB CTA while open', function (): void {
    $registrationUrl = 'https://apply.example.org/al-mustaqbal';
    PpdbSetting::query()->firstOrFail()->update([
        'registration_url' => $registrationUrl,
        'is_active' => true,
    ]);
    $labels = [
        'id' => 'Daftar PPDB',
        'en' => 'Apply for Admission',
        'ar' => 'التسجيل للقبول',
    ];

    foreach ($labels as $locale => $label) {
        $response = $this->withSession(['locale' => $locale])->get(route('home'));
        $response->assertOk()->assertViewHas('hero', function (array $hero) use ($label, $registrationUrl): bool {
            $first = $hero['slides'][0] ?? [];
            return ($first['is_primary_slide'] ?? false) === true
                && ($first['render_type'] ?? null) === 'video'
                && ($first['show_ppdb_cta'] ?? false) === true
                && ($first['ppdb_url'] ?? null) === $registrationUrl
                && ($first['ppdb_label'] ?? null) === $label;
        });
        $first = $response->viewData('hero')['slides'][0];
        $content = $response->getContent();
        $start = strpos($content, 'data-slide-index="0"');
        $end = strpos($content, 'data-slide-index="1"');
        $firstSlideHtml = substr($content, $start, $end - $start);
        $response
            ->assertSee('data-hero-ppdb-cta', false)
            ->assertSee('data-hero-roll-label', false)
            ->assertSee($label);
        expect($firstSlideHtml)
            ->not->toContain(e($first['description']))
            ->not->toContain(e($first['cta']['label']));
    }
});

it('restores the normal primary description and CTA while PPDB is closed', function (): void {
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);

    $response = $this->withSession(['locale' => 'id'])->get(route('home'));
    $response->assertOk()->assertViewHas('hero', function (array $hero): bool {
        $first = $hero['slides'][0] ?? [];
        return ($first['is_primary_slide'] ?? false) === true
            && ($first['show_ppdb_cta'] ?? true) === false
            && array_key_exists('ppdb_url', $first)
            && $first['ppdb_url'] === null;
    });
    $first = $response->viewData('hero')['slides'][0];
    $content = $response->getContent();
    $start = strpos($content, 'data-slide-index="0"');
    $end = strpos($content, 'data-slide-index="1"');
    $firstSlideHtml = substr($content, $start, $end - $start);
    $response
        ->assertDontSee('data-hero-ppdb-cta', false)
        ->assertSee($first['description']);
    expect($firstSlideHtml)
        ->toContain(e($first['description']))
        ->toContain(e($first['cta']['label']));
});

it('keeps hero motion hooks bounded and removes CTA-derived title mutation', function (): void {
    $entry = file_get_contents(resource_path('js/pages/welcome-hero.js'));
    $glow = file_get_contents(resource_path('js/pages/welcome-hero/title-glow.js'));
    $roll = file_get_contents(resource_path('js/pages/welcome-hero/ppdb-roll.js'));
    $styles = file_get_contents(resource_path('css/pages/welcome-hero/text-interactions.css'));

    expect($entry)->not->toContain("querySelector('.hero-cinema__cta[href]')")
        ->and($entry)->toContain('initHeroTitleGlow(root)')
        ->and($entry)->toContain('initHeroPpdbRoll(root)')
        ->and($glow)->toContain("granularity: 'grapheme'")
        ->and($glow)->toContain('hero-title-glow__overlay--run')
        ->and($roll)->toContain('rotateX(')
        ->and($styles)->toContain('@media (prefers-reduced-motion: reduce)')
        ->and($styles)->toContain('.hero-cinema__title-link:focus-visible');
});
