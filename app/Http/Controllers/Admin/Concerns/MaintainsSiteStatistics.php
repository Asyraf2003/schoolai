<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\SiteStatistic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait MaintainsSiteStatistics
{
    private function validatedItem(Request $request): array
    {
        $validated = $request->validate([
            'value' => ['required', 'string', 'max:80'],
            'value_en' => ['nullable', 'string', 'max:80'],
            'value_ar' => ['nullable', 'string', 'max:80'],
            'label' => ['required', 'string', 'max:120'],
            'label_en' => ['nullable', 'string', 'max:120'],
            'label_ar' => ['nullable', 'string', 'max:120'],
        ], [
            'value.required' => 'Nilai Indonesia wajib diisi.',
            'value.max' => 'Nilai Indonesia maksimal 80 karakter.',
            'value_en.max' => 'Nilai English maksimal 80 karakter.',
            'value_ar.max' => 'Nilai Arabic maksimal 80 karakter.',
            'label.required' => 'Label Indonesia wajib diisi.',
            'label.max' => 'Label Indonesia maksimal 120 karakter.',
            'label_en.max' => 'Label English maksimal 120 karakter.',
            'label_ar.max' => 'Label Arabic maksimal 120 karakter.',
        ]);

        return [
            'value' => trim($validated['value']),
            'value_en' => $this->nullableText($validated['value_en'] ?? null),
            'value_ar' => $this->nullableText($validated['value_ar'] ?? null),
            'label' => trim($validated['label']),
            'label_en' => $this->nullableText($validated['label_en'] ?? null),
            'label_ar' => $this->nullableText($validated['label_ar'] ?? null),
        ];
    }

    private function seedDefaultStatisticsIfEmpty(): void
    {
        if (SiteStatistic::withTrashed()->exists()) {
            return;
        }

        $indonesianItems = trans('home.stats.items', [], 'id');
        $englishItems = trans('home.stats.items', [], 'en');

        if (! is_array($indonesianItems)) {
            return;
        }

        $indonesianItems = array_values($indonesianItems);
        $englishItems = is_array($englishItems)
            ? array_values($englishItems)
            : [];

        foreach (
            array_slice(
                $indonesianItems,
                0,
                SiteStatistic::MAX_ITEMS
            )
            as $index => $indonesianItem
        ) {
            $englishItem = $englishItems[$index] ?? [];

            SiteStatistic::query()->create([
                'value' => $this->translatedValue($indonesianItem),
                'value_en' => $this->translatedValue($englishItem)
                    ?: $this->translatedValue($indonesianItem),
                'label' => trim(
                    (string) ($indonesianItem['label'] ?? '')
                ),
                'label_en' => trim(
                    (string) ($englishItem['label'] ?? '')
                ) ?: trim(
                    (string) ($indonesianItem['label'] ?? '')
                ),
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function translatedValue(array $item): string
    {
        return trim(
            (string) ($item['count'] ?? '')
            . (string) ($item['suffix'] ?? '')
        );
    }

    private function nullableText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function nextSortOrder(): int
    {
        return ((int) SiteStatistic::query()->max('sort_order')) + 1;
    }

    private function normalizeSortOrders(): void
    {
        $items = SiteStatistic::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'sort_order']);

        DB::transaction(function () use ($items): void {
            foreach ($items as $index => $item) {
                $expected = $index + 1;

                if ((int) $item->sort_order === $expected) {
                    continue;
                }

                DB::table('site_statistics')
                    ->where('id', $item->id)
                    ->update([
                        'sort_order' => $expected,
                        'updated_at' => now(),
                    ]);
            }
        });
    }
}
