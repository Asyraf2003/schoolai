<?php

namespace App\View\Composers;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HomeArticlesComposer
{
    public function __construct(
        private Translator $translator,
        private Request $request,
    ) {}

    public function compose(View $view): void
    {
        $preview = $this->translator->get('home_article_preview');
        $preview = is_array($preview) ? $preview : [];
        $variants = [
            'a' => 'Stanford Split',
            'b' => 'Stanford Newsroom',
            'c' => 'JIS Panorama',
            'd' => 'Editorial Mosaic',
            'e' => 'Lead + Rail',
            'f' => 'School Journal',
        ];
        $requestedVariant = strtolower(trim(
            (string) $this->request->query('article_variant', 'b'),
        ));
        $variant = array_key_exists($requestedVariant, $variants)
            ? $requestedVariant
            : 'b';
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
            'articleVariant' => $variant,
            'articleVariants' => $variants,
            'articleReviewMode' => app()->environment(['local', 'testing']),
            'articleReviewLabel' => (string) ($preview['review_label'] ?? ''),
            'articleHeadingLines' => array_values($preview['heading_lines'] ?? []),
            'articleDescription' => (string) ($preview['description'] ?? ''),
            'articleItems' => $items,
            'articleReadLabel' => (string) ($preview['read_label'] ?? ''),
            'articleCtaLabel' => (string) ($preview['cta_label'] ?? ''),
        ]);
    }
}
