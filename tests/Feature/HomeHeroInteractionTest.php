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
    $end = strpos($content, 'data-slide-index="1"', $start);
    $end = $end === false ? strpos($content, '</article>', $start) : $end;

    return substr($content, $start, $end - $start);
}

it('keeps one h1 and links the first admin article placement server side', function (): void {
    $article = createPrimaryHeroArticleVideo();
    $response = $this->withSession(['locale' => 'en'])->get(route('home'));

    $response->assertOk()->assertViewHas('hero', function (array $hero) use ($article): bool {
        $slides = $hero['slides'] ?? [];
        return count($slides) === 1
            && ($slides[0]['is_primary_slide'] ?? false) === true
            && ($slides[0]['render_type'] ?? null) === 'video'
            && ($slides[0]['article_id'] ?? null) === $article->getKey()
            && ($slides[0]['title_href'] ?? null) === route('artikel.native', $article->slug, false);
    });

    $content = $response->getContent();
    $heroStart = strpos($content, '<section');
    $heroEnd = strpos($content, '</section>', $heroStart);
    $heroHtml = substr($content, $heroStart, $heroEnd - $heroStart);
    $articleUrl = route('artikel.native', $article->slug, false);
    expect(substr_count($content, '<h1'))->toBe(1)
        ->and(substr_count($heroHtml, '<h1'))->toBe(1)
        ->and(substr_count($heroHtml, '<h2'))->toBe(0)
        ->and(substr_count($heroHtml, 'class="hero-cinema__title-link"'))->toBe(1)
        ->and($heroHtml)->toContain('href="'.e($articleUrl).'" class="hero-cinema__title-link"')
        ->and($heroHtml)->toContain('data-hero-title-glow');
});

it('turns the primary article video into a localized PPDB campaign while open', function (): void {
    $article = createPrimaryHeroArticleVideo();
    $registrationUrl = 'https://apply.example.org/al-mustaqbal';
    PpdbSetting::query()->firstOrFail()->update([
        'registration_url' => $registrationUrl,
        'is_active' => true,
    ]);
    $campaigns = [
        'id' => ['Penerimaan Peserta Didik Baru', 'Langkah Awal Menuju Pendidikan yang Bermakna', 'Bergabunglah bersama Al-Mustaqbal dan tumbuhkan potensi anak melalui pendidikan yang berakar pada nilai Islam.', 'Daftar PPDB'],
        'en' => ['New Student Admissions', 'Begin a Meaningful Learning Journey', 'Join Al-Mustaqbal and nurture every child’s potential through education rooted in Islamic values.', 'Apply for Admission'],
        'ar' => ['التسجيل للطلاب الجدد', 'بداية رحلة تعليمية هادفة', 'انضموا إلى المستقبل، ولننمِّ قدرات أبنائنا من خلال تعليم راسخ في القيم الإسلامية.', 'التسجيل للقبول'],
    ];

    foreach ($campaigns as $locale => [$eyebrow, $title, $description, $label]) {
        $response = $this->withSession(['locale' => $locale])->get(route('home'));
        $response->assertOk()->assertViewHas('hero', function (array $hero) use ($eyebrow, $title, $description, $label, $registrationUrl): bool {
            $first = $hero['slides'][0] ?? [];
            return ($first['is_primary_slide'] ?? false) === true
                && ($first['render_type'] ?? null) === 'video'
                && ($first['show_ppdb_cta'] ?? false) === true
                && ($first['ppdb_url'] ?? null) === $registrationUrl
                && ($first['ppdb_label'] ?? null) === $label
                && ($first['eyebrow'] ?? null) === $eyebrow
                && ($first['title'] ?? null) === $title
                && ($first['description'] ?? null) === $description
                && ($first['title_href'] ?? 'missing') === null
                && ($first['cta'] ?? null) === [];
        });
        $firstSlideHtml = firstHeroSlideHtml($response->getContent());
        expect($firstSlideHtml)
            ->toContain(e($eyebrow), e($title), e($description), e($label))
            ->not->toContain(e($article->titleForLocale($locale)))
            ->not->toContain('hero-cinema__title-link');
    }
});

it('restores the complete primary article presentation while PPDB is closed', function (): void {
    $article = createPrimaryHeroArticleVideo();
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);
    $response = $this->withSession(['locale' => 'id'])->get(route('home'));

    $response->assertOk()->assertViewHas('hero', function (array $hero) use ($article): bool {
        $first = $hero['slides'][0] ?? [];
        return ($first['is_primary_slide'] ?? false) === true
            && ($first['show_ppdb_cta'] ?? true) === false
            && ($first['ppdb_url'] ?? 'missing') === null
            && ($first['title'] ?? null) === $article->title_id
            && ($first['description'] ?? null) === $article->description_id
            && ($first['title_href'] ?? null) === route('artikel.native', $article->slug, false);
    });
    $firstSlideHtml = firstHeroSlideHtml($response->getContent());
    expect($firstSlideHtml)
        ->toContain(e($article->title_id), e($article->description_id), 'hero-cinema__title-link')
        ->not->toContain('data-hero-ppdb-cta')
        ->not->toContain('Langkah Awal Menuju Pendidikan yang Bermakna');
});

it('replays bounded hero glow for every active slide and keeps posters stable', function (): void {
    $entry = file_get_contents(resource_path('js/pages/welcome-hero.js'));
    $glow = file_get_contents(resource_path('js/pages/welcome-hero/title-glow.js'));
    $media = file_get_contents(resource_path('js/pages/welcome-hero/slider-media.js'));
    $roll = file_get_contents(resource_path('js/pages/welcome-hero/ppdb-roll.js'));
    $styles = file_get_contents(resource_path('css/pages/welcome-hero/text-interactions.css'));

    expect($entry)->not->toContain("querySelector('.hero-cinema__cta[href]')")
        ->and($entry)->toContain('initHeroTitleGlow(root)')
        ->and($entry)->toContain('initHeroPpdbRoll(root)')
        ->and($glow)->toContain("granularity: 'grapheme'")
        ->and($glow)->toContain("root.addEventListener('hero:slide-active'")
        ->and($glow)->toContain('playActivatedTitle(')
        ->and($glow)->not->toContain('touchActivation')
        ->and($media)->not->toContain("removeAttribute('poster')")
        ->and($roll)->toContain('rotateX(')
        ->and($styles)->toContain('@media (prefers-reduced-motion: reduce)')
        ->and($styles)->toContain('.hero-cinema__title-link:focus-visible');
});
