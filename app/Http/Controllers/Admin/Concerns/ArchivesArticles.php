<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait ArchivesArticles
{
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
}
