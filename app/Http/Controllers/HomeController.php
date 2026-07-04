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
            'gallerySection' => $this->gallerySectionData(),
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

    private function gallerySectionData(): array
    {
        $gallery = __('home.galeri');

        if (! is_array($gallery)) {
            return ['items' => []];
        }

        $gallery['items'] = $this->latestGalleryItems(6);

        return $gallery;
    }

    private function latestGalleryItems(int $limit = 6): array
    {
        $limit = max(3, min($limit, 6));

        return array_slice($this->allGalleryItems(), 0, $limit);
    }

    private function allGalleryItems(): array
    {
        $items = __('home.galeri.items');

        if (! is_array($items)) {
            return [];
        }

        $normalizedItems = array_values(array_filter(
            array_map(
                fn (mixed $item): ?array => is_array($item)
                    ? $this->normalizeGalleryItem($item)
                    : null,
                $items
            )
        ));

        usort(
            $normalizedItems,
            fn (array $first, array $second): int => strcmp(
                (string) ($second['published_at'] ?? ''),
                (string) ($first['published_at'] ?? '')
            )
        );

        return $normalizedItems;
    }

    private function normalizeGalleryItem(array $item): array
    {
        $allowedTypes = ['photo', 'video', 'reel'];
        $allowedVariants = ['normal', 'wide', 'tall', 'feature'];

        $type = $item['type'] ?? 'photo';
        $variant = $item['variant'] ?? 'normal';

        if (! in_array($type, $allowedTypes, true)) {
            $type = 'photo';
        }

        if (! in_array($variant, $allowedVariants, true)) {
            $variant = 'normal';
        }

        $item['type'] = $type;
        $item['type_label'] = match ($type) {
            'video' => 'Video',
            'reel' => 'Reel',
            default => 'Foto',
        };
        $item['is_video'] = $type !== 'photo';
        $item['variant'] = $variant;
        $item['instagram_url'] = $this->instagramUrl($item['instagram_url'] ?? null);
        $item['thumbnail_url'] = $this->publicAssetUrl($item['thumbnail'] ?? null);
        $item['published_at'] = (string) ($item['published_at'] ?? $item['date'] ?? '');
        $item['date'] = (string) ($item['date'] ?? $item['published_at']);
        $item['caption'] = (string) ($item['caption'] ?? '');
        $item['category'] = (string) ($item['category'] ?? '');
        $item['accent'] = (string) ($item['accent'] ?? '#f97316');
        $item['fallback_icon'] = (string) ($item['fallback_icon'] ?? $item['emoji'] ?? '📸');

        return $item;
    }

    private function instagramUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || ! str_ends_with(strtolower($host), 'instagram.com')) {
            return null;
        }

        return $url;
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
