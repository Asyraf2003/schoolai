<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

final class PublicArticleController extends Controller
{
    public function index(): View
    {
        return view('pages.articles', ['articles' => Article::latestPublished()->paginate(12)]);
    }

    public function show(Article $article): View
    {
        return view('pages.article', compact('article'));
    }
}
