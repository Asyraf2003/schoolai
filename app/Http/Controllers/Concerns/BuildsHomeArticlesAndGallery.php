<?php

namespace App\Http\Controllers\Concerns;

use App\Models\GalleryItem;
use Illuminate\Support\Facades\Schema;

trait BuildsHomeArticlesAndGallery
{
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
                ->homepage()
                ->where('type', 'photo')
                ->ordered()
                ->limit(6)
                ->get()
                ->map(fn (GalleryItem $item): array => $this->normalizeGalleryItem([
                    'title' => $item->titleForLocale($locale),
                    'type' => 'photo',
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

        $photoItems = array_filter(
            $items,
            static fn (mixed $item): bool => is_array($item)
                && (($item['type'] ?? 'photo') === 'photo'),
        );

        $normalizedItems = array_values(array_filter(
            array_map(
                fn (mixed $item): ?array => is_array($item)
                    ? $this->normalizeGalleryItem($item)
                    : null,
                $photoItems,
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
