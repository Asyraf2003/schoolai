<?php

it('keeps Article as one horizontal journey with parallax panels and a Demo 5 exit', function (): void {
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
    $footer = file_get_contents(
        resource_path('css/surfaces/home/article-story/footer-release.css'),
    );
    $license = base_path('docs/third-party/codrops-sticky-sections-MIT.txt');

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
        ->toContain('article-story__semantic-links')
        ->toContain("'id' => 'ARTIKEL'")
        ->toContain('->take(4)')
        ->not->toContain('data-article-opening')
        ->not->toContain('article-story__opening-title')
        ->and($controller)
        ->toContain("const DESKTOP_QUERY = '(min-width: 1280px)'")
        ->toContain('track.scrollWidth - window.innerWidth')
        ->toContain('panel.offsetLeft')
        ->toContain('relative * 8')
        ->toContain('--article-media-y')
        ->toContain('--article-heading-y')
        ->toContain('--article-description-y')
        ->toContain('phase(progress, 0.02, 0.68)')
        ->toContain('phase(progress, 0.68, 0.90)')
        ->toContain('phase(progress, 0.90, 1)')
        ->toContain('--article-exit-scale')
        ->toContain('--article-exit-y')
        ->toContain('--article-exit-rotation')
        ->toContain('-10 * effectiveExit')
        ->not->toContain('blur')
        ->not->toContain('window.scrollY')
        ->not->toContain('scrollTo(')
        ->and($desktop)
        ->toContain('position: sticky')
        ->toContain('width: 62vw')
        ->toContain('left: 19vw')
        ->toContain('left: 83vw')
        ->toContain('grid-template-columns: 50vw 50vw')
        ->toContain('width: 33vw')
        ->toContain('transform-origin: 98% 0%')
        ->toContain('rotate(var(--article-exit-rotation))')
        ->and($base)
        ->toContain('scale(1.08)')
        ->toContain('article-story__final-cta')
        ->toContain('background: transparent')
        ->and($footer)
        ->toContain('margin-top: -100svh')
        ->toContain('min-height: 100svh');

    expect(file_exists($license))->toBeTrue();
});
