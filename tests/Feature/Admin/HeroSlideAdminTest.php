<?php

use App\Models\Article;
use App\Models\HeroSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::query()->forceCreate([
        'name' => 'Admin Hero Test',
        'email' => 'admin-hero@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));
});

function heroAdminArticle(string $slug, string $status = Article::STATUS_PUBLISHED): Article
{
    return Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => $status,
        'slug' => $slug,
        'title_id' => str($slug)->headline()->toString(),
        'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
        'link_id' => url('/artikel/'.$slug),
        'published_at' => now(),
    ]);
}

it('edits only Opening copy while Article Spotlight management lives on the Article index', function (): void {
    $this->get(route('admin.hero'))
        ->assertOk()
        ->assertSee('Opening video selalu tampil pertama')
        ->assertSee('Kelola Article Spotlight')
        ->assertDontSee('name="article_id"', false)
        ->assertDontSee('enctype="multipart/form-data"', false);

    $this->put(route('admin.hero.update'), [
        'eyebrow_id' => 'Sekolah Islam',
        'title_id' => 'Opening Baru',
        'description_id' => 'Copy pembuka baru.',
        'cta_url' => '',
    ])->assertRedirect()->assertSessionHasNoErrors();

    $setting = HeroSetting::query()->firstOrFail();

    expect($setting->title_id)->toBe('Opening Baru')
        ->and($setting->description_id)->toBe('Copy pembuka baru.')
        ->and($setting->cta_url)->toBeNull()
        ->and($setting->cta_label_id)->toBeNull();
});

it('promotes reorders and removes Hero Spotlight Articles without copying their content', function (): void {
    $first = heroAdminArticle('artikel-pertama');
    $second = heroAdminArticle('artikel-kedua');
    $third = heroAdminArticle('artikel-ketiga');

    foreach ([$first, $second, $third] as $article) {
        $this->post(route('admin.hero.articles.promote'), ['article_id' => $article->getKey()])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    expect($first->fresh()->hero_position)->toBe(1)
        ->and($second->fresh()->hero_position)->toBe(2)
        ->and($third->fresh()->hero_position)->toBe(3);

    $this->patch(route('admin.hero.articles.order'), [
        'article_ids' => [$third->getKey(), $first->getKey(), $second->getKey()],
    ])->assertRedirect()->assertSessionHasNoErrors();

    expect($third->fresh()->hero_position)->toBe(1)
        ->and($first->fresh()->hero_position)->toBe(2)
        ->and($second->fresh()->hero_position)->toBe(3);

    $this->delete(route('admin.hero.articles.unpromote', $first))->assertRedirect();

    expect($first->fresh()->hero_position)->toBeNull()
        ->and($third->fresh()->hero_position)->toBe(1)
        ->and($second->fresh()->hero_position)->toBe(2);
});

it('limits Hero Spotlight to three Articles besides the fixed Opening video', function (): void {
    $articles = collect(range(1, 4))->map(
        fn (int $index): Article => heroAdminArticle('spotlight-'.$index)
    );

    foreach ($articles->take(3) as $article) {
        $this->post(route('admin.hero.articles.promote'), ['article_id' => $article->getKey()])
            ->assertSessionHasNoErrors();
    }

    $fourth = $articles->last();

    $this->post(route('admin.hero.articles.promote'), ['article_id' => $fourth->getKey()])
        ->assertSessionHasErrors('article');

    expect($fourth->fresh()->hero_position)->toBeNull()
        ->and(Article::query()->whereNotNull('hero_position')->count())->toBe(3);
});

it('rejects promotion of an unpublished Article', function (): void {
    $draft = heroAdminArticle('artikel-draft', Article::STATUS_DRAFT);

    $this->post(route('admin.hero.articles.promote'), ['article_id' => $draft->getKey()])
        ->assertSessionHasErrors('article');

    expect($draft->fresh()->hero_position)->toBeNull();
});
