<?php

use App\View\Presenters\LandingValuesPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders only the localized Values heading after Program in a shared background', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));

    $response->assertViewIs('landing.index')
        ->assertSeeHtmlInOrder(['data-program-values', 'id="program"', 'data-program-values-seam', 'id="nilai"', 'data-values-heading'])
        ->assertViewHas('values', fn (array $values): bool => $values === app(LandingValuesPresenter::class)->present())
        ->assertSee(__('home.nilai_sekolah.heading'))
        ->assertDontSee('data-values-card', false)
        ->assertDontSee('data-values-spatial', false)
        ->assertDontSee('resources_old', false);

    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $dom = new DOMXPath($document);
    expect($dom->query('//*[@data-program-type-home]')->length)->toBe(1)
        ->and($dom->query('//*[@data-program-type-line]')->length)->toBe(20)
        ->and($dom->query('//section[@id="nilai"]//h2[@id="values-title"]')->length)->toBe(1)
        ->and($dom->query('//*[@data-values-heading-line][@aria-hidden="true"]')->length)->toBe(2)
        ->and($dom->query('//section[@id="nilai"]//img | //section[@id="nilai"]//canvas')->length)->toBe(0);
})->with(['id', 'en', 'ar']);

it('uses the existing Values title without rendering future content and escapes it', function (): void {
    app()->setLocale('en');
    $values = app(LandingValuesPresenter::class)->present();
    expect($values)->toBe([
        'heading' => __('home.nilai_sekolah.heading'),
        'heading_lines' => __('home.nilai_sekolah.heading_lines'),
    ]);
    $values = ['heading' => '<script>bad()</script>', 'heading_lines' => ['<script>bad()</script>', 'Title']];
    expect(view('landing.values', compact('values'))->render())
        ->toContain('&lt;script&gt;bad()&lt;/script&gt;')
        ->not->toContain('<script>bad()</script>');
});
