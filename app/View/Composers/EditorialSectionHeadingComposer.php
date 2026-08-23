<?php

namespace App\View\Composers;

use Illuminate\View\View;

final class EditorialSectionHeadingComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $title = trim((string) ($data['title'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        $lineOne = trim((string) ($data['lineOne'] ?? ''));
        $lineTwo = trim((string) ($data['lineTwo'] ?? ''));

        if ($lineOne === '' && $title !== '') {
            $words = preg_split('/\s+/u', $title, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $split = max(1, (int) ceil(count($words) / 2));
            $lineOne = implode(' ', array_slice($words, 0, $split));
            $lineTwo = implode(' ', array_slice($words, $split));
        }

        $descriptionWords = preg_split(
            '/\s+/u',
            $description,
            -1,
            PREG_SPLIT_NO_EMPTY,
        ) ?: [];
        $descriptionSplit = max(1, (int) ceil(count($descriptionWords) / 3));

        $view->with([
            'headingTitle' => $title,
            'headingDescription' => $description,
            'headingDomId' => trim((string) ($data['headingId'] ?? 'section-heading')),
            'headingExtraClass' => trim((string) ($data['className'] ?? '')),
            'headingLineOne' => $lineOne,
            'headingLineTwo' => $lineTwo,
            'headingLineThree' => trim((string) ($data['lineThree'] ?? '')),
            'descriptionLines' => array_values(array_filter(array_map(
                static fn (array $words): string => implode(' ', $words),
                array_chunk($descriptionWords, $descriptionSplit),
            ))),
        ]);
    }
}
