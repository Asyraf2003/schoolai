import {
    CatmullRomCurve3,
    PerspectiveCamera,
    REVISION,
    Scene,
    Vector2,
    Vector3,
    WebGLRenderer,
} from 'three';
import { Line2 } from 'three/addons/lines/Line2.js';
import { LineGeometry } from 'three/addons/lines/LineGeometry.js';
import { LineMaterial } from 'three/addons/lines/LineMaterial.js';

const PI = Math.PI;
const PATHS = [
    {
        points: [[-9, -4, .2], [-6, 4, .8], [-1, 6, .2], [6, 3, -.6],
            [7, -3, .4], [1, -6, .9], [-6, -5, .1]],
        width: .28, base: [-.8, .4, .8], travel: [3.8, 2.5],
        phase: .08, ratio: .82, rotation: .42, reveal: [.02, .48],
    },
    {
        points: [[-10, 3, -.3], [-5, -5, .5], [1, -6, 1], [8, -1, -.5],
            [5, 6, .4], [-2, 5, -.8], [-8, 7, .2]],
        width: .22, base: [1.1, -.6, -.5], travel: [4.6, 3.2],
        phase: .36, ratio: 1.18, rotation: -.58, reveal: [.2, .68],
    },
    {
        points: [[-9, -7, .5], [-7, 0, -.4], [-2, 7, .7], [4, 6, -.7],
            [9, -2, .4], [2, -7, .9], [-6, -4, -.3]],
        width: .18, base: [-1.4, .2, -1.4], travel: [5.2, 3.8],
        phase: .67, ratio: 1.46, rotation: .76, reveal: [.38, .9],
    },
];

const clamp = (value) => Math.min(1, Math.max(0, value));
const smooth = (value) => {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
};
const phase = (value, start, end) => smooth((value - start) / (end - start));

function createStroke(definition) {
    const points = definition.points.map((point) => new Vector3(...point));
    const curve = new CatmullRomCurve3(points, false, 'catmullrom', .38);
    const geometry = new LineGeometry();
    geometry.setFromPoints(curve.getPoints(112));
    const material = new LineMaterial({
        alphaToCoverage: true,
        color: 0xffffff,
        depthTest: true,
        depthWrite: false,
        linewidth: definition.width,
        opacity: 0,
        transparent: true,
        worldUnits: true,
    });
    const line = new Line2(geometry, material);
    line.frustumCulled = false;
    line.position.set(...definition.base);
    return { definition, geometry, line, material };
}

export function createValuesSpatialScene(host) {
    const renderer = new WebGLRenderer({
        alpha: true,
        antialias: true,
        powerPreference: 'high-performance',
        stencil: false,
    });
    const camera = new PerspectiveCamera(46, 1, .1, 50);
    const scene = new Scene();
    const strokes = PATHS.map(createStroke);
    const resolution = new Vector2();
    const canvas = renderer.domElement;
    let suspended = false;
    let failed = false;
    let width = 0;
    let height = 0;
    let pixelRatio = 0;
    let renderCount = 0;

    camera.position.z = 9;
    renderer.setClearColor(0x000000, 0);
    canvas.className = 'values-story__spatial-canvas';
    canvas.dataset.engine = `three.js r${REVISION}`;
    canvas.dataset.valuesSpatialCanvas = '';
    host.appendChild(canvas);
    strokes.forEach(({ line }) => scene.add(line));

    function setState(state) {
        host.dataset.valuesSpatialState = state;
    }

    function resize() {
        const nextWidth = Math.max(1, Math.round(host.clientWidth));
        const nextHeight = Math.max(1, Math.round(host.clientHeight));
        const cap = nextWidth < 768 ? 1.25 : 1.5;
        const nextRatio = Math.min(window.devicePixelRatio || 1, cap);
        if (nextWidth === width && nextHeight === height
            && nextRatio === pixelRatio) return;

        width = nextWidth;
        height = nextHeight;
        pixelRatio = nextRatio;
        renderer.setDrawingBufferSize(width, height, pixelRatio);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        resolution.set(width * pixelRatio, height * pixelRatio);
        strokes.forEach(({ material }) => material.resolution.copy(resolution));
    }

    function update({ handoffProgress, storyProgress, momentum }) {
        if (suspended || failed) return;
        resize();
        const travel = clamp(handoffProgress * .28 + storyProgress * .92);
        strokes.forEach(({ definition, line, material }, index) => {
            const cycle = (travel * definition.ratio + definition.phase) * PI;
            const drift = storyProgress - .5;
            line.position.x = definition.base[0]
                + Math.sin(cycle) * definition.travel[0] + drift * (index - 1) * 2.6;
            line.position.y = definition.base[1]
                + Math.cos(cycle * .84) * definition.travel[1] - drift * (2.8 + index);
            line.position.z = definition.base[2] + Math.sin(cycle * .63) * .7;
            line.rotation.z = definition.rotation * travel
                + Math.sin(cycle * .48) * .16 + momentum * .035;
            material.opacity = phase(handoffProgress, ...definition.reveal);
        });
        renderer.render(scene, camera);
        renderCount += 1;
        canvas.dataset.valuesRenderCount = String(renderCount);
        canvas.dataset.valuesSpatialProgress = storyProgress.toFixed(4);
        host.classList.add('is-spatial-ready');
        setState('ready');
    }

    function onContextLost(event) {
        event.preventDefault();
        failed = true;
        canvas.hidden = true;
        host.classList.add('is-spatial-failed');
        setState('failed');
    }

    function onContextRestored() {
        failed = false;
        width = 0;
        canvas.hidden = false;
        host.classList.remove('is-spatial-failed');
        setState('ready');
    }

    canvas.addEventListener('webglcontextlost', onContextLost);
    canvas.addEventListener('webglcontextrestored', onContextRestored);
    setState('ready');

    return {
        update,
        resume() { suspended = false; setState('ready'); },
        suspend() { suspended = true; setState('suspended'); },
        destroy() {
            suspended = true;
            canvas.removeEventListener('webglcontextlost', onContextLost);
            canvas.removeEventListener('webglcontextrestored', onContextRestored);
            strokes.forEach(({ geometry, material }) => {
                geometry.dispose();
                material.dispose();
            });
            renderer.dispose();
            renderer.forceContextLoss();
            canvas.remove();
            host.classList.remove('is-spatial-ready', 'is-spatial-failed');
            setState('static');
        },
    };
}
