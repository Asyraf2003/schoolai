<?php

namespace App\View\Composers;

use App\View\Presenters\HomeKineticLinePresenter;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class HomeProgramComposer
{
    public function __construct(
        private Translator $translator,
        private HomeKineticLinePresenter $kineticLinePresenter,
    ) {}

    public function compose(View $view): void
    {
        $content = $this->translator->get('home_program');
        $content = is_array($content) ? $content : [];
        $media = $this->media();
        $items = collect($content['items'] ?? [])->values()->map(
            static function (mixed $item, int $index) use ($media): array {
                $item = is_array($item) ? $item : [];
                $titleLength = Str::length(trim((string) ($item['title'] ?? '')));

                return array_replace($item, [
                    'detail_id' => 'program-detail-'.Str::lower((string) ($item['code'] ?? '')),
                    'media' => $media[$index % count($media)],
                    'title_scale' => $titleLength <= 12
                        ? 'short'
                        : ($titleLength <= 20 ? 'medium' : 'long'),
                ]);
            }
        );
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

    /** @return array<int, array{url: string, position: string}> */
    private function media(): array
    {
        return [
            ['url' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 42%'],
            ['url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 46%'],
            ['url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 40%'],
            ['url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 48%'],
            ['url' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 44%'],
            ['url' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 50%'],
        ];
    }
}
