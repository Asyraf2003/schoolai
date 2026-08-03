import { DepthTrail } from './trail.js';
import { DepthTrailParticles } from './trail-particles.js';

const FULL_CIRCLE = Math.PI * 2;

export class DepthTrailController {
    constructor(THREE, gallery) {
        this.THREE = THREE;
        this.gallery = gallery;
        this.trail = new DepthTrail(THREE);
        this.particles = new DepthTrailParticles(THREE);
        this.head = new THREE.Vector3();
        this.timer = new THREE.Timer();
        this.previousProgress = null;
        this.previousDirection = 0;
        this.hasUserMoved = false;
        this.currentOpacity = 0.51;
        this.path = {
            startX: -0.96,
            startY: -1.05,
            width: 3,
            horizontalCycles: 1.85,
            verticalAmplitude: 0.78,
            verticalCycles: 2.1,
            distanceAhead: 1.65,
            baseDepthOffset: 4.78,
            depthSpan: 6.52,
            progressDepthOffset: -0.1,
        };
        this.points = {
            minimum: 14,
            maximum: 220,
            reverseScale: 0.55,
            seedCount: 10,
            seedStepZ: 0.12,
        };
        this.opacity = {
            base: 0.51,
            idle: 0.55,
            idleThreshold: 0.01,
            startBias: 0.1,
            edgeStart: 0.04,
            edgeEnd: 0.2,
            smoothing: 0.12,
        };
    }

    init(scene, camera) {
        scene.add(this.trail.object);
        scene.add(this.particles.group);
        this.seed(camera);
    }

    getProgress(camera, scroll) {
        const range = scroll.maxCameraZ - scroll.minCameraZ;
        if (range > 0) {
            return this.THREE.MathUtils.clamp(
                (scroll.maxCameraZ - camera.position.z) / range,
                0,
                1,
            );
        }
        return this.gallery.getDepthProgress(camera.position.z);
    }

    computeHead(cameraZ, progress) {
        const p = this.THREE.MathUtils.clamp(progress, 0, 1);
        const mobile = window.innerWidth <= 768;
        const startX = this.path.startX + (mobile ? 0.35 : 0);
        const width = this.path.width * (mobile ? 0.35 : 1);
        const x = startX
            + Math.sin(p * FULL_CIRCLE * this.path.horizontalCycles) * width;
        const y = this.path.startY
            + Math.sin(p * FULL_CIRCLE * this.path.verticalCycles)
                * this.path.verticalAmplitude;
        const depthProgress = this.path.progressDepthOffset
            + p * (1 - this.path.progressDepthOffset);
        const z = cameraZ + this.path.distanceAhead
            - (this.path.baseDepthOffset + depthProgress * this.path.depthSpan);
        return this.head.set(x, y, z);
    }

    seed(camera) {
        const start = this.computeHead(camera.position.z, 0).clone();
        for (let index = this.points.seedCount; index >= 0; index -= 1) {
            const point = start.clone();
            point.z -= index * this.points.seedStepZ;
            this.trail.addPoint(point);
        }
    }

    getDirection(progress) {
        if (this.previousProgress === null) return 0;
        const delta = progress - this.previousProgress;
        return Math.abs(delta) <= 0.0005 ? 0 : Math.sign(delta);
    }

    updateLength(progress, direction) {
        const scaled = direction < 0 ? progress * this.points.reverseScale : progress;
        this.trail.maxPoints = Math.round(this.THREE.MathUtils.lerp(
            this.points.minimum,
            this.points.maximum,
            this.THREE.MathUtils.clamp(scaled, 0, 1),
        ));
        this.trail.maxTrimPerFrame = direction < 0 ? 32 : 4;
    }

    updateOpacity(progress) {
        const THREE = this.THREE;
        const startDistance = THREE.MathUtils.clamp(
            progress + this.opacity.startBias,
            0,
            1,
        );
        const edge = Math.min(startDistance, 1 - progress);
        const edgeVisibility = THREE.MathUtils.smoothstep(
            edge,
            this.opacity.edgeStart,
            this.opacity.edgeEnd,
        );
        const startup = !this.hasUserMoved && progress <= this.opacity.idleThreshold
            ? this.opacity.idle
            : 0;
        const target = this.opacity.base * Math.max(edgeVisibility, startup);
        this.currentOpacity = THREE.MathUtils.lerp(
            this.currentOpacity,
            target,
            this.opacity.smoothing,
        );
        this.trail.material.opacity = this.currentOpacity;
    }

    update(camera, scroll, time) {
        this.timer.update(time);
        const progress = this.getProgress(camera, scroll);
        if (progress > this.opacity.idleThreshold) this.hasUserMoved = true;
        const direction = this.getDirection(progress);
        const reversed = direction !== 0
            && this.previousDirection !== 0
            && direction !== this.previousDirection;
        this.updateLength(progress, direction || this.previousDirection);
        const head = this.computeHead(camera.position.z, progress);
        this.updateOpacity(progress);
        if (reversed) {
            this.trail.reset();
            const lead = head.clone();
            lead.z += direction * this.points.seedStepZ;
            this.trail.addPoint(lead);
        }
        this.trail.addPoint(head);
        this.particles.update(
            this.timer.getDelta(),
            head,
            this.currentOpacity,
        );
        if (direction !== 0) this.previousDirection = direction;
        this.previousProgress = progress;
    }

    dispose(scene) {
        scene.remove(this.trail.object);
        scene.remove(this.particles.group);
        this.trail.dispose();
        this.particles.dispose();
    }
}
