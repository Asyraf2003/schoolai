<?php

namespace App\View\Presenters;

use App\Providers\Concerns\BuildsArticleHeroSlides;
use App\Providers\Concerns\InjectsDatabaseHero;
use App\Providers\Concerns\NormalizesHeroPresentation;
use App\Support\HomeHeroPresentation;

final class LandingHeroPresenter
{
    use BuildsArticleHeroSlides;
    use InjectsDatabaseHero;
    use NormalizesHeroPresentation;

    /** @return array<string, mixed> */
    public function present(): array
    {
        $hero = array_replace_recursive(__('home.hero'), __('home_parity.hero'));
        $hero['fallback_image_url'] = config('media.static.hero_school');
        // Homepage V2 shows the opening video only. Existing article placements
        // remain stored for the legacy surface and admin; they are not V2 slides.
        $hero['slides'] = HomeHeroPresentation::decorate(array_values(array_filter([
            $this->openingHeroSlide($hero, app()->getLocale()),
        ])));

        return $hero;
    }
}
