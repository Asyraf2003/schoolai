<?php

namespace App\View\Presenters;

use App\Models\GalleryItem;
use App\Support\PublicUrl;

final class SpatialGalleryPresenter
{
    /** @return array<int, array{url: string, type: string, poster: string, title: string}> */
    public function present(): array
    {
        $items = GalleryItem::query()->orderByDesc('id')->get(['type', 'media_url', 'thumbnail_url', 'alt_text'])
            ->filter(fn (GalleryItem $item): bool => PublicUrl::isSafe($item->media_url))
            ->map(fn (GalleryItem $item): array => [
                'url' => $item->media_url,
                'type' => $item->is_video ? 'video' : 'photo',
                'poster' => PublicUrl::normalize((string) $item->thumbnail_url)
                    ?: ($item->is_video ? config('media.static.navigation.gallery') : $item->media_url),
                'title' => $item->alt_text ?: __('gallery_v3.hero_alt'),
            ])->values()->all();

        return $items ?: (new LandingGalleryPresenter)->present()['items'];
    }
}
