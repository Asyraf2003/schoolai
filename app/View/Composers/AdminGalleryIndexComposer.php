<?php

namespace App\View\Composers;

use App\Models\GalleryPageSection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class AdminGalleryIndexComposer
{
    public function compose(View $view): void
    {
        if (
            ! Schema::hasTable('gallery_page_sections') ||
            ! Schema::hasColumn('gallery_page_sections', 'deleted_at')
        ) {
            $view->with([
                'archivedPageSections' => collect(),
                'sectionReplacementCandidatesByArchivedId' => collect(),
            ]);

            return;
        }

        $viewData = $view->getData();
        $activeSections = $viewData['pageSections'] ?? collect();

        if (! $activeSections instanceof Collection) {
            $activeSections = collect($activeSections);
        }

        $archivedSections = GalleryPageSection::onlyTrashed()
            ->withCount(['mediaItemsWithTrashed as media_items_count'])
            ->orderByDesc('deleted_at')
            ->orderByDesc('id')
            ->get();

        $activeByIdentity = $activeSections
            ->filter(fn (GalleryPageSection $section): bool => $section->replacementIdentity() !== null)
            ->groupBy(fn (GalleryPageSection $section): string => (string) $section->replacementIdentity());

        $replacementCandidates = $archivedSections->mapWithKeys(
            function (GalleryPageSection $archivedSection) use ($activeByIdentity): array {
                $identity = $archivedSection->replacementIdentity();

                return [
                    $archivedSection->getKey() => $identity === null
                        ? new Collection()
                        : $activeByIdentity->get($identity, new Collection())->values(),
                ];
            }
        );

        $view->with([
            'archivedPageSections' => $archivedSections,
            'sectionReplacementCandidatesByArchivedId' => $replacementCandidates,
        ]);
    }
}
