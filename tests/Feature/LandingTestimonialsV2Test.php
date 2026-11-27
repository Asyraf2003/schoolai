<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one localized testimonial wall after Gallery and before Stories', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));

    $response->assertOk()->assertSee(__('testimonials.title'))->assertSee(__('testimonials.description'));
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $dom = new DOMXPath($document);
    expect($dom->query('//*[@data-testimonials]')->length)->toBe(1);
    expect($dom->query('//*[@data-gallery]/following-sibling::*[1][@data-testimonials]')->length)->toBe(1);
    expect($dom->query('//*[@data-testimonials]/following-sibling::*[1][@data-stories]')->length)->toBe(1);
    expect($dom->query('//*[main/*[last()][@data-stories]]/main/following-sibling::*[1][@data-footer-v2]')->length)->toBe(1);
    expect($dom->query('//*[@data-testimonials][@aria-labelledby="testimonials-title"]//h2[@id="testimonials-title"]')->length)->toBe(1);
    expect($dom->query('//*[@data-testimonials]/descendant::header[1]/p[@class="sr-only"]')->length)->toBe(1);
    expect($dom->query('//*[@data-testimonials-wall]/*[@data-testimonials-row][@aria-label]')->length)->toBe(3);
    expect($dom->query('(//*[@data-testimonials-row])[1]/*[@data-testimonials-track]/article[blockquote/p]')->length)->toBe(7);
    expect($dom->query('(//*[@data-testimonials-row])[2]/*[@data-testimonials-track]/article[blockquote/p]')->length)->toBe(7);
    expect($dom->query('(//*[@data-testimonials-row])[3]/*[@data-testimonials-track]/article[blockquote/p]')->length)->toBe(7);
    expect($dom->query('//*[@data-testimonials-wall]//img[@loading="lazy"][@width="40"][@height="40"][@alt=""]')->length)->toBe(21);
    expect($dom->query('//*[@data-testimonials]//*[self::a or self::button or self::dialog or @tabindex]')->length)->toBe(0);

    foreach (__('testimonials.rows') as $row) {
        foreach ($row['cards'] as $card) {
            $response->assertSee($card['quote'])->assertSee($card['name']);
        }
    }
    $response->assertSee(__('testimonials.source_types.sample'));
})->with(['id', 'en', 'ar']);

it('renders native links and source labels only for supported Instagram URLs', function (): void {
    $cards = trans('testimonials', [], 'en')['rows'][0]['cards'];
    $sources = [
        ['story', 'https://www.instagram.com/stories/test_fixture/1234/'],
        ['comment', 'https://instagram.com/p/TEST_FIXTURE/?comment_id=4321'],
        ['story', 'https://instagram.com.evil.example/stories/test_fixture/1234/'],
        ['comment', 'javascript:alert(1)'],
        ['story', 'https://instagram.com/'],
        ['comment', 'https://fake@instagram.com/p/TEST_FIXTURE/'],
    ];
    foreach ($sources as $index => [$type, $url]) {
        $cards[$index]['source_type'] = $type;
        $cards[$index]['source_url'] = $url;
    }
    app('translator')->addLines(['testimonials.rows.0.cards' => $cards], 'en');

    $response = $this->withSession(['locale' => 'en'])->get(route('home'))->assertOk();
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $links = (new DOMXPath($document))->query('//*[@data-testimonial-link]');
    expect($links->length)->toBe(2);
    foreach (array_slice($sources, 0, 2) as $index => [$type, $url]) {
        $link = $links->item($index);
        expect($link->getAttribute('href'))->toBe($url);
        expect($link->getAttribute('aria-label'))->toContain($cards[$index]['name']);
        expect($link->parentNode->textContent)->toContain(__('testimonials.source_types.'.$type));
    }
});
