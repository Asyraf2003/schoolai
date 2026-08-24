<?php

namespace App\Support\Concerns;

use App\Support\Media\MediaUrlResolver;
use App\Support\PublicUrl;

trait ValidatesArticleContentUrls
{
    private function allowedClass(string $tag, string $class): ?string
    {
        $textColors = [
            'article-color--default',
            'article-color--muted',
            'article-color--green',
            'article-color--blue',
            'article-color--red',
            'article-color--amber',
        ];
        $blockBackgrounds = [
            'article-bg--gray',
            'article-bg--yellow',
            'article-bg--green',
            'article-bg--blue',
            'article-bg--rose',
        ];
        $blockFormatting = [
            'article-text-small',
            'article-text-large',
            'article-align-center',
            'article-align-right',
            'article-align-justify',
            ...$textColors,
            ...$blockBackgrounds,
        ];

        $allowed = match ($tag) {
            'figure' => [
                'article-image--inline',
                'article-image--compact',
                'article-image--outset',
                'article-image--screen',
                'article-image-align--left',
                'article-image-align--center',
                'article-image-align--right',
            ],
            'blockquote' => [
                'article-quote',
                'article-pull-quote',
                ...$blockFormatting,
            ],
            'p', 'h2', 'h3', 'li' => [
                'has-drop-cap',
                ...$blockFormatting,
            ],
            'span' => $textColors,
            'div' => ['article-embed', 'article-video'],
            default => [],
        };

        $clean = array_values(array_intersect(
            preg_split('/\s+/', trim($class)) ?: [],
            $allowed
        ));

        return $clean === [] ? null : implode(' ', $clean);
    }

    private function safeLinkUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            $path = strtolower((string) parse_url($url, PHP_URL_PATH));

            return str_starts_with($path, '/admin') || str_starts_with($path, '/login')
                ? null
                : $url;
        }

        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private function safeImageUrl(string $url): ?string
    {
        $url = trim($url);

        if (preg_match('~^/storage/articles/(?:content|thumbnails)/[A-Za-z0-9/_\-.]+$~', $url) === 1) {
            return $url;
        }

        if (app(MediaUrlResolver::class)->ownedKey($url) !== null) {
            return $url;
        }

        if (! PublicUrl::isSafe($url)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return $scheme === 'https' && $host === 'images.unsplash.com' ? $url : null;
    }

    private function safeEmbedUrl(string $url): ?string
    {
        if (! PublicUrl::isSafe($url)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($scheme !== 'https') {
            return null;
        }

        if ($host === 'www.youtube-nocookie.com' && preg_match('~^embed/[A-Za-z0-9_-]+$~', $path)) {
            return $url;
        }

        if ($host === 'player.vimeo.com' && preg_match('~^video/\d+$~', $path)) {
            return $url;
        }

        if ($host === 'open.spotify.com' && preg_match('~^embed/(track|episode|show|playlist|album)/[A-Za-z0-9]+$~', $path)) {
            return $url;
        }

        if ($host === 'codepen.io' && preg_match('~^[^/]+/embed/[A-Za-z0-9]+$~', $path)) {
            return $url;
        }

        return null;
    }
}
