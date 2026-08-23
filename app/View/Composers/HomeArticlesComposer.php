<?php

namespace App\View\Composers;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class HomeArticlesComposer
{
    public function __construct(private Translator $translator) {}

    public function compose(View $view): void
    {
        $section = $view->getData()['articlesSection'] ?? [];
        $section = is_array($section) ? $section : [];
        $items = collect($section['items'] ?? [])->take(5)->values()->map(
            static function (mixed $article, int $index): array {
                $article = is_array($article) ? $article : [];
                $article['display_issue'] = $article['issue'] ?? str_pad(
                    (string) ($index + 1),
                    2,
                    '0',
                    STR_PAD_LEFT,
                );

                return $article;
            }
        );
        $cta = is_array($section['cta'] ?? null) ? $section['cta'] : [];
        $displayHeading = $this->translator->get(
            'home_presentation.article.display_heading'
        );

        $view->with([
            'articleHeading' => (string) ($section['title'] ?? ''),
            'articleDescription' => (string) ($section['subtitle'] ?? ''),
            'articleItems' => $items,
            'openingArticle' => $items->first(),
            'articleCta' => $cta,
            'articleDisplayHeading' => $displayHeading,
            'articleClosingHeading' => $this->translator->get(
                'home_presentation.article.closing_heading'
            ),
            'articleOpeningHref' => trim((string) ($cta['href'] ?? '')),
            'articleOpeningLabel' => trim((string) (
                $cta['label'] ?? $displayHeading
            )),
        ]);
    }
}
