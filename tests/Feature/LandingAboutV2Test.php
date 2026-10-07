<?php

use App\View\Presenters\LandingAboutPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders localized About stories after Hero with the five intended missions', function (string $locale, string $headline, string $mission): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));

    $response->assertViewIs('landing.index')
        ->assertSeeHtmlInOrder(['data-hero', 'data-about-story="about"', 'data-about-story="vision"', 'data-about-story="mission"'])
        ->assertSee($headline)
        ->assertSee($mission)
        ->assertViewHas('about', fn (array $about): bool => count($about['stories']['mission']['items']) === 5);
})->with([
    ['id', 'Gagasan Berani,', 'Lima Jaminan Mutu Lulusan Al-Mustaqbal'],
    ['en', 'Bold Ideas,', 'Five Al-Mustaqbal Graduate Quality Assurances'],
    ['ar', 'أفكار جريئة،', 'خمسة ضمانات لجودة خريجي مدرسة المستقبل'],
]);

it('keeps full videos inert and exposes modal controls only for About and Mission', function (): void {
    $about = app(LandingAboutPresenter::class)->present();
    $document = new DOMDocument;
    @$document->loadHTML(view('landing.about', compact('about'))->render());
    $dom = new DOMXPath($document);

    expect($dom->query('//video[@src] | //source')->length)->toBe(0);
    expect($dom->query('//*[@data-about-story="about"]//button[@data-about-open]')->length)->toBe(1);
    expect($dom->query('//*[@data-about-story="mission"]//button[@data-about-open]')->length)->toBe(1);
    expect($dom->query('//*[@data-about-story="vision"]//button | //*[@data-about-story="vision"]/@data-full')->length)->toBe(0);
    expect($dom->query('//*[@data-about-layer]')->length)->toBe(2);
    expect($dom->query('//*[@class="about__mascot"]')->length)->toBe(0);
    expect($about)->not->toHaveKey('mascot');
    expect($dom->query('//dialog/video[@controls][@preload="none"]')->length)->toBe(1);
    expect($about['stories']['about']['media']['full'])->toEndWith('/about/media/main/ad3344ec-86c3-42d6-98d0-4e35b3863888.mp4');
    expect($about['stories']['mission']['media']['full'])->toEndWith('/site/vision/mission-video-v1.mp4');
});

it('uses configured derivatives and geometry33 without legacy Vision or About presentation fields', function (string $locale): void {
    app()->setLocale($locale);
    config(['media.static.ornaments.geometry_33' => 'https://example.test/tile.webp']);
    $about = app(LandingAboutPresenter::class)->present();

    $html = view('landing.about', compact('about'))->render();

    expect($html)->toContain('data-background="https://example.test/tile.webp"')
        ->not->toContain(config('media.homepage_vision_video_url'))
        ->not->toContain(config('media.static.ornaments.geometry_32'))
        ->not->toContain(__('home.about_stats_story.cta'))
        ->not->toContain(__('home.about_stats_story.media_label'));
    foreach (['about', 'vision', 'mission'] as $key) {
        expect($about['stories'][$key]['media']['poster'])->toEndWith('/site/about-v2/'.$key.'-poster-v1.webp');
        expect($about['stories'][$key]['media']['preview'])->toEndWith('/site/about-v2/'.$key.'-preview-v1.mp4');
        expect($about['stories'][$key])->not->toHaveKey('cta')->not->toHaveKey('media_label');
    }
    expect($about['stories']['vision']['body'])->toBe(implode('', array_column(__('home.visi_misi.vision.text_parts'), 'text')));
    expect(array_column($about['stories']['mission']['items'], 'title'))->toBe(array_column(__('home_parity.visi_misi.missions'), 'title'));
})->with(['id', 'en', 'ar']);
