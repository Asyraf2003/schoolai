<?php

it('owns Gallery enhancement state through one bounded lifecycle', function (): void {
    $lifecycle = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/lifecycle.js',
    ));
    $controller = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/controller.js',
    ));
    $engine = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/engine.js',
    ));
    $routeTransition = file_get_contents(resource_path(
        'js/components/gallery-route-transition.js',
    ));

    expect($lifecycle)
        ->toContain("Semantic: 'SEMANTIC'")
        ->toContain("StaticReady: 'STATIC_READY'")
        ->toContain("Fetching: 'FETCHING'")
        ->toContain("Prepared: 'PREPARED'")
        ->toContain("EnhancementReady: 'ENHANCEMENT_READY'")
        ->toContain("Active: 'ACTIVE'")
        ->toContain("Suspended: 'SUSPENDED'")
        ->toContain("Disposed: 'DISPOSED'")
        ->toContain('root.dataset.depthLifecycle = state')
        ->toContain("root.classList.toggle('is-depth-fallback', staticOnly)")
        ->toContain("root.classList.toggle('is-depth-ready', ready)")
        ->toContain("root.classList.toggle('is-depth-active', visible)")
        ->toContain('fallback.inert = false')
        ->toContain("'is-depth-leaving'")
        ->and($controller)
        ->toContain('createGalleryLifecycle')
        ->toContain('function initialize()')
        ->toContain('function activateEngine()')
        ->toContain('function start()')
        ->toContain('function stop()')
        ->toContain('function suspend()')
        ->toContain('function destroy()')
        ->toContain('if (event.persisted) suspend()')
        ->toContain('else destroy()')
        ->toContain('routeTransition?.restore()')
        ->not->toContain('let disposed')
        ->not->toContain('let initializing')
        ->not->toContain('let active')
        ->and($engine)
        ->toContain('if (this.disposed) return false')
        ->toContain('if (this.initialized) return true')
        ->toContain('if (!this.initialized || this.disposed) return false')
        ->and($routeTransition)
        ->toContain('restore() {}')
        ->toContain("root.classList.remove('is-depth-leaving')");

    $files = [
        resource_path('js/surfaces/home/gallery-depth/lifecycle.js'),
        resource_path('js/surfaces/home/gallery-depth/controller.js'),
        resource_path('js/surfaces/home/gallery-depth/engine.js'),
        resource_path('js/components/gallery-route-transition.js'),
    ];

    foreach ($files as $file) {
        expect(count(file($file)), basename($file).' exceeds 200 lines')
            ->toBeLessThanOrEqual(200);
    }
});
