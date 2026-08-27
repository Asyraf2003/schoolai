<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class HomeHeroComposer
{
    public function compose(View $view): void
    {
        $hero = $view->getData()['hero'] ?? [];
        $hero = is_array($hero) ? $hero : [];
        $heroSlides = collect($hero['slides'] ?? [])
            ->filter(static fn (mixed $slide): bool => is_array($slide))
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
