<?php

namespace App\Services;

use App\Models\InstagramGalleryItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

final class SyncInstagramGallery
{
    public function __construct(
        private readonly InstagramMediaClient $client,
        private readonly InstagramMediaMirror $mirror,
    ) {}

    /** @return array{synced:int, skipped:int} */
    public function run(int $limit = 12): array
    {
        $rows = $this->client->latest($limit);
        $synced = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $id = (string) ($row['id'] ?? '');

            try {
                if (preg_match('/^[0-9]{1,64}$/', $id) !== 1) {
                    throw new RuntimeException('Instagram media ID is invalid.');
                }

                $type = (string) ($row['media_type'] ?? '');
                if (! in_array($type, ['IMAGE', 'VIDEO', 'CAROUSEL_ALBUM'], true)) {
                    throw new RuntimeException('Instagram media type is unsupported.');
                }

                $permalink = (string) ($row['permalink'] ?? '');
                $parts = parse_url($permalink);
                if (! is_array($parts)
                    || ($parts['scheme'] ?? '') !== 'https'
                    || ! in_array(strtolower((string) ($parts['host'] ?? '')), ['instagram.com', 'www.instagram.com'], true)
                    || isset($parts['user']) || isset($parts['pass'])) {
                    throw new RuntimeException('Instagram permalink is invalid.');
                }

                $source = $row;
                if ($type === 'CAROUSEL_ALBUM' && empty($row['media_url'])) {
                    $source = $row['children']['data'][0] ?? [];
                }

                $mediaUrl = (string) ($source['media_url'] ?? '');
                if ($mediaUrl === '') {
                    throw new RuntimeException('Instagram post has no downloadable media.');
                }

                $video = ($source['media_type'] ?? $type) === 'VIDEO';
                $item = InstagramGalleryItem::firstOrNew(['instagram_id' => $id]);

                // Media is mirrored only once. Re-syncing captions never re-downloads assets.
                if (! $item->media_url) {
                    $item->media_url = $this->mirror->mirror($mediaUrl, $id, $video);
                }
                $thumbnail = (string) ($source['thumbnail_url'] ?? $row['thumbnail_url'] ?? '');
                if ($video && $thumbnail !== '' && ! $item->thumbnail_url) {
                    $item->thumbnail_url = $this->mirror->mirror($thumbnail, $id);
                }

                $item->fill([
                    'media_type' => $video ? 'VIDEO' : $type,
                    'caption' => mb_substr((string) ($row['caption'] ?? ''), 0, 6000),
                    'permalink' => $permalink,
                    'posted_at' => Carbon::parse((string) ($row['timestamp'] ?? '')),
                    'synced_at' => now(),
                ])->save();

                $synced++;
            } catch (Throwable $exception) {
                // Keep previously mirrored records when Meta or R2 fails.
                $skipped++;
                Log::warning('Instagram gallery media sync skipped', [
                    'instagram_id' => $id,
                    'reason' => $exception::class,
                ]);
            }
        }

        return ['synced' => $synced, 'skipped' => $skipped];
    }
}
