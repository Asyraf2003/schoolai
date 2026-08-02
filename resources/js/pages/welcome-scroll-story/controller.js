import { prepareStoryText } from './split-text.js';

const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));

function sceneProgress(element, viewportHeight) {
    const rect = element.getBoundingClientRect();
    return clamp((viewportHeight - rect.top) / (viewportHeight + rect.height));
}

function rootProgress(root, viewportHeight) {
    const rect = root.getBoundingClientRect();
    const travel = Math.max(1, rect.height - viewportHeight);
    return clamp(-rect.top / travel);
}

function unitProgress(progress, index, total) {
    const spread = Math.min(.7, Math.max(.18, total * .018));
    const start = total > 1 ? (index / (total - 1)) * spread : 0;
    return clamp((progress - start) / Math.max(.2, 1 - spread));
}

function paintUnit(unit, effect, progress, index) {
    const p = clamp(progress);
    const inverse = 1 - p;
    let transform = 'none';
    let filter = 'none';

    if (effect === 'stretch') {
        transform = `scaleY(${Math.max(.001, p)})`;
    } else if (effect === 'fan') {
        const sign = index % 2 ? 1 : -1;
        transform = `translate3d(${sign * inverse * 1.4}em, ${inverse * 1.1}em, 0) rotate(${sign * inverse * 18}deg)`;
    } else if (effect === 'focus') {
        transform = `translate3d(0, ${inverse * .65}em, 0) scale(${.82 + p * .18})`;
        filter = `blur(${inverse * 12}px)`;
    } else {
        transform = `translate3d(0, ${inverse * 1.25}em, 0)`;
    }

    unit.style.opacity = String(.08 + p * .92);
    unit.style.transform = transform;
    unit.style.filter = filter;
}

function paintText(item, progress) {
    const total = item.units.length;
    item.units.forEach((unit, index) => {
        paintUnit(unit, item.effect, unitProgress(progress, index, total), index);
    });
}

function paintLayers(root, progress) {
    const direction = document.documentElement.dir === 'rtl' ? -1 : 1;
    const width = window.innerWidth;
    const height = window.innerHeight;

    root.querySelectorAll('[data-story-layer]').forEach((layer) => {
        const depth = Number(layer.dataset.storyDepth || 1);
        const centered = progress - .5;
        const x = centered * width * .055 * depth * direction;
        const y = -centered * height * .035 * depth;
        const rotation = centered * (depth % 2 ? 10 : -8);
        const scale = .94 + progress * .08 + depth * .01;
        layer.style.transform = `translate3d(${x}px, ${y}px, 0) rotate(${rotation}deg) scale(${scale})`;
    });
}

export function createStoryController(root) {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const texts = prepareStoryText(root);
    const scenes = Array.from(root.querySelectorAll('[data-story-scene]'));
    let frame = 0;
    let destroyed = false;

    function render() {
        frame = 0;
        if (destroyed) return;

        const viewportHeight = window.innerHeight || 1;
        const rootValue = reduced.matches ? 1 : rootProgress(root, viewportHeight);

        if (root.dataset.storyKind === 'about') {
            texts.forEach((item) => paintText(item, rootValue));
            paintLayers(root, rootValue);
            return;
        }

        texts.forEach((item) => {
            const scene = item.element.closest('[data-story-scene]') || root;
            const progress = reduced.matches ? 1 : sceneProgress(scene, viewportHeight);
            paintText(item, progress);
        });

        scenes.forEach((scene) => {
            const progress = reduced.matches ? 1 : sceneProgress(scene, viewportHeight);
            scene.style.setProperty('--scene-progress', progress.toFixed(4));
        });
    }

    function requestRender() {
        if (!frame && !destroyed) frame = window.requestAnimationFrame(render);
    }

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', requestRender, { passive: true });
    reduced.addEventListener?.('change', requestRender);
    requestRender();

    return function destroy() {
        destroyed = true;
        if (frame) window.cancelAnimationFrame(frame);
        window.removeEventListener('scroll', requestRender);
        window.removeEventListener('resize', requestRender);
        reduced.removeEventListener?.('change', requestRender);
    };
}
