<?php

namespace App\Http\Controllers;

use App\Models\TestimonialMedia;
use App\Support\TestimonialVideoUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

final class TestimonialMediaController extends Controller
{
    private const PRIMARY_MEDIA = [
        'id' => null,
        'type' => 'video',
        'source' => 'upload',
        'media_url' => '/media/hero/202607290837.mp4',
        'thumbnail_url' => '/images/hero-video-poster.svg',
    ];

    public function __invoke(): JsonResponse
    {
        if (! Schema::hasTable('testimonial_media')) {
            return response()->json([
                'items' => [self::PRIMARY_MEDIA],
            ]);
        }

        $items = TestimonialMedia::query()
            ->where('is_published', true)
            ->ordered()
            ->limit(TestimonialMedia::MAX_ITEMS)
            ->get()
            ->map(fn (TestimonialMedia $item): array => [
                'id' => $item->getKey(),
                'type' => $item->type,
                'source' => $item->source,
                'media_url' => $item->media_url,
                'thumbnail_url' => $item->is_photo
                    ? $item->media_url
                    : TestimonialVideoUrl::thumbnail($item->media_url),
            ]);

        return response()->json([
            'items' => collect([self::PRIMARY_MEDIA])
                ->concat($items)
                ->values(),
        ]);
    }
}
