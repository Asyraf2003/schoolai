<?php

namespace App\Models\Concerns;

trait ResolvesLocalizedContent
{
    protected function localizedValue(
        string $locale,
        mixed $indonesian,
        mixed $english,
        mixed $arabic,
        string $indonesianFallback = '',
        string $englishFallback = '',
    ): string {
        $values = match ($locale) {
            'en' => [$english, $indonesian, $englishFallback, $indonesianFallback],
            'ar' => [$arabic, $indonesian, $english, $indonesianFallback, $englishFallback],
            default => [$indonesian, $english, $indonesianFallback, $englishFallback],
        };

        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }
}
