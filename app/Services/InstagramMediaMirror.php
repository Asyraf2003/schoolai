<?php

namespace App\Services;

use App\Support\Media\R2MediaStorage;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class InstagramMediaMirror
{
    public function __construct(private readonly R2MediaStorage $storage) {}

    public function mirror(string $url, string $instagramId, bool $video = false): string
    {
        $maxBytes = $video ? 100 * 1024 * 1024 : 15 * 1024 * 1024;
        $stream = tmpfile();

        if ($stream === false) {
            throw new RuntimeException('Unable to open temporary storage for Instagram media.');
        }

        try {
            // Meta can redirect between CDN hosts. Each destination must be validated.
            for ($redirects = 0; $redirects <= 3; $redirects++) {
                $this->assertTrustedUrl($url);
                rewind($stream);
                ftruncate($stream, 0);

                $response = Http::timeout(90)->withOptions([
                    'allow_redirects' => false,
                    'sink' => $stream,
                    'on_headers' => static function (ResponseInterface $headers) use ($maxBytes): void {
                        $length = (int) $headers->getHeaderLine('Content-Length');
                        if ($length > $maxBytes) {
                            throw new RuntimeException('Instagram media exceeds the R2 sync size limit.');
                        }
                    },
                    'progress' => static function ($total, $downloaded) use ($maxBytes): void {
                        if ($downloaded > $maxBytes) {
                            throw new RuntimeException('Instagram media exceeds the R2 sync size limit.');
                        }
                    },
                ])->get($url);

                if (in_array($response->status(), [301, 302, 303, 307, 308], true)) {
                    $url = $response->header('Location') ?? '';
                    continue;
                }

                if (! $response->successful()) {
                    throw new RuntimeException('Instagram media CDN returned HTTP '.$response->status().'.');
                }

                $contentType = strtolower(trim(explode(';', $response->header('Content-Type') ?: '')[0]));
                $types = $video
                    ? ['video/mp4' => 'mp4', 'video/quicktime' => 'mov']
                    : ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

                if (! isset($types[$contentType])) {
                    throw new RuntimeException('Instagram CDN returned an unsupported media type.');
                }

                if ((fstat($stream)['size'] ?? 0) < 1 || (fstat($stream)['size'] ?? 0) > $maxBytes) {
                    throw new RuntimeException('Instagram CDN returned an empty or oversized media file.');
                }

                rewind($stream);

                return $this->storage->storeStream(
                    $stream,
                    $types[$contentType],
                    $contentType,
                    'gallery/instagram',
                    $instagramId,
                )['url'];
            }

            throw new RuntimeException('Instagram CDN exceeded the redirect limit.');
        } finally {
            fclose($stream);
        }
    }

    private function assertTrustedUrl(string $url): void
    {
        $parts = parse_url($url);
        if (! is_array($parts)
            || strtolower((string) ($parts['scheme'] ?? '')) !== 'https'
            || empty($parts['host'])
            || isset($parts['user']) || isset($parts['pass'])
            || isset($parts['port']) || isset($parts['fragment'])) {
            throw new RuntimeException('Instagram media URL is not trusted.');
        }

        $host = strtolower(rtrim($parts['host'], '.'));
        $allowed = false;
        foreach (['cdninstagram.com', 'fbcdn.net'] as $domain) {
            if ($host === $domain || str_ends_with($host, '.'.$domain)) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed) {
            throw new RuntimeException('Instagram media URL is outside trusted Meta CDN hosts.');
        }
    }
}
