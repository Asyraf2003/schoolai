<?php

namespace App\Http\Controllers\Concerns;

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

        $gallery['items'] = $this->latestGalleryItems(6);
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

        $backgrounds = array_values(array_filter([
            $this->publicAssetUrl('media/home/3.png'),
            $this->publicAssetUrl('media/home/10.png'),
            $this->publicAssetUrl('media/home/6.png'),
            $this->publicAssetUrl('media/home/12.png'),
        ]));

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
