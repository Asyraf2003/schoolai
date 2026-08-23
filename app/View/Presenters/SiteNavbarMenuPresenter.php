<?php

namespace App\View\Presenters;

use Illuminate\Contracts\Translation\Translator;

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
                $item = $this->preparePublicItem(
                    $item,
                    $megaCopy['gallery'] ?? [],
                    'galeri',
                    'media/hero/activity.jpg',
                );
            } elseif ($index === 3) {
                $item = $this->preparePublicItem(
                    $item,
                    $megaCopy['article'] ?? [],
                    'artikel',
                    'media/hero/library.jpg',
                );
                $item['route_patterns'] = [
                    'artikel',
                    'artikel.detail',
                    'artikel.native',
                ];
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
        $item['mega']['media_url'] = asset('media/hero/teaching.jpg');

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
     * @return array<string, mixed>
     */
    private function preparePublicItem(
        array $item,
        array $copy,
        string $routeName,
        string $mediaPath,
    ): array {
        $links = is_array($copy['links'] ?? null) ? $copy['links'] : [];
        $item['href'] = route($routeName);
        $item['route_patterns'] = [$routeName];
        $item['mega'] = array_replace($copy, [
            'toggle_label' => $copy['eyebrow'] ?? '',
            'media_url' => asset($mediaPath),
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
