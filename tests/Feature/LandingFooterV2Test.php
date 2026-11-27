<?php

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the localized white footer without an auto-gallery panel after all four articles', function (string $locale): void {
    foreach (range(1, 4) as $index) {
        Article::create(['content_id' => '<p>Artikel '.$index.'</p>']);
    }
    $response = $this->withSession(['locale' => $locale])->get(route('home'));
    $response->assertOk()->assertSeeHtmlInOrder([
        'id="artikel"', 'data-stories-number="4"', 'id="kontak"',
    ])->assertSee(__('home.footer.brand.description'));

    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $xpath = new DOMXPath($document);
    expect($xpath->query('//*[@id="kontak"]')->length)->toBe(1)
        ->and($xpath->query('//*[@id="kontak"]//*[@class="site-footer-v2__top-space"]')->length)->toBe(1)
        ->and($xpath->query('//*[@id="kontak"]//*[@data-footer-preview]')->length)->toBe(5)
        ->and($xpath->query('//*[@id="kontak"]//*[@class="site-footer-v2__partner-grid"]//a')->length)->toBe(5)
        ->and($xpath->query('//*[@id="kontak"]//*[@data-footer-visual-image]')->length)->toBe(1)
        ->and($xpath->query('//*[@id="kontak"]//*[@class="site-footer-v2__content"]')->length)->toBe(1)
        ->and($xpath->query('//*[@id="kontak"]//*[@class="site-footer-v2__social"]//a')->length)->toBe(5)
        ->and($xpath->query('//*[@id="kontak"]//*[@data-footer-next-shell]')->length)->toBe(0)
        ->and($xpath->query('//*[@id="kontak"]//*[@data-footer-next-main]')->length)->toBe(0)
        ->and($xpath->query('//*[@id="kontak"]//*[@class="site-footer-v2__social-arrow"]')->length)->toBe(5);

    $maps = $xpath->query('//*[@id="kontak"]//*[@data-footer-preview="maps"]')->item(0);
    expect($maps->getAttribute('data-footer-preview-src'))->toBe(config('media.static.footer.school_map'))
        ->and($maps->getAttribute('data-footer-preview-fallback'))->toContain('/site/footer/maps-v1.webp');
})->with(['id', 'en', 'ar']);

it('uses the configured real footer contact and partner destinations', function (): void {
    $response = $this->get(route('home'));
    foreach (__('home.footer.channels') as $channel) {
        $response->assertSee($channel['href']);
    }
    foreach (__('home.footer.partners') as $partner) {
        $response->assertSee($partner['href']);
    }
});
