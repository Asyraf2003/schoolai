<?php

it('owns the desktop Article journey without horizontal document scrolling', function (): void {
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

    expect($view)
        ->toContain('data-article-story')
        ->toContain('data-article-journey')
        ->toContain('data-article-track')
        ->toContain('data-article-roll-stack')
        ->toContain('data-article-final-cta')
        ->toContain('article-story__semantic-links')
        ->toContain("\$articlesSection['title']")
        ->toContain("\$articlesSection['subtitle']")
        ->toContain('->take(4)')
        ->not->toContain('artikel-digest')
        ->and($controller)
        ->toContain("const DESKTOP_QUERY = '(min-width: 1280px)'")
        ->toContain('journey.getBoundingClientRect()')
        ->toContain('track.scrollWidth - window.innerWidth')
        ->toContain('rollStack.scrollHeight - rollWindow.clientHeight')
        ->toContain('window.innerWidth * .42')
        ->toContain('phase(opening, 0.48, 0.92)')
        ->toContain('--article-copy-blur')
        ->toContain('cta?.contains(document.activeElement)')
        ->not->toContain('window.scrollY')
        ->not->toContain('scrollTo(')
        ->and($desktop)
        ->toContain('position: sticky')
        ->toContain('grid-template-columns: minmax(0, 3fr) minmax(18rem, 1fr)')
        ->toContain('grid-template-columns: 38vw 16.6667vw 45.3333vw')
        ->toContain('width: 25vw')
        ->toContain('transform: translate(-50%, calc(-50% + var(--article-copy-y)))')
        ->toContain('inset-inline-start: 100%')
        ->and($base)
        ->toContain('overflow: clip')
        ->toContain('aspect-ratio: 16 / 9')
        ->and($footer)
        ->toContain('min-height: 100vh')
        ->toContain('min-height: 100svh');
});
