<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the two-stage localized footer after all four articles', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));
    $response->assertOk()->assertSeeHtmlInOrder([
        'id="artikel"', 'data-stories-number="4"', 'id="kontak"',
    ])->assertSee(__('home.footer.brand.description'))
        ->assertSee(__('footer_v2.keep_scrolling'))
        ->assertSee(__('footer_v2.gallery'));

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
        ->and($xpath->query('//*[@id="kontak"]//*[@data-footer-next-shell]')->length)->toBe(1)
        ->and($xpath->query('//*[@id="kontak"]//*[@data-footer-next-main]')->length)->toBe(1)
        ->and($xpath->query('//*[@id="kontak"]//*[@data-footer-next-alternate]')->length)->toBe(0)
        ->and($xpath->query('//*[@id="kontak"]//*[@class="site-footer-v2__social-arrow"]')->length)->toBe(5)
        ->and($xpath->query('//*[@id="kontak"]//*[@class="site-footer-v2__next-line-fill"]')->length)->toBe(1);

    $maps = $xpath->query('//*[@id="kontak"]//*[@data-footer-preview="maps"]')->item(0);
    expect($maps->getAttribute('data-footer-preview-src'))->toContain('/media/footer/school-map.webp')
        ->and($maps->getAttribute('data-footer-preview-fallback'))->toContain('/site/footer/maps-v1.webp');

    $main = $xpath->query('//*[@id="kontak"]//*[@data-footer-next-main]')->item(0);
    expect($main->getAttribute('href'))->toBe(route('galeri'));
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
