<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use App\Rules\SafeImageUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

trait MaintainsGalleryOrdering
{
    private function deleteStoredPublicFile(?string $url, int|string|null $exceptItemId = null): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
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

        $path = substr($url, strlen('/storage/'));

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/') || str_contains($path, '\\')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function wouldLeaveNoPublishedItem(?GalleryItem $currentItem, bool $nextPublished): bool
    {
        if ($nextPublished) {
            return false;
        }

        $query = GalleryItem::query()->where('is_published', true);

        if ($currentItem?->exists) {
            $query->where($currentItem->getKeyName(), '!=', $currentItem->getKey());
        }

        return $query->count() < 1;
    }

    private function nextSortOrder(): int
    {
        return min(((int) GalleryItem::query()->max('sort_order')) + 1, self::MAX_ITEMS);
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
            'min_published_items' => 1,
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
