<?php

$publicUrl = rtrim(
    env('MEDIA_PUBLIC_URL', 'https://media.almustaqbal.sch.id'),
    '/'
);

$schoolLife = [];
foreach ([
    'aula',
    'fullday',
    'gigianak',
    'haji',
    'haji2',
    'ibadah',
    'labit',
    'mushalla',
    'parenting',
    'perpustakaan',
    'pidato',
    'psikolog',
    'renang',
    'renang2',
    'solatjamaah',
    'taekwondo',
    'tahfiz',
] as $name) {
    $schoolLife[$name] = $publicUrl.'/site/school-life/'.$name.'-v1.webp';
}

$testimonials = [];
for ($index = 1; $index <= 22; $index++) {
    $testimonials[] = $publicUrl.sprintf('/site/testimonials/testi-%02d-v1.webp', $index);
}

return [
    'disk' => env('MEDIA_DISK', 's3'),
    'public_url' => $publicUrl,
    'cache_control' => env('MEDIA_CACHE_CONTROL', 'public, max-age=31536000, immutable'),

    'homepage_hero_video_url' => $publicUrl.'/site/hero/homepage-opening-v2.mp4',
    'homepage_about_video_url' => $publicUrl.'/about/media/main/ad3344ec-86c3-42d6-98d0-4e35b3863888.mp4',

    'static' => [
        'hero_school' => $publicUrl.'/site/hero/hero-school.webp',
        'school_life' => $schoolLife,
        'testimonials' => $testimonials,

        'language_flags' => [
            'id' => $publicUrl.'/site/language/id-v1.webp',
            'en' => $publicUrl.'/site/language/en-v1.webp',
            'ar' => $publicUrl.'/site/language/ar-v1.webp',
        ],

        'brand' => [
            'logo_nav' => $publicUrl.'/site/brand/logo-nav-v1.webp',
            'logo_footer' => $publicUrl.'/site/brand/logo-footer-v1.webp',
            'logo_square' => $publicUrl.'/site/brand/logo-square-v1.png',
            'favicon' => $publicUrl.'/site/brand/favicon-v1.ico',
            'apple_touch_icon' => $publicUrl.'/site/brand/apple-touch-icon-v1.png',
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
];
