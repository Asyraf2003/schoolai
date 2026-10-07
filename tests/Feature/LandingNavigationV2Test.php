<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps localized navigation and one reusable media node per submenu', function (string $locale, string $galleryLabel): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));
    $response->assertViewHas('navigation');
    $items = $response->viewData('navigation')['menuItems'];
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $dom = new DOMXPath($document);
    $headers = $dom->query('//header[@data-header]');
    $media = $dom->query('//header[@data-header]//img[@data-menu-media]');
    $expected = array_values(array_filter($items, fn (array $item): bool => $item['has_mega_menu']));

    expect($headers->length)->toBe(1);
    expect($items[2]['label'])->toBe($galleryLabel);
    expect($media->length)->toBe(count($expected));
    foreach ($expected as $index => $item) {
        $image = $media->item($index);
        expect($image->getAttribute('src'))->toBe($item['mega_media_url']);
        expect($image->getAttribute('alt'))->toBe($item['mega_media_alt']);
        expect($image->getAttribute('loading'))->toBe('lazy');
        expect($image->getAttribute('decoding'))->toBe('async');
        $response->assertSee($item['label']);
        foreach ($item['mega']['links'] as $link) {
            $response->assertSee($link['label']);
        }
    }
    $response->assertDontSee('resources_old', false);
})->with([['en', 'Gallery'], ['id', 'Galeri'], ['ar', 'المعرض']]);

it('reads the main gallery label and empty submenu fallbacks from translation ownership', function (): void {
    app()->setLocale('en');
    __('home.navbar');
    __('shared.navbar');
    app('translator')->addLines([
        'shared.navbar.labels.gallery' => 'Localized gallery',
        'shared.navbar.labels.facilities' => 'Localized facilities',
        'shared.navbar.labels.articles' => 'Localized articles',
        'home.navbar.items.2.label' => '',
        'home.navbar.items.3.label' => '',
    ], 'en');

    $response = $this->withSession(['locale' => 'en'])->get(route('home'));
    $items = $response->viewData('navigation')['menuItems'];

    expect($items[2]['label'])->toBe('Localized gallery');
    expect($items[2]['mega']['links'][0]['label'])->toBe('Localized facilities');
    expect($items[3]['mega']['links'][0]['label'])->toBe('Localized articles');
});
