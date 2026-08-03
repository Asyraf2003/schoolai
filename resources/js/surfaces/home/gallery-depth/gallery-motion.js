export function updateGalleryMotion(gallery, blend, scroll) {
    const THREE = gallery.THREE;
    gallery.pointerCurrent.lerp(
        gallery.pointerTarget,
        gallery.parallaxSmoothing,
    );
    const velocityMax = Math.max(scroll.velocityMax, 0.0001);
    const velocity = THREE.MathUtils.clamp(
        Math.abs(scroll.velocity) / velocityMax,
        0,
        1,
    );
    const endOpacity = 1 - scroll.endProgress;
    gallery.targetBreathIntensity = Math.min(
        1,
        velocity * gallery.breathGain,
    );
    gallery.breathIntensity = THREE.MathUtils.lerp(
        gallery.breathIntensity,
        gallery.targetBreathIntensity,
        gallery.breathSmoothing,
    );
    gallery.driftTarget = THREE.MathUtils.clamp(
        scroll.velocity / velocityMax,
        -1,
        1,
    );
    gallery.driftCurrent = THREE.MathUtils.lerp(
        gallery.driftCurrent,
        gallery.driftTarget,
        gallery.gestureParallaxSmoothing,
    );

    const spread = gallery.getXSpreadFactor();
    const baseScale = gallery.getBaseScale();
    gallery.planes.forEach((plane, index) => {
        let targetOpacity = index === blend.currentPlaneIndex
            ? (1 - blend.blend) * endOpacity
            : 0;
        if (index === blend.nextPlaneIndex) {
            targetOpacity = Math.max(
                targetOpacity,
                blend.blend * endOpacity,
            );
        }
        plane.material.opacity = THREE.MathUtils.lerp(
            plane.material.opacity,
            targetOpacity,
            gallery.planeFadeSmoothing,
        );
        const opacity = plane.material.opacity;
        const depthInfluence = 1 + index * 0.05;
        const parallaxInfluence = opacity * depthInfluence;
        const base = plane.userData.basePosition;
        plane.position.x = base.x * spread
            + gallery.pointerCurrent.x
                * gallery.parallaxAmountX
                * parallaxInfluence;
        plane.position.y = base.y
            + gallery.pointerCurrent.y
                * gallery.parallaxAmountY
                * parallaxInfluence
            + gallery.driftCurrent * gallery.gestureParallaxAmountY;
        const breath = gallery.breathIntensity * opacity;
        plane.rotation.x = -gallery.pointerCurrent.y
            * gallery.breathTiltAmount
            * breath;
        plane.rotation.y = gallery.pointerCurrent.x
            * gallery.breathTiltAmount
            * breath;
        const pulse = 1 + gallery.breathScaleAmount * breath;
        plane.scale.set(
            baseScale * plane.userData.aspectRatio * pulse,
            baseScale * pulse,
            1,
        );
    });
}
