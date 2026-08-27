<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsHomeArticlesAndGallery;
use App\Http\Controllers\Concerns\BuildsHomeHero;
use App\Http\Controllers\Concerns\BuildsHomePage;
use App\Http\Controllers\Concerns\BuildsHomeSections;
use App\Http\Controllers\Concerns\NormalizesHomeGallery;
use App\Http\Controllers\Concerns\NormalizesHomeMedia;

final class HomeController extends Controller
{
    use BuildsHomeArticlesAndGallery;
    use BuildsHomeHero;
    use BuildsHomePage;
    use BuildsHomeSections;
    use NormalizesHomeGallery;
    use NormalizesHomeMedia;
}
