<?php

return [
    'disk' => env('MEDIA_DISK', 's3'),
    'public_url' => env('MEDIA_PUBLIC_URL', 'https://media.almustaqbal.sch.id'),
    'cache_control' => env('MEDIA_CACHE_CONTROL', 'public, max-age=31536000, immutable'),
    'homepage_hero_video_url' => 'https://media.almustaqbal.sch.id/hero/slides/main/5d918256-de1e-4eae-ad27-3d031acc202d.mp4',
    'homepage_about_video_url' => 'https://media.almustaqbal.sch.id/about/media/main/ad3344ec-86c3-42d6-98d0-4e35b3863888.mp4',
];
