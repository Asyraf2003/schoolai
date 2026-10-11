<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the two-stage localized footer after all four articles', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));
    $response->assertOk()->assertSeeHtmlInOrder([
        'id="artikel"', 'data-stories-number="4"', 'id="kontak"',
    ])->assertSee(__('home.footer.brand.description'))
        ->assertSee(__('footer_v2.keep_scrolling'))
        ->assertSee(__('footer_v2.articles'))
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
        ->and($xpath->query('//*[@id="kontak"]//*[@data-footer-next-alternate]')->length)->toBe(1);

    $main = $xpath->query('//*[@id="kontak"]//*[@data-footer-next-main]')->item(0);
    $alternative = $xpath->query('//*[@id="kontak"]//*[@data-footer-next-alternate]')->item(0);
    expect($main->getAttribute('href'))->toBe(route('galeri'))
        ->and($alternative->getAttribute('href'))->toBe(route('artikel'));
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
