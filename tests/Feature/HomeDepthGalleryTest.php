<?php

it('locks the homepage atmospheric depth gallery source contract', function (): void {
    $gallery = file_get_contents(resource_path('views/home/sections/gallery.blade.php'));
    $depth = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $entry = file_get_contents(resource_path('css/pages/welcome-depth-gallery.css'));
    $base = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/base.css'
    ));
    $cards = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/cards.css'
    ));
    $responsive = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/responsive.css'
    ));
    $scene = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/scene.js'
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
        ->toContain('data-depth-gallery-trail')
        ->toContain('pathLength="1"')
        ->toContain('depth-gallery__card--reverse')
        ->toContain('depth-gallery__media')
        ->toContain('depth-gallery__copy')
        ->not->toContain('depth-gallery__eyebrow')
        ->toContain('role="list"')
        ->toContain('role="listitem"')
        ->toContain('href="{{ $mediaUrl }}"')
        ->and($entry)
        ->toContain('gallery-depth/base.css')
        ->toContain('gallery-depth/cards.css')
        ->toContain('gallery-depth/responsive.css')
        ->and($base)
        ->toContain('.depth-gallery__trail-line')
        ->toContain('stroke-dasharray: 1')
        ->and($cards)
        ->toContain('grid-template-columns: minmax(0, 1fr)')
        ->toContain('width: auto')
        ->toContain('height: auto')
        ->toContain('object-fit: contain')
        ->toContain('border-radius: 0')
        ->toContain('color: #111')
        ->toContain('font-size: clamp(0.88rem')
        ->not->toContain('font-size: clamp(1.55rem')
        ->and($responsive)
        ->toContain('@media (min-width: 640px)')
        ->toContain('@media (min-width: 768px)')
        ->toContain('@media (min-width: 1024px)')
        ->toContain('@media (min-width: 1280px)')
        ->toContain('@media (min-width: 1536px)')
        ->toContain('@media (prefers-reduced-motion: reduce)')
        ->toContain('html[dir="rtl"]')
        ->and($scene)
        ->toContain('viewportWidth * 0.032')
        ->toContain('12, 54')
        ->and($controller)
        ->toContain("root.style.setProperty('--depth-atmosphere'")
        ->toContain("trail?.style.setProperty('stroke-dashoffset'")
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
