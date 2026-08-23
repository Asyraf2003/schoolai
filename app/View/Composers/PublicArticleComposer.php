<?php

namespace App\View\Composers;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class PublicArticleComposer
{
    public function __construct(private Translator $translator) {}

    public function compose(View $view): void
    {
        $viewData = $view->getData();
        $page = $viewData['page'] ?? $this->translator->get('pages.artikel');
        $page = is_array($page) ? $page : [];
        $articles = collect($viewData['articles'] ?? [])->values()->map(
            static function (mixed $article, int $index): array {
                $article = is_array($article) ? $article : [];
                $article['number'] = $article['number'] ?? str_pad(
                    (string) ($index + 1),
                    2,
                    '0',
                    STR_PAD_LEFT,
                );

                return $article;
            }
        )->all();
        $categories = $viewData['categories'] ?? [];

        $view->with([
            'page' => $page,
            'hero' => is_array($page['hero'] ?? null) ? $page['hero'] : [],
            'articleItems' => $articles,
            'categoryItems' => is_array($categories) ? $categories : [],
            'readLabel' => $this->translator->get('pages.common.read_more'),
        ]);
    }
}
