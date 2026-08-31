<?php

namespace App\View\Presenters;

use App\Models\Article;
use App\Models\GalleryPageSection;
use App\Support\PublicUrl;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Schema;

final class SiteNavbarMenuPresenter
{
    public function __construct(private Translator $translator) {}

    /**
     * @param  array<string, mixed>  $siteNavbar
     * @param  array<string, mixed>  $languageItem
     * @return array<int, array<string, mixed>>
     */
    public function present(
        array $siteNavbar,
        array $languageItem,
        bool $isHomeNav,
    ): array {
        $homeUrl = route('home');
        $homeAnchor = static fn (string $anchor): string => $isHomeNav
            ? $anchor
            : $homeUrl.$anchor;
        $menuItems = array_values(
            is_array($siteNavbar['items'] ?? null)
                ? $siteNavbar['items']
                : []
        );
        $megaCopy = $this->translator->get('shared.navbar.mega');
        $megaCopy = is_array($megaCopy) ? $megaCopy : [];

        foreach ($menuItems as $index => &$item) {
            if (($item['type'] ?? null) === 'language') {
                $item = array_replace($item, $languageItem);

                continue;
            }

            if ($index === 0) {
                $item['href'] = $isHomeNav ? '#beranda' : $homeUrl;
                $item['route_patterns'] = ['home'];
            } elseif ($index === 1) {
                $this->prepareEducationItem($item, $homeAnchor);
            } elseif ($index === 2) {
                $item = $this->prepareGalleryItem(
                    $item,
                    $megaCopy['gallery'] ?? [],
                    $homeAnchor,
                    (string) config('media.static.navigation.gallery'),
                );
            } elseif ($index === 3) {
                $item = $this->prepareArticleItem(
                    $item,
                    $megaCopy['article'] ?? [],
                    $homeAnchor,
                    (string) config('media.static.navigation.article'),
                );
            } elseif ($index === 4) {
                $item['href'] = $homeAnchor('#kontak');
            }
        }
        unset($item);

        $loginItem = [
            'label' => $this->translator->get('app.auth.navigation.login'),
            'type' => 'login',
            'href' => route('portal.login'),
            'route_patterns' => [
                'portal.login',
                'login',
                'guru.login',
                'murid.login',
            ],
        ];
        $languageIndex = collect($menuItems)->search(
            fn (array $item): bool => ($item['type'] ?? null) === 'language'
        );
        array_splice(
            $menuItems,
            $languageIndex === false ? count($menuItems) : $languageIndex,
            0,
            [$loginItem],
        );

        return $menuItems;
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  callable(string): string  $homeAnchor
     */
    private function prepareEducationItem(array &$item, callable $homeAnchor): void
    {
        $item['href'] = $homeAnchor('#program');
        $item['route_patterns'] = [];
        $item['mega']['media_url'] = config(
            'media.static.navigation.education'
        );

        if (! isset($item['mega']['links']) || ! is_array($item['mega']['links'])) {
            return;
        }

        $item['mega']['links'] = array_values(array_filter(
            $item['mega']['links'],
            fn (array $link): bool => ($link['href'] ?? null) !== '/ppdb',
        ));

        foreach ($item['mega']['links'] as &$link) {
            $href = (string) ($link['href'] ?? '');
            $link['href'] = str_starts_with($href, '#')
                ? $homeAnchor($href)
                : $href;
        }
        unset($link);
    }

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
            $facilityLabel = match ($this->translator->getLocale()) {
                'en' => 'Facilities',
                'ar' => 'المرافق',
                default => 'Fasilitas',
            };
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

        $item['label'] = match ($this->translator->getLocale()) {
            'en' => 'Gallery',
            'ar' => 'المعرض',
            default => 'Galeri',
        };
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
            $articleLabel = match ($this->translator->getLocale()) {
                'en' => 'Articles',
                'ar' => 'المقالات',
                default => 'Artikel',
            };
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
                ),
                $links,
            ),
        ]);

        return $item;
    }
}
