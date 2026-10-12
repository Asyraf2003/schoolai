<?php

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the V2 Stories heading and four latest database articles in section order', function (string $locale): void {
    foreach (range(1, 4) as $index) {
        Article::create(['title_id' => 'Cerita '.$index, 'title_en' => 'Story '.$index, 'title_ar' => 'قصة '.$index,
            'description_id' => 'Deskripsi sekolah '.$index, 'content_id' => '<p>Isi</p>',
            'cover_url' => config('media.static.school_life.labit')]);
    }
    $response = $this->withSession(['locale' => $locale])->get(route('home'));
    $response->assertOk()->assertSeeHtmlInOrder(['id="testimoni"', 'id="artikel"'])->assertSee(__('stories.title'));
    $document = new DOMDocument;
    @$document->loadHTML($response->getContent());
    $xpath = new DOMXPath($document);
    expect($xpath->query('//*[@data-stories-heading-line][@aria-hidden="true"]')->length)->toBe(2)
        ->and($xpath->query('//*[@data-stories-feature]')->length)->toBe(4)
        ->and($xpath->query('//*[@data-stories-feature]//img[@data-stories-poster]')->length)->toBe(4)
        ->and($xpath->query('//*[@data-stories-feature]//video')->length)->toBe(0)
        ->and($xpath->query('//*[@data-stories-feature]//h3/a')->length)->toBe(4);
})->with(['id', 'en', 'ar']);

it('updates Stories from CRUD records and removes deleted articles without dummy fallback', function (): void {
    $article = Article::create(['title_id' => 'Artikel dari database', 'content_id' => '<p>Isi</p>']);
    $this->withSession(['locale' => 'id'])->get(route('home'))->assertSee('Artikel dari database')->assertSee($article->linkForLocale('id'), false);
    $article->update(['title_id' => 'Judul berubah']);
    $this->get(route('home'))->assertSee('Judul berubah')->assertDontSee('Artikel dari database');
    $article->delete();
    $response = $this->get(route('home'))->assertDontSee('Judul berubah');
    expect($response->viewData('articles'))->toHaveCount(0);
});
