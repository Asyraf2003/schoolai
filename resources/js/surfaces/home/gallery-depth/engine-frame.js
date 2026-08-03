export function renderDepthFrame(engine, time = performance.now()) {
    const { THREE, renderer, camera, scene } = engine;
    if (!renderer || engine.disposed) return false;

    try {
        engine.scroll.update();
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

    return size.x > 1
        && size.y > 1
        && !context.isContextLost()
        && context.getError() === context.NO_ERROR
        && hasVisiblePlane;
}
