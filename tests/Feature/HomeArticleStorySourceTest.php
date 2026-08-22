<?php

it('keeps Article as a measured horizontal rail that ends on the rolling article prompt', function (): void {
    $view = file_get_contents(resource_path('views/home/sections/articles.blade.php'));
    $controller = file_get_contents(
        resource_path('js/surfaces/home/article-story/controller.js'),
    );
    $desktop = file_get_contents(
        resource_path('css/surfaces/home/article-story/desktop.css'),
    );
    $pageCss = file_get_contents(
        resource_path('css/pages/welcome-article-story.css'),
    );

    expect($view)
        ->toContain('data-article-story')
        ->toContain('data-article-journey')
        ->toContain('data-article-track')
        ->toContain('data-article-panel')
        ->toContain('data-article-panel-image')
        ->toContain('data-article-panel-heading')
        ->toContain('data-article-panel-description')
        ->toContain('data-article-closing')
        ->toContain('data-article-roll-stack')
        ->toContain('data-article-final-cta')
        ->toContain('article-story__panel--opening')
        ->toContain("'id' => 'ARTIKEL'")
        ->toContain('->take(5)')
        ->toContain('--article-count:')
        ->toContain('tes8-')
        ->toContain("home.debug.article-ruler")
        ->and($controller)
        ->toContain("const DESKTOP_QUERY = '(min-width: 1280px)'")
        ->toContain('closing.offsetLeft')
        ->toContain('panel.offsetLeft')
        ->toContain('relative * 8')
        ->toContain('--article-media-x')
        ->toContain('-20.833333 * copyProgress')
        ->toContain('45 * copyProgress')
        ->toContain('paintClosingMotion(trackX)')
        ->not->toContain('--article-exit-scale')
        ->not->toContain('--article-exit-y')
        ->not->toContain('--article-exit-rotation')
        ->not->toContain('blur')
        ->not->toContain('window.scrollY')
        ->not->toContain('scrollTo(')
        ->and($desktop)
        ->toContain('position: sticky')
        ->toContain('flex: 0 0 70vw')
        ->toContain('top: 33.333333svh')
        ->toContain('left: 30vw')
        ->toContain('width: 40vw')
        ->toContain('height: 41.666667svh')
        ->toContain('top: 25svh')
        ->toContain('left: 20vw')
        ->toContain('left: 75vw')
        ->toContain('left: 5vw')
        ->toContain('width: 25vw')
        ->toContain('left: 45vw')
        ->toContain('width: 35vw')
        ->not->toContain('rotate(var(--article-exit-rotation))')
        ->and($pageCss)
        ->toContain('debug-ruler.css')
        ->not->toContain('footer-release.css');
});
