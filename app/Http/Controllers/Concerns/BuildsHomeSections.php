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

        $backgrounds = [
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-01/cb44c802-f387-4b57-83f3-c92009c9a4cc.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-02/c424002c-e140-4504-9d0b-0f0d75c13d1e.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-03/cd0a0ef6-4849-4826-b263-060dedf10487.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-04/a60f0c43-cf46-4a3f-b3e9-cb526c9689f8.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-05/91d73274-fa5c-49c3-93cf-1a0cf695550b.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-06/48eb28aa-2032-4c33-a592-87064b42b70c.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-07/d6ebd008-c54c-4e13-8335-22163e1de777.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-08/514f399c-5fc1-4bdf-9618-5a7bae302bfd.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-09/585f4b9b-1901-49b4-b79a-35cd5765131f.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-10/a3d9a518-840e-4cea-aa72-a27933d6f01c.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-11/05c4a3d0-a8d1-4a09-9fcc-c70e143135b2.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-12/d2b187ed-99f3-48b5-8636-3c2f45f1ece7.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-13/1818238e-1d3a-43bd-84b0-250176b40cec.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-14/54d55f92-88d4-4007-9f0f-e9b3b3227d34.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-15/e1892d09-e79f-443d-bf82-f093a68eb120.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-16/3e9774ab-51b9-42d9-b546-f148adaf1e44.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-17/919b885a-f699-48cf-836b-ac009c57f8e6.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-18/f60979b5-9f0a-447f-83c7-f596c4293547.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-19/ee446e68-bde7-4f4d-9d9a-809811dfbf26.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-20/c601d5dc-e7be-4d30-9b60-6d7dd10c926c.webp',
            'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-21/ec808c37-422b-4695-b876-0d9bfd51843a.webp',
        ];

        if (! isset($section['rows']) || ! is_array($section['rows'])) {
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
