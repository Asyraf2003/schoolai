import { WebGLRenderer, Scene, PerspectiveCamera, Mesh, MeshBasicMaterial, Texture, SRGBColorSpace, DoubleSide } from 'three';
import { bentPlane, projectPanel } from './gallery-spatial-geometry.js';

export function createSpatialRenderer(root, state, wake) {
    const viewport = root.querySelector('[data-spatial-viewport]');
    const renderer = new WebGLRenderer({ alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(devicePixelRatio, 1.5));
    renderer.domElement.className = 'gallery-spatial__canvas';
    renderer.domElement.setAttribute('aria-hidden', 'true');
    viewport.prepend(renderer.domElement);
    const scene = new Scene(), camera = new PerspectiveCamera(42, 1, .1, 100);
    camera.position.z = 8; camera.updateMatrixWorld();
    const panels = state.links.map(link => ({ link, ratio: link.querySelector('img').naturalWidth / link.querySelector('img').naturalHeight || 1.5, mesh: null, texture: null, loading: false, failed: false, ticket: 0 }));
    let width, height, disposed = false;
    const release = panel => {
        panel.ticket++; panel.loading = false; panel.request?.abort();
        if (panel.mesh) { scene.remove(panel.mesh); panel.mesh.geometry.dispose(); panel.mesh.material.dispose(); panel.mesh = null; }
        panel.texture?.dispose(); panel.texture = null;
    };
    const size = () => {
        width = root.clientWidth; height = root.clientHeight;
        renderer.setSize(width, height, false); camera.aspect = width / height; camera.updateProjectionMatrix();
        const worldHeight = 16 * Math.tan(42 * Math.PI / 360);
        const worldWidth = worldHeight * width / height;
        state.scale = worldWidth / width;
        const panelHeight = 3.05;
        let center = 0;
        panels.forEach((p, i) => {
            const previousWidth = p.w;
            p.h = Math.min(panelHeight, worldWidth * .84 / p.ratio); p.w = p.h * p.ratio;
            if (i) center += (panels[i - 1].w + p.w) / 2 + Math.min(.42, worldWidth * .025);
            state.centers[i] = center;
            if (p.mesh && p.w !== previousWidth) { p.mesh.geometry.dispose(); p.mesh.geometry = bentPlane(p.w, p.h); }
        });
        state.limit = center;
    };
    const load = panel => {
        if (panel.texture || panel.loading || panel.failed) return;
        panel.loading = true;
        const ticket = ++panel.ticket;
        panel.request = new AbortController();
        // Revalidate the CORS variant: the same URL may already be cached by a
        // non-CORS archive <img>, which is not a valid WebGL texture response.
        fetch(panel.link.dataset.poster, { mode: 'cors', cache: 'reload', signal: panel.request.signal })
            .then(response => { if (!response.ok) throw new Error('Texture unavailable'); return response.blob(); })
            .then(async blob => {
                const url = URL.createObjectURL(blob), image = new Image();
                image.src = url;
                try { await image.decode(); } finally { URL.revokeObjectURL(url); }
                return new Texture(image);
            }).then(texture => {
            if (disposed || ticket !== panel.ticket) { texture.dispose(); return; }
            panel.loading = false; panel.texture = texture;
            texture.colorSpace = SRGBColorSpace; texture.needsUpdate = true;
            panel.ratio = texture.image.width / texture.image.height;
            const currentIndex = state.centers.reduce((a, c, i) => Math.abs(c - state.target) < Math.abs(state.centers[a] - state.target) ? i : a, 0);
            const previous = state.centers[currentIndex];
            size();
            const difference = state.centers[currentIndex] - previous;
            state.target += difference; state.position += difference;
            panel.mesh = new Mesh(bentPlane(panel.w, panel.h), new MeshBasicMaterial({ map: texture, side: DoubleSide }));
            scene.add(panel.mesh); wake();
        }).catch(error => { if (disposed || ticket !== panel.ticket || error.name === 'AbortError') return; panel.loading = false; panel.failed = true; wake(); });
    };
    size();
    return {
        canvas: renderer.domElement,
        resize() { const index = state.centers.findIndex((c, i) => c >= state.position || i === state.centers.length - 1); const offset = state.position - state.centers[index]; size(); state.target = state.position = Math.max(0, Math.min(state.limit, state.centers[index] + offset)); },
        draw() {
            const nearest = panels.map((p, i) => ({ p, i, distance: Math.abs(state.centers[i] - state.position) }))
                .sort((a, b) => a.distance - b.distance).slice(0, 5);
            const active = new Set(nearest.map(({ i }) => i));
            const direction = document.documentElement.dir === 'rtl' ? -1 : 1;
            panels.forEach((p, i) => {
                p.link.style.clipPath = 'polygon(0 0,0 0,0 0)';
                if (!active.has(i)) { if (p.texture || p.loading) release(p); return; }
                load(p);
                const x = (state.centers[i] - state.position) * direction;
                if (!p.mesh) return;
                p.mesh.position.set(x, .05, -Math.min(1.3, Math.abs(x) * .1));
                p.mesh.rotation.y = -Math.max(-.35, Math.min(.35, x * .055));
                p.mesh.updateMatrixWorld();
                projectPanel(p, camera, width, height);
                
            });
            renderer.render(scene, camera);
            root.dataset.spatialTextures = String(renderer.info.memory.textures);
            root.dataset.spatialPosition = state.position.toFixed(4);
            // An unavailable visible texture must never strand the semantic action.
            return !nearest.slice(0, 3).some(({ p }) => p.failed);
        },
        dispose() { disposed = true; panels.forEach(release); renderer.dispose(); renderer.domElement.remove(); },
    };
}
