<?php

namespace App\Http\Controllers\Concerns;

use App\Support\PublicUrl;

trait NormalizesHomeMedia
{
    private function isTrustedFacebookEmbedUrl(string $url): bool
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $facebookUrl = trim((string) ($query['href'] ?? ''));

        if ($facebookUrl === '' || ! PublicUrl::isSafe($facebookUrl)) {
            return false;
        }

        $facebookScheme = strtolower((string) parse_url($facebookUrl, PHP_URL_SCHEME));
        $facebookHost = strtolower((string) parse_url($facebookUrl, PHP_URL_HOST));
        $facebookPath = trim((string) parse_url($facebookUrl, PHP_URL_PATH), '/');
        parse_str((string) parse_url($facebookUrl, PHP_URL_QUERY), $facebookQuery);

        if (
            $facebookScheme !== 'https' ||
            ($facebookHost !== 'facebook.com' && ! str_ends_with($facebookHost, '.facebook.com'))
        ) {
            return false;
        }

        if (preg_match('~^reel/\d+$~', $facebookPath)) {
            return true;
        }

        return $facebookPath === 'watch' && preg_match('~^\d+$~', (string) ($facebookQuery['v'] ?? '')) === 1;
    }

    private function videoProvider(?string $embedUrl): string
    {
        if (! is_string($embedUrl) || trim($embedUrl) === '') {
            return 'video';
        }

        $host = strtolower((string) parse_url(trim($embedUrl), PHP_URL_HOST));

        return match ($host) {
            'www.youtube.com' => 'youtube',
            'www.instagram.com' => 'instagram',
            'www.facebook.com' => 'facebook',
            'www.tiktok.com' => 'tiktok',
            'player.vimeo.com' => 'vimeo',
            default => 'video',
        };
    }

    private function videoProviderLogoUrl(?string $provider): ?string
    {
        if ($provider === null || $provider === '') {
            return null;
        }

        $url = config('media.static.providers.'.$provider);

        return is_string($url) && $url !== ''
            ? PublicUrl::normalize($url)
            : null;
    }

    private function videoThumbnailUrl(?string $embedUrl): ?string
    {
        if (! is_string($embedUrl) || trim($embedUrl) === '') {
            return null;
        }

        $embedUrl = trim($embedUrl);
        $host = strtolower((string) parse_url($embedUrl, PHP_URL_HOST));
        $path = trim((string) parse_url($embedUrl, PHP_URL_PATH), '/');

        if ($host === 'www.youtube.com' && preg_match('~^embed/([^/?#]+)$~', $path, $match)) {
            return 'https://i.ytimg.com/vi/'.rawurlencode($match[1]).'/hqdefault.jpg';
        }

        return null;
    }

    private function instagramUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (! PublicUrl::isSafe($url)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if ($host !== 'instagram.com' && ! str_ends_with($host, '.instagram.com')) {
            return null;
        }

        return $url;
    }

    private function publicArticleUrl(string $url): ?string
    {
        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }
}
