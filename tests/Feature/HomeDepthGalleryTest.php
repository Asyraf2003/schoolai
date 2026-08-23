<?php

it('locks the faithful homepage depth gallery source contract', function (): void {
    $homeSections = file_get_contents(app_path(
        'Http/Controllers/Concerns/BuildsHomeSections.php',
    ));
    $gallery = file_get_contents(resource_path('views/home/sections/gallery.blade.php'));
    $depth = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $presentation = file_get_contents(app_path('View/Composers/HomeGalleryComposer.php'));
    $entry = file_get_contents(resource_path('css/pages/welcome-depth-gallery.css'));
    $base = file_get_contents(resource_path('css/surfaces/home/gallery-depth/base.css'));
    $responsive = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/responsive.css'
    ));
    $controller = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/controller.js'
    ));
    $runtime = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/three-runtime.js'
    ));
    $threePackage = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/three-package.js'
    ));
    $engine = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/engine.js'
    ));
    $frame = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/engine-frame.js'
    ));
    $planes = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/gallery.js'
    ));
    $motion = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/gallery-motion.js'
    ));
    $scroll = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/scroll.js'
    ));
    $endCta = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/end-cta.js'
    ));
    $background = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/background.js'
    ));
    $trail = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/trail.js'
    ));
    $trailController = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/trail-controller.js'
    ));
    $license = base_path('docs/third-party/codrops-depth-gallery-MIT.txt');

    expect($homeSections)
        ->toContain("\$gallery['cta']['href'] = route('galeri')")
        ->and($gallery)
        ->toContain("@include('home.sections.gallery-depth')")
        ->not->toContain('galeri-section__action')
        ->and($depth)
        ->toContain('data-depth-gallery-canvas')
        ->toContain('data-depth-gallery-labels')
        ->toContain('data-depth-gallery-source')
        ->toContain('data-depth-gallery-end-link')
        ->toContain('data-depth-gallery-end-steps')
        ->toContain('data-depth-gallery-copy')
        ->toContain('data-depth-gallery-transition="sticky-scale"')
        ->toContain('depth-gallery__end-showcase')
        ->toContain('depth-gallery__end-media--')
        ->toContain('class="depth-gallery__fallback-item"')
        ->toContain('data-position-x="{{ $item[\'preset\'][\'x\'] }}"')
        ->toContain('data-background-color')
        ->not->toContain('<svg')
        ->not->toContain('depth-gallery__card')
        ->not->toContain('data-media-url')
        ->not->toContain('data-is-video')
        ->not->toContain('role="button"')
        ->and($presentation)
        ->toContain('$presets[$index % count($presets)]')
        ->toContain("['x' => -0.9")
        ->toContain("['x' => 0.8")
        ->and($entry)
        ->toContain('gallery-depth/base.css')
        ->toContain('gallery-depth/cards.css')
        ->toContain('gallery-depth/end-cta.css')
        ->toContain('gallery-depth/responsive.css')
        ->and($base)
        ->toContain('height: 100svh')
        ->toContain('position: sticky')
        ->toContain('--depth-step: 280px')
        ->toContain('--depth-label-opacity: 1')
        ->not->toContain('perspective:')
        ->not->toContain('transform-style: preserve-3d')
        ->and($responsive)
        ->toContain('@media (min-width: 640px)')
        ->toContain('@media (min-width: 768px)')
        ->toContain('@media (min-width: 1024px)')
        ->toContain('@media (min-width: 1280px)')
        ->toContain('@media (min-width: 1536px)')
        ->toContain('--depth-step: 500px')
        ->toContain('@media (prefers-reduced-motion: reduce)')
        ->and($controller)
        ->toContain('loadThreeRuntime')
        ->toContain('DepthGalleryEngine')
        ->toContain('bindGalleryRouteExit')
        ->toContain('IntersectionObserver')
        ->toContain('visibilitychange')
        ->not->toContain('createGalleryStoryLightbox')
        ->and($runtime)
        ->toContain("import('./three-package.js')")
        ->not->toContain('cdn.jsdelivr.net')
        ->not->toContain('/* @vite-ignore */')
        ->and($threePackage)
        ->toContain("from 'three'")
        ->and($engine)
        ->toContain('PerspectiveCamera(45, 1, 0.1, 100)')
        ->toContain('new this.THREE.WebGLRenderer')
        ->toContain('DepthGalleryEndCta')
        ->toContain('Math.min(window.devicePixelRatio || 1, 1.5)')
        ->toContain("'ResizeObserver' in window")
        ->and($frame)
        ->toContain('renderer.clearDepth()')
        ->toContain('getDrawingBufferSize')
        ->toContain('isDepthRendererHealthy')
        ->toContain('!context.isContextLost()')
        ->toContain('context.getError() === context.NO_ERROR')
        ->not->toContain('hasVisiblePlane')
        ->not->toContain('hasVisibleEndCta')
        ->not->toContain('plane.material.opacity > 0.01')
        ->and($planes)
        ->toContain('PlaneGeometry(3, 3)')
        ->toContain('this.planeGap = 5')
        ->toContain('this.desktopPlaneScale = 0.67')
        ->toContain('this.mobilePlaneScale = 0.44')
        ->toContain('this.mobileXSpreadFactor = 0.25')
        ->and($motion)
        ->toContain('const endOpacity = 1 - scroll.endProgress')
        ->and($scroll)
        ->toContain('this.scrollSmoothing = 0.08')
        ->toContain('this.velocityDamping = 0.12')
        ->toContain('this.progressTarget')
        ->toContain('this.endProgressTarget')
        ->toContain('getTransitionDistance()')
        ->toContain('getTransitionProgress()')
        ->toContain('fullTravel - transitionDistance')
        ->toContain('this.scrollCurrent / travel')
        ->toContain('this.endProgress')
        ->and($endCta)
        ->toContain('this.scroll.getTransitionProgress()')
        ->toContain('this.scroll.endProgressTarget')
        ->toContain('semanticProgress >= 0.7')
        ->toContain("root.classList.toggle('is-depth-end-ready'")
        ->toContain('is-depth-transitioning')
        ->toContain('--depth-end-progress')
        ->toContain('--depth-label-opacity')
        ->toContain('--depth-transition-scale')
        ->not->toContain('getBoundingClientRect()')
        ->and($background)
        ->toContain('ShaderMaterial')
        ->toContain('setMoodBlend')
        ->and($trail)
        ->toContain('CatmullRomCurve3')
        ->toContain('createTaperedTube')
        ->and($trailController)
        ->toContain('horizontalCycles: 1.85')
        ->toContain('verticalCycles: 2.1')
        ->toContain('this.trail.curveTension = 0.67')
        ->toContain('this.trail.pointSmoothing = 0.53');

    expect(file_exists(resource_path(
        'js/surfaces/home/gallery-depth/renderer.js'
    )))->toBeFalse();
    expect(file_exists(resource_path(
        'js/surfaces/home/gallery-depth/scene.js'
    )))->toBeFalse();
    expect(file_exists($license))->toBeTrue();
});

