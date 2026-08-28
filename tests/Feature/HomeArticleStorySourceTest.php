<?php

it('owns the new static Article preview without the legacy story instrumentation', function (): void {
    $view = file_get_contents(resource_path('views/home/sections/articles.blade.php'));
    $presentation = file_get_contents(app_path('View/Composers/HomeArticlesComposer.php'));
    $welcome = file_get_contents(resource_path('views/welcome.blade.php'));
    $pageCss = file_get_contents(resource_path('css/pages/welcome-article-showcase.css'));
    $vite = file_get_contents(base_path('vite.config.js'));

    expect($view)
        ->toContain('data-article-showcase')
        ->toContain('data-article-variant')
        ->toContain('article-showcase__title-line')
        ->toContain('article-showcase__grid')
        ->toContain('article-showcase__review')
        ->not->toContain('data-article-story')
        ->not->toContain('data-article-journey')
        ->not->toContain('article-debug-mark')
        ->not->toContain('home.debug.article-ruler')
        ->and($presentation)
        ->toContain("'b' => 'Stanford Newsroom'")
        ->toContain("'c' => 'JIS Panorama'")
        ->toContain('->take(4)')
        ->toContain("query('article_variant', 'b')")
        ->and($welcome)
        ->toContain("'resources/css/pages/welcome-article-showcase.css'")
        ->toContain("@include('home.sections.testimonials')")
        ->toContain("@include('home.sections.articles')")
        ->and(strpos($welcome, "@include('home.sections.testimonials')"))
        ->toBeLessThan(strpos($welcome, "@include('home.sections.articles')"))
        ->and($pageCss)
        ->toContain('article-showcase/base.css')
        ->toContain('article-showcase/variants-a-c.css')
        ->toContain('article-showcase/variants-d-f.css')
        ->toContain('article-showcase/responsive.css')
        ->not->toContain('debug-ruler.css')
        ->and($vite)
        ->toContain("'resources/css/pages/welcome-article-showcase.css'")
        ->not->toContain("'resources/css/pages/welcome-article-story.css'");
});
