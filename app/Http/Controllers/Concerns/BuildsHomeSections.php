<?php

namespace App\Http\Controllers\Concerns;

use App\Models\GalleryItem;

trait BuildsHomeSections
{
    private function navbarData(): array
    {
        $navbar = $this->homeSection('navbar');

        if ($navbar === []) {
            return [];
        }

        $navbar['logo']['image_url'] = $this->publicAssetUrl($navbar['logo']['image'] ?? null);
        $navbar['cta'] = [];

        return $navbar;
    }

    private function gallerySectionData(): array
    {
        $gallery = $this->homeSection('galeri');

        if ($gallery === []) {
            return ['items' => []];
        }

        $isFacilities = ($gallery['source'] ?? null) === 'facilities';

        if ($isFacilities) {
            $databaseItems = $this->latestGalleryItems(GalleryItem::MAX_HOMEPAGE_ITEMS);

            $gallery['items'] = $databaseItems !== []
                ? $databaseItems
                : collect($gallery['items'] ?? [])
                    ->filter(static fn (mixed $item): bool => is_array($item))
                    ->values()
                    ->all();
            $gallery['cta'] = [
                'label' => (string) __('home_presentation.gallery_more'),
                'href' => route('galeri'),
            ];

            return $gallery;
        }

        $gallery['items'] = $this->latestGalleryItems(GalleryItem::MAX_HOMEPAGE_ITEMS);
        if (isset($gallery['cta']) && is_array($gallery['cta'])) {
            $gallery['cta']['href'] = route('galeri');
        }

        return $gallery;
    }

    private function testimonialSectionData(): array
    {
        $section = __('testimonials');

        if (! is_array($section)) {
            return ['rows' => []];
        }

        $backgrounds = array_values(array_filter(
            config('media.static.testimonials', []),
            static fn (mixed $url): bool => is_string($url) && trim($url) !== '',
        ));

        if ($backgrounds === [] || ! isset($section['rows']) || ! is_array($section['rows'])) {
            return $section;
        }

        $cardIndex = 0;

        foreach ($section['rows'] as &$row) {
            if (! is_array($row) || ! isset($row['cards']) || ! is_array($row['cards'])) {
                continue;
            }

            foreach ($row['cards'] as &$card) {
                if (! is_array($card)) {
                    continue;
                }

                $card['background_url'] = $backgrounds[$cardIndex % count($backgrounds)];
                $cardIndex++;
            }

            unset($card);
        }

        unset($row);

        return $section;
    }
}
