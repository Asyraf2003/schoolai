<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\PpdbSetting;
use App\Models\SiteStatistic;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

trait BuildsHomeSections
{
    private function currentPpdbSetting(): PpdbSetting
    {
        if (! Schema::hasTable('ppdb_settings')) {
            return new PpdbSetting([
                'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
                'is_active' => true,
            ]);
        }

        $setting = PpdbSetting::query()->first();

        if ($setting instanceof PpdbSetting) {
            return $setting;
        }

        return new PpdbSetting([
            'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
            'is_active' => true,
        ]);
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
                'value' => trim((string) ($item['count'] ?? '') . (string) ($item['suffix'] ?? '')),
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

        if (isset($navbar['cta']) && is_array($navbar['cta'])) {
            $navbar['cta']['label'] = __('runtime.home.admission_info');
            $navbar['cta']['href'] = route('ppdb');
        }

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
