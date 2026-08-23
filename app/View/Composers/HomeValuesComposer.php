<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class HomeValuesComposer
{
    public function compose(View $view): void
    {
        $schoolValues = $view->getData()['schoolValues'] ?? [];
        $schoolValues = is_array($schoolValues) ? $schoolValues : [];
        $schoolValues['items'] = collect($schoolValues['items'] ?? [])->map(
            static function (mixed $value, int $index): array {
                $value = is_array($value) ? $value : [];
                $value['display_index'] = str_pad(
                    (string) ($index + 1),
                    2,
                    '0',
                    STR_PAD_LEFT,
                );

                return $value;
            }
        )->all();
        $heading = (string) ($schoolValues['heading'] ?? '');

        $view->with([
            'schoolValues' => $schoolValues,
            'valuesHeading' => $heading,
            'valuesHeadingLines' => $schoolValues['heading_lines'] ?? [$heading],
        ]);
    }
}
