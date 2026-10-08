<?php

namespace App\View\Presenters;

final class LandingValuesPresenter
{
    /** @return array<string, mixed> */
    public function present(): array
    {
        $content = __('home.nilai_sekolah');
        $content['items'] = array_map(
            static fn (array $item, int $index): array => array_replace($item, [
                'display_index' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            ]),
            $content['items'],
            array_keys($content['items']),
        );

        return $content;
    }
}
