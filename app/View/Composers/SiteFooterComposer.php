<?php

namespace App\View\Composers;

use Illuminate\Http\Request;
use Illuminate\View\View;

final class SiteFooterComposer
{
    public function __construct(private Request $request) {}

    public function compose(View $view): void
    {
        $homeFooter = __('home.footer');
        $homeFooterParity = __('home_parity.footer');
        $homeFooter = is_array($homeFooter) ? $homeFooter : [];
        $homeFooterParity = is_array($homeFooterParity)
            ? $homeFooterParity
            : [];
        $homeFooter = array_replace_recursive($homeFooter, $homeFooterParity);
        $viewData = $view->getData();
        $siteFooter = $viewData['siteFooter']
            ?? $viewData['footerSection']
            ?? $homeFooter;
        $siteFooter = is_array($siteFooter) ? $siteFooter : [];
        $isHomeFooter = $this->request->routeIs('home');

        $siteFooter['links'] = $this->prepareLinks(
            $siteFooter['links'] ?? [],
            $isHomeFooter,
            true,
        );
        $siteFooter['gallery_links'] = $this->prepareLinks(
            $siteFooter['gallery_links'] ?? [],
            $isHomeFooter,
        );
        $siteFooter['channels'] = $this->prepareChannels(
            $siteFooter['channels'] ?? [],
            $isHomeFooter,
        );
        $siteFooter['partners'] = $this->preparePartners(
            $siteFooter['partners'] ?? [],
            $isHomeFooter,
        );

        if (is_array($siteFooter['brand'] ?? null)) {
            $siteFooter['brand']['resolved_href'] = $this->normalizeHref(
                $siteFooter['brand']['href'] ?? '#beranda',
                $isHomeFooter,
            );
        }

        $view->with([
            'siteFooter' => $siteFooter,
            'isHomeFooter' => $isHomeFooter,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function prepareLinks(
        mixed $links,
        bool $isHomeFooter,
        bool $removePpdb = false,
    ): array {
        $links = is_array($links) ? $links : [];

        if ($removePpdb) {
            $links = array_filter(
                $links,
                fn (array $link): bool => parse_url(
                    (string) ($link['href'] ?? ''),
                    PHP_URL_PATH,
                ) !== '/ppdb',
            );
        }

        return array_values(array_map(
            fn (array $link): array => array_merge($link, [
                'resolved_href' => $this->normalizeHref(
                    $link['href'] ?? '#',
                    $isHomeFooter,
                ),
            ]),
            $links,
        ));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function prepareChannels(
        mixed $channels,
        bool $isHomeFooter,
    ): array {
        if (! is_array($channels)) {
            return [];
        }

        return array_values(array_map(function (array $channel) use (
            $isHomeFooter
        ): array {
            $href = $this->normalizeHref(
                $channel['href'] ?? '#',
                $isHomeFooter,
            );

            return array_merge($channel, [
                'is_disabled' => ! empty($channel['disabled']),
                'resolved_href' => $href,
                'external_target' => $this->externalTarget($href),
                'external_rel' => $this->externalRel($href),
                'fallback' => substr(
                    (string) ($channel['label'] ?? 'LK'),
                    0,
                    2,
                ),
            ]);
        }, $channels));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function preparePartners(
        mixed $partners,
        bool $isHomeFooter,
    ): array {
        if (! is_array($partners)) {
            return [];
        }

        return array_values(array_map(function (array $partner) use (
            $isHomeFooter
        ): array {
            $href = $this->normalizeHref(
                $partner['href'] ?? '#',
                $isHomeFooter,
            );

            return array_merge($partner, [
                'resolved_href' => $href,
                'external_target' => $this->externalTarget($href),
                'external_rel' => $this->externalRel($href),
            ]);
        }, $partners));
    }

    private function normalizeHref(mixed $href, bool $isHomeFooter): string
    {
        if (! is_string($href) || trim($href) === '') {
            return '#';
        }

        $href = trim($href);

        if (str_starts_with($href, '#')) {
            return $isHomeFooter ? $href : route('home').$href;
        }

        return $href;
    }

    private function externalTarget(string $href): string
    {
        return str_starts_with($href, 'http') ? '_blank' : '_self';
    }

    private function externalRel(string $href): string
    {
        return str_starts_with($href, 'http') ? 'noopener noreferrer' : '';
    }
}
