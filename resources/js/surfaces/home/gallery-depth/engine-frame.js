function updateGalleryHeading(engine) {
    const heading = engine.root
        ?.closest('.galeri-section')
        ?.querySelector('[data-gallery-heading]');
    if (!heading) return;

    const planeSteps = Math.max(0, engine.gallery.planes.length - 1);
    const totalSteps = Math.max(1, planeSteps + engine.scroll.endSteps);

    /*
     * Heading mengikuti perjalanan media #1 secara perseptual, bukan mentah dari
     * opacity material. Plane besar tetap terbaca walau opacity numeriknya sudah
     * turun cukup jauh, sementara teks hitam dengan angka opacity yang sama akan
     * terlihat hilang jauh lebih cepat. Karena itu visibility heading memakai
     * gabungan camera-travel + sqrt(actual plane opacity), lalu baru habis ketika
     * media #1 benar-benar meninggalkan scene menuju media #2.
     */
    const firstToSecond = engine.THREE.MathUtils.clamp(
        engine.scroll.progressCurrent * totalSteps,
        0,
        1,
    );
    const approach = engine.THREE.MathUtils.smoothstep(
        firstToSecond,
        0.02,
        0.98,
    );
    const firstPlaneOpacity = engine.THREE.MathUtils.clamp(
        engine.gallery.planes[0]?.material?.opacity ?? (1 - approach),
        0,
        1,
    );
    const cameraPresence = 1 - engine.THREE.MathUtils.smoothstep(
        firstToSecond,
        0.52,
        1,
    );
    const planePresence = Math.sqrt(firstPlaneOpacity);
    const mediaOpacity = firstToSecond < 0.01
        ? 1
        : engine.THREE.MathUtils.clamp(
            Math.max(cameraPresence, planePresence),
            0,
            1,
        );
    const departure = 1 - mediaOpacity;
    const blur = 11 * departure;
    const scale = 1 + approach * 0.72;

    heading.style.setProperty('--gh-media-opacity', mediaOpacity.toFixed(4));
    heading.style.setProperty('--gh-media-blur', `${blur.toFixed(2)}px`);
    heading.style.setProperty('--gh-media-scale', scale.toFixed(4));
    heading.dataset.galleryHeadingDeparture = departure.toFixed(4);
}

export function renderDepthFrame(engine, time = performance.now()) {
    const { THREE, renderer, camera, scene } = engine;
    if (!renderer || engine.disposed) return false;

    try {
        engine.scroll.update();
        engine.endCta.update();
        engine.trail.update(camera, engine.scroll, time);
        engine.gallery.update(camera, engine.scroll);
        updateGalleryHeading(engine);
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
