<?php

namespace App\View\Presenters;

use Illuminate\Support\ViewErrorBag;

final class AdminLanguageTabsPresenter
{
    /** @param array<string, array<mixed>> $valuesByLanguage */
    public function completion(array $valuesByLanguage): array
    {
        return collect($valuesByLanguage)
            ->map(fn (array $values): bool => collect($values)->every(filled(...)))
            ->all();
    }

    /**
     * @param  array<int, string>  $arabicFields
     * @param  array<int, string>  $englishFields
     */
    public function activeLanguage(
        mixed $errors,
        array $arabicFields,
        array $englishFields,
        bool $usesSubmittedErrors = true,
    ): string {
        if (! $usesSubmittedErrors || ! $errors instanceof ViewErrorBag) {
            return 'id';
        }

        if ($errors->hasAny($arabicFields)) {
            return 'ar';
        }

        return $errors->hasAny($englishFields) ? 'en' : 'id';
    }
}
