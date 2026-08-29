<?php

namespace App\View\Presenters;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;

final class SiteNavbarPresenter
{
    public function __construct(
        private Request $request,
        private Translator $translator,
        private SiteNavbarMenuPresenter $menuPresenter,
    ) {}

    /**
     * @param  array<string, mixed>  $viewData
     * @return array<string, mixed>
     */
    public function present(array $viewData): array
    {
        $siteNavbar = $viewData['siteNavbar']
            ?? $viewData['navbar']
            ?? $this->translator->get('home.navbar');
        $siteNavbar = is_array($siteNavbar) ? $siteNavbar : [];
        $siteNavbar['cta'] = [];

        $siteNavMode = $viewData['siteNavMode']
            ?? ($this->request->routeIs('home') ? 'home' : 'public');
        $isHomeNav = $siteNavMode === 'home';
        $currentLocale = $this->translator->getLocale();
        $languageItem = $this->languageItem($siteNavbar);
        $menuItems = $this->menuPresenter->present(
            $siteNavbar,
            $languageItem,
            $isHomeNav,
        );

        if (! collect($menuItems)->contains(
            fn (array $item): bool => ($item['type'] ?? null) === 'language'
        )) {
            $menuItems[] = $languageItem;
        }

        $logo = is_array($siteNavbar['logo'] ?? null)
            ? $siteNavbar['logo']
            : [];
        $logoImageUrl = $logo['image_url'] ?? null;

        if (empty($logoImageUrl) && ! empty($logo['image'])) {
            $logoImageUrl = asset(ltrim((string) $logo['image'], '/'));
        }

        $logoLabel = trim((string) (
            ($logo['line_1'] ?? $this->translator->get('pages.common.school_name'))
            .' '.($logo['line_2'] ?? '')
        ));
        $megaMediaUrl = $siteNavbar['mega_media_url']
            ?? config('media.static.hero_school');
        $megaMediaAlt = $siteNavbar['mega_media_alt'] ?? $logoLabel;

        return [
            'siteNavbar' => $siteNavbar,
            'isHomeNav' => $isHomeNav,
            'currentLocale' => $currentLocale,
            'languageItem' => $languageItem,
            'menuItems' => $this->decorateItems(
                $menuItems,
                $isHomeNav,
                $currentLocale,
                $megaMediaUrl,
                $megaMediaAlt,
            ),
            'logo' => $logo,
            'logoImageUrl' => $logoImageUrl,
            'logoHref' => $isHomeNav
                ? ($logo['href'] ?? '#beranda')
                : route('home'),
            'logoLabel' => $logoLabel,
            'showCta' => ! empty($siteNavbar['cta']),
            'languageModalTitle' => $this->translator->get(
                'shared.navbar.language_modal.title'
            ),
            'languageModalClose' => $this->translator->get(
                'shared.navbar.language_modal.close'
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $siteNavbar
     * @return array<string, mixed>
     */
    private function languageItem(array $siteNavbar): array
    {
        $languageItem = collect($siteNavbar['items'] ?? [])->first(
            fn (mixed $item): bool => is_array($item)
                && ($item['type'] ?? null) === 'language'
        );

        if (! is_array($languageItem)) {
            $languageItem = [
                'label' => $this->translator->get('pages.common.nav.language'),
                'type' => 'language',
            ];
        }

        $options = $this->translator->get('shared.navbar.languages');
        $languageItem['options'] = is_array($options) ? $options : [];

        return $languageItem;
    }

    /**
     * @param  array<int, array<string, mixed>>  $menuItems
     * @return array<int, array<string, mixed>>
     */
    private function decorateItems(
        array $menuItems,
        bool $isHomeNav,
        string $currentLocale,
        string $megaMediaUrl,
        string $megaMediaAlt,
    ): array {
        return array_map(function (array $item, int $index) use (
            $isHomeNav,
            $currentLocale,
            $megaMediaUrl,
            $megaMediaAlt,
        ): array {
            $isLanguageItem = ($item['type'] ?? null) === 'language';
            $routePatterns = is_array($item['route_patterns'] ?? null)
                ? $item['route_patterns']
                : [];
            $isActiveRoute = $routePatterns !== []
                && $this->request->routeIs(...$routePatterns);

            return array_merge($item, [
                'is_language' => $isLanguageItem,
                'is_login' => ($item['type'] ?? null) === 'login',
                'has_mega_menu' => ! $isLanguageItem
                    && ! empty($item['mega']['links'])
                    && is_array($item['mega']['links']),
                'is_active_route' => $isActiveRoute,
                'is_active' => $isActiveRoute
                    || ($isHomeNav && $index === 0 && ! $isLanguageItem),
                'desktop_panel_id' => 'navMegaPanel-'.$index,
                'mobile_panel_id' => 'mobileNavMegaPanel-'.$index,
                'current_option' => $isLanguageItem
                    ? collect($item['options'] ?? [])->firstWhere(
                        'locale',
                        $currentLocale,
                    )
                    : null,
                'mega_media_url' => $item['mega']['media_url']
                    ?? $megaMediaUrl,
                'mega_media_alt' => $item['mega']['media_alt']
                    ?? $megaMediaAlt,
            ]);
        }, $menuItems, array_keys($menuItems));
    }
}
