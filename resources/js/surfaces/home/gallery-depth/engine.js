import { DepthBackground } from './background.js';
import { DepthGalleryPlanes } from './gallery.js';
import { DepthLabel } from './label.js';
import { DepthScroll } from './scroll.js';
import { DepthTrailController } from './trail-controller.js';

export class DepthGalleryEngine {
    constructor(THREE, root, config, onFailure) {
        this.THREE = THREE;
        this.root = root;
        this.config = config;
        this.canvas = root.querySelector('[data-depth-gallery-canvas]');
        this.journey = root.querySelector('[data-depth-gallery-journey]');
        this.viewport = root.querySelector('[data-depth-gallery-viewport]');
        this.onFailure = onFailure;
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(45, 1, 0.1, 100);
        this.camera.position.set(0, 0, 6);
        this.renderer = null;
        this.background = new DepthBackground(THREE);
        this.gallery = new DepthGalleryPlanes(THREE, config);
        this.label = new DepthLabel(root, this.gallery, config);
        this.scroll = new DepthScroll(
            THREE,
            this.camera,
            this.gallery,
            this.journey,
            this.viewport,
        );
        this.trail = new DepthTrailController(THREE, this.gallery);
        this.textures = new Map();
        this.frame = 0;
        this.running = false;
        this.initialized = false;
        this.disposed = false;
        this.animate = this.update.bind(this);
        this.onResize = this.resize.bind(this);
        this.onContextLost = this.handleContextLost.bind(this);
    }

    async init() {
        if (!this.canvas || !this.journey || !this.viewport) return false;
        try {
            this.renderer = new this.THREE.WebGLRenderer({
                canvas: this.canvas,
                antialias: true,
            });
            this.renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));
            this.renderer.outputColorSpace = this.THREE.SRGBColorSpace;
            this.renderer.autoClear = false;
            this.textures = await this.preloadTextures();
            if (this.disposed) return false;
            this.gallery.init(this.scene, this.textures);
            this.background.init();
            this.trail.init(this.scene, this.camera);
            this.scroll.init();
            this.resize();
            window.addEventListener('resize', this.onResize, { passive: true });
            this.canvas.addEventListener('webglcontextlost', this.onContextLost);
            this.initialized = true;
            return true;
        } catch (error) {
            this.onFailure(error);
            return false;
        }
    }

    async preloadTextures() {
        const sources = [...new Set(
            this.config.map((item) => item.textureSrc).filter(Boolean),
        )];
        const loader = new this.THREE.TextureLoader();
        const textures = new Map();
        await Promise.all(sources.map(async (source) => {
            try {
                const texture = await loader.loadAsync(source);
                texture.colorSpace = this.THREE.SRGBColorSpace;
                textures.set(source, texture);
            } catch (error) {
                console.warn('Gallery texture failed to load', source, error);
            }
        }));
        return textures;
    }

    resize() {
        if (!this.renderer || !this.canvas) return;
        const width = this.canvas.clientWidth || 1;
        const height = this.canvas.clientHeight || 1;
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height, false);
        this.gallery.updatePlaneScale();
        this.gallery.layoutPlanes();
    }

    update(time = performance.now()) {
        if (!this.running || this.disposed) return;
        this.frame = requestAnimationFrame(this.animate);
        this.scroll.update();
        this.trail.update(this.camera, this.scroll, time);
        this.gallery.update(this.camera, this.scroll);
        this.label.update(this.camera);
        const planeBlend = this.gallery.getPlaneBlendData(this.camera.position.z);
        const moodBlend = this.gallery.getMoodBlendData(this.camera.position.z);
        if (moodBlend) this.background.setMoodBlend(moodBlend);
        const velocity = this.THREE.MathUtils.clamp(
            Math.abs(this.scroll.velocity) / Math.max(this.scroll.velocityMax, 0.0001),
            0,
            1,
        );
        const blend = planeBlend?.blend || 0;
        const stability = this.THREE.MathUtils.smoothstep(
            Math.abs(blend - 0.5) * 2,
            0.35,
            1,
        );
        this.background.setMotionResponse({
            depthProgress: this.gallery.getDepthProgress(this.camera.position.z),
            velocityIntensity: velocity * stability,
        });
        this.background.update(time);
        this.renderer.clear(true, true, true);
        this.background.render(this.renderer);
        this.renderer.clearDepth();
        this.renderer.render(this.scene, this.camera);
    }

    start() {
        if (!this.initialized || this.running || this.disposed) return;
        this.running = true;
        this.update();
    }

    stop() {
        this.running = false;
        if (this.frame) cancelAnimationFrame(this.frame);
        this.frame = 0;
    }

    handleContextLost(event) {
        event.preventDefault();
        this.stop();
        this.onFailure(new Error('WebGL context lost'));
    }

    dispose() {
        if (this.disposed) return;
        this.disposed = true;
        this.stop();
        window.removeEventListener('resize', this.onResize);
        this.canvas?.removeEventListener('webglcontextlost', this.onContextLost);
        this.label.clear();
        this.trail.dispose(this.scene);
        this.gallery.dispose(this.scene);
        this.background.dispose();
        this.textures.forEach((texture) => texture.dispose());
        this.textures.clear();
        this.renderer?.dispose();
        this.renderer = null;
        this.scene.clear();
    }
}
