<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait ManagesHomepageArticlePlacement
{
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
     * @param  array<int, int|string>  $currentIds
     * @param  array<int, int>  $submittedIds
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
     * @param  Collection<int, Article>  $articles
     * @param  array<int, int|string>  $articleIds
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
