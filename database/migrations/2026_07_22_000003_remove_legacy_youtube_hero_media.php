<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hero_slides')) {
            return;
        }

        $columns = ['id', 'media_url', 'poster_url'];

        if (Schema::hasColumn('hero_slides', 'article_id')) {
            $columns[] = 'article_id';
        }

        DB::table('hero_slides')
            ->orderBy('id')
            ->get($columns)
            ->each(function (object $slide): void {
                $mediaUrl = is_string($slide->media_url ?? null) ? trim($slide->media_url) : '';
                $posterUrl = is_string($slide->poster_url ?? null) ? trim($slide->poster_url) : '';

                if (! self::isYoutubeAsset($mediaUrl) && ! self::isYoutubeAsset($posterUrl)) {
                    return;
                }

                $articleThumbnail = null;
                $articleId = $slide->article_id ?? null;

                if (
                    $articleId !== null
                    && Schema::hasTable('articles')
                    && Schema::hasColumn('articles', 'thumbnail_url')
                ) {
                    $candidate = DB::table('articles')
                        ->where('id', $articleId)
                        ->value('thumbnail_url');

                    if (is_string($candidate) && trim($candidate) !== '' && ! self::isYoutubeAsset($candidate)) {
                        $articleThumbnail = trim($candidate);
                    }
                }

                $safePoster = $posterUrl !== '' && ! self::isYoutubeAsset($posterUrl)
                    ? $posterUrl
                    : $articleThumbnail;
                $updates = [];

                if (self::isYoutubeAsset($mediaUrl)) {
                    $updates['type'] = 'image';
                    $updates['media_url'] = $safePoster ?: 'media/home/hero-school.png';
                }

                if (self::isYoutubeAsset($posterUrl)) {
                    $updates['poster_url'] = $articleThumbnail;
                }

                if ($updates !== []) {
                    $updates['updated_at'] = now();
                    DB::table('hero_slides')->where('id', $slide->id)->update($updates);
                }
            });
    }

    public function down(): void
    {
        // Removed provider URLs cannot be reconstructed safely.
    }

    private static function isYoutubeAsset(?string $url): bool
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
};
