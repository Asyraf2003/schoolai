<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

trait ManagesArticles
{
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

    public function pinHomepage(Article $article): RedirectResponse
    {
        DB::transaction(function () use ($article): void {
            $locked = Article::query()->lockForUpdate()->findOrFail($article->getKey());
            $this->ensureArticleCanBePlaced($locked, 'homepage_position');

            if ($locked->homepage_position !== null) {
                return;
            }

            $pinned = Article::query()
                ->whereNotNull('homepage_position')
                ->lockForUpdate()
                ->get(['id', 'homepage_position']);

            if ($pinned->count() >= Article::HOMEPAGE_FEATURED_LIMIT) {
                throw ValidationException::withMessages([
                    'homepage_position' => 'Homepage Article sudah penuh. Lepas salah satu pin sebelum menambahkan artikel lain.',
                ]);
            }

            $locked->update([
                'homepage_position' => $this->firstAvailablePosition(
                    $pinned->pluck('homepage_position')->all(),
                    Article::HOMEPAGE_FEATURED_LIMIT,
                ),
            ]);
        });

        return back()->with('success', 'Artikel dipin ke Homepage Article.');
    }

    public function unpinHomepage(Article $article): RedirectResponse
    {
        DB::transaction(function () use ($article): void {
            Article::query()
                ->lockForUpdate()
                ->findOrFail($article->getKey())
                ->update(['homepage_position' => null]);

            $this->compactPositions('homepage_position', Article::HOMEPAGE_FEATURED_LIMIT);
        });

        return back()->with('success', 'Pin Homepage Article dilepas. Slot kosong akan diisi artikel terbaru secara otomatis.');
    }

    public function reorderHomepage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'article_ids' => ['present', 'array', 'max:'.Article::HOMEPAGE_FEATURED_LIMIT],
            'article_ids.*' => ['integer', 'distinct'],
        ]);

        $articleIds = array_map('intval', $data['article_ids']);

        DB::transaction(function () use ($articleIds): void {
            $current = Article::query()
                ->whereNotNull('homepage_position')
                ->orderBy('homepage_position')
                ->lockForUpdate()
                ->get(['id', 'homepage_position']);

            $this->ensureSamePlacementSet($current->modelKeys(), $articleIds, 'homepage_position');
            $this->rewritePositions($current, $articleIds, 'homepage_position');
        });

        return back()->with('success', 'Urutan Homepage Article diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        DB::transaction(function () use ($article): void {
            $article->update([
                'homepage_position' => null,
                'hero_position' => null,
            ]);
            $article->delete();

            $this->compactPositions('homepage_position', Article::HOMEPAGE_FEATURED_LIMIT);
            $this->compactPositions('hero_position', Article::HERO_SPOTLIGHT_LIMIT);
        });

        return redirect()
            ->route('admin.artikel')
            ->with('success', 'Artikel dipindahkan ke arsip dan dapat dipulihkan.');
    }

    public function restore(Request $request, int $article): RedirectResponse
    {
        $data = $request->validate([
            'replacement_article_id' => ['nullable', 'integer'],
        ]);

        $replacementArticleId = isset($data['replacement_article_id'])
            ? (int) $data['replacement_article_id']
            : null;

        if ($replacementArticleId === null) {
            Article::onlyTrashed()->findOrFail($article)->restore();

            return redirect()
                ->route('admin.artikel')
                ->with('success', 'Artikel berhasil dipulihkan.');
        }

        DB::transaction(function () use ($article, $replacementArticleId): void {
            $trashedArticle = Article::onlyTrashed()
                ->lockForUpdate()
                ->findOrFail($article);

            $replacementArticle = Article::query()
                ->lockForUpdate()
                ->findOrFail($replacementArticleId);

            $trashedIdentity = Article::normalizedLinkIdentity($trashedArticle->link_id);
            $replacementIdentity = Article::normalizedLinkIdentity($replacementArticle->link_id);

            if ($trashedIdentity === null || $trashedIdentity !== $replacementIdentity) {
                throw ValidationException::withMessages([
                    'replacement_article_id' => 'Artikel pengganti harus merupakan artikel aktif dengan link Indonesia yang identik.',
                ]);
            }

            $replacementArticle->update([
                'homepage_position' => null,
                'hero_position' => null,
            ]);
            $replacementArticle->delete();
            $trashedArticle->restore();

            $this->compactPositions('homepage_position', Article::HOMEPAGE_FEATURED_LIMIT);
            $this->compactPositions('hero_position', Article::HERO_SPOTLIGHT_LIMIT);
        });

        return redirect()
            ->route('admin.artikel')
            ->with('success', 'Artikel lama dipulihkan dan artikel aktif pengganti dipindahkan ke arsip.');
    }

    private function ensureArticleCanBePlaced(Article $article, string $field): void
    {
        if ($article->isPubliclyVisibleNow()) {
            return;
        }

        throw ValidationException::withMessages([
            $field => 'Hanya artikel yang sudah terbit dan dapat dibaca publik yang dapat dipin.',
        ]);
    }

    /** @param array<int, mixed> $used */
    private function firstAvailablePosition(array $used, int $limit): int
    {
        $used = array_map('intval', $used);

        for ($position = 1; $position <= $limit; $position++) {
            if (! in_array($position, $used, true)) {
                return $position;
            }
        }

        throw ValidationException::withMessages([
            'position' => 'Tidak ada slot placement yang tersedia.',
        ]);
    }

    /**
     * @param array<int, int|string> $currentIds
     * @param array<int, int> $submittedIds
     */
    private function ensureSamePlacementSet(array $currentIds, array $submittedIds, string $field): void
    {
        $currentIds = array_map('intval', $currentIds);
        sort($currentIds);
        $sortedSubmitted = $submittedIds;
        sort($sortedSubmitted);

        if ($currentIds === $sortedSubmitted) {
            return;
        }

        throw ValidationException::withMessages([
            $field => 'Daftar artikel berubah saat urutan disimpan. Muat ulang halaman lalu coba lagi.',
        ]);
    }

    private function compactPositions(string $field, int $limit): void
    {
        $articles = Article::query()
            ->whereNotNull($field)
            ->orderBy($field)
            ->orderBy('id')
            ->lockForUpdate()
            ->get(['id', $field]);

        $keptIds = array_slice($articles->modelKeys(), 0, $limit);
        $this->rewritePositions($articles, $keptIds, $field);
    }

    /**
     * @param \Illuminate\Support\Collection<int, Article> $articles
     * @param array<int, int|string> $articleIds
     */
    private function rewritePositions($articles, array $articleIds, string $field): void
    {
        if ($articles->isEmpty()) {
            return;
        }

        Article::query()
            ->whereIn('id', $articles->modelKeys())
            ->update([$field => null]);

        foreach (array_values($articleIds) as $index => $articleId) {
            Article::query()
                ->whereKey((int) $articleId)
                ->update([$field => $index + 1]);
        }
    }
}
