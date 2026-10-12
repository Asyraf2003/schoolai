<?php

namespace App\Support\Media;

use LogicException;

final class R2MediaConfiguration
{
    public function assertValid(): void
    {
        $disk = config('filesystems.disks.s3');
        $origin = 'https://media.almustaqbal.sch.id';

        if (config('media.disk') !== 's3' || ($disk['driver'] ?? null) !== 's3') {
            throw new LogicException('Public media must use the R2-backed s3 disk.');
        }

        if (rtrim((string) config('media.public_url'), '/') !== $origin
            || rtrim((string) ($disk['url'] ?? ''), '/') !== $origin) {
            throw new LogicException('Public media must use the canonical media.almustaqbal.sch.id HTTPS origin.');
        }

        $endpoint = parse_url((string) ($disk['endpoint'] ?? ''));
        if (! is_array($endpoint) || ($endpoint['scheme'] ?? '') !== 'https'
            || preg_match('/^[a-z0-9-]+\.r2\.cloudflarestorage\.com$/', $endpoint['host'] ?? '') !== 1
            || array_intersect(['user', 'pass', 'query', 'fragment', 'port'], array_keys($endpoint)) !== []
            || ! in_array($endpoint['path'] ?? '', ['', '/'], true)) {
            throw new LogicException('The media disk must use a canonical Cloudflare R2 S3 endpoint.');
        }

        if (($disk['region'] ?? null) !== 'auto') {
            throw new LogicException('Cloudflare R2 requires AWS_DEFAULT_REGION=auto.');
        }

        foreach (['key', 'secret', 'bucket'] as $name) {
            if (! is_string($disk[$name] ?? null) || trim($disk[$name]) === '') {
                throw new LogicException('Cloudflare R2 credentials and bucket must be configured privately.');
            }
        }
    }
}
