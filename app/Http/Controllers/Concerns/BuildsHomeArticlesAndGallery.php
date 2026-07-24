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

trait BuildsHomeArticlesAndGallery
{
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
}
