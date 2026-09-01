<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

it('renders versioned static public media from R2 on every homepage locale', function (): void {
    $expectedUrls = array_values(array_filter([
        config('media.static.brand.logo_nav'),
        config('media.static.brand.logo_footer'),
        config('media.static.brand.favicon'),
        config('media.static.brand.apple_touch_icon'),
        config('media.static.seo.home_og'),
        config('media.static.ornaments.geometry_32'),
        config('media.static.ornaments.geometry_33'),
        config('media.static.footer.maps'),
        config('media.static.footer.whatsapp'),
        config('media.static.footer.instagram'),
        config('media.static.footer.facebook'),
        config('media.static.footer.gmail'),
        ...config('media.static.language_flags', []),
        ...config('media.static.footer.partners', []),
    ], static fn (mixed $url): bool => is_string($url) && $url !== '')));

    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);
        $response = $this->withSession(['locale' => $locale])->get(route('home'));
        $response->assertOk();

        $content = $response->getContent();
        foreach ($expectedUrls as $url) {
            expect($content)->toContain($url);
        }

        expect($content)
            ->not->toContain('images.unsplash.com')
            ->not->toContain('resources.finalsite.net')
            ->not->toContain('testimonial-nature-')
            ->not->toContain('src="/media/')
            ->not->toContain('href="'.url('/favicon.ico'))
            ->not->toContain('href="'.url('/apple-touch-icon.png'))
            ->not->toContain('/media/seed/hero/gallery-ornament-');
    }
});

it('rejects legacy static media URLs from Vite-owned source', function (): void {
    $roots = [resource_path('css'), resource_path('js')];
    $violations = [];
    $legacyMediaReference = "~(?:url\\(\\s*['\"]?|['\"`])/media/~";

    foreach ($roots as $root) {
        foreach (File::allFiles($root) as $file) {
            if (! in_array($file->getExtension(), ['css', 'js'], true)) {
                continue;
            }

            if (preg_match($legacyMediaReference, $file->getContents()) === 1) {
                $violations[] = str_replace(
                    base_path().DIRECTORY_SEPARATOR,
                    '',
                    $file->getPathname(),
                );
            }
        }
    }

    expect($violations)->toBe([], 'Legacy /media/ Vite references: '.implode(', ', $violations));
});

it('keeps static content media out of the public filesystem', function (): void {
    expect(is_dir(public_path('media')))->toBeFalse()
        ->and(is_file(public_path('favicon.ico')))->toBeFalse()
        ->and(is_file(public_path('apple-touch-icon.png')))->toBeFalse()
        ->and(config('media.static_publish'))->toBeNull()
        ->and(is_file(app_path('Support/Media/StaticPublicMediaPublisher.php')))->toBeFalse();
});

it('keeps real school media and testimonial URLs versioned on canonical R2', function (): void {
    $schoolLife = config('media.static.school_life', []);
    $testimonials = config('media.static.testimonials', []);
    $languageFlags = config('media.static.language_flags', []);

    expect($schoolLife)->toBeArray()->toHaveCount(17)
        ->and($testimonials)->toBeArray()->toHaveCount(22)
        ->and($languageFlags)->toBeArray()->toHaveCount(3);

    foreach ([...array_values($schoolLife), ...array_values($testimonials), ...array_values($languageFlags)] as $url) {
        expect($url)->toBeString()
            ->and($url)->toStartWith('https://media.almustaqbal.sch.id/site/')
            ->and(preg_match('/-v\\d+\\.webp$/', $url))->toBe(1);
    }
});
