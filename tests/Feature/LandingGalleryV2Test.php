<?php

use App\Models\GalleryItem;
use App\View\Presenters\LandingGalleryPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('retains testimonial quotes when optional avatars are unavailable', function (): void {
    config(['media.static.testimonials' => []]);
    $response = $this->get(route('home'));
    $response->assertOk()->assertSee(__('testimonials.rows.0.cards.0.quote'));
    expect($response->viewData('testimonials')['rows'][0][0]['avatar_url'])->toBeNull();
});

it('renders thirty-four media with four random bottom side picks in order before Testimonials and Stories', function (string $locale): void {
    foreach (range(1, 10) as $index) {
        GalleryItem::create(['media_url' => config('media.public_url').'/gallery/media/test-'.$index.($index > 5 ? '.mp4' : '.webp'),
            'type' => $index > 5 ? 'video' : 'photo', 'alt_text' => 'Media '.$index,
            'thumbnail_url' => config('media.static.school_life.labit')]);
    }
    $response = $this->withSession(['locale' => $locale])->get(route('home'));
    $response->assertOk()->assertSeeHtmlInOrder(['id="nilai"', 'id="galeri"', 'id="testimoni"', 'id="artikel"'])
        ->assertSee(__('gallery_v2.title'))->assertSee(implode(' ', __('gallery_v2.statement_lines')), false);
    $items = $response->viewData('gallery')['items'];
    expect($items)->toHaveCount(10)->and(array_unique(array_column($items, 'url')))->toHaveCount(10);
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $dom = new DOMXPath($document);
    $figures = $dom->query('//*[@data-gallery-item]');
    $posters = array_map(static fn (DOMElement $figure): string => $figure->getElementsByTagName('img')[0]->getAttribute('src'),
        iterator_to_array($figures));
    $passOne = array_slice($posters, 0, 10);
    $counts = array_count_values($posters);
    expect($figures->length)->toBe(34)
        ->and($passOne)->toBe(array_column($items, 'poster'))
        ->and(min($counts))->toBeGreaterThanOrEqual(3)
        ->and(array_sum($counts))->toBe(34)
        ->and($dom->query('//*[@data-gallery-item][@aria-hidden="true"]')->length)->toBe(24)
        ->and($dom->query('//img[@data-gallery-poster][@loading="lazy"]')->length)->toBe(34)
        ->and($dom->query('//video[@data-gallery-video][@src or @poster]')->length)->toBe(0)
        ->and($dom->query('//video[@data-gallery-video][@preload="none"]')->length)->toBeGreaterThanOrEqual(15);
})->with(['id', 'en', 'ar']);

it('keeps admin media unlimited and the landing catalog independent from uploaded media', function (): void {
    foreach (range(1, 12) as $index) {
        GalleryItem::create(['media_url' => config('media.public_url').'/gallery/media/photo-'.$index.'.webp', 'alt_text' => 'Media '.$index]);
    }
    expect(GalleryItem::count())->toBe(12)->and(app(LandingGalleryPresenter::class)->present()['items'])->toHaveCount(10);
    $this->get(route('home'))->assertDontSee('/gallery/media/photo-12.webp', false);
    $this->get(route('galeri'))->assertOk()->assertSee('data-coming-soon="galeri"', false);
});

it('retains the built in catalog when database media is deleted or unsafe', function (): void {
    $item = GalleryItem::create(['media_url' => config('media.public_url').'/gallery/media/it-v1.mp4']);
    $item->delete();
    GalleryItem::create(['media_url' => 'javascript:alert(1)']);
    expect(app(LandingGalleryPresenter::class)->present()['items'])->toHaveCount(10);
    $this->get(route('home'))->assertOk()->assertSee('id="galeri"', false);
});
