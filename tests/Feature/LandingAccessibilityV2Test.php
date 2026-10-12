<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps visible action text in accessible names and exposes every Stories description', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $xpath = new DOMXPath($document);

    $fonts = $xpath->query('//head/link[@rel="preload"][@as="font"][@crossorigin]');
    expect($fonts->length)->toBe($locale === 'ar' ? 2 : 1);
    foreach ($fonts as $font) {
        expect($font->getAttribute('href'))->toContain($locale === 'ar' ? 'cairo-arabic-' : 'inter-latin-variable-');
    }

    $audio = $xpath->query('//*[@data-audio]')->item(0);
    expect($audio->getAttribute('aria-label'))->toBe(__('shared.navbar.audio.off').'. '.__('shared.navbar.audio.enable_label'));
    foreach ($xpath->query('//a[contains(@class,"hero__copy-action")]') as $action) {
        expect($action->hasAttribute('aria-label'))->toBeFalse();
        expect(trim($action->textContent))->not->toBeEmpty();
    }
    foreach ($xpath->query('//*[@data-stories-feature]//p') as $index => $paragraph) {
        expect($paragraph->hasAttribute('aria-label'))->toBeFalse();
        $description = $xpath->query('./span[@class="sr-only"]', $paragraph);
        expect($description->length)->toBe(1);
        expect($description->item(0)->textContent)->toBe(__('stories.features.'.$index.'.description'));
    }
})->with(['id', 'en', 'ar']);
