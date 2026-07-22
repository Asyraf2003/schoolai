<?php

namespace App\Support;

final class HeroVideoUrl
{
    private const VIDEO_EXTENSIONS = ['mp4', 'webm', 'ogg', 'ogv'];

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
        $path = (string) parse_url($url, PHP_URL_PATH);

        if ($scheme !== 'https' || ! self::isDirectVideo($path)) {
            return null;
        }

        return PublicUrl::normalize($url);
    }

    public static function isDirectVideo(?string $urlOrPath): bool
    {
        if (! is_string($urlOrPath) || trim($urlOrPath) === '') {
            return false;
        }

        $path = (string) (parse_url(trim($urlOrPath), PHP_URL_PATH) ?: $urlOrPath);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::VIDEO_EXTENSIONS, true);
    }

    public static function isYoutubeAsset(?string $url): bool
    {
        if (! is_string($url) || trim($url) === '') {
            return false;
        }

        $host = strtolower((string) parse_url(trim($url), PHP_URL_HOST));

        return $host === 'youtu.be'
            || $host === 'youtube.com'
            || str_ends_with($host, '.youtube.com')
            || $host === 'youtube-nocookie.com'
            || str_ends_with($host, '.youtube-nocookie.com')
            || $host === 'ytimg.com'
            || str_ends_with($host, '.ytimg.com');
    }
}
