<?php

namespace App\View\Composers;

use App\View\Presenters\AdminLanguageTabsPresenter;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class AdminSiteStatisticsEditComposer
{
    public function __construct(private readonly AdminLanguageTabsPresenter $languageTabs) {}

    public function compose(View $view): void
    {
        $data = $view->getData();
        $errors = $data['errors'] ?? null;
        $statistics = $this->collection($data['statistics'] ?? []);
        $archivedStatistics = $this->collection($data['archivedStatistics'] ?? []);
        $candidates = $this->collection($data['replacementCandidatesByArchivedId'] ?? []);
        $createFailed = old('form_context') === 'create';
        $editingId = old('form_context') === 'update' ? (int) old('editing_id') : null;
        $createValues = $this->values($createFailed);

        $view->with([
            'createFailed' => $createFailed,
            'editingId' => $editingId,
            'archivedStatistics' => $archivedStatistics,
            'replacementCandidatesByArchivedId' => $candidates,
            'createValues' => $createValues,
            'createLanguageCompletion' => $this->completion($createValues),
            'createActiveLanguage' => $this->languageTabs->activeLanguage(
                $errors,
                ['value_ar', 'label_ar'],
                ['value_en', 'label_en'],
                $createFailed,
            ),
            'statisticRows' => $statistics->map(function ($statistic) use ($editingId, $errors): array {
                $isCurrentEdit = $editingId === $statistic->id;
                $values = $this->values($isCurrentEdit, $statistic);

                return [
                    'statistic' => $statistic,
                    'isCurrentEdit' => $isCurrentEdit,
                    'editValues' => $values,
                    'editLanguageCompletion' => $this->completion($values),
                    'editActiveLanguage' => $this->languageTabs->activeLanguage(
                        $errors,
                        ['value_ar', 'label_ar'],
                        ['value_en', 'label_en'],
                        $isCurrentEdit,
                    ),
                ];
            }),
            'archivedStatisticRows' => $archivedStatistics->map(fn ($statistic): array => [
                'statistic' => $statistic,
                'replacementCandidates' => $candidates->get($statistic->getKey(), collect()),
            ]),
        ]);
    }

    private function collection(mixed $value): Collection
    {
        return $value instanceof Collection ? $value : collect($value);
    }

    private function values(bool $usesOldInput, mixed $statistic = null): array
    {
        return collect(['value', 'label', 'value_en', 'label_en', 'value_ar', 'label_ar'])
            ->mapWithKeys(fn (string $field): array => [
                $field => $usesOldInput ? old($field) : ($statistic?->{$field} ?? ''),
            ])
            ->all();
    }

    private function completion(array $values): array
    {
        return $this->languageTabs->completion([
            'id' => [$values['value'], $values['label']],
            'en' => [$values['value_en'], $values['label_en']],
            'ar' => [$values['value_ar'], $values['label_ar']],
        ]);
    }
}
