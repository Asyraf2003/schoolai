<?php

it('guards the depth gallery bootstrap and removes its legacy owner', function (): void {
    $base = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/base.css'
    ));
    $controller = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/controller.js'
    ));
    $engine = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/engine.js'
    ));
    $frame = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/engine-frame.js'
    ));
    $blade = file_get_contents(resource_path(
        'views/home/sections/gallery-depth.blade.php'
    ));
    $head = file_get_contents(resource_path(
        'views/partials/site-head-meta.blade.php'
    ));

    expect($base)
        ->toContain('display: block')
        ->toContain('visibility: hidden')
        ->toContain('.depth-gallery.is-depth-active .depth-gallery__canvas')
        ->toContain('.depth-gallery.is-depth-active .depth-gallery__fallback-list')
        ->toContain('clip-path: inset(50%)')
        ->and($controller)
        ->toContain('requestAnimationFrame')
        ->toContain('engine.activate()')
        ->toContain("root.classList.add('is-depth-active')")
        ->toContain('fallback.inert = false')
        ->toContain('applyFallbackState()')
        ->and($engine)
        ->toContain("'ResizeObserver' in window")
        ->toContain('getBoundingClientRect()')
        ->toContain('hasPrimaryTexture()')
        ->toContain('activate()')
        ->and($frame)
        ->toContain('getDrawingBufferSize')
        ->toContain('size.x > 1')
        ->toContain('hasVisiblePlane')
        ->toContain('hasVisibleEndCta')
        ->and($blade)
        ->toContain('aria-hidden="true"')
        ->toContain('data-depth-gallery-end-steps')
        ->not->toContain('role="button"')
        ->not->toContain('tabindex="-1"');

    expect($base)
        ->not->toContain(
            '.depth-gallery.is-depth-ready .depth-gallery__canvas {'.PHP_EOL
            .'    display: block;'
        );

    expect($head)->not->toContain('welcome-gallery-desktop.css');

    expect(file_exists(public_path(
        'css/welcome-gallery-desktop.css'
    )))->toBeFalse();

    $files = [
        resource_path('js/surfaces/home/gallery-depth/controller.js'),
        resource_path('js/surfaces/home/gallery-depth/engine.js'),
        resource_path('js/surfaces/home/gallery-depth/engine-frame.js'),
        resource_path('js/surfaces/home/gallery-depth/end-cta.js'),
    ];

    foreach ($files as $file) {
        expect(count(file($file)))->toBeLessThanOrEqual(200);
    }
});
