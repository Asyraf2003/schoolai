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
        $data = $view->getData();
        $activeSections = $this->collection($data['pageSections'] ?? []);
        $archivedItems = $this->collection($data['archivedItems'] ?? []);
        $galleryCandidates = $this->collection($data['replacementCandidatesByArchivedId'] ?? []);
        $view->with([
            'page' => __('admin.gallery'),
            'activePageSections' => $activeSections,
            'homepageLimit' => $data['limits']['max_items'] ?? 6,
            'homepageCount' => $this->collection($data['activeItems'] ?? [])
                ->where('show_on_homepage', true)
                ->count(),
            'archivedGalleryItemRows' => $archivedItems->map(fn ($item): array => [
                'item' => $item,
                'replacementCandidates' => $galleryCandidates->get($item->getKey(), collect()),
            ]),
        ]);

        if (
            ! Schema::hasTable('gallery_page_sections') ||
            ! Schema::hasColumn('gallery_page_sections', 'deleted_at')
        ) {
            $view->with([
                'archivedPageSections' => collect(),
                'sectionReplacementCandidatesByArchivedId' => collect(),
                'archivedPageSectionRows' => collect(),
            ]);

            return;
        }

        $archivedSections = GalleryPageSection::onlyTrashed()
            ->withCount('items')
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
                        ? new Collection
                        : $activeByIdentity->get($identity, new Collection)->values(),
                ];
            }
        );

        $view->with([
            'archivedPageSections' => $archivedSections,
            'sectionReplacementCandidatesByArchivedId' => $replacementCandidates,
            'archivedPageSectionRows' => $archivedSections->map(fn (GalleryPageSection $section): array => [
                'section' => $section,
                'replacementCandidates' => $replacementCandidates->get($section->getKey(), collect()),
            ]),
        ]);
    }

    private function collection(mixed $value): Collection
    {
        return $value instanceof Collection ? $value : collect($value);
    }
}
