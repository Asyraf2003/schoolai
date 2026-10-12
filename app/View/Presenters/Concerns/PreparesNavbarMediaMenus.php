<?php

namespace App\View\Presenters\Concerns;

trait PreparesNavbarMediaMenus
{
    /** @param array<string, mixed> $item
     * @param  array<string, mixed>  $copy
     * @return array<string, mixed>
     */
    private function prepareGalleryItem(array $item, array $copy, callable $homeAnchor, string $mediaUrl): array
    {
        $item['label'] = $this->translator->get('shared.navbar.labels.gallery');
        $item['href'] = route('galeri');
        $item['route_patterns'] = ['galeri'];
        $item['mega'] = array_replace($copy, ['media_url' => $mediaUrl, 'media_alt' => $copy['title'] ?? '',
            'links' => [['label' => $item['label'], 'href' => $homeAnchor('#galeri')]]]);

        return $item;
    }

    /** @param array<string, mixed> $item
     * @param  array<string, mixed>  $copy
     * @return array<string, mixed>
     */
    private function prepareArticleItem(array $item, array $copy, callable $homeAnchor, string $mediaUrl): array
    {
        $item['label'] = trim((string) ($item['label'] ?? '')) ?: $this->translator->get('shared.navbar.labels.articles');
        $item['href'] = route('artikel');
        $item['route_patterns'] = ['artikel', 'artikel.native'];
        $item['mega'] = array_replace($copy, ['media_url' => $mediaUrl, 'media_alt' => $copy['title'] ?? '',
            'links' => [['label' => $item['label'], 'href' => route('artikel')]]]);

        return $item;
    }
}
