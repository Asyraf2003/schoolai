<?php

it('separates Article opening, full-height parallax media, and the roll closing', function (): void {
    $view = file_get_contents(resource_path('views/home/sections/articles.blade.php'));
    $presentation = file_get_contents(app_path('View/Composers/HomeArticlesComposer.php'));
    $idCopy = file_get_contents(lang_path('id/home_presentation.php'));
    $desktop = file_get_contents(
        resource_path('css/surfaces/home/article-story/desktop.css'),
    );
    $desktopClosing = file_get_contents(
        resource_path('css/surfaces/home/article-story/desktop-closing.css'),
    );
    $base = file_get_contents(
        resource_path('css/surfaces/home/article-story/base.css'),
    );
    $closing = file_get_contents(
        resource_path('css/surfaces/home/article-story/closing.css'),
    );
    $pageCss = file_get_contents(
        resource_path('css/pages/welcome-article-story.css'),
    );

    expect($view)
        ->toContain('data-article-story')
        ->toContain('data-article-opening')
        ->toContain('data-article-opening-link')
        ->toContain('article-story__opening-media')
        ->toContain('article-story__opening-heading')
        ->toContain('<h3>{{ $articleDisplayHeading }}</h3>')
        ->toContain('article-story__opening-description')
        ->toContain('<p>{{ $articleDescription }}</p>')
        ->toContain('data-article-main-item')
        ->toContain('data-article-main-image')
        ->toContain('data-article-main-copy')
        ->toContain('article-story__main-description')
        ->toContain('data-article-roll-stack')
        ->toContain('data-article-final-cta')
        ->toContain('article-debug-mark')
        ->and($presentation)
        ->toContain('->take(5)')
        ->toContain("'display_issue'")
        ->and($idCopy)
        ->toContain("'closing_heading' => 'Mau lihat artikel selengkapnya?'")
        ->and($desktop)
        ->toContain('article-story__opening-link')
        ->toContain('left: 25vw')
        ->toContain('width: 50vw')
        ->toContain('height: 41.666667svh')
        ->toContain('left: 20vw')
        ->toContain('left: 75.6vw')
        ->toContain('width: min(5.5vw, 6.75rem)')
        ->toContain('flex: 0 0 calc(100svh + 40vw)')
        ->toContain('width: calc(100svh + 10vw)')
        ->toContain('height: 100svh')
        ->toContain('left: calc(100svh + 12.5vw)')
        ->and($desktopClosing)
        ->toContain('width: 25vw')
        ->toContain('left: 45vw')
        ->and($base)
        ->toContain('background: #f6f3eb')
        ->toContain('article-story__opening-link')
        ->toContain('article-story__main-copy')
        ->and($closing)
        ->toContain('article-story__roll-item')
        ->and($pageCss)
        ->toContain('closing.css')
        ->toContain('desktop-closing.css')
        ->toContain('debug-ruler.css')
        ->not->toContain('footer-release.css');
});
