<?php

namespace App\View\Presenters;

use App\Models\GalleryItem;
use App\Support\PublicUrl;

final class LandingGalleryPresenter
{
    /** @return array{title: string, items: array<int, array<string, mixed>>} */
    public function present(): array
    {
        $items = GalleryItem::query()->orderByDesc('id')->limit(10)->get()
            ->filter(fn (GalleryItem $item): bool => PublicUrl::isSafe($item->media_url))
            ->map(function (GalleryItem $item): array {
                $poster = PublicUrl::normalize((string) $item->thumbnail_url);

                return ['url' => $item->media_url, 'type' => $item->type,
                    'poster' => $poster ?: ($item->is_video ? config('media.static.hero_school') : $item->media_url),
                    'title' => $item->alt_text ?? ''];
            })->values()->all();

        return ['title' => __('gallery_v2.title'), 'items' => $items];
    }
}
