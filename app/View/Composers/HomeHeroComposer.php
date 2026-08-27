<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class HomeHeroComposer
{
    public function compose(View $view): void
    {
        $hero = $view->getData()['hero'] ?? [];
        $hero = is_array($hero) ? $hero : [];
        $heroVideoUrl = (string) config('media.homepage_hero_video_url');
        $heroSlides = collect($hero['slides'] ?? [])
            ->take(1)
            ->map(static function (mixed $slide) use ($heroVideoUrl): ?array {
                if (! is_array($slide)) {
                    return null;
                }

                return array_replace($slide, [
                    'type' => 'video',
                    'render_type' => 'video',
                    'media_url' => $heroVideoUrl,
                    'video_mime_type' => 'video/mp4',
                ]);
            })
            ->filter()
            ->values();
        $heroSlideCount = $heroSlides->count();

        $view->with([
            'heroSlides' => $heroSlides,
            'heroSlideCount' => $heroSlideCount,
            'heroStatus' => static fn (int $current): string => strtr(
                (string) ($hero['slide_label'] ?? 'Slide :current / :total'),
                [
                    ':current' => (string) $current,
                    ':total' => (string) $heroSlideCount,
                ],
            ),
        ]);
    }
}
