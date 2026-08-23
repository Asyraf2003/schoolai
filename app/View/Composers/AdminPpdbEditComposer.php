<?php

namespace App\View\Composers;

use App\Models\PpdbShowcaseItem;
use App\View\Presenters\AdminLanguageTabsPresenter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class AdminPpdbEditComposer
{
    public function __construct(private readonly AdminLanguageTabsPresenter $languageTabs) {}

    public function compose(View $view): void
    {
        $data = $view->getData();
        $activeItems = $this->collection($data['showcaseItems'] ?? []);
        $showcaseItemForm = $data['showcaseItemForm'] ?? null;
        $showcaseFormMode = $data['showcaseFormMode'] ?? 'create';
        $view->with([
            'showcaseItems' => $activeItems,
            'showcaseAudienceGroups' => collect($data['audienceOptions'] ?? [])->map(
                fn (string $label, string $audience): array => [
                    'audience' => $audience,
                    'audienceLabel' => $label,
                    'items' => $activeItems->where('audience', $audience)->values(),
                ]
            ),
            'showcaseFormMode' => $showcaseFormMode,
            'showcaseItemForm' => $showcaseItemForm,
            'showcaseFormIsEdit' => $showcaseFormMode === 'edit' && (bool) $showcaseItemForm?->exists,
            'showcaseAudience' => old('audience', $showcaseItemForm->audience ?? 'parents'),
            'showcaseMediaType' => old('media_type', $showcaseItemForm->media_type ?? 'photo'),
            'showcaseLanguageCompletion' => $this->languageTabs->completion([
                'id' => [old('title_id', $showcaseItemForm->title_id ?? ''), old('description_id', $showcaseItemForm->description_id ?? '')],
                'en' => [old('title_en', $showcaseItemForm->title_en ?? ''), old('description_en', $showcaseItemForm->description_en ?? '')],
                'ar' => [old('title_ar', $showcaseItemForm->title_ar ?? ''), old('description_ar', $showcaseItemForm->description_ar ?? '')],
            ]),
            'showcaseActiveLanguage' => $this->languageTabs->activeLanguage(
                $data['errors'] ?? null,
                ['title_ar', 'description_ar'],
                ['title_en', 'description_en'],
            ),
        ]);

        if (
            ! Schema::hasTable('ppdb_showcase_items') ||
            ! Schema::hasColumn('ppdb_showcase_items', 'deleted_at')
        ) {
            $view->with([
                'archivedShowcaseItems' => collect(),
                'showcaseReplacementCandidatesByArchivedId' => collect(),
                'archivedShowcaseRows' => collect(),
            ]);

            return;
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
            'archivedShowcaseRows' => $archivedItems->map(fn (PpdbShowcaseItem $item): array => [
                'item' => $item,
                'replacementCandidates' => $replacementCandidates->get($item->getKey(), collect()),
            ]),
        ]);
    }

    private function collection(mixed $value): Collection
    {
        return $value instanceof Collection ? $value : collect($value);
    }
}
