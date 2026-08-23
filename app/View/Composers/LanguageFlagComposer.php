<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class LanguageFlagComposer
{
    public function compose(View $view): void
    {
        $locale = $view->getData()['locale'] ?? null;
        $flagLocale = in_array($locale, ['id', 'en', 'ar'], true)
            ? $locale
            : 'id';
        $flagImagePath = 'media/home/'.$flagLocale.'.webp';

        $view->with([
            'flagLocale' => $flagLocale,
            'flagImagePath' => $flagImagePath,
            'hasFlagImage' => is_file(public_path($flagImagePath)),
        ]);
    }
}
