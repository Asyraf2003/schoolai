<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the localized question and every existing disclosed sample in one testimonial wall', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));

    $response->assertOk()->assertSee(__('testimonials.title'))->assertSee(__('testimonials.description'));
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $dom = new DOMXPath($document);
    expect($dom->query('//*[@id="testimonials-title"]')->length)->toBe(1);
    expect($dom->query('//*[@data-testimonials-row]')->length)->toBe(3);
    expect($dom->query('//*[@data-testimonials-row][@tabindex="0"][@aria-describedby="testimonials-browse"]')->length)->toBe(3);
    expect($dom->query('//*[@data-testimonials-track]/article')->length)->toBe(21);
    expect($dom->query('//*[@data-testimonials-track]//img[@loading="lazy"][@width="960"][@height="480"]')->length)->toBe(21);
    foreach (__('testimonials.rows') as $row) {
        foreach ($row['cards'] as $card) {
            $response->assertSee($card['quote'])->assertSee($card['name']);
        }
    }
})->with(['id', 'en', 'ar']);
