<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class AdminLayoutComposer
{
    public function compose(View $view): void
    {
        $viewData = $view->getData();

        $view->with([
            'activeAdminPage' => $viewData['activeAdminPage']
                ?? $viewData['adminPageKey']
                ?? 'dashboard',
            'adminMenu' => [
                [
                    'key' => 'dashboard',
                    'label' => __('admin.nav.dashboard'),
                    'route' => 'admin.dashboard',
                ],
                [
                    'key' => 'accounts',
                    'label' => __('admin.nav.accounts'),
                    'route' => 'admin.accounts.index',
                ],
                [
                    'key' => 'ppdb',
                    'label' => __('admin.nav.ppdb'),
                    'route' => 'admin.ppdb',
                ],
                [
                    'key' => 'artikel',
                    'label' => __('admin.nav.artikel'),
                    'route' => 'admin.artikel',
                ],
                [
                    'key' => 'galeri',
                    'label' => __('admin.nav.galery'),
                    'route' => 'admin.galeri',
                ],
            ],
        ]);
    }
}
