<?php

namespace App\Support;

final class TestimonialVideoUrl
{
    public static function normalize(string $url): ?string
    {
        $url = trim($url);

        if ($url === '' || ! PublicUrl::isSafe($url)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return null;
        }

        if (self::hostMatches($host, 'youtu.be') && $path !== '') {
            return 'https://www.youtube.com/embed/'.rawurlencode(explode('/', $path)[0]);
        }

        if (self::hostMatches($host, 'youtube.com')) {
            if (! empty($query['v'])) {
                return 'https://www.youtube.com/embed/'.rawurlencode((string) $query['v']);
            }

            if (preg_match('~(?:^|/)(?:shorts|embed)/([^/?#]+)~', $path, $match)) {
                return 'https://www.youtube.com/embed/'.rawurlencode($match[1]);
            }
        }

        if (self::hostMatches($host, 'tiktok.com') && preg_match('~(?:^|/)(?:video|player/v1|embed/v2)/(\d+)(?:/|$)~', $path, $match)) {
            return 'https://www.tiktok.com/player/v1/'.$match[1];
        }

        if (self::hostMatches($host, 'instagram.com') && preg_match('~^(p|reel|tv)/([^/]+)~', $path, $match)) {
            return 'https://www.instagram.com/'.$match[1].'/'.rawurlencode($match[2]).'/embed';
        }

        if (self::hostMatches($host, 'vimeo.com') && preg_match('~^(?:video/)?(\d+)$~', $path, $match)) {
            return 'https://player.vimeo.com/video/'.$match[1];
        }

        if ($scheme === 'https' && self::hostMatches($host, 'facebook.com')) {
            $facebookUrl = $path === 'plugins/video.php'
                ? trim((string) ($query['href'] ?? ''))
                : $url;
            $videoId = self::facebookVideoId($facebookUrl);

            if ($videoId !== null) {
                $reelUrl = 'https://www.facebook.com/reel/'.rawurlencode($videoId).'/';

                return 'https://www.facebook.com/plugins/video.php?'.http_build_query([
                    'height' => 476,
                    'href' => $reelUrl,
                    'show_text' => 'false',
                    'width' => 267,
                    't' => 0,
                ], '', '&', PHP_QUERY_RFC3986);
            }
        }

        return null;
    }

    public static function thumbnail(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if (self::hostMatches($host, 'youtube.com') && preg_match('~^embed/([^/?#]+)$~', $path, $match)) {
            return 'https://i.ytimg.com/vi/'.rawurlencode($match[1]).'/hqdefault.jpg';
        }

        return null;
    }

    private static function facebookVideoId(string $url): ?string
    {
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if ($scheme !== 'https' || ! self::hostMatches($host, 'facebook.com')) {
            return null;
        }

        if (preg_match('~^reel/(\d+)$~', $path, $match)) {
            return $match[1];
        }

        $watchId = (string) ($query['v'] ?? '');

        return $path === 'watch' && preg_match('~^\d+$~', $watchId)
            ? $watchId
            : null;
    }

    private static function hostMatches(string $host, string $domain): bool
    {
        return $host === $domain || str_ends_with($host, '.'.$domain);
    }
}
