<?php

namespace App\View\Presenters;

final class SiteFooterMediaPresenter
{
    /** @param array<string, mixed> $footer
     * @return array<string, mixed>
     */
    public function present(array $footer): array
    {
        $brand = config('media.static.brand.logo_footer');
        if (is_array($footer['brand'] ?? null) && is_string($brand) && $brand !== '') {
            $footer['brand']['image'] = $brand;
        }

        $assets = config('media.static.footer', []);
        $assets = is_array($assets) ? $assets : [];
        $channelKeys = [
            'maps' => 'maps',
            'whatsapp' => 'whatsapp',
            'instagram' => 'instagram',
            'facebook' => 'facebook',
            'email' => 'gmail',
        ];

        if (is_array($footer['channels'] ?? null)) {
            $footer['channels'] = array_map(
                static function (array $channel) use ($assets, $channelKeys): array {
                    $key = $channelKeys[$channel['icon'] ?? ''] ?? null;
                    $url = $key !== null ? ($assets[$key] ?? null) : null;
                    if (is_string($url) && $url !== '') {
                        $channel['asset'] = $url;
                    }

                    return $channel;
                },
                $footer['channels'],
            );
        }

        $partnerAssets = $assets['partners'] ?? [];
        if (is_array($footer['partners'] ?? null) && is_array($partnerAssets)) {
            $footer['partners'] = array_map(
                static function (array $partner, int $index) use ($partnerAssets): array {
                    $url = $partnerAssets[$index] ?? null;
                    if (is_string($url) && $url !== '') {
                        $partner['image'] = $url;
                    }

                    return $partner;
                },
                $footer['partners'],
                array_keys($footer['partners']),
            );
        }

        return $footer;
    }
}
