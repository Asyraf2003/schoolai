<?php

namespace App\View\Composers;

use App\View\Presenters\GalleryWallCardPresenter;
use Illuminate\View\View;

final class GalleryWallCardComposer
{
    public function __construct(private GalleryWallCardPresenter $presenter) {}

    public function compose(View $view): void
    {
        $item = $view->getData()['item'] ?? [];

        $view->with($this->presenter->present(is_array($item) ? $item : []));
    }
}
