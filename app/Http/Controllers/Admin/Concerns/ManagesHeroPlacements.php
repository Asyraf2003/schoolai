<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait ManagesHeroPlacements
{
    public function promote(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'article_id' => ['required', 'integer', 'exists:articles,id'],
        ]);
        $article = Article::query()->findOrFail((int) $data['article_id']);

        if (! $article->isPubliclyVisibleNow()) {
            throw ValidationException::withMessages([
                'article' => 'Hanya artikel yang sudah terbit dan dapat dibaca publik yang dapat dijadikan Hero Spotlight.',
            ]);
        }

        DB::transaction(function () use ($article): void {
            $locked = Article::query()->lockForUpdate()->findOrFail($article->getKey());

            if ($locked->hero_position !== null) {
                return;
            }

            $pinned = Article::query()
                ->whereNotNull('hero_position')
                ->orderBy('hero_position')
                ->lockForUpdate()
                ->get(['id', 'hero_position']);

            if ($pinned->count() >= Article::HERO_SPOTLIGHT_LIMIT) {
                throw ValidationException::withMessages([
                    'article' => 'Hero Spotlight sudah penuh. Maksimal '.Article::HERO_SPOTLIGHT_LIMIT.' artikel selain Opening video.',
                ]);
            }

            $used = $pinned->pluck('hero_position')->map(fn ($position): int => (int) $position)->all();
            $nextPosition = null;

            for ($position = 1; $position <= Article::HERO_SPOTLIGHT_LIMIT; $position++) {
                if (! in_array($position, $used, true)) {
                    $nextPosition = $position;
                    break;
                }
            }

            if ($nextPosition === null) {
                throw ValidationException::withMessages([
                    'article' => 'Tidak ada slot Hero Spotlight yang tersedia.',
                ]);
            }

            $locked->update(['hero_position' => $nextPosition]);
        });

        return back()->with('success', 'Artikel ditambahkan ke Hero Spotlight.');
    }

    public function unpromote(Article $article): RedirectResponse
    {
        DB::transaction(function () use ($article): void {
            Article::query()->lockForUpdate()->findOrFail($article->getKey())
                ->update(['hero_position' => null]);
            $this->compactHeroPositions();
        });

        return back()->with('success', 'Artikel dilepas dari Hero Spotlight tanpa mengubah kontennya.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'article_ids' => ['present', 'array', 'max:'.Article::HERO_SPOTLIGHT_LIMIT],
            'article_ids.*' => ['integer', 'distinct'],
        ]);
        $articleIds = array_map('intval', $data['article_ids']);

        DB::transaction(function () use ($articleIds): void {
            $current = Article::query()
                ->whereNotNull('hero_position')
                ->orderBy('hero_position')
                ->lockForUpdate()
                ->get(['id', 'hero_position']);

            $currentIds = array_map('intval', $current->modelKeys());
            $submitted = $articleIds;
            sort($currentIds);
            sort($submitted);

            if ($currentIds !== $submitted) {
                throw ValidationException::withMessages([
                    'article_ids' => 'Daftar Hero Spotlight berubah saat urutan disimpan. Muat ulang halaman lalu coba lagi.',
                ]);
            }

            $this->rewriteHeroPositions($current, $articleIds);
        });

        return back()->with('success', 'Urutan Hero Spotlight diperbarui.');
    }

    public function moveUp(Article $article): RedirectResponse
    {
        $this->move($article, true);

        return back()->with('success', 'Urutan artikel Hero diperbarui.');
    }

    public function moveDown(Article $article): RedirectResponse
    {
        $this->move($article, false);

        return back()->with('success', 'Urutan artikel Hero diperbarui.');
    }

    private function move(Article $article, bool $up): void
    {
        DB::transaction(function () use ($article, $up): void {
            $current = Article::query()->lockForUpdate()->findOrFail($article->getKey());

            if ($current->hero_position === null) {
                return;
            }

            $other = Article::query()
                ->whereNotNull('hero_position')
                ->where('hero_position', $up ? '<' : '>', $current->hero_position)
                ->orderBy('hero_position', $up ? 'desc' : 'asc')
                ->lockForUpdate()
                ->first();

            if (! $other) {
                return;
            }

            $currentPosition = $current->hero_position;
            $current->update(['hero_position' => null]);
            $otherPosition = $other->hero_position;
            $other->update(['hero_position' => $currentPosition]);
            $current->update(['hero_position' => $otherPosition]);
        });
    }

    private function compactHeroPositions(): void
    {
        $articles = Article::query()
            ->whereNotNull('hero_position')
            ->orderBy('hero_position')
            ->orderBy('id')
            ->lockForUpdate()
            ->get(['id', 'hero_position']);

        $this->rewriteHeroPositions($articles, $articles->modelKeys());
    }

    private function rewriteHeroPositions($articles, array $articleIds): void
    {
        if ($articles->isEmpty()) {
            return;
        }

        Article::query()
            ->whereIn('id', $articles->modelKeys())
            ->update(['hero_position' => null]);

        foreach (array_values($articleIds) as $index => $articleId) {
            if ($index >= Article::HERO_SPOTLIGHT_LIMIT) {
                break;
            }

            Article::query()
                ->whereKey((int) $articleId)
                ->update(['hero_position' => $index + 1]);
        }
    }
}
