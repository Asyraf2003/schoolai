<?php

return [
    'disk' => env('MEDIA_DISK', 's3'),
    'public_url' => env('MEDIA_PUBLIC_URL', 'https://media.almustaqbal.sch.id'),
    'cache_control' => env('MEDIA_CACHE_CONTROL', 'public, max-age=31536000, immutable'),
    'homepage_hero_video_url' => 'https://media.almustaqbal.sch.id/hero/slides/main/e28f6b4c-3024-4c73-9fe3-cfdd97a75d02.mp4',
];
