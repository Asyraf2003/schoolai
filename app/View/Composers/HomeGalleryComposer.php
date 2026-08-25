<?php

namespace App\View\Composers;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class HomeGalleryComposer
{
    public function __construct(private Translator $translator) {}

    public function compose(View $view): void
    {
        if ($view->name() === 'home.sections.gallery') {
            $view->with(
                'galleryHeading',
                $this->translator->get('home_presentation.gallery_heading')
            );

            return;
        }

        $gallerySection = $view->getData()['gallerySection'] ?? [];
        $gallerySection = is_array($gallerySection) ? $gallerySection : [];
        $presets = $this->depthPresets();
        $items = collect($gallerySection['items'] ?? [])->values()->map(
            static function (mixed $item, int $index) use ($presets): array {
                $item = is_array($item) ? $item : [];

                return array_replace($item, [
                    'thumbnail_url' => (string) ($item['thumbnail_url'] ?? ''),
                    'title' => (string) ($item['title'] ?? ''),
                    'caption' => (string) ($item['caption'] ?? ''),
                    'preset' => $presets[$index % count($presets)],
                ]);
            }
        );
        $cta = is_array($gallerySection['cta'] ?? null)
            ? $gallerySection['cta']
            : [];
        $hasCta = trim((string) ($cta['href'] ?? '')) !== ''
            && trim((string) ($cta['label'] ?? '')) !== '';
        $endSteps = $hasCta ? 1 : 0;

        $view->with([
            'depthItems' => $items,
            'depthCta' => $cta,
            'hasDepthCta' => $hasCta,
            'galleryMoreLabel' => $this->translator->get('home_presentation.gallery_more'),
            'depthEndSteps' => $endSteps,
            'depthJourneyCount' => max(1, $items->count() + $endSteps),
            'depthClosingMedia' => $items->slice(max(0, $items->count() - 2))->values(),
            'depthClosingCopy' => trim((string) (
                $gallerySection['section_subtitle']
                ?? $gallerySection['subtitle']
                ?? ''
            )),
        ]);
    }

    /** @return array<int, array<string, float|string>> */
    private function depthPresets(): array
    {
        return [
            ['x' => 0.9, 'fallback' => '#d8b96f', 'accent' => '#d8b96f', 'background' => '#6f9b72', 'blob1' => '#a9c2a0', 'blob2' => '#d7e3cf'],
            ['x' => -0.9, 'fallback' => '#9b684f', 'accent' => '#9b684f', 'background' => '#c6ad78', 'blob1' => '#ead9ae', 'blob2' => '#b88e61'],
            ['x' => 0.9, 'fallback' => '#66849b', 'accent' => '#66849b', 'background' => '#91a8b3', 'blob1' => '#cbd8dc', 'blob2' => '#6f8fa0'],
            ['x' => -0.9, 'fallback' => '#9b6654', 'accent' => '#9b6654', 'background' => '#b98770', 'blob1' => '#dec0ad', 'blob2' => '#99634f'],
            ['x' => 0.9, 'fallback' => '#788b61', 'accent' => '#788b61', 'background' => '#9eaa82', 'blob1' => '#d4dabd', 'blob2' => '#71805b'],
        ];
    }
}
