<?php

it('keeps a hero-gated sequential homepage preparation graph with a DOM owned gallery', function (): void {
    $preparation = file_get_contents(resource_path(
        'js/pages/welcome/preparation.js',
    ));
    $page = file_get_contents(resource_path('js/pages/welcome.js'));
    $hero = file_get_contents(resource_path('js/pages/welcome-hero.js'));
    $heroReadiness = file_get_contents(resource_path(
        'js/pages/welcome-hero/readiness.js',
    ));
    $heroCarousel = file_get_contents(resource_path(
        'js/pages/welcome-hero/carousel.js',
    ));
    $program = file_get_contents(resource_path('js/pages/welcome/program-cards.js'));
    $programController = file_get_contents(resource_path(
        'js/surfaces/home/program-journey/controller.js',
    ));
    $gallery = file_get_contents(resource_path(
        'js/pages/welcome-depth-gallery.js',
    ));
    $vision = file_get_contents(resource_path(
        'js/pages/welcome-vision-story.js',
    ));
    $values = file_get_contents(resource_path(
        'js/surfaces/home/values/controller.js',
    ));
    $vite = file_get_contents(base_path('vite.config.js'));
    $blade = file_get_contents(resource_path('views/welcome.blade.php'));

    expect($preparation)
        ->toContain("'hero',\n  'vision',\n  'program',\n  'values',\n  'gallery',\n  'footer',")
        ->toContain("const HERO_READY_EVENT = 'schoolai:hero-ready'")
        ->toContain('waiting-hero')
        ->toContain('window.addEventListener(HERO_READY_EVENT')
        ->toContain("import('../../surfaces/home/program-values-world.js')")
        ->toContain('prepareHomepageProgram')
        ->toContain("import('../../surfaces/home/values/controller.js')")
        ->toContain('prepareHomepageVisionStory')
        ->toContain('prepareHomepageDepthGallery')
        ->not->toContain("import('../../surfaces/home/article-story/controller.js')")
        ->toContain('for (const section of HOME_PREPARATION_ORDER)')
        ->toContain('await preparationSteps[section]()')
        ->toContain('await yieldToBrowser()')
        ->and($hero)
        ->toContain("import { armHeroReadySignal } from './welcome-hero/readiness.js'")
        ->toContain('armHeroReadySignal(root, slides[0])')
        ->toContain("import('./welcome-hero/carousel.js')")
        ->not->toContain("from './welcome-hero/slider-media.js'")
        ->and($heroCarousel)
        ->toContain("import '../../../css/pages/welcome-hero-carousel.css'")
        ->toContain("from './slider-media.js'")
        ->and($heroReadiness)
        ->toContain("export const HERO_READY_EVENT = 'schoolai:hero-ready'")
        ->toContain("reason: 'shell-painted'")
        ->toContain('window.requestAnimationFrame(finish)')
        ->not->toContain('HERO_READY_FALLBACK_MS')
        ->toContain('window.dispatchEvent(new CustomEvent(HERO_READY_EVENT')
        ->and($program)
        ->toContain('prepareHomepageProgram')
        ->toContain('Promise.resolve(journeyCleanup.ready)')
        ->and($programController)
        ->toContain('cleanup.ready = ready')
        ->toContain('PROGRAM_ENHANCEMENT_ROOT_MARGIN')
        ->toContain('new IntersectionObserver')
        ->toContain('startEnhanced();')
        ->toContain("Promise.resolve('armed')")
        ->not->toContain("resolveReady('gsap')")
        ->not->toContain("resolveReady('fallback')")
        ->and($page)
        ->toContain("import { scheduleHomepagePreparation } from './welcome/preparation.js'")
        ->not->toContain("import '../surfaces/home/values/controller.js'")
        ->not->toContain("import '../surfaces/home/article-story/controller.js'")
        ->and($gallery)
        ->toContain('prepareHomepageDepthGallery')
        ->toContain("'(prefers-reduced-motion: reduce)'")
        ->toContain('readValuesExitProgress')
        ->toContain('paintItems')
        ->not->toContain('loadThreeRuntime')
        ->not->toContain('three-package.js')
        ->and($vision)
        ->toContain('prepareHomepageVisionStory')
        ->and($values)
        ->toContain('const VALUES_SPATIAL_ENABLED = false')
        ->and($vite)
        ->toContain("'resources/css/pages/welcome-critical.css'")
        ->not->toContain("'resources/js/pages/welcome-vision-story.js'")
        ->not->toContain("'resources/js/pages/welcome-depth-gallery.js'")
        ->and($blade)
        ->toContain("'resources/css/pages/welcome-critical.css'")
        ->toContain("app()->getLocale() === 'ar'")
        ->not->toContain("'resources/js/pages/welcome-vision-story.js'")
        ->not->toContain("'resources/js/pages/welcome-depth-gallery.js'")
        ->not->toContain('welcome-article-story.css');
});
