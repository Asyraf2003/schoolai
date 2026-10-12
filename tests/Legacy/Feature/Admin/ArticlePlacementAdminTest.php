<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::query()->forceCreate([
        'name' => 'Admin Placement Test',
        'email' => 'admin-placement@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));
});

function placementAdminArticle(string $slug, mixed $publishedAt = null): Article
{
    return Article::query()->create([
        'article_source' => Article::SOURCE_EXTERNAL,
        'article_status' => Article::STATUS_PUBLISHED,
        'title_id' => str($slug)->headline()->toString(),
        'description_id' => 'Ringkasan '.$slug,
        'thumbnail_url' => (string) config('media.static.seo.home_og'),
        'link_id' => 'https://example.test/'.$slug,
        'published_at' => $publishedAt ?? now()->subMinute(),
    ]);
}

it('shows Homepage and Hero placement management on the Article index', function (): void {
    placementAdminArticle('artikel-index');

    $this->get(route('admin.artikel'))
        ->assertOk()
        ->assertSee('1 Head + 3 Rail')
        ->assertSee('Hero Spotlight')
        ->assertSee('Opening Video')
        ->assertSee('Tambah Artikel Eksternal')
        ->assertSee('+ Homepage')
        ->assertSee('+ Spotlight')
        ->assertDontSee('Tambah Link Medium');
});

it('pins at most four Homepage Articles and supports explicit reorder', function (): void {
    $articles = collect(range(1, 5))->map(
        fn (int $index): Article => placementAdminArticle('homepage-'.$index)
    );

    foreach ($articles->take(4) as $article) {
        $this->post(route('admin.artikel.homepage.pin', $article))
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    expect($articles[0]->fresh()->homepage_position)->toBe(1)
        ->and($articles[1]->fresh()->homepage_position)->toBe(2)
        ->and($articles[2]->fresh()->homepage_position)->toBe(3)
        ->and($articles[3]->fresh()->homepage_position)->toBe(4);

    $this->post(route('admin.artikel.homepage.pin', $articles[4]))
        ->assertSessionHasErrors('homepage_position');

    expect($articles[4]->fresh()->homepage_position)->toBeNull();

    $this->patch(route('admin.artikel.homepage.order'), [
        'article_ids' => [
            $articles[3]->getKey(),
            $articles[1]->getKey(),
            $articles[0]->getKey(),
            $articles[2]->getKey(),
        ],
    ])->assertRedirect()->assertSessionHasNoErrors();

    expect($articles[3]->fresh()->homepage_position)->toBe(1)
        ->and($articles[1]->fresh()->homepage_position)->toBe(2)
        ->and($articles[0]->fresh()->homepage_position)->toBe(3)
        ->and($articles[2]->fresh()->homepage_position)->toBe(4);
});

it('rejects Homepage placement before an Article is publicly visible', function (): void {
    $future = placementAdminArticle('future-homepage', now()->addHour());

    $this->post(route('admin.artikel.homepage.pin', $future))
        ->assertSessionHasErrors('homepage_position');

    expect($future->fresh()->homepage_position)->toBeNull();
});

it('releases Homepage and Hero placements when an Article is archived', function (): void {
    $first = placementAdminArticle('first-placement');
    $second = placementAdminArticle('second-placement');

    $this->post(route('admin.artikel.homepage.pin', $first))->assertSessionHasNoErrors();
    $this->post(route('admin.artikel.homepage.pin', $second))->assertSessionHasNoErrors();
    $this->post(route('admin.hero.articles.promote'), ['article_id' => $first->getKey()])->assertSessionHasNoErrors();
    $this->post(route('admin.hero.articles.promote'), ['article_id' => $second->getKey()])->assertSessionHasNoErrors();

    $this->delete(route('admin.artikel.destroy', $first))
        ->assertRedirect(route('admin.artikel'));

    $archived = Article::withTrashed()->findOrFail($first->getKey());

    expect($archived->trashed())->toBeTrue()
        ->and($archived->homepage_position)->toBeNull()
        ->and($archived->hero_position)->toBeNull()
        ->and($second->fresh()->homepage_position)->toBe(1)
        ->and($second->fresh()->hero_position)->toBe(1);
});
