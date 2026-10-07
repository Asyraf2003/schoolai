<?php

namespace App\View\Presenters;

use Illuminate\Support\Str;

final class LandingProgramPresenter
{
    /** @var array<string, array{key: string, position: string}> */
    private const MEDIA = [
        'TQ' => ['key' => 'tahfiz', 'position' => 'center 44%'],
        'KH' => ['key' => 'pidato', 'position' => 'center 45%'],
        'LC' => ['key' => 'aula', 'position' => 'center 46%'],
        'SJ' => ['key' => 'solatjamaah', 'position' => 'center 46%'],
        'TS' => ['key' => 'taekwondo', 'position' => 'center 45%'],
        'IT' => ['key' => 'labit', 'position' => 'center 45%'],
        'FD' => ['key' => 'fullday', 'position' => 'center 45%'],
        'SC' => ['key' => 'perpustakaan', 'position' => 'center 45%'],
    ];

    public function __construct(private HomeKineticLinePresenter $kineticLines) {}

    /** @return array<string, mixed> */
    public function present(): array
    {
        $content = __('home_program');
        $content['items'] = collect($content['items'])
            ->filter(static fn (array $item): bool => isset(self::MEDIA[$item['code']]))
            ->map(static function (array $item): array {
                $media = self::MEDIA[$item['code']];
                $length = Str::length(trim($item['title']));

                return array_replace($item, [
                    'id' => 'program-'.Str::lower($item['code']),
                    'media' => [
                        'url' => config('media.static.school_life.'.$media['key']),
                        'position' => $media['position'],
                    ],
                    'title_scale' => $length <= 12 ? 'short' : ($length <= 20 ? 'medium' : 'long'),
                ]);
            })->values()->all();
        $content['kinetic_lines'] = $this->kineticLines->present(collect($content['kinetic_words']), 20);
        $content['entry_texture'] = config('media.static.ornaments.geometry_33');

        return $content;
    }
}
