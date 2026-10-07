<?php

namespace App\View\Presenters;

final class LandingAboutPresenter
{
    /** @return array<string, mixed> */
    public function present(): array
    {
        $about = __('home_about');
        foreach ($about['stories'] as $key => &$story) {
            $story['media'] = config('media.about_v2.'.$key);
            $story['open_label'] = __('home_about.open_video', ['story' => $story['label']]);
        }
        unset($story);
        $about['background'] = config('media.static.ornaments.geometry_33');

        return $about;
    }
}
