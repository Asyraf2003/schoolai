<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\PpdbAccess;
use Illuminate\View\View;

final class PublicArticleController extends Controller
{
    public function index(PpdbAccess $ppdb): View
    {
        return view('pages.articles', ['articles' => Article::latestPublished()->paginate(12), 'ppdbUrl' => $ppdb->current()->publicRegistrationUrl()]);
    }

    public function show(Article $article, PpdbAccess $ppdb): View
    {
        return view('pages.article', ['article' => $article, 'ppdbUrl' => $ppdb->current()->publicRegistrationUrl()]);
    }
}
