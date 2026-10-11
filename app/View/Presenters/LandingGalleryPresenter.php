<?php

namespace App\View\Presenters;

use App\Models\GalleryItem;
use App\Support\Media\MediaUrlResolver;

final class LandingGalleryPresenter
{
    public function __construct(private MediaUrlResolver $media) {}

    /** @return array{title: string, items: array<int, array<string, mixed>>} */
    public function present(): array
    {
        $catalog = $this->catalog();
        $known = GalleryItem::withTrashed()->pluck('media_url')->all();
        $items = GalleryItem::query()->where('is_published', true)
            ->where(fn ($query) => $query->where('show_on_homepage', true)->orWhere('show_on_gallery_page', true)
                ->orWhereIn('media_url', array_column($catalog, 'url')))
            ->orderByDesc('show_on_homepage')->ordered()->get()
            ->map(function (GalleryItem $item) use ($catalog): ?array {
                $url = (string) $item->media_url;
                if ($this->media->ownedKey($url) === null) {
                    return null;
                }
                $match = collect($catalog)->firstWhere('url', $url);
                if ($item->is_video && $match === null) {
                    return null;
                }

                return ['url' => $url, 'type' => $item->type,
                    'poster' => $item->is_video ? $match['poster'] : $url,
                    'title' => $item->titleForLocale(app()->getLocale())];
            })->filter()->sortBy(fn (array $item): int => $item['type'] === 'video' ? 0 : 1)->values();
        foreach ($catalog as $item) {
            if (! in_array($item['url'], $known, true)) {
                $items->push($item);
            }
        }
        $items = $items->unique('url')->take(10)
            ->sortBy(fn (array $item): int => $item['type'] === 'video' ? 0 : 1)->values()->all();

        return ['title' => __('gallery_v2.title'), 'items' => $items];
    }

    /** @return array<int, array{url: string, type: string, poster: string, title: string}> */
    private function catalog(): array
    {
        $facilities = __('home_parity.galeri.items');
        $sources = [
            ['labit', 'it', 0], ['mushalla', 'musola', 1], ['aula', 'aula', 2],
            ['renang', 'renang', 3], ['taekwondo', 'tekwondo', null],
            ['perpustakaan', null, 4], ['psikolog', null, 5], ['parenting', null, 6],
            ['gigianak', null, 7], ['fullday', null, 8],
        ];

        return array_map(static function (array $source) use ($facilities): array {
            [$photo, $video, $index] = $source;
            $poster = (string) config('media.static.school_life.'.$photo);

            return ['url' => $video ? rtrim(config('media.public_url'), '/').'/gallery/media/'.$video.'-v1.mp4' : $poster,
                'type' => $video ? 'video' : 'photo', 'poster' => $poster,
                'title' => $index === null ? __('gallery_v2.taekwondo') : $facilities[$index]['title']];
        }, $sources);
    }
}
