<?php

namespace App\Http\Controllers;

use App\View\Presenters\LandingHeroPresenter;
use App\View\Presenters\SiteNavbarPresenter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;

final class HomeController extends Controller
{
    public function __invoke(LandingHeroPresenter $hero, SiteNavbarPresenter $navbar): View
    {
        App::setLocale('en');

        return view('landing.index', [
            'hero' => $hero->present(),
            'navigation' => $navbar->present(['siteNavMode' => 'home']),
        ]);
    }
}
