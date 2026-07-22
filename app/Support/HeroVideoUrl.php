<?php

namespace App\Support;

final class HeroVideoUrl
{
    public static function normalize(?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($scheme !== 'https') {
            return null;
        }

        $videoId = self::youtubeVideoId($url, $host, $path);

        if ($videoId !== null) {
            $encoded = rawurlencode($videoId);

            return 'https://www.youtube-nocookie.com/embed/'.$encoded
                .'?autoplay=1&mute=1&controls=0&loop=1&playlist='.$encoded
                .'&playsinline=1&rel=0&modestbranding=1&enablejsapi=1';
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (! in_array($extension, ['mp4', 'webm', 'ogg', 'ogv'], true)) {
            return null;
        }

        return PublicUrl::normalize($url);
    }

    private static function youtubeVideoId(string $url, string $host, string $path): ?string
    {
        $youtubeHosts = [
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtube-nocookie.com',
            'www.youtube-nocookie.com',
        ];

        $videoId = null;

        if ($host === 'youtu.be') {
            $videoId = explode('/', $path)[0] ?? null;
        } elseif (in_array($host, $youtubeHosts, true)) {
            if (preg_match('~^(?:embed|shorts)/([^/?#]+)~', $path, $matches) === 1) {
                $videoId = $matches[1];
            } else {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
                $videoId = $query['v'] ?? null;
            }
        }

        if (! is_string($videoId) || preg_match('/^[A-Za-z0-9_-]{6,32}$/', $videoId) !== 1) {
            return null;
        }

        return $videoId;
    }
}
