<?php

namespace App\View\Composers;

use Illuminate\Support\Collection;
use Illuminate\View\View;

final class AdminGallerySectionShowComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $candidates = $data['replacementCandidatesByArchivedId'] ?? collect();

        if (! $candidates instanceof Collection) {
            $candidates = collect($candidates);
        }

        $rows = collect($data['mediaItems'] ?? [])->map(fn ($item): array => [
            'item' => $item,
            'isDeleted' => $item->trashed(),
            'replacementCandidates' => $candidates->get($item->getKey(), collect()),
        ]);

        $view->with('mediaRows', $rows);
    }
}
