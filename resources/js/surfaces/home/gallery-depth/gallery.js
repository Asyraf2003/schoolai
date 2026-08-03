/*
 * Adapted from Houmahani Kane / Codrops Atmospheric Depth Gallery.
 * MIT license notice: docs/third-party/codrops-depth-gallery-MIT.txt
 */

export class DepthGalleryPlanes {
    constructor(THREE, config) {
        this.THREE = THREE;
        this.config = config;
        this.planes = [];
        this.textures = new Map();
        this.geometry = null;
        this.planeGap = 5;
        this.desktopPlaneScale = 1;
        this.mobilePlaneScale = 0.65;
        this.mobileXSpreadFactor = 0.25;
        this.mobileBreakpoint = 768;
        this.moodSampleOffset = 1;
        this.planeFadeSampleOffset = 1;
        this.planeFadeSmoothing = 0.14;
        this.parallaxAmountX = 0.16;
        this.parallaxAmountY = 0.08;
        this.parallaxSmoothing = 0.08;
        this.pointerTarget = new THREE.Vector2();
        this.pointerCurrent = new THREE.Vector2();
        this.breathTiltAmount = 0.045;
        this.breathScaleAmount = 0.03;
        this.breathSmoothing = 0.14;
        this.breathGain = 1.1;
        this.breathIntensity = 0;
        this.targetBreathIntensity = 0;
        this.gestureParallaxAmountY = 0.05;
        this.gestureParallaxSmoothing = 0.05;
        this.driftCurrent = 0;
        this.driftTarget = 0;
        this.onPointerMove = this.handlePointerMove.bind(this);
        this.onPointerLeave = () => this.pointerTarget.set(0, 0);
    }

    init(scene, textures) {
        this.textures = textures;
        this.geometry = new this.THREE.PlaneGeometry(3, 3);
        this.config.forEach((item, index) => this.createPlane(scene, item, index));
        this.updatePlaneScale();
        this.layoutPlanes();
        window.addEventListener('pointermove', this.onPointerMove, { passive: true });
        window.addEventListener('pointerleave', this.onPointerLeave, { passive: true });
    }

    createPlane(scene, item, index) {
        const texture = this.textures.get(item.textureSrc) || null;
        const image = texture?.image;
        const aspectRatio = image?.width > 0 && image?.height > 0
            ? image.width / image.height
            : 1;
        const material = new this.THREE.MeshBasicMaterial({
            color: texture ? '#ffffff' : item.fallbackColor,
            map: texture,
            side: this.THREE.DoubleSide,
            transparent: true,
            depthWrite: false,
            opacity: index === 0 ? 1 : 0,
        });
        const plane = new this.THREE.Mesh(this.geometry, material);
        plane.userData = {
            item,
            aspectRatio,
            basePosition: item.position,
            backgroundColor: item.backgroundColor,
            blob1Color: item.blob1Color,
            blob2Color: item.blob2Color,
        };
        scene.add(plane);
        this.planes.push(plane);
    }

    handlePointerMove(event) {
        this.pointerTarget.set(
            (event.clientX / window.innerWidth) * 2 - 1,
            -((event.clientY / window.innerHeight) * 2 - 1),
        );
    }

    getBaseScale() {
        return window.innerWidth <= this.mobileBreakpoint
            ? this.mobilePlaneScale
            : this.desktopPlaneScale;
    }

    getXSpreadFactor() {
        return window.innerWidth <= this.mobileBreakpoint
            ? this.mobileXSpreadFactor
            : 1;
    }

    updatePlaneScale() {
        const scale = this.getBaseScale();
        this.planes.forEach((plane) => {
            plane.scale.set(scale * plane.userData.aspectRatio, scale, 1);
        });
    }

    layoutPlanes() {
        const spread = this.getXSpreadFactor();
        this.planes.forEach((plane, index) => {
            const position = plane.userData.basePosition;
            plane.position.set(position.x * spread, position.y, -index * this.planeGap);
        });
    }

    getDepthRange() {
        const positions = this.planes.map((plane) => plane.position.z);
        return {
            nearestZ: positions.length ? Math.max(...positions) : 0,
            deepestZ: positions.length ? Math.min(...positions) : 0,
        };
    }

