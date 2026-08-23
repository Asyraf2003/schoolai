<?php

it('separates authoritative Gallery state from smoothed presentation', function (): void {
    $scroll = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/scroll.js',
    ));
    $endCta = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/end-cta.js',
    ));
    $motion = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/gallery-motion.js',
    ));
    $frame = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/engine-frame.js',
    ));

    expect($scroll)
        ->toContain('this.progressTarget = 0')
        ->toContain('this.progressCurrent = 0')
        ->toContain('this.endProgressTarget = 0')
        ->toContain('this.endProgress = 0')
        ->toContain('this.transitionProgress = 0')
        ->toContain('this.scrollTarget / travel')
        ->toContain('this.scrollCurrent / travel')
        ->toContain('this.readEndProgress(this.progressTarget)')
        ->toContain('this.readEndProgress(this.progressCurrent)')
        ->toContain('(this.handoffTarget - travel) / transitionDistance')
        ->toContain('return this.transitionProgress')
        ->toContain('this.THREE.MathUtils.lerp(')
        ->not->toContain('this.handoffCurrent')
        ->and($endCta)
        ->toContain('this.scroll.getTransitionProgress()')
        ->toContain('this.scroll.endProgressTarget')
        ->toContain('semanticProgress >= 0.7')
        ->toContain('this.scroll.endProgress')
        ->toContain("root.classList.toggle('is-depth-end-ready'")
        ->toContain("'is-depth-transitioning'")
        ->not->toContain('getBoundingClientRect()')
        ->and($motion)
        ->toContain('const endOpacity = 1 - scroll.endProgress')
        ->not->toContain('scroll.endProgressTarget')
        ->and($frame)
        ->toContain('engine.scroll.progressCurrent * totalSteps')
        ->not->toContain('engine.scroll.progressTarget * totalSteps');

    expect(count(file(resource_path(
        'js/surfaces/home/gallery-depth/scroll.js',
    ))))->toBeLessThanOrEqual(200);

    expect(count(file(resource_path(
        'js/surfaces/home/gallery-depth/end-cta.js',
    ))))->toBeLessThanOrEqual(200);
});
