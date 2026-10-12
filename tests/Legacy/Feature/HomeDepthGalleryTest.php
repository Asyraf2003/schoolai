<?php

it('locks the homepage editorial gallery story contract', function (): void {
    $gallery = file_get_contents(resource_path('views/home/sections/gallery.blade.php'));
    $story = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $presentation = file_get_contents(app_path('View/Composers/HomeGalleryComposer.php'));
    $styles = implode("\n", array_map(
        static fn (string $file): string => file_get_contents(resource_path($file)),
        [
            'css/pages/welcome-depth-gallery/base.css',
            'css/pages/welcome-depth-gallery/handoff.css',
            'css/pages/welcome-depth-gallery/responsive.css',
        ],
    ));
    $controller = readOwnedSource(resource_path('js/pages/welcome-depth-gallery.js'), ['resources/js/pages/welcome/gallery-story-frame.js', 'resources/js/pages/welcome/scroll-frame.js']);
    $pattern = file_get_contents(resource_path('js/pages/welcome/gallery-pattern.js'));
    $visual = file_get_contents(resource_path('js/pages/welcome/gallery-story-visual.js'));
    $welcome = file_get_contents(resource_path('views/welcome.blade.php'));

    expect($gallery)
        ->toContain("@include('home.sections.gallery-depth')")
        ->toContain("config('media.static.ornaments.geometry_33')")
        ->not->toContain('gallery-mask-handoff')
        ->and($story)
        ->toContain('data-gallery-story')
        ->toContain('data-gallery-story-handoff')
        ->toContain('data-gallery-story-title')
        ->toContain('data-gallery-story-intro')
        ->toContain('data-gallery-story-item')
        ->toContain('data-gallery-story-media')
        ->toContain('data-gallery-story-visual')
        ->toContain('data-gallery-story-copy')
        ->toContain('data-gallery-background')
        ->toContain("gallery-story__item--{{ \$loop->odd ? 'right' : 'left' }}")
        ->toContain('$loop->last && $hasDepthCta')
        ->toContain('gallery-story__item-link')
        ->toContain('data-depth-gallery-end-link')
        ->not->toContain('gallery-story__closing-link')
        ->not->toContain('data-depth-gallery-canvas')
        ->not->toContain('<canvas')
        ->not->toContain('data-media-url')
        ->not->toContain('data-is-video')
        ->and($presentation)
        ->toContain('$presets[$index % count($presets)]')
        ->toContain('home_presentation.gallery_more')
        ->toContain("'background' => '#6f9b72'")
        ->toContain("'background' => '#c6ad78'")
        ->and($styles)
        ->toContain('--gallery-story-bg: #6f9b72')
        ->toContain('transition: background-color 760ms')
        ->toContain('--gallery-story-pattern: none')
        ->toContain('.is-gallery-pattern-ready::before')
        ->toContain('background-repeat: repeat')
        ->toContain('background-image: none')
        ->not->toContain('/media/seed/hero/')
        ->not->toContain('data:image/svg+xml')
        ->not->toContain('repeating-conic-gradient')
        ->not->toContain('aspect-ratio: 4 / 5')
        ->toContain('object-fit: contain')
        ->toContain('max-height: 100%')
        ->toContain('background: transparent')
        ->toContain('overflow: visible')
        ->toContain('is-gallery-enhanced .gallery-story__visual')
        ->toContain('var(--gallery-window-top, 40%)')
        ->toContain('var(--gallery-window-bottom, 0%)')
        ->toContain('.gallery-story__item--right .gallery-story__media')
        ->toContain('.gallery-story__item--left .gallery-story__media')
        ->toContain('.gallery-story__item-link')
        ->toContain('.gallery-story__intro {')
        ->toContain('--gallery-intro-height: clamp(34rem, 68svh, 48rem)')
        ->toContain('--gallery-intro-height: clamp(28rem, 62svh, 38rem)')
        ->toContain('--gallery-intro-height: clamp(22rem, 56svh, 30rem)')
        ->toContain('min-height: var(--gallery-intro-height)')
        ->toContain('place-items: center')
        ->toContain('.gallery-story__title {')
        ->toContain('transform: none')
        ->toContain('.home-page .values-story__exit {')
        ->toContain('background: none')
        ->toContain('.home-page .values-story__exit::before')
        ->toContain('display: none')
        ->toContain('.galeri-section.is-gallery-handoff')
        ->toContain('--values-gallery-world-opacity-pct')
        ->toContain('@media (max-width: 1023px)')
        ->toContain('@media (prefers-reduced-motion: reduce)')
        ->and($controller)
        ->toContain('armGalleryPattern')
        ->toContain('readValuesExitProgress')
        ->toContain('mixColor(BLUE, GALLERY, exitProgress)')
        ->toContain('paintHandoff')
        ->toContain('galleryStoryVisual(item)')
        ->toContain('visualTop')
        ->toContain('Math.abs(signed)')
        ->toContain('signed >= 0 ? closingInset : 0')
        ->toContain('signed < 0 ? closingInset : 0')
        ->toContain('item.dataset.galleryBackground')
        ->toContain("section.style.setProperty('--gallery-story-bg'")
        ->toContain('--gallery-window-top')
        ->toContain('--gallery-window-bottom')
        ->toContain('subscribeHomepageFrame')
        ->toContain("'(prefers-reduced-motion: reduce)'")
        ->not->toContain('loadThreeRuntime')
        ->not->toContain('DepthGalleryEngine')
        ->not->toContain('WebGLRenderer')
        ->and($pattern)
        ->toContain("rootMargin: '75% 0px 75% 0px'")
        ->toContain("section.classList.add('is-gallery-pattern-ready')")
        ->and($visual)
        ->toContain('media.clientWidth / visual.naturalWidth')
        ->toContain('media.clientHeight / visual.naturalHeight')
        ->and($welcome)
        ->toContain("@include('home.sections.articles')")
        ->toContain('welcome-article-showcase.css')
        ->not->toContain('welcome-article-story.css');
});

it('keeps the homepage gallery controller small and scroll owned', function (): void {
    $file = resource_path('js/pages/welcome-depth-gallery.js');
    $source = readOwnedSource($file, ['resources/js/pages/welcome/gallery-story-frame.js', 'resources/js/pages/welcome/scroll-frame.js']);

    expect(count(file($file)))->toBeLessThanOrEqual(200)
        ->and($source)
        ->toContain('requestAnimationFrame')
        ->toContain('getBoundingClientRect()')
        ->toContain('--gallery-media-opacity')
        ->toContain('--gallery-window-top')
        ->toContain('--gallery-window-bottom')
        ->toContain('--gallery-copy-opacity')
        ->toContain("window.addEventListener('pagehide', destroy)")
        ->toContain("window.removeEventListener('pagehide', destroy)");
});
