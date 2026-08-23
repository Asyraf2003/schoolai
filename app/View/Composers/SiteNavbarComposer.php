<?php

namespace App\View\Composers;

use App\View\Presenters\SiteNavbarPresenter;
use Illuminate\View\View;

final class SiteNavbarComposer
{
    public function __construct(private SiteNavbarPresenter $presenter) {}

    public function compose(View $view): void
    {
        $view->with($this->presenter->present($view->getData()));
    }
}
