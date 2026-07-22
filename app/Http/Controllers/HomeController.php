<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\PpdbSetting;
use App\Models\SiteStatistic;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

final class HomeController extends Controller
{
    private ?array $homeDataCache = null;

    public function __invoke(): View
    {
        $home = $this->homeData();

        return view('welcome', [
            'meta' => $home['meta'] ?? [],
            'hero' => $this->heroData(),
            'navbar' => $this->navbarData(),
            'stats' => $this->statsData(),
            'quickInfo' => $home['quick_info']['items'] ?? [],
            'ppdb' => $home['ppdb'] ?? [],
            'visiMisi' => $home['visi_misi'] ?? [],
            'schoolValues' => $home['nilai_sekolah'] ?? [],
            'featuredPrograms' => $home['program_unggulan'] ?? [],
            'gallerySection' => $this->gallerySectionData(),
            'articlesSection' => $this->articlesSectionData(),
            'footerSection' => $home['footer'] ?? [],
        ]);
    }

    /**
     * Normalizes the locale-backed hero data into one presentation contract.
     *
     * The slides currently come from translation fallback data. A future
     * database query only needs to provide the same keys before this boundary.
     */
    private function heroData(): array
    {
        $hero = $this->homeSection('hero');
        $slides = $hero['slides'] ?? [];
        $fallbackImageUrl = $this->publicAssetUrl($hero['fallback_image'] ?? null);
        $normalizedSlides = [];
        $ppdbSetting = null;

        if (! is_array($slides)) {
            $slides = [];
        }

        foreach ($slides as $slide) {
            if (! is_array($slide)) {
                continue;
            }

            $type = in_array(($slide['type'] ?? null), ['image', 'video'], true)
                ? $slide['type']
                : 'image';
            $mediaUrl = $this->publicAssetUrl($slide['media'] ?? null);
            $posterUrl = $this->publicAssetUrl($slide['poster'] ?? null);

            if (HeroVideoUrl::isYoutubeAsset($mediaUrl)) {
                $mediaUrl = null;
            }

            if (HeroVideoUrl::isYoutubeAsset($posterUrl)) {
                $posterUrl = null;
            }

            $renderType = $type === 'video'
                && $mediaUrl !== null
                && HeroVideoUrl::isDirectVideo($mediaUrl)
                    ? 'video'
                    : 'image';

            if ($renderType === 'image') {
                $mediaUrl = $type === 'image'
                    ? ($mediaUrl ?: $posterUrl ?: $fallbackImageUrl)
                    : ($posterUrl ?: $fallbackImageUrl);
            }

            if ($mediaUrl === null) {
                continue;
            }

            $cta = isset($slide['cta']) && is_array($slide['cta'])
                ? $slide['cta']
                : [];

            if (($cta['action'] ?? null) === 'admission') {
                $ppdbSetting ??= $this->currentPpdbSetting();
                $cta['href'] = $ppdbSetting->isRegistrationOpen()
                    ? $ppdbSetting->publicRegistrationUrl()
                    : route('ppdb');
            } else {
                $cta['href'] = $this->heroLinkUrl($cta['href'] ?? null);
            }

            $normalizedSlides[] = array_replace($slide, [
                'type' => $type,
                'render_type' => $renderType,
                'media_url' => $mediaUrl,
                'poster_url' => $posterUrl ?: $fallbackImageUrl,
                'is_media_fallback' => $type !== $renderType,
                'focal_position' => $this->heroFocalPosition(
                    $slide['focal_position'] ?? null
                ),
                'overlay_strength' => $this->heroOverlayStrength(
                    $slide['overlay_strength'] ?? null
                ),
                'video_mime_type' => $this->heroVideoMimeType($mediaUrl),
                'cta' => $cta,
            ]);
        }

        if ($normalizedSlides === [] && $fallbackImageUrl !== null) {
            $normalizedSlides[] = [
                'type' => 'image',
                'render_type' => 'image',
                'media_url' => $fallbackImageUrl,
                'poster_url' => $fallbackImageUrl,
                'media_alt' => $hero['fallback_image_alt'] ?? 'Al Mustaqbal School',
                'eyebrow' => 'Al Mustaqbal School',
                'title' => $hero['fallback_title'] ?? 'Al Mustaqbal School',
                'description' => $hero['fallback_description'] ?? '',
                'focal_position' => 'center center',
                'overlay_strength' => 0.58,
                'video_mime_type' => 'video/mp4',
                'is_media_fallback' => true,
                'cta' => [],
            ];
        }

        $hero['slides'] = $normalizedSlides;
        $hero['fallback_image_url'] = $fallbackImageUrl;
        $hero['autoplay_interval'] = min(
            15000,
            max(4000, (int) ($hero['autoplay_interval'] ?? 7000))
        );

        return $hero;
    }

