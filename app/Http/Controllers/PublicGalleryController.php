<?php

namespace App\Http\Controllers;

use App\View\Presenters\SiteNavbarPresenter;
use Illuminate\View\View;

final class PublicGalleryController extends Controller
{
    public function __invoke(SiteNavbarPresenter $navbar): View
    {
        return view('pages.coming-soon', [
            'pageKey' => 'galeri',
            'pageTitle' => __('pages.common.nav.galeri'),
            'navigation' => $navbar->present(['siteNavMode' => 'public']),
        ]);
    }
}
