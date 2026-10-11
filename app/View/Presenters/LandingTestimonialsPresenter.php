<?php

namespace App\View\Presenters;

final class LandingTestimonialsPresenter
{
    /** @return array<string, mixed> */
    public function present(): array
    {
        $section = __('testimonials');
        $photos = config('media.static.testimonials', []);
        $index = 0;
        foreach ($section['rows'] as &$row) {
            foreach ($row['cards'] as &$card) {
                $card['background_url'] = $photos ? $photos[$index % count($photos)] : '';
                $index++;
            }
            unset($card);
        }
        unset($row);

        return $section;
    }
}
