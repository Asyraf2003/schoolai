import { WebGLRenderer, Scene, PerspectiveCamera, Mesh } from 'three';
import { trackPlane, deformTrack, projectPanel, projectTrack, mediaIndex } from './gallery-spatial-geometry.js';
import { spatialTextures } from './gallery-spatial-textures.js';

export function createSpatialRenderer(root, state, wake) {
    const viewport = root.querySelector('[data-spatial-viewport]');
    const track = root.querySelector('[data-spatial-track]');
    const renderer = new WebGLRenderer({ alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(devicePixelRatio, 1.5));
    renderer.domElement.className = 'gallery-spatial__canvas';
    renderer.domElement.setAttribute('aria-hidden', 'true');
    viewport.prepend(renderer.domElement);
    const scene = new Scene(), camera = new PerspectiveCamera(42, 1, .1, 100);
    camera.position.z = 8; camera.updateMatrixWorld();
    const textures = spatialTextures(state.links, wake), instances = new Map();
    const direction = document.documentElement.dir === 'rtl' ? -1 : 1;
    let width, height, panelWidth, pitch, extent;
    const curve = { radius: 1, depth: 2.4 };
    state.centers = state.links.map((_, i) => i);
    const remove = slot => {
        const instance = instances.get(slot);
        scene.remove(instance.mesh); instance.mesh.geometry.dispose(); instance.link.remove(); instances.delete(slot);
    };
    const size = () => {
        width = root.clientWidth; height = root.clientHeight;
        renderer.setSize(width, height, false); camera.aspect = width / height; camera.updateProjectionMatrix();
        const worldWidth = 16 * Math.tan(42 * Math.PI / 360) * width / height;
        panelWidth = Math.min(5.1, worldWidth * .84);
        pitch = panelWidth + Math.min(.18, worldWidth * .025);
        curve.radius = worldWidth * .45;
        state.scale = worldWidth / width / pitch;
        // Find the global coordinate just beyond the viewport, using exactly
        // the camera/curve used by the mesh. Recycle only beyond that boundary.
        let low = 0, high = worldWidth * 4;
        for (let i = 0; i < 30; i++) {
            const mid = (low + high) / 2;
            if (projectTrack(mid, 0, curve, camera, width, height)[0] < width + 40) low = mid;
            else high = mid;
        }
        extent = high;
    };
    const create = (slot, index, asset) => {
        const source = state.links[index];
        const link = source.cloneNode(false);
        link.removeAttribute('data-spatial-panel'); link.dataset.spatialInstance = String(slot);
        link.dataset.spatialMedia = String(index); link.tabIndex = -1; link.setAttribute('aria-hidden', 'true');
        link.append(source.querySelector('span').cloneNode(true)); track.append(link);
        const mesh = new Mesh(trackPlane(), asset.material);
        mesh.frustumCulled = false; scene.add(mesh);
        const instance = { mesh, link, index, center: 0, w: panelWidth, h: 3.05 };
        instances.set(slot, instance);
        return instance;
    };
    state.links.forEach(link => { link.style.clipPath = 'polygon(0 0,0 0,0 0)'; });
    size();
    return {
        canvas: renderer.domElement,
        resize: size,
        draw() {
            const start = Math.ceil(state.position - (extent + panelWidth / 2) / pitch);
            const end = Math.floor(state.position + (extent + panelWidth / 2) / pitch);
            const active = new Set(), identities = new Set();
            let failed = false;
            for (let slot = start; slot <= end; slot++) {
                const index = mediaIndex(slot, state.links.length), asset = textures.get(index);
                active.add(slot); identities.add(index);
                const instance = instances.get(slot) ?? create(slot, index, asset);
                instance.center = (slot - state.position) * pitch * direction;
                const image = asset.texture?.image ?? state.links[index].querySelector('img');
                const ratio = (image.naturalWidth || image.width) / (image.naturalHeight || image.height) || 1.5;
                instance.h = Math.min(3.05, panelWidth / ratio); instance.w = instance.h * ratio;
                instance.mesh.visible = !!asset.texture;
                instance.link.style.visibility = asset.texture ? '' : 'hidden';
                if (asset.failed) failed = true;
                if (!asset.texture) continue;
                deformTrack(instance.mesh.geometry, instance.center, instance.w, instance.h, curve);
                projectPanel(instance, curve, camera, width, height);
                instance.link.toggleAttribute('data-spatial-focused', state.links[index] === document.activeElement);
            }
            // Decode one upcoming source on either side while it is still
            // wholly offscreen; upload only when a visible mesh uses it.
            for (const slot of [start - 1, end + 1]) {
                const index = mediaIndex(slot, state.links.length);
                identities.add(index); textures.get(index);
            }
            for (const slot of instances.keys()) if (!active.has(slot)) remove(slot);
            textures.retain(identities);
            renderer.render(scene, camera);
            root.dataset.spatialTextures = String(renderer.info.memory.textures);
            root.dataset.spatialPosition = state.position.toFixed(6);
            root.dataset.spatialCount = String(state.links.length);
            root.dataset.spatialPitch = pitch.toFixed(6);
            root.dataset.spatialRadius = curve.radius.toFixed(6);
            return !failed;
        },
        dispose() { for (const slot of instances.keys()) remove(slot); textures.dispose(); renderer.dispose(); renderer.domElement.remove(); },
    };
}
