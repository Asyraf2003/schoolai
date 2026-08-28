<?php

namespace App\View\Composers;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class HomeArticlesComposer
{
    public function __construct(private Translator $translator) {}

    public function compose(View $view): void
    {
        $preview = $this->translator->get('home_article_preview');
        $preview = is_array($preview) ? $preview : [];
        $media = [
            'media/home/2.png',
            'media/home/5.png',
            'media/home/11.png',
            'media/home/12.png',
        ];
        $items = collect($preview['items'] ?? [])
            ->take(4)
            ->values()
            ->map(static function (mixed $item, int $index) use ($media): array {
                $item = is_array($item) ? $item : [];
                $item['media_url'] = asset($media[$index] ?? $media[0]);
                $item['href'] = route('artikel');

                return $item;
            })
            ->all();

        $view->with([
            'articleHeadingLines' => array_values($preview['heading_lines'] ?? []),
            'articleDescription' => (string) ($preview['description'] ?? ''),
            'articleItems' => $items,
            'articleReadLabel' => (string) ($preview['read_label'] ?? ''),
            'articleCtaLabel' => (string) ($preview['cta_label'] ?? ''),
        ]);
    }
}
