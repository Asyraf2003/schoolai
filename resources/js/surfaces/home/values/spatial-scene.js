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

const SAMPLE_COUNT = 220;
const HANDOFF_ENTRY_WEIGHT = .24;
const STORY_JOURNEY_WEIGHT = .88;

const PATHS = [
    {
        // C: masuk dari kanan atas, menyapu ke kiri, turun, lalu keluar kanan bawah.
        points: [
            [10.8, 7.8, .08],
            [8.0, 5.5, .08],
            [3.8, 4.35, .08],
            [-1.8, 4.05, .08],
            [-5.3, 2.15, .08],
            [-5.9, -.75, .08],
            [-4.6, -3.1, .08],
            [-1.4, -4.25, .08],
            [3.4, -4.3, .08],
            [7.8, -5.45, .08],
            [11.2, -7.8, .08],
        ],
        width: .20,
        reveal: [0, .68],
    },
    {
        // S: masuk dari kiri atas sebelum C selesai dan berakhir ke kanan bawah.
        points: [
            [-10.8, 7.8, .20],
            [-7.6, 5.55, .20],
            [-3.1, 4.2, .20],
            [2.6, 3.8, .20],
            [5.55, 2.05, .20],
            [4.45, .15, .20],
            [1.45, -.65, .20],
            [-2.65, -1.0, .20],
            [-5.25, -2.55, .20],
            [-3.45, -4.15, .20],
            [.35, -4.75, .20],
            [5.6, -5.35, .20],
            [10.8, -7.8, .20],
        ],
        width: .16,
        reveal: [.18, .82],
    },
    {
        // U/O: datang dari kanan, menimpa jalur awal C, membuat U lalu loop,
        // kemudian turun dan keluar ke kiri bawah.
        points: [
            [10.8, 7.8, .72],
            [8.0, 5.5, .72],
            [3.8, 4.35, .72],
            [5.25, 2.0, .72],
            [5.25, -1.65, .72],
            [3.65, -3.7, .72],
            [.45, -4.35, .72],
            [-3.05, -3.65, .72],
            [-4.7, -1.3, .72],
            [-4.0, 1.45, .72],
            [-1.55, 2.65, .72],
            [1.35, 2.2, .72],
            [2.9, .55, .72],
            [2.25, -1.25, .72],
            [.2, -2.05, .72],
            [-2.0, -1.4, .72],
            [-3.15, .15, .72],
            [-3.7, -2.9, .72],
            [-5.6, -5.45, .72],
            [-9.4, -7.8, .72],
        ],
        width: .18,
        reveal: [.50, 1],
    },
];

const clamp = (value) => Math.min(1, Math.max(0, value));
const rangeProgress = (value, start, end) => (
    clamp((value - start) / Math.max(.0001, end - start))
);

function writePoint(buffer, offset, point) {
    buffer[offset] = point.x;
    buffer[offset + 1] = point.y;
    buffer[offset + 2] = point.z;
}

function writeHead(buffer, offset, from, to, amount) {
    buffer[offset] = from.x + (to.x - from.x) * amount;
    buffer[offset + 1] = from.y + (to.y - from.y) * amount;
    buffer[offset + 2] = from.z + (to.z - from.z) * amount;
}

function createStroke(definition) {
    const controls = definition.points.map((point) => new Vector3(...point));
    const curve = new CatmullRomCurve3(controls, false, 'centripetal');
    const master = curve.getPoints(SAMPLE_COUNT);
    const buffer = new Float32Array((master.length + 1) * 3);
    const geometry = new LineGeometry();
    const first = master[0];
    geometry.setPositions([
        first.x, first.y, first.z,
        first.x, first.y, first.z,
    ]);
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

    return { buffer, definition, geometry, line, master, material };
}

function revealStroke(stroke, progress) {
    const { buffer, geometry, master, material } = stroke;
    const scaled = clamp(progress) * (master.length - 1);
    const fullIndex = Math.floor(scaled);
    const fraction = scaled - fullIndex;
    let pointCount = 0;

    for (let index = 0; index <= fullIndex; index += 1) {
        writePoint(buffer, pointCount * 3, master[index]);
        pointCount += 1;
    }

    if (fullIndex < master.length - 1) {
        writeHead(
            buffer, pointCount * 3, master[fullIndex],
            master[fullIndex + 1], fraction,
        );
        pointCount += 1;
    }

    if (pointCount === 1) {
        writePoint(buffer, 3, master[0]);
        pointCount = 2;
    }

    geometry.setPositions(buffer.subarray(0, pointCount * 3));
    material.opacity = clamp(progress * 12);
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

    function update({ handoffProgress, storyProgress }) {
        if (suspended || failed) return;

        resize();
        const journey = clamp(
            handoffProgress * HANDOFF_ENTRY_WEIGHT
            + storyProgress * STORY_JOURNEY_WEIGHT,
        );

        strokes.forEach((stroke) => {
            const growth = rangeProgress(journey, ...stroke.definition.reveal);
            revealStroke(stroke, growth);
        });

        renderer.render(scene, camera);
        renderCount += 1;
        canvas.dataset.valuesRenderCount = String(renderCount);
        canvas.dataset.valuesSpatialProgress = journey.toFixed(4);
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
