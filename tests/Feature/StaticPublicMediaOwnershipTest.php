<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders versioned static public media from R2 on every homepage locale', function (): void {
    $expectedUrls = array_values(array_filter([
        config('media.static.brand.logo_nav'),
        config('media.static.brand.logo_footer'),
        config('media.static.seo.home_og'),
        config('media.static.ornaments.geometry_32'),
        config('media.static.ornaments.geometry_33'),
        config('media.static.footer.maps'),
        config('media.static.footer.whatsapp'),
        config('media.static.footer.instagram'),
        config('media.static.footer.facebook'),
        config('media.static.footer.gmail'),
        ...config('media.static.footer.partners', []),
    ], static fn (mixed $url): bool => is_string($url) && $url !== ''));

    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);
        $response = $this->withSession(['locale' => $locale])->get(route('home'));
        $response->assertOk();

        $content = $response->getContent();
        foreach ($expectedUrls as $url) {
            expect($content)->toContain($url);
        }

        expect($content)
            ->not->toContain('src="/media/home/')
            ->not->toContain('src="'.url('/media/home/'))
            ->not->toContain('/media/seed/hero/gallery-ornament-');
    }
});

it('keeps the static R2 publish manifest complete versioned and collision free', function (): void {
    $entries = config('media.static_publish', []);

    expect($entries)->toBeArray()->not->toBeEmpty();

    $keys = [];
    foreach ($entries as $entry) {
        expect($entry)->toBeArray()
            ->and($entry['source'] ?? null)->toBeString()
            ->and($entry['key'] ?? null)->toBeString();

        $source = (string) $entry['source'];
        $key = (string) $entry['key'];

        expect(is_file(public_path($source)))->toBeTrue()
            ->and(str_starts_with($key, 'site/'))->toBeTrue()
            ->and(preg_match('/-v\d+\.[a-z0-9]+$/i', $key))->toBe(1);

        $keys[] = $key;
    }

    expect(array_unique($keys))->toHaveCount(count($keys));
});

it('publishes immutable static media with upload verification and no overwrite', function (): void {
    $publisher = file_get_contents(app_path(
        'Support/Media/StaticPublicMediaPublisher.php',
    ));
    $commands = file_get_contents(base_path('routes/console.php'));

    expect($publisher)
        ->toContain("config('media.static_publish'")
        ->toContain("config('media.cache_control')")
        ->toContain('$disk->exists($key)')
        ->toContain('$disk->size($key)')
        ->toContain('Bump the versioned key instead of overwriting immutable media')
        ->toContain('Static R2 upload size mismatch')
        ->and($commands)
        ->toContain("media:publish-static-r2 {--dry-run}")
        ->toContain('StaticPublicMediaPublisher::class');
});
