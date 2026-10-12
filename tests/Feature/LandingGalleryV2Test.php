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

it('renders database photo and video media after Values without changing the V2 DOM', function (string $locale): void {
    foreach (range(1, 10) as $index) {
        GalleryItem::create(['media_url' => config('media.public_url').'/gallery/media/test-'.$index.($index > 5 ? '.mp4' : '.webp'),
            'type' => $index > 5 ? 'video' : 'photo', 'alt_text' => 'Media '.$index,
            'thumbnail_url' => config('media.static.school_life.labit')]);
    }
    $response = $this->withSession(['locale' => $locale])->get(route('home'));
    $response->assertOk()->assertSeeHtmlInOrder(['id="nilai"', 'id="galeri"', 'id="testimoni"'])
        ->assertSee(__('gallery_v2.title'))->assertSee(implode(' ', __('gallery_v2.statement_lines')), false);
    $items = $response->viewData('gallery')['items'];
    expect($items)->toHaveCount(10)->and(array_unique(array_column($items, 'url')))->toHaveCount(10);
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $dom = new DOMXPath($document);
    expect($dom->query('//*[@data-gallery-item]')->length)->toBe(10)
        ->and($dom->query('//video[@data-gallery-video][@src]')->length)->toBe(0)
        ->and($dom->query('//video[@data-gallery-video][@poster][@preload="none"]')->length)->toBe(5);
})->with(['id', 'en', 'ar']);

it('keeps admin media unlimited while showing the latest ten on the landing composition', function (): void {
    foreach (range(1, 12) as $index) {
        GalleryItem::create(['media_url' => config('media.public_url').'/gallery/media/photo-'.$index.'.webp', 'alt_text' => 'Media '.$index]);
    }
    expect(GalleryItem::count())->toBe(12)->and(app(LandingGalleryPresenter::class)->present()['items'])->toHaveCount(10);
});

it('does not resurrect deleted media or include unsafe URLs and supports an empty gallery', function (): void {
    $item = GalleryItem::create(['media_url' => config('media.public_url').'/gallery/media/it-v1.mp4']);
    $item->delete();
    GalleryItem::create(['media_url' => 'javascript:alert(1)']);
    expect(app(LandingGalleryPresenter::class)->present()['items'])->toBeEmpty();
    $this->get(route('home'))->assertOk()->assertSee('id="galeri"', false);
});
