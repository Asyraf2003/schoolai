<?php

namespace App\Http\Controllers\Concerns;

use App\Models\PpdbSetting;
use App\Models\SiteStatistic;
use App\Services\PpdbAccess;
use Illuminate\Support\Facades\Schema;

trait BuildsHomeSections
{
    private function currentPpdbSetting(): PpdbSetting
    {
        return app(PpdbAccess::class)->current();
    }

    private function statsData(): array
    {
        if (! Schema::hasTable('site_statistics')) {
            return $this->languageStatsData();
        }

        $locale = app()->getLocale();

        $statistics = SiteStatistic::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'value',
                'value_en',
                'value_ar',
                'label',
                'label_en',
                'label_ar',
            ]);

        if ($statistics->isEmpty()) {
            return $this->languageStatsData();
        }

        return $statistics
            ->map(fn (SiteStatistic $statistic): array => [
                'value' => $statistic->valueForLocale($locale),
                'label' => $statistic->labelForLocale($locale),
            ])
            ->all();
    }

    private function languageStatsData(): array
    {
        $stats = $this->homeSection('stats');
        $items = $stats['items'] ?? [];

        if (! is_array($items)) {
            return [];
        }

        return array_map(
            fn (array $item): array => [
                'value' => trim((string) ($item['count'] ?? '').(string) ($item['suffix'] ?? '')),
                'count' => $item['count'] ?? null,
                'suffix' => $item['suffix'] ?? '',
                'label' => $item['label'] ?? '',
            ],
            $items,
        );
    }

    private function navbarData(): array
    {
        $navbar = $this->homeSection('navbar');

        if ($navbar === []) {
            return [];
        }

        $navbar['logo']['image_url'] = $this->publicAssetUrl($navbar['logo']['image'] ?? null);

        $navbar['cta'] = [];

        return $navbar;
    }

    private function gallerySectionData(): array
    {
        $gallery = $this->homeSection('galeri');

        if ($gallery === []) {
            return ['items' => []];
        }

        $gallery['items'] = $this->latestGalleryItems(6);

        return $gallery;
    }
}
