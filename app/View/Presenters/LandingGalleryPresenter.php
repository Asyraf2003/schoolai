<?php

namespace App\View\Presenters;

final class LandingGalleryPresenter
{
    /** @return array{title: string, items: array<int, array<string, mixed>>} */
    public function present(): array
    {
        $facilities = __('home_parity.galeri.items');
        $sources = [
            ['labit', 'it', 0], ['mushalla', 'musola', 1], ['aula', 'aula', 2],
            ['renang', 'renang', 3], ['taekwondo', 'tekwondo', null],
            ['perpustakaan', null, 4], ['psikolog', null, 5], ['parenting', null, 6],
            ['gigianak', null, 7], ['fullday', null, 8],
        ];
        $items = array_map(static function (array $source) use ($facilities): array {
            [$photo, $video, $index] = $source;
            $poster = (string) config('media.static.school_life.'.$photo);

            return ['url' => $video ? rtrim(config('media.public_url'), '/').'/gallery/media/'.$video.'-v1.mp4' : $poster,
                'type' => $video ? 'video' : 'photo', 'poster' => $poster,
                'title' => $index === null ? __('gallery_v2.taekwondo') : $facilities[$index]['title']];
        }, $sources);

        return ['title' => __('gallery_v2.title'), 'items' => $items];
    }
}
