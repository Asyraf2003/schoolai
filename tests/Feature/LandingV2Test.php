<?php

use App\Models\Article;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('delivers the saved Arabic locale with direction foundation', function (): void {
    $this->withSession(['locale' => 'ar'])
        ->get(route('home'))
        ->assertOk()
        ->assertViewIs('landing.index')
        ->assertSee('<html lang="ar" dir="rtl">', false)
        ->assertSessionHas('locale', 'ar')
        ->assertDontSee('resources_old', false);
});

it('preserves one opening video and the V2 sections while articles remain separate records', function (): void {
    Article::create(['title_id' => 'Cerita Sekolah', 'content_id' => '<p>Isi</p>']);
    PpdbSetting::query()->firstOrFail()->update(['registration_url' => null]);
    $response = $this->get(route('home'))->assertOk()->assertSee('id="kontak"', false)
        ->assertSee('id="program"', false)->assertSee(config('media.homepage_hero_video_url'), false)
        ->assertSee(config('media.static.hero_school'), false)->assertDontSee('data-next', false)->assertDontSee('data-previous', false);
    expect($response->viewData('hero')['slides'])->toHaveCount(1);
    expect($response->viewData('hero')['slides'][0]['is_opening'])->toBeTrue();
});

it('preserves the admissions campaign for a configured Google Form', function (): void {
    PpdbSetting::query()->firstOrFail()->update(['registration_url' => 'https://forms.gle/V2Form']);
    $this->get(route('home'))->assertSee('Begin a Meaningful Learning Journey')
        ->assertSee('href="https://forms.gle/V2Form"', false)
        ->assertViewHas('hero', fn (array $hero): bool => $hero['slides'][0]['is_ppdb_campaign'] === true);
});

it('switches the landing language through the existing route', function (string $locale, string $direction): void {
    $this->from(route('home'))->post(route('language.switch', $locale))
        ->assertRedirect(route('home'))
        ->assertSessionHas('locale', $locale);

    $this->get(route('home'))
        ->assertSee('<html lang="'.$locale.'" dir="'.$direction.'">', false)
        ->assertSee('action="'.route('language.switch', 'en').'"', false)
        ->assertSee('action="'.route('language.switch', 'id').'"', false)
        ->assertSee('action="'.route('language.switch', 'ar').'"', false)
        ->assertDontSee('data-playback', false);
})->with([['en', 'ltr'], ['id', 'ltr'], ['ar', 'rtl']]);
