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
        ->and($xpath->query('//*[@data-stories-feature]')->length)->toBe(4)
        ->and($xpath->query('//*[@data-stories-feature]//img[@data-stories-poster]')->length)->toBe(4)
        ->and($xpath->query('//*[@data-stories-feature]//video[@data-stories-video]')->length)->toBe(4)
        ->and($xpath->query('//*[@data-stories-feature]//h3')->length)->toBe(4)
        ->and($xpath->query('//*[@data-stories-feature]//h3[@aria-label]')->length)->toBe(4)
        ->and($xpath->query('//*[@data-stories-feature]//p[@aria-label]')->length)->toBe(4)
        ->and($xpath->query('//*[@data-stories-feature]//*[@data-stories-word][@aria-hidden="true"]')->length)->toBeGreaterThan(20);

    $videos = $xpath->query('//*[@data-stories-feature]//video');
    foreach (['it', 'musola', 'renang', 'tekwondo'] as $index => $name) {
        $video = $videos->item($index);
        expect($video->hasAttribute('muted'))->toBeTrue()
            ->and($video->hasAttribute('autoplay'))->toBeTrue()
            ->and($video->hasAttribute('loop'))->toBeTrue()
            ->and($video->hasAttribute('playsinline'))->toBeTrue()
            ->and($video->hasAttribute('controls'))->toBeFalse()
            ->and($video->hasAttribute('src'))->toBeFalse()
            ->and($video->getAttribute('data-src'))->toContain('/gallery/media/'.$name.'-v1.mp4');
        $response->assertSee(__('stories.features.'.$index.'.title'))
            ->assertSee(__('stories.features.'.$index.'.description'));
    }

    foreach (__('stories.lines') as $line) {
        $response->assertSee($line);
    }
})->with([
    ['id', 'Stories & Insights'],
    ['en', 'Stories & Insights'],
    ['ar', 'قصص ورؤى'],
]);
