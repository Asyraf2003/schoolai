<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class AdminGalleryShowComposer
{
    public function compose(View $view): void
    {
        $view->with('page', __('admin.gallery'));
    }
}
