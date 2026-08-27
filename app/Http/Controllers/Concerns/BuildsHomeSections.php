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
}
