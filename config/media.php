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

        'brand' => [
            'logo_nav' => $publicUrl.'/site/brand/logo-nav-v1.webp',
            'logo_footer' => $publicUrl.'/site/brand/logo-footer-v1.webp',
            'logo_square' => $publicUrl.'/site/brand/logo-square-v1.png',
        ],

        'seo' => [
            'home_og' => $publicUrl.'/site/seo/og-home-v1.jpg',
        ],

        'vision' => [
            'paper_01' => $publicUrl.'/site/vision/vision-paper-01.webp',
            'paper_02' => $publicUrl.'/site/vision/vision-paper-02.webp',
            'paper_03' => $publicUrl.'/site/vision/vision-paper-03.webp',
        ],

        'ornaments' => [
            'geometry_32' => $publicUrl.'/site/ornaments/gallery-ornament-32-v1.webp',
            'geometry_33' => $publicUrl.'/site/ornaments/gallery-ornament-33-v1.webp',
        ],

        'navigation' => [
            'education' => $publicUrl.'/site/navigation/teaching.webp',
            'gallery' => $publicUrl.'/site/navigation/activity.webp',
            'article' => $publicUrl.'/site/navigation/library.webp',
        ],

        'footer' => [
            'maps' => $publicUrl.'/site/footer/maps-v1.webp',
            'whatsapp' => $publicUrl.'/site/footer/whatsapp-v1.svg',
            'instagram' => $publicUrl.'/site/footer/instagram-v1.svg',
            'facebook' => $publicUrl.'/site/footer/facebook-v1.webp',
            'gmail' => $publicUrl.'/site/footer/gmail-v1.webp',
            'partners' => [
                $publicUrl.'/site/footer/partners/6-v1.webp',
                $publicUrl.'/site/footer/partners/2-v1.webp',
                $publicUrl.'/site/footer/partners/3-v1.webp',
                $publicUrl.'/site/footer/partners/4-v1.webp',
                $publicUrl.'/site/footer/partners/5-v1.webp',
            ],
        ],

        'providers' => [
            'youtube' => $publicUrl.'/site/providers/youtube-v1.webp',
            'instagram' => $publicUrl.'/site/footer/instagram-v1.svg',
            'facebook' => $publicUrl.'/site/footer/facebook-v1.webp',
            'tiktok' => $publicUrl.'/site/providers/tiktok-v1.webp',
            'vimeo' => $publicUrl.'/site/providers/vimeo-v1.webp',
        ],
    ],

    'static_publish' => [
        ['source' => 'media/home/logo-nav.webp', 'key' => 'site/brand/logo-nav-v1.webp'],
        ['source' => 'media/home/logo-footer.webp', 'key' => 'site/brand/logo-footer-v1.webp'],
        ['source' => 'media/home/logo.png', 'key' => 'site/brand/logo-square-v1.png'],
        ['source' => 'media/home/og-home.jpg', 'key' => 'site/seo/og-home-v1.jpg'],
        ['source' => 'media/seed/hero/gallery-ornament-32.webp', 'key' => 'site/ornaments/gallery-ornament-32-v1.webp'],
        ['source' => 'media/seed/hero/gallery-ornament-33.webp', 'key' => 'site/ornaments/gallery-ornament-33-v1.webp'],
        ['source' => 'media/home/maps.webp', 'key' => 'site/footer/maps-v1.webp'],
        ['source' => 'media/home/wa.svg', 'key' => 'site/footer/whatsapp-v1.svg'],
        ['source' => 'media/home/instagram.svg', 'key' => 'site/footer/instagram-v1.svg'],
        ['source' => 'media/home/facebook.webp', 'key' => 'site/footer/facebook-v1.webp'],
        ['source' => 'media/home/gmail.webp', 'key' => 'site/footer/gmail-v1.webp'],
        ['source' => 'media/home/6.webp', 'key' => 'site/footer/partners/6-v1.webp'],
        ['source' => 'media/home/2.webp', 'key' => 'site/footer/partners/2-v1.webp'],
        ['source' => 'media/home/3.webp', 'key' => 'site/footer/partners/3-v1.webp'],
        ['source' => 'media/home/4.webp', 'key' => 'site/footer/partners/4-v1.webp'],
        ['source' => 'media/home/5.webp', 'key' => 'site/footer/partners/5-v1.webp'],
        ['source' => 'media/home/youtube.webp', 'key' => 'site/providers/youtube-v1.webp'],
        ['source' => 'media/home/tiktok.webp', 'key' => 'site/providers/tiktok-v1.webp'],
        ['source' => 'media/home/vimeo.webp', 'key' => 'site/providers/vimeo-v1.webp'],
    ],
];
