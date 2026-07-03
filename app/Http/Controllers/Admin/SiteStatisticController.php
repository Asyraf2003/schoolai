<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteStatistic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SiteStatisticController extends Controller
{
    public function edit(): View
    {
        $this->seedDefaultStatisticsIfEmpty();

        return view('admin.site-statistics.edit', [
            'statistics' => SiteStatistic::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'statistics' => ['required', 'array'],
            'statistics.*.id' => ['required', 'integer', 'exists:site_statistics,id'],
            'statistics.*.value' => ['required', 'string', 'max:80'],
            'statistics.*.label' => ['required', 'string', 'max:120'],
            'statistics.*.description' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated['statistics'] as $index => $item) {
            SiteStatistic::query()
                ->whereKey($item['id'])
                ->update([
                    'value' => trim($item['value']),
                    'label' => trim($item['label']),
                    'description' => isset($item['description']) ? trim((string) $item['description']) : null,
                    'sort_order' => $index + 1,
                ]);
        }

        return back()->with('success', 'Statistik homepage berhasil diperbarui.');
    }

    private function seedDefaultStatisticsIfEmpty(): void
    {
        if (SiteStatistic::query()->exists()) {
            return;
        }

        foreach (__('home.stats.items') as $index => $item) {
            SiteStatistic::query()->create([
                'value' => trim((string) ($item['count'] ?? '') . (string) ($item['suffix'] ?? '')),
                'label' => (string) ($item['label'] ?? ''),
                'description' => $item['description'] ?? null,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
