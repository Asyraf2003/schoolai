<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Rules\SafeImageUpload;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
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
            ->latestPublished()
            ->get(['id', 'title_id', 'title_en', 'link_id', 'published_at'])
            ->groupBy(function (Article $article): string {
                return Article::normalizedLinkIdentity($article->link_id)
                    ?? '__invalid_active_' . $article->getKey();
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

        return view('admin.articles.index', [
            'adminPageKey' => 'artikel',
            'articles' => $articles,
            'replacementCandidatesByArticle' => $replacementCandidatesByArticle,
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
            $article = Article::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        return redirect()
            ->route('admin.artikel.show', $article)
            ->with('success', 'Artikel berhasil ditambahkan.');
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
        [$data, $newPath] = $this->applyThumbnail($request, $data);

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
            ->route('admin.artikel.show', $article)
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

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

            $replacementArticle->delete();
            $trashedArticle->restore();
        });

        return redirect()
            ->route('admin.artikel')
            ->with('success', 'Artikel lama dipulihkan dan artikel aktif pengganti dipindahkan ke arsip.');
    }
}
