<?php

it('guards the editorial gallery bootstrap without the legacy canvas owner', function (): void {
    $styles = file_get_contents(resource_path('css/pages/welcome-depth-gallery.css'));
    $controller = file_get_contents(resource_path('js/pages/welcome-depth-gallery.js'));
    $blade = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $head = file_get_contents(resource_path('views/partials/site-head-meta.blade.php'));

    expect($styles)
        ->toContain('.gallery-story__title-rail')
        ->toContain('position: sticky')
        ->toContain('.gallery-story__handoff')
        ->toContain('.gallery-story__stream')
        ->not->toContain('@import "../surfaces/home/gallery-depth/base.css"')
        ->and($controller)
        ->toContain('requestAnimationFrame')
        ->toContain('paintHandoff()')
        ->toContain('paintIntro(handoffActive)')
        ->toContain('paintItems()')
        ->toContain('paintStatic()')
        ->toContain('reducedMotion.addEventListener')
        ->not->toContain('loadThreeRuntime')
        ->and($blade)
        ->toContain('data-gallery-story')
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="button"')
        ->not->toContain('tabindex="-1"')
        ->not->toContain('<canvas');

    expect($head)->not->toContain('welcome-gallery-desktop.css');
    expect(file_exists(public_path('css/welcome-gallery-desktop.css')))->toBeFalse();
    expect(count(file(resource_path('js/pages/welcome-depth-gallery.js'))))
        ->toBeLessThanOrEqual(200);
});
