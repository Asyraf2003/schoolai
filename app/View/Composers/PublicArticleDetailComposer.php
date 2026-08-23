<?php

namespace App\View\Composers;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class PublicArticleDetailComposer
{
    public function __construct(private Translator $translator) {}

    public function compose(View $view): void
    {
        $article = $this->translator->get('pages.artikel_detail');

        $view->with('article', is_array($article) ? $article : []);
    }
}
