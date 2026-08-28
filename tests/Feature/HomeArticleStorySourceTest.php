<?php

it('owns the database-backed three-card Lead Rail Article showcase without legacy story instrumentation', function (): void {
    $view = file_get_contents(resource_path('views/home/sections/articles.blade.php'));
    $galleryView = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $programView = file_get_contents(resource_path('views/home/sections/featured-programs.blade.php'));
    $presentation = file_get_contents(app_path('View/Composers/HomeArticlesComposer.php'));
    $welcome = file_get_contents(resource_path('views/welcome.blade.php'));
    $pageCss = file_get_contents(resource_path('css/pages/welcome-article-showcase.css'));
    $baseCss = file_get_contents(resource_path('css/surfaces/home/article-showcase/base.css'));
    $responsiveCss = file_get_contents(resource_path('css/surfaces/home/article-showcase/responsive.css'));
    $typographyCss = file_get_contents(resource_path('css/surfaces/home/article-showcase/typography.css'));
    $sharedHeadingCss = file_get_contents(resource_path('css/surfaces/home/section-display-heading.css'));
    $galleryBaseCss = file_get_contents(resource_path('css/pages/welcome-depth-gallery/base.css'));
    $galleryResponsiveCss = file_get_contents(resource_path('css/pages/welcome-depth-gallery/responsive.css'));
    $programHeadingCss = file_get_contents(resource_path('css/pages/welcome/program-journey/heading.css'));
    $programWideCss = file_get_contents(resource_path('css/pages/welcome/program-journey/wide.css'));
    $programCompactCss = file_get_contents(resource_path('css/pages/welcome/program-journey/compact.css'));
    $vite = file_get_contents(base_path('vite.config.js'));

    expect($view)
        ->toContain('data-article-showcase')
        ->toContain('article-showcase__title-line')
        ->toContain('home-section-display__header')
        ->toContain('home-section-display__title')
        ->toContain('home-section-display__line')
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
        ->and($galleryView)
        ->toContain('home-section-display__header')
        ->toContain('home-section-display__title')
        ->toContain('home-section-display__line')
        ->and($programView)
        ->toContain('home-section-display__header')
        ->toContain('home-section-display__title')
        ->toContain('home-section-display__line')
        ->and($presentation)
        ->toContain('Article::query()')
        ->toContain('->latestPublished()')
        ->toContain('->limit(3)')
        ->not->toContain("'media/home/")
        ->not->toContain("$preview['items']")
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
        ->toContain('grid-template-rows: repeat(2, minmax(14rem, 1fr))')
        ->toContain('grid-column: 1 / span 8')
        ->toContain('grid-column: 9 / 13')
        ->not->toContain('.article-showcase__card:nth-child(4)')
        ->toContain('@media (min-width: 768px)')
        ->toContain('@media (max-width: 767px)')
        ->and($sharedHeadingCss)
        ->toContain('font-size: clamp(3.4rem, 15vw, 7rem)')
        ->toContain('font-size: clamp(7rem, 12vw, 16rem)')
        ->toContain('font-size: clamp(3.25rem, 16vw, 5rem)')
        ->toContain('line-height: .84')
        ->toContain('line-height: 1.08')
        ->and($typographyCss)
        ->toContain('@import "../section-display-heading.css"')
        ->not->toContain('font-size: clamp(7rem, 12vw, 16rem)')
        ->not->toContain('font-size: clamp(3.25rem, 16vw, 5rem)')
        ->and($galleryBaseCss)
        ->toContain('section-display-heading.css')
        ->and($galleryResponsiveCss)
        ->not->toContain('font-size: clamp(7rem, 12vw, 16rem)')
        ->not->toContain('font-size: clamp(3.25rem, 16vw, 5rem)')
        ->and($programHeadingCss)
        ->toContain('section-display-heading.css')
        ->and($programWideCss)
        ->not->toContain('font-size: clamp(7rem, 12vw, 16rem)')
        ->and($programCompactCss)
        ->not->toContain('font-size: clamp(3.25rem, 16vw, 5rem)')
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
