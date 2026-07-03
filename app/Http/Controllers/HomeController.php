<?php

namespace App\Http\Controllers;

use App\Models\SiteStatistic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

final class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'meta' => __('home.meta'),
            'hero' => $this->heroData(),
            'navbar' => $this->navbarData(),
            'stats' => $this->statsData(),
            'quickInfo' => __('home.quick_info.items'),
            'ppdb' => __('home.ppdb'),
            'visiMisi' => __('home.visi_misi'),
            'schoolValues' => __('home.nilai_sekolah'),
            'featuredPrograms' => __('home.program_unggulan'),
            'extracurricular' => __('home.ekstrakurikuler'),
            'gallerySection' => __('home.galeri'),
            'articlesSection' => __('home.artikel'),
            'announcementsSection' => __('home.pengumuman'),
            'facilitiesSection' => __('home.fasilitas'),
            'contactSection' => __('home.kontak'),
            'footerSection' => __('home.footer'),
        ]);
    }

    /**
     * Menyiapkan data hero dari file bahasa dan hanya mengaktifkan URL gambar
     * jika file fisiknya memang tersedia di public/.
     */
    private function heroData(): array
    {
        $hero = __('home.hero');

        if (! is_array($hero)) {
            return [];
        }

        $hero['background_image_url'] = $this->publicAssetUrl($hero['background_image'] ?? null);
        $hero['visual_image_url'] = $this->publicAssetUrl($hero['visual_image'] ?? null);
        $hero['logo_image_url'] = $this->publicAssetUrl($hero['logo_image'] ?? null);

        $hero['badges'] = array_map(function (array $badge): array {
            $badge['image_url'] = $this->publicAssetUrl($badge['image'] ?? null);

            return $badge;
        }, $hero['badges'] ?? []);

        return $hero;
    }

    private function statsData(): array
    {
        if (! Schema::hasTable('site_statistics')) {
            return $this->languageStatsData();
        }

        $statistics = SiteStatistic::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['value', 'label']);

        if ($statistics->isEmpty()) {
            return $this->languageStatsData();
        }

        return $statistics
            ->map(fn (SiteStatistic $statistic): array => [
                'value' => $statistic->value,
                'label' => $statistic->label,
            ])
            ->all();
    }

    private function languageStatsData(): array
    {
        return array_map(
            fn (array $item): array => [
                'value' => trim((string) ($item['count'] ?? '') . (string) ($item['suffix'] ?? '')),
                'count' => $item['count'] ?? null,
                'suffix' => $item['suffix'] ?? '',
                'label' => $item['label'] ?? '',
            ],
            __('home.stats.items')
        );
    }

    private function navbarData(): array
    {
        $navbar = __('home.navbar');

        if (! is_array($navbar)) {
            return [];
        }

        $navbar['logo']['image_url'] = $this->publicAssetUrl($navbar['logo']['image'] ?? null);

        return $navbar;
    }

    private function publicAssetUrl(mixed $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $relativePath = ltrim($path, '/');

        if (! file_exists(public_path($relativePath))) {
            return null;
        }

        return asset($relativePath);
    }
}
