<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\PpdbAccess;
use App\View\Presenters\SiteNavbarPresenter;
use Illuminate\View\View;

final class PublicArticleController extends Controller
{
    public function index(SiteNavbarPresenter $navbar): View
    {
        return view('pages.coming-soon', [
            'pageKey' => 'artikel',
            'pageTitle' => __('pages.common.nav.artikel'),
            'navigation' => $navbar->present(['siteNavMode' => 'public']),
        ]);
    }

    public function show(Article $article, PpdbAccess $ppdb): View
    {
        return view('pages.article', ['article' => $article, 'ppdbUrl' => $ppdb->current()->publicRegistrationUrl()]);
    }
}
