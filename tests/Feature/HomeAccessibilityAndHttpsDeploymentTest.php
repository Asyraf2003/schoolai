<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;

uses(RefreshDatabase::class);

it('preserves accessible latent editorial descriptions in every locale', function (): void {
    $copyByLocale = [
        'id' => [
            'title' => 'Kehidupan Sekolah',
            'description' => 'Belajar bersama untuk masa depan.',
        ],
        'en' => [
            'title' => 'School Life',
            'description' => 'Learning together for a brighter future.',
        ],
        'ar' => [
            'title' => 'الحياة المدرسية',
            'description' => 'نتعلم معًا من أجل مستقبل مشرق.',
        ],
    ];

    foreach ($copyByLocale as $locale => $copy) {
        app()->setLocale($locale);

        $content = View::make('home.partials.editorial-section-heading', [
            'title' => $copy['title'],
            'description' => $copy['description'],
        ])->render();

        preg_match_all(
            '/<p class="welcome-editorial-heading__description"([^>]*)>(.*?)<\/p>/su',
            $content,
            $descriptions,
            PREG_SET_ORDER,
        );

        expect($descriptions)->toHaveCount(1);

        foreach ($descriptions as $descriptionMatch) {
            expect($descriptionMatch[1])
                ->not->toContain('aria-label')
                ->and($descriptionMatch[2])
                ->toContain('<span class="sr-only">'.e($copy['description']).'</span>')
                ->toContain('class="welcome-editorial-heading__description-clip"')
                ->toContain('aria-hidden="true"');
        }
    }
});

it('ships a production-only canonical HTTPS redirect', function (): void {
    $htaccess = file_get_contents(public_path('.htaccess'));

    expect($htaccess)
        ->toBeString()
        ->toContain('RewriteCond %{HTTP_HOST} ^www\\.almustaqbal\\.sch\\.id')
        ->toContain('RewriteCond %{HTTP_HOST} ^almustaqbal\\.sch\\.id')
        ->toContain('RewriteCond %{HTTPS} !=on')
        ->toContain(
            'RewriteRule ^ https://almustaqbal.sch.id%{REQUEST_URI} [R=301,L]'
        );
});
