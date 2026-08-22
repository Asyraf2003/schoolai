const TRANSITION_QUERY = '(min-width: 1280px) and (prefers-reduced-motion: no-preference)';

export class DepthScroll {
    constructor(THREE, camera, gallery, journey, viewport, endSteps = 0) {
        this.THREE = THREE;
        this.camera = camera;
        this.gallery = gallery;
        this.journey = journey;
        this.viewport = viewport;
        this.endSteps = Math.max(0, endSteps);
        this.transitionMedia = window.matchMedia(TRANSITION_QUERY);
        this.scrollTarget = 0;
        this.scrollCurrent = 0;
        this.previousScrollCurrent = 0;
        this.progressCurrent = 0;
        this.endProgress = 0;
        this.scrollSmoothing = 0.08;
        this.velocity = 0;
        this.velocityDamping = 0.12;
        this.velocityMax = 1.5;
        this.minCameraZ = 0;
        this.maxCameraZ = 0;
    }

    init() {
        this.updateCameraBounds();
        this.camera.position.z = this.maxCameraZ;
    }

    updateCameraBounds() {
        const range = this.gallery.getDepthRange();
        const endDepth = this.endSteps * this.gallery.planeGap;
        this.maxCameraZ = range.nearestZ + 5;
        this.minCameraZ = Math.min(
            this.maxCameraZ,
            range.deepestZ + 5 - endDepth,
        );
    }

    getTransitionDistance() {
        if (
            this.journey.dataset.depthGalleryTransition !== 'sticky-scale'
            || !this.transitionMedia.matches
        ) return 0;

        const enabled = getComputedStyle(this.journey)
            .getPropertyValue('--depth-sticky-transition-enabled')
            .trim();

        return enabled === '1' ? this.viewport.clientHeight : 0;
    }

    readScrollTarget(travel) {
        const rect = this.journey.getBoundingClientRect();
        return this.THREE.MathUtils.clamp(-rect.top, 0, travel);
    }

    updateEndProgress() {
        const planeSteps = Math.max(0, this.gallery.planes.length - 1);
        const totalSteps = Math.max(1, planeSteps + this.endSteps);
        const endStart = planeSteps / totalSteps;

        this.endProgress = this.endSteps > 0 && endStart < 1
            ? this.THREE.MathUtils.clamp(
                (this.progressCurrent - endStart) / (1 - endStart),
                0,
                1,
            )
            : 0;
    }

    update() {
        this.updateCameraBounds();
        const fullTravel = Math.max(
            1,
            this.journey.offsetHeight - this.viewport.clientHeight,
        );
        const transitionDistance = Math.min(
            this.getTransitionDistance(),
            Math.max(0, fullTravel - 1),
        );
        const travel = Math.max(1, fullTravel - transitionDistance);

        this.scrollTarget = this.readScrollTarget(travel);
        this.scrollCurrent = this.THREE.MathUtils.lerp(
            this.scrollCurrent,
            this.scrollTarget,
            this.scrollSmoothing,
        );
        const rawVelocity = this.scrollCurrent - this.previousScrollCurrent;
        this.velocity = this.THREE.MathUtils.lerp(
            this.velocity,
            rawVelocity,
            this.velocityDamping,
        );
        this.velocity = this.THREE.MathUtils.clamp(
            this.velocity,
            -this.velocityMax,
            this.velocityMax,
        );
        if (Math.abs(this.velocity) < 0.0001) this.velocity = 0;
        this.previousScrollCurrent = this.scrollCurrent;
        this.progressCurrent = this.THREE.MathUtils.clamp(
            this.scrollCurrent / travel,
            0,
            1,
        );
        this.updateEndProgress();
        this.camera.position.z = this.THREE.MathUtils.lerp(
            this.maxCameraZ,
            this.minCameraZ,
            this.progressCurrent,
        );
    }
}
