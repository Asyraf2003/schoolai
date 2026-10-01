<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

trait ManagesArticles
{
    use ArchivesArticles;
    use ManagesHomepageArticlePlacement;

    public function index(): View
    {
        $articles = Article::query()
            ->withTrashed()
            ->latestForAdmin()
            ->paginate(20);

        $activeArticlesByIdentity = Article::query()
            ->latestForAdmin()
            ->get(['id', 'title_id', 'title_en', 'link_id', 'published_at'])
            ->groupBy(function (Article $article): string {
                return Article::normalizedLinkIdentity($article->link_id)
                    ?? '__invalid_active_'.$article->getKey();
            });

        $replacementCandidatesByArticle = $articles
            ->getCollection()
            ->filter(fn (Article $article): bool => $article->trashed())
            ->mapWithKeys(function (Article $article) use ($activeArticlesByIdentity): array {
                $identity = Article::normalizedLinkIdentity($article->link_id);

                return [
                    $article->getKey() => $identity
                        ? $activeArticlesByIdentity->get($identity, collect())
                        : collect(),
                ];
            });

        $homepagePinnedArticles = Article::query()
            ->whereNotNull('homepage_position')
            ->orderBy('homepage_position')
            ->orderBy('id')
            ->get();

        $homepagePinnedIds = $homepagePinnedArticles->modelKeys();
        $homepagePreviewArticles = Article::query()
            ->homepageFeatured()
            ->limit(Article::HOMEPAGE_FEATURED_LIMIT)
            ->get();
        $homepageAutoArticles = $homepagePreviewArticles
            ->reject(fn (Article $article): bool => in_array($article->getKey(), $homepagePinnedIds, true))
            ->values();

        $heroPinnedArticles = Article::query()
            ->whereNotNull('hero_position')
            ->orderBy('hero_position')
            ->orderBy('id')
            ->get();

        return view('admin.articles.index', [
            'adminPageKey' => 'artikel',
            'articles' => $articles,
            'replacementCandidatesByArticle' => $replacementCandidatesByArticle,
            'homepagePinnedArticles' => $homepagePinnedArticles,
            'homepageAutoArticles' => $homepageAutoArticles,
            'homepageLimit' => Article::HOMEPAGE_FEATURED_LIMIT,
            'heroPinnedArticles' => $heroPinnedArticles,
            'heroLimit' => Article::HERO_SPOTLIGHT_LIMIT,
        ]);
    }

    public function create(): View
    {
        return view('admin.articles.form', [
            'adminPageKey' => 'artikel',
            'mode' => 'create',
            'article' => new Article([
                'author' => Article::DEFAULT_AUTHOR,
                'published_at' => now(),
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        [$data, $newPath] = $this->applyThumbnail($request, $data);

        try {
            Article::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        return redirect()
            ->route('admin.artikel')
            ->with('success', 'Artikel berhasil ditambahkan. Atur placement-nya langsung dari daftar Artikel.');
    }

    public function show(Article $article): View
    {
        return view('admin.articles.show', [
            'adminPageKey' => 'artikel',
            'article' => $article,
        ]);
    }

    public function edit(Article $article): View|RedirectResponse
    {
        if ($article->isNative()) {
            return redirect()->route('admin.artikel.canvas.edit', $article);
        }

        return view('admin.articles.form', [
            'adminPageKey' => 'artikel',
            'mode' => 'edit',
            'article' => $article,
        ]);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $oldThumbnailUrl = $article->thumbnail_url;
        $data = $this->validatedData($request, $article);
        [$data, $newPath] = $this->applyThumbnail($request, $data, $article);

        try {
            $article->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        if ($newPath !== null) {
            $this->deleteStoredPublicFile($oldThumbnailUrl);
        }

        return redirect()
            ->route('admin.artikel')
            ->with('success', 'Artikel berhasil diperbarui.');
    }
}
