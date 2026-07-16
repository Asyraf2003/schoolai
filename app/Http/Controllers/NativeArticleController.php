<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;

final class NativeArticleController extends Controller
{
    public function show(Article $article): View
    {
        abort_unless($article->isNative() && $article->isPubliclyVisibleNow(), 404);

        $locale = app()->getLocale();
        $content = $article->contentForLocale($locale);
        $wordCount = max(1, $article->word_count);

        return view('pages.artikel-native', [
            'article' => $article,
            'articleTitle' => $article->titleForLocale($locale),
            'articleSubtitle' => $article->subtitleForLocale($locale),
            'articleDescription' => $article->descriptionForLocale($locale),
            'articleContent' => $content,
            'readingMinutes' => max(1, (int) ceil($wordCount / 220)),
        ]);
    }
}
