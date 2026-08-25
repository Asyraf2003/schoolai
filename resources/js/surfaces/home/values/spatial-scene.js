import {
    PerspectiveCamera,
    REVISION,
    Scene,
    Vector2,
    WebGLRenderer,
} from 'three';
import {
    clamp,
    createValuesStrokes,
    rangeProgress,
    revealStroke,
} from './spatial-strokes.js';

const HANDOFF_ENTRY_WEIGHT = .24;
const STORY_JOURNEY_WEIGHT = .88;

export function createValuesSpatialScene(host) {
    const renderer = new WebGLRenderer({
        alpha: true,
        antialias: true,
        powerPreference: 'high-performance',
        stencil: false,
    });
    const camera = new PerspectiveCamera(46, 1, .1, 50);
    const scene = new Scene();
    const strokes = createValuesStrokes();
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
