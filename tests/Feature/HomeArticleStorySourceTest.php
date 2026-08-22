<?php

it('adapts Codrops StickySections demo 3 for the homepage Article journey', function (): void {
    $view = file_get_contents(resource_path('views/home/sections/articles.blade.php'));
    $controller = file_get_contents(
        resource_path('js/surfaces/home/article-story/controller.js'),
    );
    $desktop = file_get_contents(
        resource_path('css/surfaces/home/article-story/desktop.css'),
    );
    $base = file_get_contents(
        resource_path('css/surfaces/home/article-story/base.css'),
    );
    $license = base_path('docs/third-party/codrops-sticky-sections-MIT.txt');

    expect($view)
        ->toContain('data-article-story')
        ->toContain('data-article-journey')
        ->toContain('data-article-stack')
        ->toContain('data-article-sticky')
        ->toContain('data-article-final-cta')
        ->toContain('article-story__semantic-links')
        ->toContain("\$articlesSection['title']")
        ->toContain("\$articlesSection['subtitle']")
        ->toContain('->take(4)')
        ->and($controller)
        ->toContain("const DESKTOP_QUERY = '(min-width: 1280px)'")
        ->toContain('stack.getBoundingClientRect()')
        ->toContain('const localScroll = -stackRect.top')
        ->toContain('const progress = clamp((localScroll - start) / viewportHeight)')
        ->toContain("'--article-sticky-scale'")
        ->not->toContain('window.scrollY')
        ->not->toContain('gsap')
        ->not->toContain('Lenis')
        ->and($desktop)
        ->toContain('position: sticky')
        ->toContain('min-height: 100svh')
        ->toContain('will-change: transform')
        ->and($base)
        ->toContain('overflow: clip')
        ->toContain('transform: scale(var(--article-sticky-scale))')
        ->toContain('transform-origin: 50% 0%')
        ->toContain('transform-origin: 50% 100%');

    expect(file_exists($license))->toBeTrue();
});
