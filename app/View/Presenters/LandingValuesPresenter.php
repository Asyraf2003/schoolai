<?php

namespace App\View\Presenters;

final class LandingValuesPresenter
{
    /** @return array{heading: string, heading_lines: array<int, string>} */
    public function present(): array
    {
        return [
            'heading' => __('home.nilai_sekolah.heading'),
            'heading_lines' => __('home.nilai_sekolah.heading_lines'),
        ];
    }
}
