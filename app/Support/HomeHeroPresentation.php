<?php

namespace App\Support;

final class HomeHeroPresentation
{
    /** @param array<int, array<string, mixed>> $slides */
    public static function decorate(array $slides): array
    {
        return array_values(array_map(
            static function (array $slide, int $index): array {
                $isPrimary = $index === 0;
                $isPpdbCampaign = $isPrimary
                    && ($slide['is_ppdb_campaign'] ?? false) === true;

                return array_replace($slide, [
                    'is_primary_slide' => $isPrimary,
                    'is_ppdb_campaign' => $isPpdbCampaign,
                    'campaign_link_label' => $isPpdbCampaign
                        ? self::stringOrNull($slide['campaign_link_label'] ?? null)
                        : null,
                    'title_href' => $isPpdbCampaign
                        ? self::publicTitleUrl($slide['title_href'] ?? null)
                        : ($isPrimary ? null : self::articleTitleUrl($slide)),
                    'description_href' => null,
                ]);
            },
            $slides,
            array_keys($slides),
        ));
    }

    /** @param array<string, mixed> $slide */
    private static function articleTitleUrl(array $slide): ?string
    {
        if (empty($slide['article_id'])) {
            return null;
        }

        return self::publicTitleUrl($slide['title_href'] ?? null);
    }

    private static function publicTitleUrl(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $url = trim($value);

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
            $path = '/'.ltrim((string) ($parts['path'] ?? ''), '/');
            $query = isset($parts['query']) ? '?'.$parts['query'] : '';
            $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

            return $path.$query.$fragment;
        }

        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private static function hasBlockedPath(string $url): bool
    {
        $path = '/'.ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        foreach (['/admin', '/login', '/auth'] as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }
}
