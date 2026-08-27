<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\GalleryItem;
use App\Models\GalleryPageMediaItem;
use App\Support\Media\R2MediaStorage;

trait MaintainsGalleryOrdering
{
    private function deleteStoredPublicFile(?string $url, int|string|null $exceptItemId = null): void
    {
        if (! $url) {
            return;
        }

        $otherReference = GalleryItem::withTrashed()
            ->where('media_url', $url)
            ->when(
                $exceptItemId !== null,
                fn ($query) => $query->where('id', '!=', $exceptItemId)
            )
            ->exists();

        if ($otherReference) {
            return;
        }

        if (GalleryPageMediaItem::withTrashed()->where('media_url', $url)->exists()) {
            return;
        }

        app(R2MediaStorage::class)->deleteOwnedUrl($url);
    }

    private function nextSortOrder(): int
    {
        return ((int) GalleryItem::query()->max('sort_order')) + 1;
    }

    private function normalizeSortOrdersIfNeeded(): void
    {
        $orders = GalleryItem::query()
            ->ordered()
            ->pluck('sort_order')
            ->values()
            ->all();

        foreach ($orders as $index => $order) {
            if ((int) $order !== $index + 1) {
                $this->normalizeSortOrders();

                return;
            }
        }
    }

    private function normalizeSortOrders(): void
    {
        GalleryItem::query()
            ->ordered()
            ->get()
            ->values()
            ->each(function (GalleryItem $item, int $index): void {
                $expectedOrder = $index + 1;

                if ($item->sort_order !== $expectedOrder) {
                    $item->forceFill(['sort_order' => $expectedOrder])->save();
                }
            });
    }

    private function swapSortOrder(GalleryItem $firstItem, GalleryItem $secondItem): void
    {
        $firstSortOrder = $firstItem->sort_order;

        $firstItem->forceFill(['sort_order' => $secondItem->sort_order])->save();
        $secondItem->forceFill(['sort_order' => $firstSortOrder])->save();
    }

    private function limits(): array
    {
        return [
            'max_items' => self::MAX_ITEMS,
            'max_photo_mb' => 10,
            'max_photo_kb' => self::MAX_PHOTO_KB,
        ];
    }

    private function typeOptions(): array
    {
        return [
            'photo' => 'Foto',
            'video' => 'Video',
        ];
    }
}
