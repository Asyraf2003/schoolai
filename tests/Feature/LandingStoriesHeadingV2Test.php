<?php

it('renders the localized Stories heading immediately after Testimonials', function (string $locale, string $title): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));

    $response->assertOk()
        ->assertSeeHtmlInOrder(['id="testimoni"', 'id="artikel"'])
        ->assertSee('<section class="stories" id="artikel" data-stories aria-labelledby="stories-title">', false)
        ->assertSee($title);

    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $xpath = new DOMXPath($document);

    expect($xpath->query('//*[@id="artikel"]//h2[@id="stories-title"]')->length)->toBe(1);
    expect($xpath->query('//*[@id="artikel"]//img | //*[@id="artikel"]//video')->length)->toBe(0);
})->with([
    ['id', 'Stories & Insights'],
    ['en', 'Stories & Insights'],
    ['ar', 'قصص ورؤى'],
]);
