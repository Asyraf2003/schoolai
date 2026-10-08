<?php

use App\View\Composers\HomeProgramComposer;
use App\View\Presenters\LandingProgramPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('preserves the legacy selected data and media without using its frontend runtime', function (): void {
    app()->setLocale('en');
    $legacy = view()->file(base_path('resources_old/views/home/sections/featured-programs.blade.php'));
    app(HomeProgramComposer::class)->compose($legacy);
    $program = app(LandingProgramPresenter::class)->present();
    $oldItems = $legacy->getData()['programItems']->all();

    foreach (['code', 'eyebrow', 'title', 'summary', 'description', 'media', 'title_scale'] as $field) {
        expect(array_column($program['items'], $field))->toBe(array_column($oldItems, $field));
    }
    if ($path = getenv('PROGRAM_LEGACY_REFERENCE_HTML')) {
        file_put_contents($path, '<!doctype html><html lang="en"><head><meta charset="utf-8">'
            .'<link rel="stylesheet" href="http://127.0.0.1:8000/build/'.collect(json_decode(file_get_contents(public_path('build/manifest.json')), true))->get('resources/css/index.css')['file'].'">'
            .'<link rel="stylesheet" href="file://'.base_path('resources_old/css/pages/welcome/program-showcase-desktop.css').'">'
            .'<link rel="stylesheet" href="file://'.base_path('resources_old/css/surfaces/home/values/story-kinetic.css').'">'
            .'</head><body><div class="program-values-world">'.$legacy->render().'</div></body></html>');
    }
});

it('renders the eight featured programs after Mission in the saved language', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('home'));

    $response->assertViewIs('landing.index')
        ->assertSeeHtmlInOrder(['data-about-story="mission"', 'id="program"', 'data-program-story-end', 'data-program-values-seam', 'data-values-entry-anchor'])
        ->assertViewHas('program', fn (array $program): bool => array_column($program['items'], 'code') === ['TQ', 'KH', 'LC', 'SJ', 'TS', 'IT', 'FD', 'SC'])
        ->assertSee(__('home_program.section_label'))
        ->assertSee(__('home_program.back'))
        ->assertDontSee('data-program-handoff', false)
        ->assertSee('data-values-heading', false)
        ->assertSee('data-values-card', false)
        ->assertDontSee('resources_old', false);
})->with(['id', 'en', 'ar']);

it('keeps copy and media localized with accessible disclosures and intent-only detail media', function (string $locale): void {
    app()->setLocale($locale);
    $program = app(LandingProgramPresenter::class)->present();
    $document = new DOMDocument;
    @$document->loadHTML(view('landing.program', compact('program'))->render());
    $dom = new DOMXPath($document);

    expect($dom->query('//details[@data-program-card]/summary[@data-program-open]')->length)->toBe(8);
    expect($dom->query('//details/article[@data-program-detail]')->length)->toBe(8);
    expect($dom->query('//summary//img[@loading="lazy"][@width][@height]')->length)->toBe(8);
    expect($dom->query('//*[@data-program-image][@src]')->length)->toBe(0);
    expect($dom->query('//dialog[@data-program-dialog]')->length)->toBe(1);
    expect($dom->query('//*[@data-program-type-line]')->length)->toBe(20);
    expect($dom->query('//*[@data-program-type-home]//*[@data-program-type-viewport][@aria-hidden="true"]')->length)->toBe(1);
    expect($dom->query('//dialog//*[@data-program-type-line]')->length)->toBe(0);
    expect($dom->query('//button[@data-program-back][@hidden]/following-sibling::h3')->length)->toBe(8);
    $original = array_column(__('home_program.items'), null, 'code');
    foreach ($program['items'] as $item) {
        expect($item['title'])->toBe($original[$item['code']]['title']);
        expect($item['summary'])->toBe($original[$item['code']]['summary']);
        expect($item['description'])->toBe($original[$item['code']]['description']);
        expect($item['media']['url'])->toStartWith('https://media.almustaqbal.sch.id/site/school-life/');
    }
    expect($program['heading_lines'])->toBe(__('home_program.heading_lines'));
})->with(['id', 'en', 'ar']);

it('sizes titles from localized content length rather than identity and escapes visible copy', function (): void {
    app()->setLocale('en');
    __('home_program');
    app('translator')->addLines(['home_program.items' => [
        ['code' => 'TQ', 'title' => 'Tiny'],
        ['code' => 'KH', 'title' => 'A medium title'],
        ['code' => 'LC', 'title' => 'A substantially longer program title'],
    ]], 'en');

    $program = app(LandingProgramPresenter::class)->present();

    expect(array_column($program['items'], 'title_scale'))->toBe(['short', 'medium', 'long']);
    $program = app(LandingProgramPresenter::class)->present();
    $program['items'][0] = array_replace($program['items'][0], [
        'title' => '<script>alert(1)</script>', 'eyebrow' => 'Eyebrow',
        'summary' => 'Summary', 'description' => 'Description',
    ]);
    $program['items'] = [$program['items'][0]];
    expect(view('landing.program', compact('program'))->render())
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;')
        ->not->toContain('<script>alert(1)</script>');
});

it('does not query a Program or Values database domain', function (): void {
    $queries = [];
    DB::listen(function ($query) use (&$queries): void {
        $queries[] = $query->sql;
    });

    $this->get(route('home'))->assertViewHas('program');

    expect(implode(' ', $queries))->not->toContain('programs')->not->toContain('values');
});
