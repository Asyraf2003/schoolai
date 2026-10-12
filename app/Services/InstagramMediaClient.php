<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

final class InstagramMediaClient
{
    /** @return array<int, array<string, mixed>> */
    public function latest(int $limit = 12): array
    {
        $token = trim((string) config('services.instagram.access_token'));

        if ($token === '') {
            throw new RuntimeException('INSTAGRAM_ACCESS_TOKEN is not configured.');
        }

        $response = Http::timeout(25)
            ->retry(2, 500)
            ->get('https://graph.instagram.com/me/media', [
                'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,children{media_type,media_url,thumbnail_url}',
                'limit' => max(1, min($limit, 30)),
                'access_token' => $token,
            ]);

        if ($response->failed()) {
            // Meta's response body may contain access tokens. Never log or echo it.
            throw new RuntimeException('Instagram API returned HTTP '.$response->status().'.');
        }

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('Instagram API returned an invalid media collection.');
        }

        return array_values(array_filter($data, 'is_array'));
    }
}
