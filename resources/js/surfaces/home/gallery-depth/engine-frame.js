function updateGalleryHeading(engine) {
    const heading = engine.root
        ?.closest('.galeri-section')
        ?.querySelector('[data-gallery-heading]');
    if (!heading) return;

    const planeSteps = Math.max(0, engine.gallery.planes.length - 1);
    const totalSteps = Math.max(1, planeSteps + engine.scroll.endSteps);
    const firstToSecond = engine.THREE.MathUtils.clamp(
        engine.scroll.progressCurrent * totalSteps,
        0,
        1,
    );
    const reveal = engine.THREE.MathUtils.smoothstep(firstToSecond, 0, 1);
    const blur = 14 * (1 - reveal);

    heading.style.setProperty('--gh-media-blur', `${blur.toFixed(2)}px`);
    heading.dataset.galleryHeadingReveal = reveal.toFixed(4);
}

export function renderDepthFrame(engine, time = performance.now()) {
    const { THREE, renderer, camera, scene } = engine;
    if (!renderer || engine.disposed) return false;

    try {
        engine.scroll.update();
        updateGalleryHeading(engine);
        engine.endCta.update();
        engine.trail.update(camera, engine.scroll, time);
        engine.gallery.update(camera, engine.scroll);
        engine.label.update(camera);

        const planeBlend = engine.gallery.getPlaneBlendData(camera.position.z);
        const moodBlend = engine.gallery.getMoodBlendData(camera.position.z);
        if (moodBlend) engine.background.setMoodBlend(moodBlend);

        const velocity = THREE.MathUtils.clamp(
            Math.abs(engine.scroll.velocity)
                / Math.max(engine.scroll.velocityMax, 0.0001),
            0,
            1,
        );
        const blend = planeBlend?.blend || 0;
        const stability = THREE.MathUtils.smoothstep(
            Math.abs(blend - 0.5) * 2,
            0.35,
            1,
        );

        engine.background.setMotionResponse({
            depthProgress: engine.gallery.getDepthProgress(camera.position.z),
            velocityIntensity: velocity * stability,
        });
        engine.background.update(time);

        renderer.clear(true, true, true);
        engine.background.render(renderer);
        renderer.clearDepth();
        renderer.render(scene, camera);

        return isDepthFrameHealthy(engine);
    } catch (error) {
        console.warn('Depth gallery frame failed', error);
        return false;
    }
}

function isDepthFrameHealthy(engine) {
    const size = engine.renderer.getDrawingBufferSize(
        new engine.THREE.Vector2(),
    );
    const context = engine.renderer.getContext();
    const hasVisiblePlane = engine.gallery.planes.some(
        (plane) => plane.material.opacity > 0.01,
    );
    const hasVisibleEndCta = engine.endCta.isVisible();

    return size.x > 1
        && size.y > 1
        && !context.isContextLost()
        && context.getError() === context.NO_ERROR
        && (hasVisiblePlane || hasVisibleEndCta);
}
