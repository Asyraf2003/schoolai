<?php

use App\View\Presenters\LandingValuesPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders four localized Values cards in V2 after Program with one line and one text plane', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));

    $response->assertViewIs('landing.index')
        ->assertSeeHtmlInOrder(['data-program-values', 'id="program"', 'data-program-values-seam', 'id="nilai"', 'data-values-heading', 'data-values-cards-track'])
        ->assertViewHas('values', fn (array $values): bool => $values === app(LandingValuesPresenter::class)->present())
        ->assertSee(__('home.nilai_sekolah.heading'))
        ->assertSee(__('home.nilai_sekolah.subtitle'))
        ->assertDontSee('data-values-spatial', false)
        ->assertDontSee('resources_old', false);

    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $dom = new DOMXPath($document);
    expect($dom->query('//*[@data-program-type-home]')->length)->toBe(1)
        ->and($dom->query('//*[@data-program-type-line]')->length)->toBe(20)
        ->and($dom->query('//section[@id="nilai"]//h2[@id="values-title"]')->length)->toBe(1)
        ->and($dom->query('//*[@data-values-heading-line][@aria-hidden="true"]')->length)->toBe(2)
        ->and($dom->query('//section[@id="nilai"]//*[@data-values-line][@aria-hidden="true"]')->length)->toBe(1)
        ->and($dom->query('//*[@data-values-line]//*[local-name()="svg"][@viewbox="0 0 1920 5400"][@focusable="false"]')->length)->toBe(1)
        ->and($dom->query('//*[@data-values-line]//*[local-name()="path"][@fill="none"][@stroke="#fff"]')->length)->toBe(1)
        ->and($dom->query('//section[@id="nilai"]//*[@data-values-card]')->length)->toBe(4)
        ->and($dom->query('//section[@id="nilai"]//*[@data-values-card-inner]')->length)->toBe(4)
        ->and($dom->query('//section[@id="nilai"]//h3')->length)->toBe(4)
        ->and($dom->query('//section[@id="nilai"]//*[@aria-hidden="true" and contains(@class,"values__card-back")]')->length)->toBe(4)
        ->and($dom->query('//section[@id="nilai"]//img | //section[@id="nilai"]//canvas')->length)->toBe(0);

    foreach (__('home.nilai_sekolah.items') as $index => $item) {
        $response->assertSee($item['title'])->assertSee($item['summary']);
        expect($dom->query('//*[@id="value-card-'.($index + 1).'-title"]')->length)->toBe(1);
    }
})->with(['id', 'en', 'ar']);

it('escapes all Values card content without interpreting arbitrary markup', function (): void {
    app()->setLocale('en');
    $values = app(LandingValuesPresenter::class)->present();
    expect(array_column($values['items'], 'display_index'))->toBe(['01', '02', '03', '04']);

    $values['items'][0]['title'] = '<script>bad()</script>';
    $values['items'][0]['text_parts'] = [['text' => '<img src=x onerror=bad()>', 'mark' => 'green']];
    $html = view('landing.values', compact('values'))->render();
    expect($html)->toContain('&lt;script&gt;bad()&lt;/script&gt;')
        ->toContain('&lt;img src=x onerror=bad()&gt;')
        ->not->toContain('<script>bad()</script>')
        ->not->toContain('<img src=x onerror=bad()>');
});
