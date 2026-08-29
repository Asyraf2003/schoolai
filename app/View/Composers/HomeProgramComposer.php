<?php

namespace App\View\Composers;

use App\View\Presenters\HomeKineticLinePresenter;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class HomeProgramComposer
{
    /** @var array<int, string> */
    private const HOMEPAGE_FEATURED_CODES = [
        'TQ',
        'KH',
        'LC',
        'SJ',
        'TS',
        'IT',
        'FD',
        'SC',
    ];

    /** @var array<string, array{0: string, 1: string}> */
    private const PROGRAM_MEDIA = [
        'TQ' => ['tahfiz', 'center 44%'],
        'KH' => ['pidato', 'center 45%'],
        'LC' => ['aula', 'center 46%'],
        'SJ' => ['solatjamaah', 'center 46%'],
        'TS' => ['taekwondo', 'center 45%'],
        'IT' => ['labit', 'center 45%'],
        'FD' => ['fullday', 'center 45%'],
        'SC' => ['perpustakaan', 'center 45%'],
    ];

    public function __construct(
        private Translator $translator,
        private HomeKineticLinePresenter $kineticLinePresenter,
    ) {}

    public function compose(View $view): void
    {
        $content = $this->translator->get('home_program');
        $content = is_array($content) ? $content : [];
        $items = collect($content['items'] ?? [])
            ->filter(
                static fn (mixed $item): bool => is_array($item)
                    && in_array(
                        (string) ($item['code'] ?? ''),
                        self::HOMEPAGE_FEATURED_CODES,
                        true,
                    )
            )
            ->values()
            ->map(static function (mixed $item): array {
                $item = is_array($item) ? $item : [];
                $code = (string) ($item['code'] ?? '');
                $titleLength = Str::length(trim((string) ($item['title'] ?? '')));

                return array_replace($item, [
                    'detail_id' => 'program-detail-'.Str::lower($code),
                    'media' => self::mediaFor($code),
                    'title_scale' => $titleLength <= 12
                        ? 'short'
                        : ($titleLength <= 20 ? 'medium' : 'long'),
                ]);
            });
        $words = collect($content['kinetic_words'] ?? [])
            ->filter(
                static fn (mixed $word): bool => is_string($word)
                    && trim($word) !== ''
            )
            ->values();

        $view->with([
            'programContent' => $content,
            'programItems' => $items,
            'programHeadingLines' => $content['heading_lines']
                ?? [$content['section_label'] ?? ''],
            'programKineticLines' => $this->kineticLinePresenter->present($words, 20),
        ]);
    }

    /** @return array{url: string, position: string} */
    private static function mediaFor(string $code): array
    {
        [$key, $position] = self::PROGRAM_MEDIA[$code] ?? ['aula', 'center center'];

        return [
            'url' => (string) config('media.static.school_life.'.$key),
            'position' => $position,
        ];
    }
}
