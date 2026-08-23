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
            ['x' => -0.9, 'fallback' => '#feca4f', 'accent' => '#feca4f', 'background' => '#fffaf0', 'blob1' => '#ffdf94', 'blob2' => '#fce7c4'],
            ['x' => 0.8, 'fallback' => '#80455a', 'accent' => '#80455a', 'background' => '#fffaf0', 'blob1' => '#d29a41', 'blob2' => '#bb96af'],
            ['x' => -0.7, 'fallback' => '#fa7b71', 'accent' => '#fa7b71', 'background' => '#5f81ab', 'blob1' => '#f88b8d', 'blob2' => '#cfbbdd'],
            ['x' => 1, 'fallback' => '#3c72c6', 'accent' => '#3c72c6', 'background' => '#5b9bc2', 'blob1' => '#ffaa00', 'blob2' => '#00e1ff'],
            ['x' => -0.7, 'fallback' => '#fdd895', 'accent' => '#fdd895', 'background' => '#7d936e', 'blob1' => '#fdd895', 'blob2' => '#a5b599'],
        ];
    }
}
