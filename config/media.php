<?php

$publicUrl = rtrim(
    env('MEDIA_PUBLIC_URL', 'https://media.almustaqbal.sch.id'),
    '/'
);

return [
    'disk' => env('MEDIA_DISK', 's3'),
    'public_url' => $publicUrl,
    'cache_control' => env('MEDIA_CACHE_CONTROL', 'public, max-age=31536000, immutable'),

    'homepage_hero_video_url' => $publicUrl.'/site/hero/homepage-opening-30s-v1.mp4',
    'homepage_about_video_url' => $publicUrl.'/about/media/main/ad3344ec-86c3-42d6-98d0-4e35b3863888.mp4',

    'static' => [
        'hero_school' => $publicUrl.'/site/hero/hero-school.webp',

        'vision' => [
            'paper_01' => $publicUrl.'/site/vision/vision-paper-01.webp',
            'paper_02' => $publicUrl.'/site/vision/vision-paper-02.webp',
            'paper_03' => $publicUrl.'/site/vision/vision-paper-03.webp',
        ],

        'navigation' => [
            'education' => $publicUrl.'/site/navigation/teaching.webp',
            'gallery' => $publicUrl.'/site/navigation/activity.webp',
            'article' => $publicUrl.'/site/navigation/library.webp',
        ],
    ],
];
