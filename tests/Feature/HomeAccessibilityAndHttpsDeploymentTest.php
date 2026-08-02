<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exposes editorial descriptions without unsupported paragraph aria labels', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);

        $content = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'))
            ->assertOk()
            ->getContent();

        preg_match_all(
            '/<p class="welcome-editorial-heading__description"([^>]*)>(.*?)<\/p>/su',
            $content,
            $descriptions,
            PREG_SET_ORDER,
        );

        expect($descriptions)->not->toBeEmpty();

        foreach ($descriptions as $description) {
            expect($description[1])
                ->not->toContain('aria-label')
                ->and($description[2])
                ->toContain('class="sr-only"')
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
