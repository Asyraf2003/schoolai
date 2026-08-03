export class DepthScroll {
    constructor(THREE, camera, gallery, journey, viewport) {
        this.THREE = THREE;
        this.camera = camera;
        this.gallery = gallery;
        this.journey = journey;
        this.viewport = viewport;
        this.scrollTarget = 0;
        this.scrollCurrent = 0;
        this.previousScrollCurrent = 0;
        this.progressCurrent = 0;
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
        this.maxCameraZ = range.nearestZ + 5;
        this.minCameraZ = Math.min(this.maxCameraZ, range.deepestZ + 5);
    }

    readScrollTarget(travel) {
        const rect = this.journey.getBoundingClientRect();
        return this.THREE.MathUtils.clamp(-rect.top, 0, travel);
    }

    update() {
        this.updateCameraBounds();
        const travel = Math.max(
            1,
            this.journey.offsetHeight - this.viewport.clientHeight,
        );
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
        this.camera.position.z = this.THREE.MathUtils.lerp(
            this.maxCameraZ,
            this.minCameraZ,
            this.progressCurrent,
        );
    }
}
