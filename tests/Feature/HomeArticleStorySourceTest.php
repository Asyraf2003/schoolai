<?php

it('separates Article opening, full-height parallax media, and the roll closing', function (): void {
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
    $pageCss = file_get_contents(
        resource_path('css/pages/welcome-article-story.css'),
    );

    expect($view)
        ->toContain('data-article-story')
        ->toContain('data-article-opening')
        ->toContain('article-story__opening-media')
        ->toContain('article-story__opening-heading')
        ->toContain('article-story__opening-description')
        ->toContain('data-article-main-item')
        ->toContain('data-article-main-image')
        ->toContain('data-article-main-copy')
        ->toContain('article-story__main-description')
        ->toContain('data-article-roll-stack')
        ->toContain('data-article-final-cta')
        ->toContain("'id' => 'Mau lihat artikel selengkapnya?'")
        ->toContain('->take(5)')
        ->toContain('article-debug-mark')
        ->and($controller)
        ->toContain("const HORIZONTAL_END = 0.78")
        ->toContain('const HANDOFF_HOLD_VIEWPORTS = 1')
        ->toContain('closing.offsetLeft')
        ->toContain('const mediaSize = viewportHeight')
        ->toContain('const mediaRight = itemLeft + mediaSize')
        ->toContain('relative * 8')
        ->toContain('--article-main-media-x')
        ->toContain('--article-main-copy-y')
        ->toContain('viewportHeight / 24')
        ->toContain('index % 2 === 0')
        ->toContain('const handoffHold = window.innerHeight * HANDOFF_HOLD_VIEWPORTS')
        ->toContain('journey.offsetHeight - window.innerHeight - handoffHold')
        ->toContain('(progress - HORIZONTAL_END) / (1 - HORIZONTAL_END)')
        ->not->toContain('blur')
        ->not->toContain('scrollTo(')
        ->and($desktop)
        ->toContain('left: 25vw')
        ->toContain('width: 50vw')
        ->toContain('height: 41.666667svh')
        ->toContain('left: 20vw')
        ->toContain('left: 76vw')
        ->toContain('width: min(8vw, 9.5rem)')
        ->toContain('flex: 0 0 calc(100svh + 30vw)')
        ->toContain('width: 100svh')
        ->toContain('height: 100svh')
        ->toContain('left: calc(100svh + 2.5vw)')
        ->toContain('width: 25vw')
        ->toContain('left: 45vw')
        ->and($base)
        ->toContain('article-story__main-copy')
        ->toContain('article-story__roll-item')
        ->and($pageCss)
        ->toContain('debug-ruler.css')
        ->not->toContain('footer-release.css');
});
