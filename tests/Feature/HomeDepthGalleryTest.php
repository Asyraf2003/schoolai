<?php

it('locks the faithful homepage depth gallery source contract', function (): void {
    $gallery = file_get_contents(resource_path('views/home/sections/gallery.blade.php'));
    $depth = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
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

    expect($gallery)
        ->toContain("@include('home.sections.gallery-depth')")
        ->not->toContain('galeri-section__action')
        ->and($depth)
        ->toContain('data-depth-gallery-canvas')
        ->toContain('data-depth-gallery-labels')
        ->toContain('data-depth-gallery-source')
        ->toContain('data-depth-gallery-end-link')
        ->toContain('data-depth-gallery-end-steps')
        ->toContain('class="depth-gallery__fallback-item"')
        ->toContain('data-position-x="{{ $preset[\'x\'] }}"')
        ->toContain('data-background-color')
        ->not->toContain('<svg')
        ->not->toContain('depth-gallery__card')
        ->not->toContain('data-media-url')
        ->not->toContain('data-is-video')
        ->not->toContain('role="button"')
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
        ->toContain('three@0.183.0')
        ->toContain('/* @vite-ignore */')
        ->and($engine)
        ->toContain('PerspectiveCamera(45, 1, 0.1, 100)')
        ->toContain('new this.THREE.WebGLRenderer')
        ->toContain('DepthGalleryEndCta')
        ->toContain('Math.min(window.devicePixelRatio || 1, 1.5)')
        ->toContain("'ResizeObserver' in window")
        ->and($frame)
        ->toContain('renderer.clearDepth()')
        ->toContain('getDrawingBufferSize')
        ->toContain('hasVisibleEndCta')
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
        ->toContain('this.scrollCurrent / travel')
        ->toContain('this.endProgress')
        ->and($endCta)
        ->toContain("root.classList.toggle('is-depth-end-ready'")
        ->toContain('--depth-label-opacity')
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
