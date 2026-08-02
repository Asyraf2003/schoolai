<?php

it('locks the homepage atmospheric depth gallery source contract', function (): void {
    $gallery = file_get_contents(resource_path('views/home/sections/gallery.blade.php'));
    $depth = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $entry = file_get_contents(resource_path('css/pages/welcome-depth-gallery.css'));
    $responsive = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/responsive.css'
    ));
    $controller = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/controller.js'
    ));
    $renderer = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/renderer.js'
    ));
    $welcome = file_get_contents(resource_path('views/welcome.blade.php'));
    $vite = file_get_contents(base_path('vite.config.js'));

    expect($gallery)
        ->toContain("@include('home.sections.gallery-depth')")
        ->not->toContain("@include('home.sections.gallery-story')")
        ->and($depth)
        ->toContain('data-depth-gallery')
        ->toContain('data-depth-gallery-canvas')
        ->toContain('aria-hidden="true"')
        ->toContain('role="list"')
        ->toContain('role="listitem"')
        ->toContain('data-media-url')
        ->toContain('href="{{ $mediaUrl }}"')
        ->and($entry)
        ->toContain('gallery-depth/base.css')
        ->toContain('gallery-depth/cards.css')
        ->toContain('gallery-depth/responsive.css')
        ->and($responsive)
        ->toContain('@media (min-width: 640px)')
        ->toContain('@media (min-width: 768px)')
        ->toContain('@media (min-width: 1024px)')
        ->toContain('@media (min-width: 1280px)')
        ->toContain('@media (min-width: 1536px)')
        ->toContain('@media (prefers-reduced-motion: reduce)')
        ->toContain('html[dir="rtl"]')
        ->and($controller)
        ->toContain('IntersectionObserver')
        ->toContain('visibilitychange')
        ->toContain('pagehide')
        ->toContain('requestAnimationFrame')
        ->and($renderer)
        ->toContain("getContext('webgl'")
        ->toContain("powerPreference: 'low-power'")
        ->toContain('Math.min(window.devicePixelRatio || 1, 1.5)')
        ->toContain('webglcontextlost')
        ->and($welcome)
        ->toContain('resources/css/pages/welcome-depth-gallery.css')
        ->toContain('resources/js/pages/welcome-depth-gallery.js')
        ->and($vite)
        ->toContain('resources/css/pages/welcome-depth-gallery.css')
        ->toContain('resources/js/pages/welcome-depth-gallery.js');
});
