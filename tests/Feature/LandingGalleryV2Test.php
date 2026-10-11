<?php

use App\Models\GalleryItem;
use App\View\Presenters\LandingGalleryPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('retains testimonial quotes when optional background photographs are unavailable', function (): void {
    config(['media.static.testimonials' => []]);
    $response = $this->get(route('home'));
    $response->assertOk()->assertSee(__('testimonials.rows.0.cards.0.quote'));
    expect($response->viewData('testimonials')['rows'][0]['cards'][0]['background_url'])->toBe('');
});

it('renders ten distinct real media after Values and a populated testimonial wall', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));
    $response->assertOk()->assertSeeHtmlInOrder(['id="nilai"', 'id="galeri"', 'id="testimoni"'])
        ->assertSee(__('gallery_v2.title'))->assertSee(__('testimonials.description'));
    $items = $response->viewData('gallery')['items'];
    expect($items)->toHaveCount(10)
        ->and(array_unique(array_column($items, 'url')))->toHaveCount(10)
        ->and(array_filter($items, fn ($item) => $item['type'] === 'video'))->toHaveCount(5)
        ->and($items[0]['type'])->toBe('video')
        ->and($response->viewData('testimonials')['rows'][0]['cards'])->not->toBeEmpty();
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $dom = new DOMXPath($document);
    expect($dom->query('//*[@data-gallery-item]')->length)->toBe(10)
        ->and($dom->query('//video[@data-gallery-video][@src]')->length)->toBe(0)
        ->and($dom->query('//video[@data-gallery-video][@poster][@preload="none"]')->length)->toBe(5);
})->with(['id', 'en', 'ar']);

it('does not impose the legacy nine-item limit on published V2 media', function (): void {
    for ($index = 1; $index <= 10; $index++) {
        GalleryItem::query()->create(['title' => 'Gallery test', 'type' => 'photo', 'is_published' => true,
            'show_on_homepage' => true, 'media_url' => config('media.public_url')."/gallery/media/real-$index.webp"]);
    }
    $items = app(LandingGalleryPresenter::class)->present()['items'];
    expect($items)->toHaveCount(10)
        ->and(array_filter($items, fn ($item) => $item['type'] === 'photo'))->toHaveCount(10);
});

it('does not resurrect withheld seed media or include unsafe media URLs', function (): void {
    $withheld = config('media.public_url').'/gallery/media/it-v1.mp4';
    GalleryItem::query()->create(['title' => 'Gallery test', 'media_url' => $withheld, 'is_published' => false]);
    GalleryItem::query()->create(['title' => 'Gallery test', 'media_url' => 'javascript:alert(1)', 'is_published' => true, 'show_on_homepage' => true]);
    $urls = array_column(app(LandingGalleryPresenter::class)->present()['items'], 'url');
    expect($urls)->not->toContain($withheld)->not->toContain('javascript:alert(1)');
});
