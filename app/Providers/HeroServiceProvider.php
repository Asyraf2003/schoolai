<?php

namespace App\Providers;

use App\Http\Controllers\Admin\HeroSlideAdminController;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\PpdbSetting;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

final class HeroServiceProvider extends ServiceProvider
{
    use \App\Providers\Concerns\RegistersHeroIntegration;
    use \App\Providers\Concerns\InjectsDatabaseHero;
    use \App\Providers\Concerns\BuildsArticleHeroSlides;
    use \App\Providers\Concerns\NormalizesHeroPresentation;


























}

