<?php

namespace App\Http\Controllers;

use App\View\Presenters\LandingHeroPresenter;
use App\View\Presenters\SiteNavbarPresenter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

final class HomeController extends Controller
{
    public function __invoke(LandingHeroPresenter $hero, SiteNavbarPresenter $navbar, Request $request): View
    {
        if (! $request->session()->has('locale') && ! $request->hasCookie('site_locale')) {
            App::setLocale('en');
        }

        return view('landing.index', [
            'hero' => $hero->present(),
            'navigation' => $navbar->present(['siteNavMode' => 'home']),
        ]);
    }
}
