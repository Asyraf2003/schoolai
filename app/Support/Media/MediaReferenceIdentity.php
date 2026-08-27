<?php

namespace App\Support\Media;

final class MediaReferenceIdentity
{
    public static function normalize(?string $mediaUrl): ?string
    {
        if (! is_string($mediaUrl)) {
            return null;
        }

        $mediaUrl = trim($mediaUrl);

        if ($mediaUrl === '') {
            return null;
        }

        if (str_starts_with($mediaUrl, '/storage/')) {
            return 'local:'.rtrim($mediaUrl, '/');
        }

        if (! filter_var($mediaUrl, FILTER_VALIDATE_URL)) {
            return 'path:'.rtrim($mediaUrl, '/');
        }

        $scheme = strtolower((string) parse_url($mediaUrl, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($mediaUrl, PHP_URL_HOST));
        $port = parse_url($mediaUrl, PHP_URL_PORT);
        $path = '/'.ltrim((string) parse_url($mediaUrl, PHP_URL_PATH), '/');
        $path = $path === '/' ? '/' : rtrim($path, '/');
        parse_str((string) parse_url($mediaUrl, PHP_URL_QUERY), $query);
        ksort($query);

        $defaultPort = ($scheme === 'https' && $port === 443) || ($scheme === 'http' && $port === 80);
        $authority = $host.(($port && ! $defaultPort) ? ':'.$port : '');
        $queryString = $query === [] ? '' : '?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        return $scheme.'://'.$authority.$path.$queryString;
    }
}
