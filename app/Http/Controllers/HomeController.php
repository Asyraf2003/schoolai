<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\PpdbSetting;
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
            'gallerySection' => $this->gallerySectionData(),
            'articlesSection' => $this->articlesSectionData(),
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

        if (isset($hero['primary_cta']) && is_array($hero['primary_cta'])) {
            $ppdbSetting = $this->currentPpdbSetting();
            $registrationUrl = $ppdbSetting->isRegistrationOpen()
                ? $ppdbSetting->publicRegistrationUrl()
                : null;

            $hero['primary_cta']['href'] = $registrationUrl ?: route('ppdb');
        }

        return $hero;
    }

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


    private function articlesSectionData(): array
    {
        $section = __('home.artikel');

        if (! is_array($section)) {
            return ['items' => []];
        }

        if (! Schema::hasTable('articles')) {
            return $section;
        }

        $locale = app()->getLocale();

        $articles = Article::query()
            ->latestPublished()
            ->limit(4)
            ->get()
            ->filter(fn (Article $article): bool => $this->publicArticleUrl($article->linkForLocale($locale)) !== null)
            ->values();

        if ($articles->isEmpty()) {
            return $section;
        }

        $section['items'] = $articles
            ->values()
            ->map(function (Article $article, int $index) use ($locale): array {
                $publishedDate = $article->published_date;

                return [
                    'issue' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'title' => $article->titleForLocale($locale),
                    'description' => $article->descriptionForLocale($locale) ?: (
                        $locale === 'en'
                            ? 'Read the latest school story by ' . $article->authorForDisplay() . '.'
                            : 'Baca cerita terbaru sekolah oleh ' . $article->authorForDisplay() . '.'
                    ),
                    'highlight' => $article->authorForDisplay(),
                    'category' => $locale === 'en' ? 'Article' : 'Artikel',
                    'date' => $publishedDate?->translatedFormat('j F Y') ?? '',
                    'published_at' => $publishedDate?->toDateString() ?? '',
                    'reading_time' => $locale === 'en' ? 'External article' : 'Artikel eksternal',
                    'href' => $this->publicArticleUrl($article->linkForLocale($locale)),
                    'thumbnail_url' => $this->publicAssetUrl($article->thumbnail_url) ?? $article->thumbnail_url,
                    'emoji' => '📰',
                    'gradient_from' => 'var(--color-yellow-soft)',
                    'gradient_to' => 'var(--color-orange-soft)',
                ];
            })
            ->all();

        if (isset($section['cta']) && is_array($section['cta'])) {
            $section['cta']['href'] = route('artikel');
        }

        return $section;
    }

    private function latestGalleryItems(int $limit = 6): array
    {
        $limit = max(3, min($limit, 6));

        return array_slice($this->allGalleryItems(), 0, $limit);
    }

    private function allGalleryItems(): array
    {
        if (Schema::hasTable('gallery_items')) {
            $locale = app()->getLocale();

            return GalleryItem::query()
                ->where('is_published', true)
                ->ordered()
                ->limit(6)
                ->get()
                ->map(fn (GalleryItem $item): array => $this->normalizeGalleryItem([
                    'title' => $item->titleForLocale($locale),
                    'type' => $item->type,
                    'type_label' => $item->typeLabelForLocale($locale),
                    'media_url' => $item->media_url,
                    'published_at' => optional($item->published_at)->toDateString() ?? '',
                    'date' => optional($item->published_at)->translatedFormat('j F Y') ?? '',
                    'caption' => $item->captionForLocale($locale),
                    'category' => $item->categoryForLocale($locale),
                ]))
                ->all();
        }

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
        $allowedTypes = ['photo', 'video'];
        $allowedVariants = ['normal', 'wide', 'tall', 'feature'];

        $type = $item['type'] ?? 'photo';
        $variant = $item['variant'] ?? 'normal';

        if (! in_array($type, $allowedTypes, true)) {
            $type = 'photo';
        }

        if (! in_array($variant, $allowedVariants, true)) {
            $variant = 'normal';
        }

        $rawMedia = $item['media_url'] ?? $item['thumbnail'] ?? null;
        $mediaUrl = $type === 'video'
            ? $this->trustedVideoEmbedUrl($rawMedia)
            : $this->publicAssetUrl($rawMedia);

        $item['type'] = $type;
        $item['type_label'] = (string) ($item['type_label'] ?? ($type === 'video' ? 'Video' : (app()->getLocale() === 'en' ? 'Photo' : 'Foto')));
        $item['is_video'] = $type === 'video';
        $item['variant'] = $variant;
        $item['instagram_url'] = $this->instagramUrl($item['instagram_url'] ?? null);
        $item['media_url'] = $mediaUrl;
        $item['thumbnail_url'] = $type === 'photo' ? $mediaUrl : $this->videoThumbnailUrl($mediaUrl);
        $item['published_at'] = (string) ($item['published_at'] ?? $item['date'] ?? '');
        $item['date'] = (string) ($item['date'] ?? $item['published_at']);
        $item['caption'] = (string) ($item['caption'] ?? '');
        $item['category'] = (string) ($item['category'] ?? '');
        $item['accent'] = $type === 'video' ? '#f97316' : '#19aee6';
        $item['fallback_icon'] = $type === 'video' ? '▶' : '📸';

        return $item;
    }

    private function trustedVideoEmbedUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($scheme !== 'https' || $host === '') {
            return null;
        }

        if ($host === 'www.youtube.com' && preg_match('~^embed/[^/?#]+$~', $path)) {
            return $url;
        }

        if ($host === 'www.tiktok.com' && preg_match('~^embed/v2/\d+$~', $path)) {
            return $url;
        }

        if ($host === 'www.instagram.com' && preg_match('~^(p|reel|tv)/[^/]+/embed$~', $path)) {
            return $url;
        }

        if ($host === 'player.vimeo.com' && preg_match('~^video/\d+$~', $path)) {
            return $url;
        }

        return null;
    }


    private function videoThumbnailUrl(?string $embedUrl): ?string
    {
        if (! is_string($embedUrl) || trim($embedUrl) === '') {
            return null;
        }

        $embedUrl = trim($embedUrl);
        $host = strtolower((string) parse_url($embedUrl, PHP_URL_HOST));
        $path = trim((string) parse_url($embedUrl, PHP_URL_PATH), '/');

        if ($host === 'www.youtube.com' && preg_match('~^embed/([^/?#]+)$~', $path, $match)) {
            return 'https://i.ytimg.com/vi/' . rawurlencode($match[1]) . '/hqdefault.jpg';
        }

        return null;
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

    private function publicArticleUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = '/' . ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return null;
        }

        if (
            $host === 'localhost' ||
            $host === '127.0.0.1' ||
            $host === '::1' ||
            str_ends_with($host, '.local') ||
            str_starts_with($host, '10.') ||
            str_starts_with($host, '192.168.') ||
            preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $host) === 1
        ) {
            return null;
        }

        if (
            $path === '/admin' ||
            str_starts_with($path, '/admin/') ||
            $path === '/login' ||
            str_starts_with($path, '/auth/')
        ) {
            return null;
        }

        return $url;
    }

    private function publicAssetUrl(mixed $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $relativePath = ltrim($path, '/');

        if (str_starts_with($relativePath, 'storage/')) {
            return asset($relativePath);
        }

        if (! file_exists(public_path($relativePath))) {
            return null;
        }

        return asset($relativePath);
    }
}
