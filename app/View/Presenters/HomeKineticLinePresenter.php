<?php

namespace App\View\Presenters;

use Illuminate\Support\Collection;

final class HomeKineticLinePresenter
{
    /** @return Collection<int, string> */
    public function present(
        Collection $words,
        int $lineCount,
        bool $removeEmptyLines = false,
    ): Collection {
        $wordCount = max(1, $words->count());
        $lines = collect(range(0, $lineCount - 1))->map(
            static function (int $line) use ($words, $wordCount): string {
                $lineWords = collect(range(0, 7))
                    ->map(
                        static fn (int $offset): mixed => $words[
                            ($line * 3 + $offset) % $wordCount
                        ] ?? ''
                    )
                    ->filter()
                    ->implode(' ');

                return trim($lineWords.' '.$lineWords);
            }
        );

        return $removeEmptyLines ? $lines->filter()->values() : $lines;
    }
}
