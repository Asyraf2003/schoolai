<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteStatistic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class SiteStatisticController extends Controller
{
    public function __construct()
    {
        app()->setLocale('id');
    }

    public function edit(): View
    {
        $this->seedDefaultStatisticsIfEmpty();
        $this->normalizeSortOrders();

        $statistics = SiteStatistic::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.site-statistics.edit', [
            'statistics' => $statistics,
            'canCreate' => $statistics->count() < SiteStatistic::MAX_ITEMS,
            'maxItems' => SiteStatistic::MAX_ITEMS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (SiteStatistic::query()->count() >= SiteStatistic::MAX_ITEMS) {
            throw ValidationException::withMessages([
                'value' => 'Maksimal hanya boleh ada 4 statistik homepage.',
            ]);
        }

        SiteStatistic::query()->create([
            ...$this->validatedItem($request),
            'sort_order' => $this->nextSortOrder(),
        ]);

        $this->normalizeSortOrders();

        return redirect()
            ->route('admin.stats.edit')
            ->with('success', 'Statistik berhasil ditambahkan.');
    }

    public function update(
        Request $request,
        SiteStatistic $siteStatistic
    ): RedirectResponse {
        $siteStatistic->update($this->validatedItem($request));

        return redirect()
            ->route('admin.stats.edit')
            ->with('success', 'Statistik berhasil diperbarui.');
    }

    public function destroy(
        SiteStatistic $siteStatistic
    ): RedirectResponse {
        if (SiteStatistic::query()->count() <= 1) {
            return back()->withErrors([
                'delete' => 'Minimal harus ada 1 statistik homepage.',
            ]);
        }

        $siteStatistic->delete();
        $this->normalizeSortOrders();

        return redirect()
            ->route('admin.stats.edit')
            ->with('success', 'Statistik berhasil dihapus.');
    }

    private function validatedItem(Request $request): array
    {
        $validated = $request->validate([
            'value' => ['required', 'string', 'max:80'],
            'value_en' => ['required', 'string', 'max:80'],
            'label' => ['required', 'string', 'max:120'],
            'label_en' => ['required', 'string', 'max:120'],
        ], [
            'value.required' => 'Nilai Indonesia wajib diisi.',
            'value.max' => 'Nilai Indonesia maksimal 80 karakter.',
            'value_en.required' => 'Nilai English wajib diisi.',
            'value_en.max' => 'Nilai English maksimal 80 karakter.',
            'label.required' => 'Label Indonesia wajib diisi.',
            'label.max' => 'Label Indonesia maksimal 120 karakter.',
            'label_en.required' => 'Label English wajib diisi.',
            'label_en.max' => 'Label English maksimal 120 karakter.',
        ]);

        return [
            'value' => trim($validated['value']),
            'value_en' => trim($validated['value_en']),
            'label' => trim($validated['label']),
            'label_en' => trim($validated['label_en']),
        ];
    }

    private function seedDefaultStatisticsIfEmpty(): void
    {
        if (SiteStatistic::query()->exists()) {
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