    getDepthProgress(cameraZ) {
        const { nearestZ, deepestZ } = this.getDepthRange();
        const span = nearestZ - deepestZ;
        return span > 0
            ? this.THREE.MathUtils.clamp((nearestZ - cameraZ) / span, 0, 1)
            : 0;
    }

    getPlaneBlendData(cameraZ) {
        if (!this.planes.length) return null;
        const lastIndex = this.planes.length - 1;
        const sampledZ = cameraZ - this.planeGap * this.planeFadeSampleOffset;
        const normalized = this.THREE.MathUtils.clamp(
            (this.planes[0].position.z - sampledZ) / this.planeGap,
            0,
            lastIndex,
        );
        const currentPlaneIndex = Math.floor(normalized);
        return {
            currentPlaneIndex,
            nextPlaneIndex: Math.min(currentPlaneIndex + 1, lastIndex),
            blend: normalized - currentPlaneIndex,
        };
    }

    getMoodBlendData(cameraZ) {
        if (!this.planes.length) return null;
        const sampledZ = cameraZ - this.planeGap * this.moodSampleOffset;
        const lastIndex = this.planes.length - 1;
        const normalized = this.THREE.MathUtils.clamp(
            (this.planes[0].position.z - sampledZ) / this.planeGap,
            0,
            lastIndex,
        );
        const currentIndex = Math.floor(normalized);
        const nextIndex = Math.min(currentIndex + 1, lastIndex);
        return {
            currentMood: this.readMood(currentIndex),
            nextMood: this.readMood(nextIndex),
            blend: normalized - currentIndex,
        };
    }

    readMood(index) {
        const data = this.planes[index]?.userData;
        return data ? {
            background: data.backgroundColor,
            blob1: data.blob1Color,
            blob2: data.blob2Color,
        } : null;
    }

    update(camera, scroll) {
        const blend = this.getPlaneBlendData(camera.position.z);
        if (!blend) return;
        this.pointerCurrent.lerp(this.pointerTarget, this.parallaxSmoothing);
        const velocityMax = Math.max(scroll.velocityMax, 0.0001);
        const velocity = this.THREE.MathUtils.clamp(
            Math.abs(scroll.velocity) / velocityMax,
            0,
            1,
        );
        this.targetBreathIntensity = Math.min(1, velocity * this.breathGain);
        this.breathIntensity = this.THREE.MathUtils.lerp(
            this.breathIntensity,
            this.targetBreathIntensity,
            this.breathSmoothing,
        );
        this.driftTarget = this.THREE.MathUtils.clamp(scroll.velocity / velocityMax, -1, 1);
        this.driftCurrent = this.THREE.MathUtils.lerp(
            this.driftCurrent,
            this.driftTarget,
            this.gestureParallaxSmoothing,
        );
        this.updatePlanes(blend);
    }

    updatePlanes(blend) {
        const spread = this.getXSpreadFactor();
        const baseScale = this.getBaseScale();
        this.planes.forEach((plane, index) => {
            let targetOpacity = index === blend.currentPlaneIndex ? 1 - blend.blend : 0;
            if (index === blend.nextPlaneIndex) targetOpacity = Math.max(targetOpacity, blend.blend);
            plane.material.opacity = this.THREE.MathUtils.lerp(
                plane.material.opacity,
                targetOpacity,
                this.planeFadeSmoothing,
            );
            const opacity = plane.material.opacity;
            const base = plane.userData.basePosition;
            plane.position.x = base.x * spread + this.pointerCurrent.x * this.parallaxAmountX * opacity;
            plane.position.y = base.y + this.pointerCurrent.y * this.parallaxAmountY * opacity
                + this.driftCurrent * this.gestureParallaxAmountY;
            const breath = this.breathIntensity * opacity;
            plane.rotation.x = -this.pointerCurrent.y * this.breathTiltAmount * breath;
            plane.rotation.y = this.pointerCurrent.x * this.breathTiltAmount * breath;
            const pulse = 1 + this.breathScaleAmount * breath;
            plane.scale.set(baseScale * plane.userData.aspectRatio * pulse, baseScale * pulse, 1);
        });
    }

    dispose(scene) {
        window.removeEventListener('pointermove', this.onPointerMove);
        window.removeEventListener('pointerleave', this.onPointerLeave);
        this.planes.forEach((plane) => {
            scene.remove(plane);
            plane.material.dispose();
        });
        this.geometry?.dispose();
        this.planes = [];
    }
}
