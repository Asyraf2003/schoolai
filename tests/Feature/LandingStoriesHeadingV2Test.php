<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the OLD-inspired two-line Stories heading in the V2 section order', function (string $locale, string $title): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));

    $response->assertOk()
        ->assertSeeHtmlInOrder(['id="testimoni"', 'id="artikel"'])
        ->assertSee('<section class="stories" id="artikel" data-stories aria-labelledby="stories-title">', false)
        ->assertSee($title);

    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $xpath = new DOMXPath($document);
    $heading = $xpath->query('//*[@id="artikel"]//h2[@id="stories-title"][@data-stories-heading]');

    expect($heading->length)->toBe(1)
        ->and($heading->item(0)->getAttribute('aria-label'))->toBe($title)
        ->and($xpath->query('//*[@data-stories-heading]/*[contains(@class,"stories__heading-clip")]')->length)->toBe(2)
        ->and($xpath->query('//*[@data-stories-heading-line][@aria-hidden="true"]')->length)->toBe(2)
        ->and($xpath->query('//*[@id="artikel"]//img | //*[@id="artikel"]//video')->length)->toBe(0);

    foreach (__('stories.lines') as $line) {
        $response->assertSee($line);
    }
})->with([
    ['id', 'Stories & Insights'],
    ['en', 'Stories & Insights'],
    ['ar', 'قصص ورؤى'],
]);
