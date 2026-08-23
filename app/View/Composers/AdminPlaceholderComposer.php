<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class AdminPlaceholderComposer
{
    public function compose(View $view): void
    {
        $adminPageKey = (string) ($view->getData()['adminPageKey'] ?? 'dashboard');
        $adminPage = __('admin.pages.'.$adminPageKey);

        if (! is_array($adminPage)) {
            $adminPageKey = 'dashboard';
            $adminPage = __('admin.pages.dashboard');
        }

        $view->with([
            'adminPageKey' => $adminPageKey,
            'adminPage' => $adminPage,
            'simpleText' => $adminPageKey === 'dashboard' ? 'ini dashboard' : 'ini '.strtolower($adminPageKey),
        ]);
    }
}