it('keeps empty Gallery transition frames out of the failure contract', function (): void {
    $frame = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/engine-frame.js'
    ));

    expect($frame)
        ->toContain('return isDepthRendererHealthy(engine);')
        ->toContain('function isDepthRendererHealthy(engine)')
        ->toContain('getDrawingBufferSize')
        ->toContain('!context.isContextLost()')
        ->toContain('context.getError() === context.NO_ERROR')
        ->not->toContain('engine.gallery.planes.some')
        ->not->toContain('engine.endCta.isVisible()');
});

it('keeps Gallery heading continuity locally owned and depth Gallery untouched', function (): void {
    $heading = file_get_contents(resource_path('js/pages/welcome-gallery-heading.js'));
    $continuity = file_get_contents(resource_path(
        'js/surfaces/home/gallery-heading/desktop-continuity.js',
    ));
    $desktop = file_get_contents(
        resource_path('css/pages/welcome-gallery-heading-desktop.css'),
    );

    expect($heading)
        ->toContain("matchMedia('(min-width: 1280px)')")
        ->toContain('initialiseDesktopContinuity')
        ->and($continuity)
        ->toContain("querySelector('[data-depth-gallery]')")
        ->toContain('--gh-opacity')
        ->toContain('--gh-top-y')
        ->toContain('--gh-bottom-y')
        ->toContain('--gh-blur')
        ->toContain('--gallery-handoff-progress')
        ->toContain("window.addEventListener('pagehide', destroy)")
        ->not->toContain('window.scrollTo')
        ->and($desktop)
        ->toContain('@media (min-width: 1280px)')
        ->toContain('.gallery-heading-motion--scroll-linked')
        ->toContain('opacity: var(--gh-opacity)')
        ->toContain('filter: blur(var(--gh-blur))')
        ->toContain('@media (min-width: 1280px) and (prefers-reduced-motion: reduce)');
});

it('uses the closing Gallery composition as the sticky-scale handoff to Article', function (): void {
    $blade = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $presentation = file_get_contents(app_path('View/Composers/HomeGalleryComposer.php'));
    $controller = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/end-cta.js',
    ));
    $styles = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/end-cta.css',
    ));
    $handoffStyles = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/article-handoff.css',
    ));

    expect($blade)
        ->toContain('$depthClosingMedia')
        ->toContain('depth-gallery__end-showcase')
        ->toContain('depth-gallery__end-copy')
        ->toContain('data-depth-gallery-transition="sticky-scale"')
        ->and($presentation)
        ->toContain('$items->slice(max(0, $items->count() - 2))->values()')
        ->and($controller)
        ->toContain('--depth-end-progress')
        ->toContain('--depth-transition-progress')
        ->toContain('--depth-transition-scale')
        ->toContain('this.scroll.getTransitionProgress()')
        ->toContain('this.scroll.endProgressTarget')
        ->toContain('semanticProgress >= 0.7')
        ->toContain('(this.transitionProgress - 0.30) / 0.70')
        ->not->toContain('getBoundingClientRect()')
        ->and($styles)
        ->toContain('.depth-gallery__end-media--1')
        ->toContain('.depth-gallery__end-media--2')
        ->toContain('margin-top: -100svh')
        ->toContain('transform-origin: 50% 0%')
        ->and($handoffStyles)
        ->toContain('.galeri-section:has(.depth-gallery.is-depth-transitioning)')
        ->toContain('background: #f6f3eb');
});

it('keeps every active depth gallery source within the file limit', function (): void {
    $files = glob(resource_path('js/surfaces/home/gallery-depth/*.js'));
    $files[] = resource_path('js/components/gallery-route-transition.js');

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $lines = count(file($file));
        expect($lines, basename($file).' exceeds 200 lines')
            ->toBeLessThanOrEqual(200);
    }
});
