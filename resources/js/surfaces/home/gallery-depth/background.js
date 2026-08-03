import {
    backgroundFragmentShader,
    backgroundVertexShader,
} from './shaders.js';

export class DepthBackground {
    constructor(THREE) {
        this.THREE = THREE;
        this.scene = new THREE.Scene();
        this.camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
        this.backgroundColor = new THREE.Color('#FBE8CD');
        this.blob1Color = new THREE.Color('#FFD56D');
        this.blob2Color = new THREE.Color('#5D816A');
        this.nextBackgroundColor = new THREE.Color();
        this.nextBlob1Color = new THREE.Color();
        this.nextBlob2Color = new THREE.Color();
        this.baseBlobRadius = 0.65;
        this.secondaryBlobRadiusRatio = 0.78;
        this.baseBlobStrength = 0.9;
        this.depthToRadiusAmount = 0.08;
        this.velocityToStrengthAmount = 0.1;
        this.motionSmoothing = 0.1;
        this.motionDepthProgress = 0;
        this.motionVelocityIntensity = 0;
        this.smoothedDepthProgress = 0;
        this.smoothedVelocityIntensity = 0;
        this.noiseStrength = 0.04;
        this.material = null;
        this.mesh = null;
    }

    init() {
        const THREE = this.THREE;
        this.material = new THREE.ShaderMaterial({
            vertexShader: backgroundVertexShader,
            fragmentShader: backgroundFragmentShader,
            depthWrite: false,
            depthTest: false,
            uniforms: {
                uBackgroundColor: { value: this.backgroundColor },
                uBlob1Color: { value: this.blob1Color },
                uBlob2Color: { value: this.blob2Color },
                uNoiseStrength: { value: this.noiseStrength },
                uBlobRadius: { value: this.baseBlobRadius },
                uBlobRadiusSecondary: {
                    value: this.baseBlobRadius * this.secondaryBlobRadiusRatio,
                },
                uBlobStrength: { value: this.baseBlobStrength },
                uTime: { value: 0 },
                uVelocityIntensity: { value: 0 },
            },
        });
        this.mesh = new THREE.Mesh(new THREE.PlaneGeometry(2, 2), this.material);
        this.scene.add(this.mesh);
    }

    setMoodBlend({ currentMood, nextMood, blend } = {}) {
        if (!currentMood || !this.material) return;
        const amount = this.THREE.MathUtils.clamp(blend || 0, 0, 1);
        const next = nextMood || currentMood;
        this.backgroundColor
            .set(currentMood.background)
            .lerp(this.nextBackgroundColor.set(next.background), amount);
        this.blob1Color
            .set(currentMood.blob1)
            .lerp(this.nextBlob1Color.set(next.blob1), amount);
        this.blob2Color
            .set(currentMood.blob2)
            .lerp(this.nextBlob2Color.set(next.blob2), amount);
        this.material.uniforms.uBackgroundColor.value.copy(this.backgroundColor);
        this.material.uniforms.uBlob1Color.value.copy(this.blob1Color);
        this.material.uniforms.uBlob2Color.value.copy(this.blob2Color);
    }

    setMotionResponse({ depthProgress, velocityIntensity } = {}) {
        const clamp = this.THREE.MathUtils.clamp;
        if (Number.isFinite(depthProgress)) {
            this.motionDepthProgress = clamp(depthProgress, 0, 1);
        }
        if (Number.isFinite(velocityIntensity)) {
            this.motionVelocityIntensity = clamp(velocityIntensity, 0, 1);
        }
    }

    update(time = 0) {
        if (!this.material) return;
        const math = this.THREE.MathUtils;
        this.smoothedDepthProgress = math.lerp(
            this.smoothedDepthProgress,
            this.motionDepthProgress,
            this.motionSmoothing,
        );
        this.smoothedVelocityIntensity = math.lerp(
            this.smoothedVelocityIntensity,
            this.motionVelocityIntensity,
            this.motionSmoothing,
        );
        const radius = math.clamp(
            this.baseBlobRadius + this.smoothedDepthProgress * this.depthToRadiusAmount,
            0.05,
            1,
        );
        const strength = math.clamp(
            this.baseBlobStrength
                + this.smoothedVelocityIntensity * this.velocityToStrengthAmount,
            0,
            1,
        );
        this.material.uniforms.uTime.value = time;
        this.material.uniforms.uVelocityIntensity.value = this.smoothedVelocityIntensity;
        this.material.uniforms.uBlobRadius.value = radius;
        this.material.uniforms.uBlobRadiusSecondary.value =
            radius * this.secondaryBlobRadiusRatio;
        this.material.uniforms.uBlobStrength.value = strength;
    }

    render(renderer) {
        renderer.render(this.scene, this.camera);
    }

    dispose() {
        this.mesh?.geometry.dispose();
        this.material?.dispose();
        this.scene.clear();
    }
}
