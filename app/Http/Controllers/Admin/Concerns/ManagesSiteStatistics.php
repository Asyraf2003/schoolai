<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\SiteStatistic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait ManagesSiteStatistics
{
    public function edit(): View
    {
        $this->seedDefaultStatisticsIfEmpty();
        $this->normalizeSortOrders();

        $statistics = SiteStatistic::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $archivedStatistics = SiteStatistic::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->orderByDesc('id')
            ->get();

        $activeByIdentity = $statistics
            ->filter(fn (SiteStatistic $statistic): bool => $statistic->replacementIdentity() !== null)
            ->groupBy(fn (SiteStatistic $statistic): string => (string) $statistic->replacementIdentity());

        $replacementCandidatesByArchivedId = $archivedStatistics->mapWithKeys(
            function (SiteStatistic $archivedStatistic) use ($activeByIdentity): array {
                $identity = $archivedStatistic->replacementIdentity();

                return [
                    $archivedStatistic->getKey() => $identity === null
                        ? collect()
                        : $activeByIdentity->get($identity, collect())->values(),
                ];
            }
        );

        return view('admin.site-statistics.edit', [
            'statistics' => $statistics,
            'archivedStatistics' => $archivedStatistics,
            'replacementCandidatesByArchivedId' => $replacementCandidatesByArchivedId,
            'canCreate' => $statistics->count() < SiteStatistic::MAX_ITEMS,
            'canRestoreWithoutReplacement' => $statistics->count() < SiteStatistic::MAX_ITEMS,
            'maxItems' => SiteStatistic::MAX_ITEMS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (SiteStatistic::query()->count() >= SiteStatistic::MAX_ITEMS) {
            throw ValidationException::withMessages([
                'value' => 'Maksimal hanya boleh ada 4 statistik homepage aktif.',
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
                'delete' => 'Minimal harus ada 1 statistik homepage aktif.',
            ]);
        }

        $siteStatistic->delete();
        $this->normalizeSortOrders();

        return redirect()
            ->route('admin.stats.edit')
            ->with('success', 'Statistik dipindahkan ke arsip dan dapat dipulihkan.');
    }

    public function restore(Request $request, int $siteStatistic): RedirectResponse
    {
        $data = $request->validate([
            'replacement_site_statistic_id' => ['nullable', 'integer'],
        ]);

        $replacementId = isset($data['replacement_site_statistic_id'])
            ? (int) $data['replacement_site_statistic_id']
            : null;

        if ($replacementId === null) {
            DB::transaction(function () use ($siteStatistic): void {
                SiteStatistic::query()->lockForUpdate()->get();

                if (SiteStatistic::query()->count() >= SiteStatistic::MAX_ITEMS) {
                    throw ValidationException::withMessages([
                        'replacement_site_statistic_id' => 'Sudah ada 4 statistik aktif. Gunakan Pulihkan & Gantikan pada statistik dengan label identik.',
                    ]);
                }

                $archivedStatistic = SiteStatistic::onlyTrashed()
                    ->lockForUpdate()
                    ->findOrFail($siteStatistic);

                $archivedStatistic->forceFill([
                    'sort_order' => $this->nextSortOrder(),
                ])->save();
                $archivedStatistic->restore();
            });

            $this->normalizeSortOrders();

            return redirect()
                ->route('admin.stats.edit')
                ->with('success', 'Statistik berhasil dipulihkan.');
        }

        DB::transaction(function () use ($siteStatistic, $replacementId): void {
            $archivedStatistic = SiteStatistic::onlyTrashed()
                ->lockForUpdate()
                ->findOrFail($siteStatistic);

            $replacementStatistic = SiteStatistic::query()
                ->lockForUpdate()
                ->findOrFail($replacementId);

            $archivedIdentity = $archivedStatistic->replacementIdentity();
            $replacementIdentity = $replacementStatistic->replacementIdentity();

            if ($archivedIdentity === null || $archivedIdentity !== $replacementIdentity) {
                throw ValidationException::withMessages([
                    'replacement_site_statistic_id' => 'Statistik pengganti harus aktif serta memiliki label Indonesia yang identik.',
                ]);
            }

            $sortOrder = $replacementStatistic->sort_order;

            $replacementStatistic->delete();
            $archivedStatistic->forceFill(['sort_order' => $sortOrder])->save();
            $archivedStatistic->restore();
        });

        $this->normalizeSortOrders();

        return redirect()
            ->route('admin.stats.edit')
            ->with('success', 'Statistik lama dipulihkan dan statistik aktif pengganti dipindahkan ke arsip.');
    }
}
