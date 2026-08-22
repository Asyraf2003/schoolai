<?php

it('keeps Article horizontal and gives its closing a Demo 5 handoff', function (): void {
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
        ->toContain('data-article-opening')
        ->toContain('article-story__opening-title')
        ->toContain('data-article-track')
        ->toContain('data-article-closing')
        ->toContain('data-article-roll-stack')
        ->toContain('data-article-final-cta')
        ->toContain('article-story__semantic-links')
        ->toContain("\$articlesSection['title']")
        ->toContain("\$articlesSection['subtitle']")
        ->toContain("'id' => 'ARTIKEL'")
        ->toContain('->take(4)')
        ->not->toContain('artikel-digest')
        ->and($controller)
        ->toContain("const DESKTOP_QUERY = '(min-width: 1280px)'")
        ->toContain('journey.getBoundingClientRect()')
        ->toContain('track.scrollWidth - window.innerWidth')
        ->toContain('rollStack.scrollHeight - rollWindow.clientHeight')
        ->toContain('phase(progress, 0.21, 0.62)')
        ->toContain('phase(progress, 0.88, 1)')
        ->toContain('--article-exit-scale')
        ->toContain('--article-exit-y')
        ->toContain('--article-exit-rotation')
        ->toContain('-10 * effectiveExit')
        ->not->toContain('window.scrollY')
        ->not->toContain('scrollTo(')
        ->and($desktop)
        ->toContain('position: sticky')
        ->toContain('grid-template-columns: minmax(0, 3fr) minmax(18rem, 1fr)')
        ->toContain('grid-template-columns: 52vw 48vw')
        ->toContain('transform-origin: 98% 0%')
        ->toContain('rotate(var(--article-exit-rotation))')
        ->toContain('height: 24svh')
        ->and($base)
        ->toContain('article-story__opening-title')
        ->toContain('article-story__final-cta')
        ->toContain('background: transparent')
        ->and($footer)
        ->toContain('margin-top: -100svh')
        ->toContain('min-height: 100svh');

    expect(file_exists($license))->toBeTrue();
});
