<?php

use App\Models\Article;
use App\Models\PpdbSetting;
use App\Models\User;
use Database\Seeders\StoryArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders Coming Soon with the landing header on gallery and article pages in every locale', function (string $locale, string $direction): void {
    PpdbSetting::query()->updateOrCreate(['id' => 1], ['registration_url' => 'https://forms.gle/RealForm']);

    foreach (['galeri', 'artikel'] as $route) {
        $this->withSession(['locale' => $locale])->get(route($route))->assertOk()
            ->assertSee('dir="'.$direction.'"', false)
            ->assertSee('data-coming-soon="'.$route.'"', false)
            ->assertSee('data-header', false)
            ->assertSee('coming-soon-v1.webp', false)
            ->assertSee(route('artikel'), false)
            ->assertSee(route('galeri'), false)
            ->assertSee(route('language.switch', 'ar'), false);
    }
})->with([['id', 'ltr'], ['en', 'ltr'], ['ar', 'rtl']]);

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
    $response->assertSee($new->title_id)->assertSee(route('artikel'), false)->assertDontSee($oldest->title_id);
    $this->get(route('artikel'))->assertOk()->assertSee('data-coming-soon="artikel"', false);
});
