<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\PpdbSetting;
use App\Models\SiteStatistic;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

final class HomeController extends Controller
{
    use \App\Http\Controllers\Concerns\BuildsHomePage;
    use \App\Http\Controllers\Concerns\BuildsHomeHero;
    use \App\Http\Controllers\Concerns\BuildsHomeSections;
    use \App\Http\Controllers\Concerns\BuildsHomeArticlesAndGallery;
    use \App\Http\Controllers\Concerns\NormalizesHomeGallery;
    use \App\Http\Controllers\Concerns\NormalizesHomeMedia;
}
