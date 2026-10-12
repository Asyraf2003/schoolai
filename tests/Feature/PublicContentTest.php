<?php

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\PpdbSetting;
use App\Models\User;
use Database\Seeders\StoryArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('loads database gallery media thumbnails and alt text in every locale', function (string $locale, string $direction): void {
    GalleryItem::create(['media_url' => config('media.public_url').'/gallery/photo.webp', 'alt_text' => 'Foto nyata']);
    GalleryItem::create(['type' => 'video', 'media_url' => config('media.public_url').'/gallery/video.mp4',
        'thumbnail_url' => config('media.static.school_life.labit'), 'alt_text' => 'Video nyata']);
    PpdbSetting::query()->updateOrCreate(['id' => 1], ['registration_url' => 'https://forms.gle/RealForm']);

    $this->withSession(['locale' => $locale])->get(route('galeri'))->assertOk()
        ->assertSee('dir="'.$direction.'"', false)->assertSee('Foto nyata')->assertSee('Video nyata')
        ->assertSee('poster="'.config('media.static.school_life.labit').'"', false)
        ->assertSee('controls playsinline preload="none"', false)->assertSee('https://forms.gle/RealForm', false)
        ->assertSee(route('artikel'), false)->assertSee(route('language.switch', 'ar'), false);
})->with([['id', 'ltr'], ['en', 'ltr'], ['ar', 'rtl']]);

it('paginates the gallery beyond the landing catalog and hides archived or unsafe media', function (): void {
    foreach (range(1, 25) as $index) {
        GalleryItem::create(['media_url' => config('media.public_url').'/gallery/photo-'.$index.'.webp', 'alt_text' => 'Photo '.$index]);
    }
    $archived = GalleryItem::create(['media_url' => config('media.public_url').'/gallery/archived.webp', 'alt_text' => 'Archived']);
    $archived->delete();
    GalleryItem::create(['media_url' => 'javascript:alert(1)', 'alt_text' => 'Unsafe']);

    $this->get(route('galeri'))->assertOk()->assertSee('photo-25.webp', false)
        ->assertDontSee('Archived')->assertDontSee('Unsafe')->assertDontSee('javascript:alert(1)', false)
        ->assertSee('rel="next"', false);
    $this->get(route('galeri', ['page' => 2]))->assertOk()->assertSee('photo-1.webp', false);
});

it('renders localized empty states without a broken PPDB link', function (string $locale): void {
    PpdbSetting::query()->updateOrCreate(['id' => 1], ['registration_url' => null]);
    foreach (['galeri' => 'gallery_empty', 'artikel' => 'articles_empty'] as $route => $key) {
        $this->withSession(['locale' => $locale])->get(route($route))->assertOk()
            ->assertSee(trans('public_content.'.$key, [], $locale))->assertDontSee('href="'.route('ppdb').'"', false);
    }
})->with(['id', 'en', 'ar']);

it('publishes four original Stories with their own video posters and archives earlier data safely', function (): void {
    $old = Article::create(['content_id' => '<p>Artikel lama tetap utuh</p>', 'cover_url' => config('media.static.school_life.aula')]);
    $this->seed(StoryArticleSeeder::class);

    expect(Article::count())->toBe(4)->and(Article::withTrashed()->count())->toBe(5);
    $this->assertSoftDeleted($old);
    expect($old->fresh()->content_id)->toBe('<p>Artikel lama tetap utuh</p>');
    $sources = [['labit', 'it'], ['mushalla', 'musola'], ['renang', 'renang'], ['taekwondo', 'tekwondo']];
    foreach (Article::latestPublished()->get() as $index => $article) {
        expect($article->cover_url)->toBe(config('media.public_url').'/gallery/media/'.$sources[$index][1].'-v1.mp4');
        expect($article->cover_thumbnail_url)->toBe(config('media.static.school_life.'.$sources[$index][0]));
        foreach (['id', 'en', 'ar'] as $locale) {
            $this->withSession(['locale' => $locale])->get($article->linkForLocale($locale))->assertOk()
                ->assertSee(trans('stories.features.'.$index.'.title', [], $locale))
                ->assertSee($article->contentForLocale($locale), false)
                ->assertSee('poster="'.$article->cover_thumbnail_url.'"', false);
        }
    }
    $this->get(route('home'))->assertSee('poster="'.config('media.static.school_life.renang').'"', false);
});

it('keeps later admin articles and edits when the initial Stories seeder is rerun', function (): void {
    $this->seed(StoryArticleSeeder::class);
    $first = Article::latestPublished()->first();
    $first->update(['content_id' => '<p>Edit admin</p>']);
    $first->delete();
    $new = Article::create(['title_id' => 'Cerita kelima', 'content_id' => '<p>Isi kelima</p>']);

    $this->seed(StoryArticleSeeder::class);

    expect(Article::count())->toBe(4)->and(Article::withTrashed()->count())->toBe(5);
    $this->assertSoftDeleted($first);
    expect($first->fresh()->content_id)->toBe('<p>Edit admin</p>');
    $this->assertModelExists($new);
});

it('always shows the latest four on landing and includes older active articles in the full listing', function (): void {
    $this->seed(StoryArticleSeeder::class);
    $oldest = Article::orderBy('id')->first();
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->post(route('admin.artikel.store'), ['title_id' => 'Artikel kelima', 'content_id' => '<p>Isi baru</p>'])
        ->assertRedirect(route('admin.artikel'))->assertSessionHasNoErrors();
    $new = Article::latestPublished()->first();

    $response = $this->withSession(['locale' => 'id'])->get(route('home'))->assertOk();
    expect($response->viewData('articles')->pluck('id')->all())->toBe(Article::latestPublished()->limit(4)->pluck('id')->all());
    $response->assertSee($new->linkForLocale('id'), false)->assertDontSee($oldest->linkForLocale('id'), false);
    $this->get(route('artikel'))->assertOk()->assertSee($new->linkForLocale('id'), false)
        ->assertSee($oldest->linkForLocale('id'), false)->assertSee($oldest->cover_thumbnail_url, false);
});

it('keeps all active articles reachable through pagination and excludes archived articles', function (): void {
    foreach (range(1, 13) as $index) {
        Article::create(['title_id' => 'Judul '.$index, 'content_id' => '<p>Isi</p>']);
    }
    $archived = Article::create(['title_id' => 'Judul arsip', 'content_id' => '<p>Isi</p>']);
    $archived->delete();

    $this->withSession(['locale' => 'id'])->get(route('artikel'))->assertOk()->assertSee('Judul 13')
        ->assertDontSee('Judul arsip')->assertSee('rel="next"', false);
    $this->get(route('artikel', ['page' => 2]))->assertOk()->assertSee('Judul 1')->assertSee('rel="prev"', false);
});