    private function heroLinkUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (preg_match('/^#[A-Za-z][A-Za-z0-9_-]*$/', $url) === 1) {
            return $url;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private function heroFocalPosition(mixed $position): string
    {
        if (! is_string($position)) {
            return 'center center';
        }

        $position = trim($position);

        return preg_match('/^[a-z0-9%.\s-]{1,60}$/i', $position) === 1
            ? $position
            : 'center center';
    }

    private function heroOverlayStrength(mixed $strength): float
    {
        $strength = is_numeric($strength) ? (float) $strength : 0.58;

        return round(min(0.88, max(0.28, $strength)), 2);
    }

    private function heroVideoMimeType(?string $url): string
    {
        $extension = strtolower(pathinfo(
            (string) parse_url((string) $url, PHP_URL_PATH),
            PATHINFO_EXTENSION
        ));

        return match ($extension) {
            'webm' => 'video/webm',
            'ogv', 'ogg' => 'video/ogg',
            default => 'video/mp4',
        };
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

    private function articlesSectionData(): array
    {
        $section = $this->homeSection('artikel');

        if ($section === []) {
            return ['items' => []];
        }

        // Artikel homepage hanya berasal dari database.
        // Item statis pada file bahasa tidak boleh tampil sebagai artikel palsu.
        $section['items'] = [];

        if (! Schema::hasTable('articles')) {
            return $section;
        }

        $locale = app()->getLocale();

        $articles = Article::query()
            ->latestPublished()
            ->limit(4)
            ->get()
            ->filter(fn (Article $article): bool => $article->isNative()
                || $this->publicArticleUrl($article->linkForLocale($locale)) !== null)
            ->values();

        if ($articles->isEmpty()) {
            return $section;
        }

        $section['items'] = $articles
            ->values()
            ->map(function (Article $article, int $index) use ($locale): array {
                $publishedAt = $article->published_at;

                return [
                    'issue' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'title' => $article->titleForLocale($locale),
                    'description' => $article->descriptionForLocale($locale)
                        ?: __('runtime.home.latest_story_by', ['author' => $article->authorForDisplay()]),
                    'highlight' => $article->authorForDisplay(),
                    'category' => __('runtime.home.article'),
                    'date' => $publishedAt
                        ? $publishedAt->translatedFormat('j F Y, H:i').' WIB'
                        : '',
                    'published_at' => $publishedAt?->toIso8601String() ?? '',
                    'reading_time' => $article->isNative()
                        ? __('runtime.home.min_read', [
                            'count' => max(1, (int) ceil(max(1, $article->word_count) / 220)),
                        ])
                        : __('runtime.home.external_article'),
                    'href' => $article->isNative()
                        ? $article->linkForLocale($locale)
                        : $this->publicArticleUrl($article->linkForLocale($locale)),
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

        $gallery = $this->homeSection('galeri');
        $items = $gallery['items'] ?? [];

        if (! is_array($items)) {
            return [];
        }

        $normalizedItems = array_values(array_filter(
            array_map(
                fn (mixed $item): ?array => is_array($item)
                    ? $this->normalizeGalleryItem($item)
                    : null,
                $items,
            ),
        ));

        usort(
            $normalizedItems,
            fn (array $first, array $second): int => strcmp(
                (string) ($second['published_at'] ?? ''),
                (string) ($first['published_at'] ?? ''),
            ),
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
        $videoProvider = $type === 'video'
            ? $this->videoProvider($mediaUrl)
            : null;

        $item['type'] = $type;
        $item['type_label'] = (string) ($item['type_label'] ?? (
            $type === 'video' ? __('runtime.home.video') : __('runtime.home.photo')
        ));
        $item['is_video'] = $type === 'video';
        $item['variant'] = $variant;
        $item['instagram_url'] = $this->instagramUrl($item['instagram_url'] ?? null);
        $item['media_url'] = $mediaUrl;
        $item['thumbnail_url'] = $type === 'photo' ? $mediaUrl : $this->videoThumbnailUrl($mediaUrl);
        $item['video_provider'] = $videoProvider;
        $item['video_provider_logo_url'] = $this->videoProviderLogoUrl($videoProvider);
        $item['video_provider_label'] = match ($videoProvider) {
            'youtube' => 'YouTube',
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'tiktok' => 'TikTok',
            'vimeo' => 'Vimeo',
            default => __('runtime.home.video'),
        };
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

        if (! PublicUrl::isSafe($url)) {
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

        if ($host === 'www.tiktok.com' && preg_match('~^(?:player/v1|embed/v2)/\d+$~', $path)) {
            return $url;
        }

        if ($host === 'www.instagram.com' && preg_match('~^(p|reel|tv)/[^/]+/embed$~', $path)) {
            return $url;
        }

        if ($host === 'player.vimeo.com' && preg_match('~^video/\d+$~', $path)) {
            return $url;
        }

        if ($host === 'www.facebook.com' && $path === 'plugins/video.php' && $this->isTrustedFacebookEmbedUrl($url)) {
            return $url;
        }

        return null;
    }

    private function isTrustedFacebookEmbedUrl(string $url): bool
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $facebookUrl = trim((string) ($query['href'] ?? ''));

        if ($facebookUrl === '' || ! PublicUrl::isSafe($facebookUrl)) {
            return false;
        }

        $facebookScheme = strtolower((string) parse_url($facebookUrl, PHP_URL_SCHEME));
        $facebookHost = strtolower((string) parse_url($facebookUrl, PHP_URL_HOST));
        $facebookPath = trim((string) parse_url($facebookUrl, PHP_URL_PATH), '/');
        parse_str((string) parse_url($facebookUrl, PHP_URL_QUERY), $facebookQuery);

        if (
            $facebookScheme !== 'https' ||
            ($facebookHost !== 'facebook.com' && ! str_ends_with($facebookHost, '.facebook.com'))
        ) {
            return false;
        }

        if (preg_match('~^reel/\d+$~', $facebookPath)) {
            return true;
        }

        return $facebookPath === 'watch' && preg_match('~^\d+$~', (string) ($facebookQuery['v'] ?? '')) === 1;
    }

    private function videoProvider(?string $embedUrl): string
    {
        if (! is_string($embedUrl) || trim($embedUrl) === '') {
            return 'video';
        }

        $host = strtolower((string) parse_url(trim($embedUrl), PHP_URL_HOST));

        return match ($host) {
            'www.youtube.com' => 'youtube',
            'www.instagram.com' => 'instagram',
            'www.facebook.com' => 'facebook',
            'www.tiktok.com' => 'tiktok',
            'player.vimeo.com' => 'vimeo',
            default => 'video',
        };
    }

    private function videoProviderLogoUrl(?string $provider): ?string
    {
        if ($provider === null || $provider === '') {
            return null;
        }

        $path = match ($provider) {
            'youtube' => 'media/home/youtube.png',
            'instagram' => 'media/home/instagram.svg',
            'facebook' => 'media/home/facebook.png',
            'tiktok' => 'media/home/tiktok.png',
            'vimeo' => 'media/home/vimeo.png',
            default => null,
        };

        return $this->publicAssetUrl($path);
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

        if (! PublicUrl::isSafe($url)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if ($host !== 'instagram.com' && ! str_ends_with($host, '.instagram.com')) {
            return null;
        }

        return $url;
    }

    private function publicArticleUrl(string $url): ?string
    {
        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private function homeData(): array
    {
        if ($this->homeDataCache !== null) {
            return $this->homeDataCache;
        }

        $base = __('home');
        $parity = __('home_parity');

        $base = is_array($base) ? $base : [];
        $parity = is_array($parity) ? $parity : [];

        return $this->homeDataCache = array_replace_recursive($base, $parity);
    }

    private function homeSection(string $key): array
    {
        $section = $this->homeData()[$key] ?? [];

        return is_array($section) ? $section : [];
    }

    private function publicAssetUrl(mixed $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return PublicUrl::normalize($path);
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
