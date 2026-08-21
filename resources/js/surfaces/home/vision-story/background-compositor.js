import { clamp } from './geometry.js';

const DEFAULT_STATE = Object.freeze({
    color: 'var(--vision-background-default)',
    pattern: 'none',
    patternSize: 'auto',
    patternPosition: 'center',
});

const DESKTOP_STATE_NAMES = ['about', 'vision', 'mission'];

function stateFor(panel, index, desktop) {
    const stateName = DESKTOP_STATE_NAMES[index];
    const configured = desktop && stateName
        ? {
            color: `var(--vision-${stateName}-color)`,
            pattern: `var(--vision-${stateName}-pattern)`,
            patternSize: `var(--vision-${stateName}-pattern-size)`,
            patternPosition: `var(--vision-${stateName}-pattern-position)`,
        }
        : DEFAULT_STATE;

    return {
        color: panel?.dataset.visionBackgroundColor || configured.color,
        pattern: panel?.dataset.visionBackgroundPattern || configured.pattern,
        patternSize: panel?.dataset.visionBackgroundPatternSize
            || configured.patternSize,
        patternPosition: panel?.dataset.visionBackgroundPatternPosition
            || configured.patternPosition,
    };
}

function smooth(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

function applyState(layer, state) {
    layer.style.setProperty('--vision-state-color', state.color);
    layer.style.setProperty('--vision-state-pattern', state.pattern);
    layer.style.setProperty('--vision-state-pattern-size', state.patternSize);
    layer.style.setProperty(
        '--vision-state-pattern-position',
        state.patternPosition,
    );
}

export function createVisionBackgroundCompositor(root, panels) {
    const layers = Array.from(
        root.querySelectorAll('[data-vision-background-layer]'),
    );
    if (layers.length !== 2 || !panels.length) {
        return { setProgress() {}, destroy() {} };
    }

    const desktop = window.matchMedia('(min-width: 1280px)').matches;
    const states = panels.map((panel, index) => (
        stateFor(panel, index, desktop)
    ));
    const transitionCount = Math.max(1, states.length - 1);

    function setProgress(progress) {
        const scaled = clamp(progress) * transitionCount;
        const currentIndex = Math.min(states.length - 1, Math.floor(scaled));
        const nextIndex = Math.min(states.length - 1, currentIndex + 1);
        const blend = smooth(scaled - currentIndex);

        applyState(layers[0], states[currentIndex]);
        applyState(layers[1], states[nextIndex]);
        layers[0].style.setProperty('--vision-state-opacity', '1');
        layers[0].style.setProperty(
            '--vision-state-diffusion',
            `${(blend * 3).toFixed(2)}px`,
        );
        layers[1].style.setProperty('--vision-state-opacity', blend.toFixed(4));
        layers[1].style.setProperty(
            '--vision-state-diffusion',
            `${((1 - blend) * 12).toFixed(2)}px`,
        );
        layers[0].style.setProperty('--vision-state-drift', `${(-blend * 1.5).toFixed(2)}%`);
        layers[1].style.setProperty('--vision-state-drift', `${((1 - blend) * 1.5).toFixed(2)}%`);
    }

    function destroy() {
        layers.forEach((layer) => layer.removeAttribute('style'));
    }

    return { setProgress, destroy };
}
