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

        $data = $this->validatedItem($request);

        SiteStatistic::query()->create([
            ...$data,
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

    public function moveUp(
        SiteStatistic $siteStatistic
    ): RedirectResponse {
        $this->normalizeSortOrders();
        $siteStatistic->refresh();

        $previous = SiteStatistic::query()
            ->where('sort_order', '<', $siteStatistic->sort_order)
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->first();

        if ($previous) {
            $this->swapSortOrder($siteStatistic, $previous);
            $this->normalizeSortOrders();
        }

        return redirect()
            ->route('admin.stats.edit')
            ->with('success', 'Urutan statistik berhasil diperbarui.');
    }

    public function moveDown(
        SiteStatistic $siteStatistic
    ): RedirectResponse {
        $this->normalizeSortOrders();
        $siteStatistic->refresh();

        $next = SiteStatistic::query()
            ->where('sort_order', '>', $siteStatistic->sort_order)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($next) {
            $this->swapSortOrder($siteStatistic, $next);
            $this->normalizeSortOrders();
        }

        return redirect()
            ->route('admin.stats.edit')
            ->with('success', 'Urutan statistik berhasil diperbarui.');
    }

    private function validatedItem(Request $request): array
    {
        $validated = $request->validate([
            'value' => ['required', 'string', 'max:80'],
            'label' => ['required', 'string', 'max:120'],
        ], [
            'value.required' => 'Nilai statistik wajib diisi.',
            'value.max' => 'Nilai statistik maksimal 80 karakter.',
            'label.required' => 'Label statistik wajib diisi.',
            'label.max' => 'Label statistik maksimal 120 karakter.',
        ]);

        return [
            'value' => trim($validated['value']),
            'label' => trim($validated['label']),
        ];
    }

    private function seedDefaultStatisticsIfEmpty(): void
    {
        if (SiteStatistic::query()->exists()) {
            return;
        }

        $items = __('home.stats.items');

        if (! is_array($items)) {
            return;
        }

        foreach (
            array_slice($items, 0, SiteStatistic::MAX_ITEMS)
            as $index => $item
        ) {
            SiteStatistic::query()->create([
                'value' => trim(
                    (string) ($item['count'] ?? '')
                    . (string) ($item['suffix'] ?? '')
                ),
                'label' => trim((string) ($item['label'] ?? '')),
                'sort_order' => $index + 1,
            ]);
        }
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

    private function swapSortOrder(
        SiteStatistic $first,
        SiteStatistic $second
    ): void {
        $firstOrder = (int) $first->sort_order;
        $secondOrder = (int) $second->sort_order;
        $temporaryOrder = SiteStatistic::MAX_ITEMS + 100;

        DB::transaction(function () use (
            $first,
            $second,
            $firstOrder,
            $secondOrder,
            $temporaryOrder
        ): void {
            DB::table('site_statistics')
                ->where('id', $first->id)
                ->update([
                    'sort_order' => $temporaryOrder,
                    'updated_at' => now(),
                ]);

            DB::table('site_statistics')
                ->where('id', $second->id)
                ->update([
                    'sort_order' => $firstOrder,
                    'updated_at' => now(),
                ]);

            DB::table('site_statistics')
                ->where('id', $first->id)
                ->update([
                    'sort_order' => $secondOrder,
                    'updated_at' => now(),
                ]);
        });
    }
}
