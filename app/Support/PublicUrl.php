<?php

namespace App\Support;

final class PublicUrl
{
    /** @var array<string, array<int, string>> */
    private static array $dnsCache = [];

    /**
     * Validate a URL that is stored for browser navigation or embedding.
     *
     * The application never fetches these URLs server-side. If that changes,
     * every redirect hop must be revalidated before a response body is read.
     *
     * @param array<int, string> $blockedPathPrefixes
     * @param array<int, string>|null $resolvedAddresses Test override for DNS results.
     */
    public static function isSafe(
        string $url,
        array $blockedPathPrefixes = [],
        ?array $resolvedAddresses = null,
    ): bool {
        $url = trim($url);

        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            return false;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower(trim((string) ($parts['host'] ?? ''), '[]'));

        if (
            ! in_array($scheme, ['http', 'https'], true) ||
            $host === '' ||
            isset($parts['user']) ||
            isset($parts['pass'])
        ) {
            return false;
        }

        if (! self::isPublicHost($host, $resolvedAddresses)) {
            return false;
        }

        $path = '/' . ltrim((string) ($parts['path'] ?? ''), '/');

        foreach ($blockedPathPrefixes as $prefix) {
            $prefix = '/' . trim($prefix, '/');

            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return false;
            }
        }

        return true;
    }

    /** @param array<int, string> $blockedPathPrefixes */
    public static function normalize(string $url, array $blockedPathPrefixes = []): ?string
    {
        $url = trim($url);

        return self::isSafe($url, $blockedPathPrefixes) ? $url : null;
    }

    /** @param array<int, string>|null $resolvedAddresses */
    private static function isPublicHost(string $host, ?array $resolvedAddresses): bool
    {
        $host = rtrim($host, '.');

        if (
            $host === '' ||
            $host === 'localhost' ||
            str_ends_with($host, '.localhost') ||
            str_ends_with($host, '.local') ||
            str_ends_with($host, '.internal') ||
            str_contains($host, '%')
        ) {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return self::isPublicIp($host);
        }

        // Reject single-integer, shortened, octal, hexadecimal, and mixed
        // IPv4 notations before treating the value as a DNS hostname.
        if (
            preg_match('/^\d+$/', $host) === 1 ||
            preg_match('/^0x[0-9a-f]+$/i', $host) === 1 ||
            preg_match('/^\d+(?:\.\d+){1,3}$/', $host) === 1 ||
            preg_match('/(?:^|\.)0x[0-9a-f]+(?:\.|$)/i', $host) === 1 ||
            preg_match('/(?:^|\.)0[0-7]+(?:\.|$)/', $host) === 1
        ) {
            return false;
        }

        if (
            strlen($host) > 253 ||
            str_contains($host, 'xn--') ||
            preg_match('/^[a-z0-9.-]+$/', $host) !== 1
        ) {
            return false;
        }

        foreach (explode('.', $host) as $label) {
            if (
                $label === '' ||
                strlen($label) > 63 ||
                str_starts_with($label, '-') ||
                str_ends_with($label, '-')
            ) {
                return false;
            }
        }

        $addresses = $resolvedAddresses ?? self::resolveHost($host);

        foreach ($addresses as $address) {
            if (! self::isPublicIp($address)) {
                return false;
            }
        }

        return true;
    }

    private static function isPublicIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

    /** @return array<int, string> */
    private static function resolveHost(string $host): array
    {
        if (array_key_exists($host, self::$dnsCache)) {
            return self::$dnsCache[$host];
        }

        if (! function_exists('dns_get_record')) {
            return self::$dnsCache[$host] = [];
        }

        $records = @dns_get_record($host, DNS_A | DNS_AAAA);
        $addresses = [];

        if (is_array($records)) {
            foreach ($records as $record) {
                $address = $record['ip'] ?? $record['ipv6'] ?? null;

                if (is_string($address) && $address !== '') {
                    $addresses[] = $address;
                }
            }
        }

        return self::$dnsCache[$host] = array_values(array_unique($addresses));
    }
}
