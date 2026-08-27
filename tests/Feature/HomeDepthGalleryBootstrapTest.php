<?php

it('guards the editorial gallery bootstrap without the legacy canvas owner', function (): void {
    $styles = implode("\n", array_map(
        static fn (string $file): string => file_get_contents(resource_path($file)),
        [
            'css/pages/welcome-depth-gallery/base.css',
            'css/pages/welcome-depth-gallery/handoff.css',
            'css/pages/welcome-depth-gallery/responsive.css',
        ],
    ));
    $controller = file_get_contents(resource_path('js/pages/welcome-depth-gallery.js'));
    $blade = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $head = file_get_contents(resource_path('views/partials/site-head-meta.blade.php'));

    expect($styles)
        ->toContain('.gallery-story__title-rail')
        ->toContain('.gallery-story__handoff')
        ->toContain('.gallery-story__stream')
        ->toContain('gallery-ornament-33.webp')
        ->toContain('background-repeat: repeat')
        ->toContain('background-image: none')
        ->toContain('.home-page .galeri-section::after')
        ->not->toContain('@import "../surfaces/home/gallery-depth/base.css"')
        ->and($controller)
        ->toContain('requestAnimationFrame')
        ->toContain('paintHandoff()')
        ->toContain('paintItems()')
        ->toContain('paintStatic()')
        ->toContain('prepareHomepageDepthGallery')
        ->toContain('reducedMotion.addEventListener')
        ->not->toContain('loadThreeRuntime')
        ->not->toContain('DepthGalleryEngine')
        ->and($blade)
        ->toContain('data-gallery-story')
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="button"')
        ->not->toContain('tabindex="-1"')
        ->not->toContain('<canvas');

    expect($head)->not->toContain('welcome-gallery-desktop.css');
    expect(file_exists(public_path('css/welcome-gallery-desktop.css')))->toBeFalse();
    expect(is_dir(resource_path('js/surfaces/home/gallery-depth')))->toBeFalse();
    expect(is_dir(resource_path('css/surfaces/home/gallery-depth')))->toBeFalse();
    expect(count(file(resource_path('js/pages/welcome-depth-gallery.js'))))
        ->toBeLessThanOrEqual(200);
});
