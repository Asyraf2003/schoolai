<?php

namespace App\View\Composers;

use Illuminate\Support\Collection;
use Illuminate\View\View;

final class AdminArticleIndexComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $articles = $data['articles'];
        $candidates = $data['replacementCandidatesByArticle'] ?? collect();

        if (! $candidates instanceof Collection) {
            $candidates = collect($candidates);
        }

        $rows = $articles->getCollection()->map(function ($article) use ($candidates): array {
            $isDeleted = $article->trashed();
            $statusLabel = $article->statusLabel();

            return [
                'article' => $article,
                'isDeleted' => $isDeleted,
                'canPlace' => ! $isDeleted && $article->isPubliclyVisibleNow(),
                'replacementCandidates' => $candidates->get($article->getKey(), collect()),
                'statusLabel' => $statusLabel,
                'statusClass' => $isDeleted || $article->isDraft() || $statusLabel === 'Terjadwal'
                    ? 'is-deleted'
                    : 'is-active',
            ];
        });

        $view->with('articleRows', $rows);
    }
}
