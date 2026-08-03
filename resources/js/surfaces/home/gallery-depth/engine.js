import { DepthBackground } from './background.js';
import { DepthGalleryEndCta } from './end-cta.js';
import { renderDepthFrame } from './engine-frame.js';
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
        const endSteps = Number.parseInt(
            root.getAttribute('data-depth-gallery-end-steps') || '0',
            10,
        );
        this.scroll = new DepthScroll(
            THREE,
            this.camera,
            this.gallery,
            this.journey,
            this.viewport,
            endSteps,
        );
        this.endCta = new DepthGalleryEndCta(root, this.scroll);
        this.trail = new DepthTrailController(THREE, this.gallery);
        this.textures = new Map();
        this.resizeObserver = null;
        this.frame = 0;
        this.running = false;
        this.initialized = false;
        this.disposed = false;
        this.animate = this.update.bind(this);
        this.onResize = this.handleResize.bind(this);
        this.onContextLost = this.handleContextLost.bind(this);
    }

    async init() {
        if (!this.canvas || !this.journey || !this.viewport) return false;
        try {
            this.renderer = new this.THREE.WebGLRenderer({
                canvas: this.canvas,
                antialias: true,
            });
            this.renderer.setPixelRatio(
                Math.min(window.devicePixelRatio || 1, 1.5),
            );
            this.renderer.outputColorSpace = this.THREE.SRGBColorSpace;
            this.renderer.autoClear = false;
            this.textures = await this.preloadTextures();
            if (this.disposed || !this.hasPrimaryTexture()) return false;

            this.gallery.init(this.scene, this.textures);
            this.background.init();
            this.trail.init(this.scene, this.camera);
            this.scroll.init();
            this.endCta.init();
            if (!this.resize()) return false;

            window.addEventListener('resize', this.onResize, { passive: true });
            this.canvas.addEventListener(
                'webglcontextlost',
                this.onContextLost,
            );
            if ('ResizeObserver' in window) {
                this.resizeObserver = new ResizeObserver(this.onResize);
                this.resizeObserver.observe(this.viewport);
            }

            this.initialized = true;
            return this.renderOnce(performance.now());
        } catch (error) {
            console.warn('Depth gallery engine failed to initialize', error);
            return false;
        }
    }

    hasPrimaryTexture() {
        const primarySource = this.config[0]?.textureSrc;
        return !primarySource || this.textures.has(primarySource);
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
        if (!this.renderer || !this.viewport) return false;
        const rect = this.viewport.getBoundingClientRect();
        const width = Math.round(rect.width);
        const readyHeight = this.root.classList.contains('is-depth-ready')
            ? rect.height
            : Math.max(window.innerHeight || 0, 560);
        const height = Math.round(readyHeight);
        if (width < 2 || height < 2) return false;

        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height, false);
        this.gallery.updatePlaneScale();
        this.gallery.layoutPlanes();
        return true;
    }

    handleResize() {
        if (this.disposed || !this.resize()) return;
        if (this.initialized) this.renderOnce(performance.now());
    }

    renderOnce(time = performance.now()) {
        return renderDepthFrame(this, time);
    }

    activate() {
        return this.resize() && this.renderOnce(performance.now());
    }

    update(time = performance.now()) {
        if (!this.running || this.disposed) return;
        if (!this.renderOnce(time)) {
            this.stop();
            this.onFailure(
                new Error('Depth gallery produced an invalid frame'),
            );
            return;
        }
        this.frame = requestAnimationFrame(this.animate);
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
        this.resizeObserver?.disconnect();
        this.resizeObserver = null;
        this.canvas?.removeEventListener(
            'webglcontextlost',
            this.onContextLost,
        );
        this.endCta.dispose();
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
