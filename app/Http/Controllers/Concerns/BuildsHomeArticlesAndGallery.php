<?php

namespace App\Http\Controllers\Concerns;

use App\Models\GalleryItem;

trait BuildsHomeArticlesAndGallery
{
    private function latestGalleryItems(int $limit = GalleryItem::MAX_HOMEPAGE_ITEMS): array
    {
        $limit = max(1, min($limit, GalleryItem::MAX_HOMEPAGE_ITEMS));

        return array_slice($this->allGalleryItems(), 0, $limit);
    }

    private function allGalleryItems(): array
    {
        $locale = app()->getLocale();
        $directVideoPrefix = rtrim((string) config('media.public_url'), '/')
            .'/gallery/media/%';

        return GalleryItem::query()
            ->where('is_published', true)
            ->homepage()
            ->where(function ($query) use ($directVideoPrefix): void {
                $query->where('type', 'photo')
                    ->orWhere(function ($videoQuery) use ($directVideoPrefix): void {
                        $videoQuery->where('type', 'video')
                            ->where('media_url', 'like', $directVideoPrefix);
                    });
            })
            ->ordered()
            ->limit(GalleryItem::MAX_HOMEPAGE_ITEMS)
            ->get()
            ->map(function (GalleryItem $item) use ($locale): ?array {
                $normalized = $this->normalizeGalleryItem([
                    'title' => $item->titleForLocale($locale),
                    'type' => $item->is_video ? 'video' : 'photo',
                    'type_label' => $item->typeLabelForLocale($locale),
                    'media_url' => $item->media_url,
                    'published_at' => optional($item->published_at)->toDateString() ?? '',
                    'date' => optional($item->published_at)->translatedFormat('j F Y') ?? '',
                    'caption' => $item->captionForLocale($locale),
                    'category' => $item->categoryForLocale($locale),
                ]);

                if ($item->is_video && ! ($normalized['is_direct_video'] ?? false)) {
                    return null;
                }

                return $normalized;
            })
            ->filter()
            ->values()
            ->all();
    }
}
