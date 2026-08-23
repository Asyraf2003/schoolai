<?php

namespace App\View\Composers;

use App\View\Presenters\HomeKineticLinePresenter;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class HomePageComposer
{
    public function __construct(
        private Translator $translator,
        private HomeKineticLinePresenter $kineticLinePresenter,
    ) {}

    public function compose(View $view): void
    {
        $content = $this->translator->get('home_program');
        $words = collect(is_array($content) ? ($content['kinetic_words'] ?? []) : [])
            ->filter(
                static fn (mixed $word): bool => is_string($word)
                    && trim($word) !== ''
            )
            ->values();

        $view->with('programValuesKineticLines', $this->kineticLinePresenter->present(
            $words,
            8,
            true,
        ));
    }
}
