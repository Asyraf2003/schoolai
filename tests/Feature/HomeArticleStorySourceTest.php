<?php

it('owns the static Lead Rail Article preview without legacy story instrumentation', function (): void {
    $view = file_get_contents(resource_path('views/home/sections/articles.blade.php'));
    $presentation = file_get_contents(app_path('View/Composers/HomeArticlesComposer.php'));
    $welcome = file_get_contents(resource_path('views/welcome.blade.php'));
    $pageCss = file_get_contents(resource_path('css/pages/welcome-article-showcase.css'));
    $baseCss = file_get_contents(resource_path('css/surfaces/home/article-showcase/base.css'));
    $responsiveCss = file_get_contents(resource_path('css/surfaces/home/article-showcase/responsive.css'));
    $typographyCss = file_get_contents(resource_path('css/surfaces/home/article-showcase/typography.css'));
    $galleryResponsiveCss = file_get_contents(resource_path('css/pages/welcome-depth-gallery/responsive.css'));
    $programWideCss = file_get_contents(resource_path('css/pages/welcome/program-journey/wide.css'));
    $programCompactCss = file_get_contents(resource_path('css/pages/welcome/program-journey/compact.css'));
    $vite = file_get_contents(base_path('vite.config.js'));

    expect($view)
        ->toContain('data-article-showcase')
        ->toContain('article-showcase__title-line')
        ->toContain('article-showcase__grid')
        ->toContain('article-showcase__body')
        ->toContain('article-showcase__all')
        ->toContain('article-showcase__all-icon')
        ->not->toContain('data-article-variant')
        ->not->toContain('article-showcase__review')
        ->not->toContain('data-article-story')
        ->not->toContain('data-article-journey')
        ->not->toContain('article-debug-mark')
        ->not->toContain('home.debug.article-ruler')
        ->and($presentation)
        ->toContain('->take(4)')
        ->not->toContain('article_variant')
        ->not->toContain('Request')
        ->and($baseCss)
        ->toContain('position: absolute')
        ->toContain('linear-gradient(')
        ->toContain('margin-inline-start: auto')
        ->toContain('[dir="rtl"] .article-showcase__all')
        ->toContain('--article-cta-arrow-x: -2px')
        ->toContain('--article-cta-arrow-scale-x: -1')
        ->toContain('scaleX(var(--article-cta-arrow-scale-x))')
        ->and($responsiveCss)
        ->toContain('grid-template-columns: repeat(12, minmax(0, 1fr))')
        ->toContain('grid-column: 1 / span 8')
        ->toContain('grid-column: 9 / 13')
        ->toContain('@media (min-width: 768px)')
        ->toContain('@media (max-width: 767px)')
        ->and($typographyCss)
        ->toContain('font-size: clamp(7rem, 12vw, 16rem)')
        ->toContain('font-size: clamp(3.25rem, 16vw, 5rem)')
        ->and($galleryResponsiveCss)
        ->toContain('font-size: clamp(7rem, 12vw, 16rem)')
        ->toContain('font-size: clamp(3.25rem, 16vw, 5rem)')
        ->and($programWideCss)
        ->toContain('font-size: clamp(7rem, 12vw, 16rem)')
        ->and($programCompactCss)
        ->toContain('font-size: clamp(3.25rem, 16vw, 5rem)')
        ->and($welcome)
        ->toContain("'resources/css/pages/welcome-article-showcase.css'")
        ->toContain("@include('home.sections.testimonials')")
        ->toContain("@include('home.sections.articles')")
        ->and(strpos($welcome, "@include('home.sections.testimonials')"))
        ->toBeLessThan(strpos($welcome, "@include('home.sections.articles')"))
        ->and($pageCss)
        ->toContain('article-showcase/base.css')
        ->toContain('article-showcase/typography.css')
        ->toContain('article-showcase/responsive.css')
        ->not->toContain('variants-a-c.css')
        ->not->toContain('variants-d-f.css')
        ->not->toContain('debug-ruler.css')
        ->and($vite)
        ->toContain("'resources/css/pages/welcome-article-showcase.css'")
        ->not->toContain("'resources/css/pages/welcome-article-story.css'");
});
