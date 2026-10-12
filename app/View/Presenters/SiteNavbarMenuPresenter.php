<?php

namespace App\View\Presenters;

use App\Services\PpdbAccess;
use App\View\Presenters\Concerns\PreparesNavbarMediaMenus;
use Illuminate\Contracts\Translation\Translator;

final class SiteNavbarMenuPresenter
{
    use PreparesNavbarMediaMenus;

    public function __construct(private Translator $translator, private PpdbAccess $ppdb) {}

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

        foreach ($menuItems as &$item) {
            if (($item['type'] ?? null) === 'language') {
                continue;
            }

            $publicRoute = collect(['artikel', 'galeri'])->first(fn (string $route): bool => in_array($route, $item['route_patterns'] ?? [], true));
            $item['href'] = $publicRoute ? route($publicRoute) : '#';
            if (is_array($item['mega']['links'] ?? null)) {
                foreach ($item['mega']['links'] as &$link) {
                    $link['href'] = $item['href'];
                }
                unset($link);
            }
        }
        unset($item);

        $ppdbItem = [
            'label' => $this->translator->get('pages.common.nav.ppdb'),
            'type' => 'ppdb',
            'href' => $this->ppdb->current()->publicRegistrationUrl(),
            'route_patterns' => ['ppdb'],
        ];
        $loginItem = [
            'label' => $this->translator->get('app.auth.navigation.login'),
            'type' => 'login',
            'href' => auth()->check() ? route('dashboard') : route('portal.login'),
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
            array_values(array_filter([$ppdbItem, $loginItem], fn (array $item): bool => ! empty($item['href']))),
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
}
