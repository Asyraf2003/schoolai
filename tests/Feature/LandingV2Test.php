<?php

use App\Models\Article;
use App\Models\HeroSetting;
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

it('preserves database copy and active legacy section destinations', function (): void {
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);
    HeroSetting::query()->firstOrFail()->update([
        'title_en' => 'A <strong>school</strong> for everyone',
        'cta_label_en' => 'Explore our programs',
        'cta_url' => '#program',
    ]);

    $this->get(route('home'))
        ->assertSee('A &lt;strong&gt;school&lt;/strong&gt; for everyone', false)
        ->assertDontSee('<strong>school</strong>', false)
        ->assertSee('href="#program"', false)
        ->assertSee('href="#visi-misi"', false)
        ->assertSee('href="#kontak"', false)
        ->assertSee('id="program"', false)
        ->assertDontSee('<footer', false)
        ->assertSee(config('media.homepage_hero_video_url'), false)
        ->assertSee(config('media.static.hero_school'), false);
});

it('preserves the admissions campaign while registration is open', function (): void {
    PpdbSetting::query()->firstOrFail()->update(['is_active' => true]);

    $this->get(route('home'))
        ->assertSee('Begin a Meaningful Learning Journey')
        ->assertSee('href="/ppdb"', false)
        ->assertViewHas('hero', fn (array $hero): bool => $hero['slides'][0]['is_ppdb_campaign'] === true);
});

it('shows only published promoted stories after the opening hero', function (): void {
    $story = Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'landing-v2-story',
        'link_id' => '/artikel/landing-v2-story',
        'title_id' => 'Cerita Sekolah',
        'title_en' => 'Our school story',
        'description_en' => 'A published story.',
        'thumbnail_url' => config('media.static.hero_school'),
        'published_at' => now()->subDay(),
        'hero_position' => 1,
    ]);

    $this->get(route('home'))
        ->assertSee('Our school story')
        ->assertSee('data-next', false)
        ->assertViewHas('hero', fn (array $hero): bool => count($hero['slides']) === 2
            && $hero['slides'][1]['article_id'] === $story->id);

    $story->update(['article_status' => Article::STATUS_DRAFT]);

    $this->get(route('home'))
        ->assertDontSee('Our school story')
        ->assertDontSee('data-next', false);
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
