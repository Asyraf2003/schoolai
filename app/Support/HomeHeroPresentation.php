<?php

namespace App\Support;

use App\Models\PpdbSetting;

final class HomeHeroPresentation
{
    /** @param array<int, array<string, mixed>> $slides */
    public static function decorate(
        array $slides,
        PpdbSetting $ppdbSetting,
        string $ppdbLabel,
    ): array {
        $registrationUrl = $ppdbSetting->isRegistrationOpen()
            ? $ppdbSetting->publicRegistrationUrl()
            : null;

        return array_values(array_map(
            static function (array $slide, int $index) use ($registrationUrl, $ppdbLabel): array {
                $isPrimary = $index === 0;
                $showPpdb = $isPrimary
                    && ($slide['render_type'] ?? null) === 'video'
                    && $registrationUrl !== null;

                return array_replace($slide, [
                    'is_primary_slide' => $isPrimary,
                    'show_ppdb_cta' => $showPpdb,
                    'ppdb_url' => $showPpdb ? $registrationUrl : null,
                    'ppdb_label' => $ppdbLabel,
                    'title_href' => self::articleTitleUrl($slide),
                ]);
            },
            $slides,
            array_keys($slides),
        ));
    }

    /** @param array<string, mixed> $slide */
    private static function articleTitleUrl(array $slide): ?string
    {
        if (empty($slide['article_id']) || ! is_string($slide['title_href'] ?? null)) {
            return null;
        }

        $url = trim($slide['title_href']);

        if ($url === '' || self::hasBlockedPath($url)) {
            return null;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        $parts = parse_url($url);
        $appParts = parse_url(url('/'));

        if (
            is_array($parts)
            && is_array($appParts)
            && ($parts['host'] ?? null) === ($appParts['host'] ?? null)
            && ($parts['port'] ?? null) === ($appParts['port'] ?? null)
        ) {
            $path = '/' . ltrim((string) ($parts['path'] ?? ''), '/');
            $query = isset($parts['query']) ? '?' . $parts['query'] : '';
            $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

            return $path . $query . $fragment;
        }

        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private static function hasBlockedPath(string $url): bool
    {
        $path = '/' . ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        foreach (['/admin', '/login', '/auth'] as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return true;
            }
        }

        return false;
    }
}
