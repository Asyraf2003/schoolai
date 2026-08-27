<?php

namespace App\Providers;

use App\Providers\Concerns\BuildsArticleHeroSlides;
use App\Providers\Concerns\InjectsDatabaseHero;
use App\Providers\Concerns\NormalizesHeroPresentation;
use App\Providers\Concerns\RegistersHeroIntegration;
use Illuminate\Support\ServiceProvider;

final class HeroServiceProvider extends ServiceProvider
{
    use BuildsArticleHeroSlides;
    use InjectsDatabaseHero;
    use NormalizesHeroPresentation;
    use RegistersHeroIntegration;
}
