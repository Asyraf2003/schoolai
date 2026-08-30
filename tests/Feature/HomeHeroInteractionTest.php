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
        'thumbnail_url' => (string) config('media.static.seo.home_og'),
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
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);
    HeroSetting::query()->firstOrFail()->update([
        'title_en' => 'Independent School Opening',
        'description_en' => 'Independent opening copy.',
        'cta_label_en' => 'Admissions',
        'cta_url' => '#program',
    ]);

    $response = $this->withSession(['locale' => 'en'])->get(route('home'));
    $content = $response->getContent();
    $opening = heroSlideHtml($content, 0);
    $promoted = heroSlideHtml($content, 1);
    $articleUrl = route('artikel.native', $article->slug, false);

    expect(substr_count($content, '<h1'))->toBe(1)
        ->and($opening)->toContain('Independent School Opening')
        ->toContain('Independent opening copy.')
        ->toContain('href="#program"')
        ->not->toContain('hero-cinema__title-link')
        ->and($promoted)->toContain('<h2')
        ->toContain('Linked Hero Story')
        ->toContain('Hero article description.')
        ->toContain('href="'.e($articleUrl).'"')
        ->toContain((string) config('media.static.seo.home_og'))
        ->not->toContain('Canvas body must not become Hero copy.');
});

it('makes every open PPDB campaign copy affordance lead to localized PPDB information', function (
    string $locale,
    string $eyebrow,
    string $title,
    string $description,
    string $linkLabel,
): void {
    HeroSetting::query()->firstOrFail()->update([
        'eyebrow_id' => 'Eyebrow admin lama',
        'title_id' => 'Copy Opening Milik Admin',
        'description_id' => 'Deskripsi opening milik admin.',
        'cta_label_id' => 'CTA admin',
        'cta_url' => '#program',
    ]);
    PpdbSetting::query()->firstOrFail()->update([
        'registration_url' => 'https://apply.example.test/form',
        'is_active' => true,
    ]);

    $opening = heroSlideHtml(
        $this->withSession(['locale' => $locale])->get(route('home'))->getContent(),
        0,
    );

    expect($opening)
        ->toContain($eyebrow)
        ->toContain($title)
        ->toContain($description)
        ->toContain($linkLabel)
        ->toContain('hero-cinema__eyebrow-link')
        ->toContain('hero-cinema__title-link')
        ->toContain('hero-cinema__description-link')
        ->toContain('hero-cinema__cta')
        ->and(substr_count($opening, 'href="/ppdb"'))->toBe(4)
        ->and($opening)->toContain('aria-label="'.e($linkLabel).'"')
        ->not->toContain('Copy Opening Milik Admin')
        ->not->toContain('Deskripsi opening milik admin.');
})->with([
    'Indonesia' => [
        'id',
        'Penerimaan Peserta Didik Baru',
        'Langkah Awal Menuju Pendidikan yang Bermakna',
        'Bergabunglah bersama Al-Mustaqbal dan tumbuhkan potensi anak melalui pendidikan yang berakar pada nilai Islam.',
        'Buka informasi pendaftaran PPDB',
    ],
    'English' => [
        'en',
        'New Student Admissions',
        'Begin a Meaningful Learning Journey',
        'Join Al-Mustaqbal and nurture every child’s potential through education rooted in Islamic values.',
        'Open admission information',
    ],
    'Arabic' => [
        'ar',
        'التسجيل للطلاب الجدد',
        'بداية رحلة تعليمية هادفة',
        'انضموا إلى المستقبل، ولننمِّ قدرات أبنائنا من خلال تعليم راسخ في القيم الإسلامية.',
        'فتح معلومات التسجيل والقبول',
    ],
]);

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
        ->toContain("button.addEventListener('click'")
        ->toContain("video.setAttribute('loop', '')")
        ->toContain("'requestIdleCallback' in window")
        ->toContain('window.requestIdleCallback(startDeferredVideo, { timeout: 900 })')
        ->toContain('window.setTimeout(startDeferredVideo, 120)')
        ->not->toContain('data-hero-playback')
        ->not->toContain('userPaused')
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
