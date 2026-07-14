<?php

namespace App\View\Composers;

use App\Models\PpdbShowcaseItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class AdminPpdbEditComposer
{
    public function compose(View $view): void
    {
        if (
            ! Schema::hasTable('ppdb_showcase_items') ||
            ! Schema::hasColumn('ppdb_showcase_items', 'deleted_at')
        ) {
            $view->with([
                'archivedShowcaseItems' => collect(),
                'showcaseReplacementCandidatesByArchivedId' => collect(),
            ]);

            return;
        }

        $activeItems = $view->getData()['showcaseItems'] ?? collect();

        if (! $activeItems instanceof Collection) {
            $activeItems = collect($activeItems);
        }

        $archivedItems = PpdbShowcaseItem::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->orderByDesc('id')
            ->get();

        $activeByIdentity = $activeItems
            ->filter(fn (PpdbShowcaseItem $item): bool => $item->replacementIdentity() !== null)
            ->groupBy(fn (PpdbShowcaseItem $item): string => (string) $item->replacementIdentity());

        $replacementCandidates = $archivedItems->mapWithKeys(
            function (PpdbShowcaseItem $archivedItem) use ($activeByIdentity): array {
                $identity = $archivedItem->replacementIdentity();

                return [
                    $archivedItem->getKey() => $identity === null
                        ? collect()
                        : $activeByIdentity->get($identity, collect())->values(),
                ];
            }
        );

        $view->with([
            'archivedShowcaseItems' => $archivedItems,
            'showcaseReplacementCandidatesByArchivedId' => $replacementCandidates,
        ]);
    }
}
