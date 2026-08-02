<?php

it('locks the homepage atmospheric depth gallery source contract', function (): void {
    $gallery = file_get_contents(resource_path('views/home/sections/gallery.blade.php'));
    $depth = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $entry = file_get_contents(resource_path('css/pages/welcome-depth-gallery.css'));
    $cards = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/cards.css'
    ));
    $responsive = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/responsive.css'
    ));
    $controller = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/controller.js'
    ));
    $renderer = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/renderer.js'
    ));
    $welcomeScript = file_get_contents(resource_path('js/pages/welcome.js'));
    $welcome = file_get_contents(resource_path('views/welcome.blade.php'));
    $vite = file_get_contents(base_path('vite.config.js'));

    expect($gallery)
        ->toContain("@include('home.sections.gallery-depth')")
        ->not->toContain("@include('home.sections.gallery-story')")
        ->and(file_exists(resource_path('views/home/sections/gallery-story.blade.php')))
        ->toBeFalse()
        ->and(file_exists(resource_path('js/pages/welcome/gallery-story.js')))
        ->toBeFalse()
        ->and($welcomeScript)
        ->not->toContain("import './welcome/gallery-story.js'")
        ->and($depth)
        ->toContain('data-depth-gallery')
        ->toContain('data-depth-gallery-canvas')
        ->toContain('depth-gallery__card--reverse')
        ->toContain('depth-gallery__media')
        ->toContain('depth-gallery__copy')
        ->toContain('aria-hidden="true"')
        ->toContain('role="list"')
        ->toContain('role="listitem"')
        ->toContain('data-media-url')
        ->toContain('href="{{ $mediaUrl }}"')
        ->and($entry)
        ->toContain('gallery-depth/base.css')
        ->toContain('gallery-depth/cards.css')
        ->toContain('gallery-depth/responsive.css')
        ->and($cards)
        ->toContain('grid-template-columns: minmax(0, 1fr)')
        ->toContain('width: auto')
        ->toContain('height: auto')
        ->toContain('max-width: 100%')
        ->toContain('object-fit: contain')
        ->toContain('border-radius: 0')
        ->and($responsive)
        ->toContain('@media (min-width: 640px)')
        ->toContain('@media (min-width: 768px)')
        ->toContain('@media (min-width: 1024px)')
        ->toContain('@media (min-width: 1280px)')
        ->toContain('@media (min-width: 1536px)')
        ->toContain('@media (prefers-reduced-motion: reduce)')
        ->toContain('html[dir="rtl"]')
        ->and($controller)
        ->toContain("root.style.setProperty('--depth-atmosphere'")
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
