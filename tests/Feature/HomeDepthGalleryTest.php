<?php

it('locks the homepage editorial gallery story contract', function (): void {
    $gallery = file_get_contents(resource_path('views/home/sections/gallery.blade.php'));
    $story = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $presentation = file_get_contents(app_path('View/Composers/HomeGalleryComposer.php'));
    $styles = file_get_contents(resource_path('css/pages/welcome-depth-gallery.css'));
    $controller = file_get_contents(resource_path('js/pages/welcome-depth-gallery.js'));
    $welcome = file_get_contents(resource_path('views/welcome.blade.php'));

    expect($gallery)
        ->toContain("@include('home.sections.gallery-depth')")
        ->not->toContain('gallery-mask-handoff')
        ->and($story)
        ->toContain('data-gallery-story')
        ->toContain('data-gallery-story-handoff')
        ->toContain('data-gallery-story-title')
        ->toContain('data-gallery-story-intro')
        ->toContain('data-gallery-story-item')
        ->toContain('data-gallery-story-media')
        ->toContain('data-gallery-story-copy')
        ->toContain('data-depth-gallery-end-link')
        ->toContain('gallery-story__item--{{ ($loop->index % 5) + 1 }}')
        ->not->toContain('data-depth-gallery-canvas')
        ->not->toContain('<canvas')
        ->not->toContain('data-media-url')
        ->not->toContain('data-is-video')
        ->and($presentation)
        ->toContain('$presets[$index % count($presets)]')
        ->and($styles)
        ->toContain('--gallery-story-bg: #6f9b72')
        ->toContain('aspect-ratio: 4 / 5')
        ->toContain('clip-path: inset(var(--gallery-window-inset, 20%)')
        ->toContain('--gallery-image-shift')
        ->toContain('.gallery-story__item--1 .gallery-story__media')
        ->toContain('.gallery-story__item--5 .gallery-story__media')
        ->toContain('.galeri-section.is-gallery-handoff')
        ->toContain('--values-gallery-world-opacity-pct')
        ->toContain('@media (max-width: 1023px)')
        ->toContain('@media (prefers-reduced-motion: reduce)')
        ->and($controller)
        ->toContain('readValuesExitProgress')
        ->toContain('mixColor(BLUE, GALLERY, exitProgress)')
        ->toContain('paintHandoff')
        ->toContain('paintIntro')
        ->toContain('paintItems')
        ->toContain('viewportCenter')
        ->toContain('--gallery-window-inset')
        ->toContain('--gallery-image-shift')
        ->toContain("window.addEventListener('scroll', requestRender")
        ->toContain("'(prefers-reduced-motion: reduce)'")
        ->not->toContain('loadThreeRuntime')
        ->not->toContain('DepthGalleryEngine')
        ->not->toContain('WebGLRenderer')
        ->and($welcome)
        ->not->toContain("@include('home.sections.articles')")
        ->not->toContain('welcome-article-story.css');
});

it('keeps the homepage gallery controller small and scroll owned', function (): void {
    $file = resource_path('js/pages/welcome-depth-gallery.js');
    $source = file_get_contents($file);

    expect(count(file($file)))->toBeLessThanOrEqual(200)
        ->and($source)
        ->toContain('requestAnimationFrame')
        ->toContain('getBoundingClientRect()')
        ->toContain('--gallery-media-opacity')
        ->toContain('--gallery-window-inset')
        ->toContain('--gallery-image-shift')
        ->toContain('--gallery-copy-opacity')
        ->toContain("window.addEventListener('pagehide', destroy)")
        ->toContain("window.removeEventListener('pagehide', destroy)");
});
