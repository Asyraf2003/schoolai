export class DepthScroll {
    constructor(THREE, camera, gallery, journey, viewport) {
        this.THREE = THREE;
        this.camera = camera;
        this.gallery = gallery;
        this.journey = journey;
        this.viewport = viewport;
        this.progressTarget = 0;
        this.progressCurrent = 0;
        this.previousProgress = 0;
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

    readProgress() {
        const rect = this.journey.getBoundingClientRect();
        const travel = Math.max(1, this.journey.offsetHeight - this.viewport.clientHeight);
        return this.THREE.MathUtils.clamp(-rect.top / travel, 0, 1);
    }

    update() {
        this.updateCameraBounds();
        this.progressTarget = this.readProgress();
        this.progressCurrent = this.THREE.MathUtils.lerp(
            this.progressCurrent,
            this.progressTarget,
            this.scrollSmoothing,
        );
        const rawVelocity = this.progressCurrent - this.previousProgress;
        this.velocity = this.THREE.MathUtils.lerp(
            this.velocity,
            rawVelocity * 100,
            this.velocityDamping,
        );
        this.velocity = this.THREE.MathUtils.clamp(
            this.velocity,
            -this.velocityMax,
            this.velocityMax,
        );
        if (Math.abs(this.velocity) < 0.0001) this.velocity = 0;
        this.previousProgress = this.progressCurrent;
        this.camera.position.z = this.THREE.MathUtils.lerp(
            this.maxCameraZ,
            this.minCameraZ,
            this.progressCurrent,
        );
    }
}
