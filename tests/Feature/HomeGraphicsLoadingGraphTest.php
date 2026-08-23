<?php

it('owns one package Three runtime and a sequential homepage preparation graph', function (): void {
    $package = json_decode(file_get_contents(base_path('package.json')), true);
    $lock = json_decode(file_get_contents(base_path('package-lock.json')), true);
    $runtime = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/three-runtime.js',
    ));
    $threePackage = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/three-package.js',
    ));
    $preparation = file_get_contents(resource_path(
        'js/pages/welcome/preparation.js',
    ));
    $page = file_get_contents(resource_path('js/pages/welcome.js'));
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

    expect($package['dependencies']['three'])->toBe('^0.185.1')
        ->and($lock['packages']['node_modules/three']['version'])->toBe('0.185.1')
        ->and($runtime)
        ->toContain("import('./three-package.js')")
        ->not->toContain('cdn.jsdelivr.net')
        ->not->toContain('@vite-ignore')
        ->and($threePackage)
        ->toContain("from 'three'")
        ->and($preparation)
        ->toContain("'hero',\n  'program',\n  'values',\n  'vision',\n  'gallery',\n  'article',\n  'footer',")
        ->toContain("import('../../surfaces/home/program-values-world.js')")
        ->toContain("import('../../surfaces/home/values/controller.js')")
        ->toContain('prepareHomepageVisionStory')
        ->toContain('prepareHomepageDepthGallery')
        ->toContain("import('../../surfaces/home/article-story/controller.js')")
        ->toContain('for (const section of HOME_PREPARATION_ORDER)')
        ->toContain('await preparationSteps[section]()')
        ->and($page)
        ->toContain("import { scheduleHomepagePreparation } from './welcome/preparation.js'")
        ->not->toContain("import '../surfaces/home/values/controller.js'")
        ->not->toContain("import '../surfaces/home/article-story/controller.js'")
        ->and($gallery)
        ->toContain('prepareHomepageDepthGallery')
        ->toContain("'(prefers-reduced-motion: reduce)'")
        ->toContain('loadThreeRuntime')
        ->toContain('mountWhenRelevant')
        ->toContain("rootMargin: '100% 0px 100% 0px'")
        ->and($vision)
        ->toContain('prepareHomepageVisionStory')
        ->and($values)
        ->toContain('const VALUES_SPATIAL_ENABLED = false')
        ->and($vite)
        ->not->toContain("'resources/js/pages/welcome-vision-story.js'")
        ->not->toContain("'resources/js/pages/welcome-depth-gallery.js'")
        ->and($blade)
        ->not->toContain("'resources/js/pages/welcome-vision-story.js'")
        ->not->toContain("'resources/js/pages/welcome-depth-gallery.js'");
});
