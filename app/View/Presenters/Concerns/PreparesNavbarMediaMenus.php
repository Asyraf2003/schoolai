<?php

namespace App\View\Presenters\Concerns;

use App\Models\Article;
use App\Models\GalleryPageSection;
use App\Support\PublicUrl;
use Illuminate\Support\Facades\Schema;

trait PreparesNavbarMediaMenus
{
    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>  $copy
     * @param  callable(string): string  $homeAnchor
     * @return array<string, mixed>
     */
    private function prepareGalleryItem(
        array $item,
        array $copy,
        callable $homeAnchor,
        string $mediaUrl,
    ): array {
        $facilityLabel = trim((string) ($item['label'] ?? ''));

        if ($facilityLabel === '') {
            $facilityLabel = $this->translator->get('shared.navbar.labels.facilities');
        }

        $links = [[
            'label' => $facilityLabel,
            'href' => $homeAnchor('#galeri'),
        ]];

        if (
            Schema::hasTable('gallery_page_sections')
            && Schema::hasTable('gallery_item_gallery_page_section')
            && Schema::hasTable('gallery_items')
        ) {
            $locale = $this->translator->getLocale();
            $sections = GalleryPageSection::query()
                ->where('is_published', true)
                ->whereHas('items', fn ($query) => $query
                    ->where('gallery_items.is_published', true)
                    ->where('gallery_item_gallery_page_section.is_published', true))
                ->inRandomOrder()
                ->limit(3)
                ->get();

            foreach ($sections as $section) {
                $links[] = [
                    'label' => $section->titleForLocale($locale),
                    'href' => route('galeri').'#gallery-section-'.$section->id,
                ];
            }
        }

        $item['label'] = $this->translator->get('shared.navbar.labels.gallery');
        $item['href'] = route('galeri');
        $item['route_patterns'] = ['galeri'];
        $item['mega'] = array_replace($copy, [
            'toggle_label' => $copy['eyebrow'] ?? '',
            'media_url' => $mediaUrl,
            'media_alt' => $copy['title'] ?? '',
            'links' => $links,
        ]);

        return $item;
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>  $copy
     * @param  callable(string): string  $homeAnchor
     * @return array<string, mixed>
     */
    private function prepareArticleItem(
        array $item,
        array $copy,
        callable $homeAnchor,
        string $mediaUrl,
    ): array {
        $articleLabel = trim((string) ($item['label'] ?? ''));

        if ($articleLabel === '') {
            $articleLabel = $this->translator->get('shared.navbar.labels.articles');
        }

        $links = [[
            'label' => $articleLabel,
            'href' => $homeAnchor('#artikel'),
        ]];

        if (Schema::hasTable('articles')) {
            $locale = $this->translator->getLocale();
            $categories = Article::query()
                ->latestPublished()
                ->get()
                ->filter(fn (Article $article): bool => $article->isNative()
                    || PublicUrl::normalize(
                        $article->linkForLocale($locale),
                        ['/admin', '/login', '/auth'],
                    ) !== null)
                ->flatMap(fn (Article $article): array => array_values($article->tags ?? []))
                ->filter(fn (mixed $tag): bool => is_string($tag) && trim($tag) !== '')
                ->map(fn (string $tag): string => trim($tag))
                ->unique(fn (string $tag): string => mb_strtolower($tag))
                ->shuffle()
                ->take(3)
                ->values();

            foreach ($categories as $category) {
                $links[] = [
                    'label' => $category,
                    'href' => route('artikel', ['kategori' => $category]),
                ];
            }
        }

        $item['href'] = route('artikel');
        $item['route_patterns'] = [
            'artikel',
            'artikel.detail',
            'artikel.native',
        ];
        $item['mega'] = array_replace($copy, [
            'toggle_label' => $copy['eyebrow'] ?? '',
            'media_url' => $mediaUrl,
            'media_alt' => $copy['title'] ?? '',
            'links' => $links,
        ]);

        return $item;
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>  $copy
     * @return array<string, mixed>
     */
    private function preparePublicItem(
        array $item,
        array $copy,
        string $routeName,
        string $mediaUrl,
    ): array {
        $links = is_array($copy['links'] ?? null) ? $copy['links'] : [];
        $item['href'] = route($routeName);
        $item['route_patterns'] = [$routeName];
        $item['mega'] = array_replace($copy, [
            'toggle_label' => $copy['eyebrow'] ?? '',
            'media_url' => $mediaUrl,
            'media_alt' => $copy['title'] ?? '',
            'links' => array_map(
                fn (array $link): array => array_merge($link, [
                    'href' => route($routeName, [
                        'kategori' => $link['category'],
                    ]),
                ]),
                $links,
            ),
        ]);

        return $item;
    }
}
