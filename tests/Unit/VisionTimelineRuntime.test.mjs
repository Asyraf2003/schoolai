import test from 'node:test';
import assert from 'node:assert/strict';
import { createVisionTimeline } from '../../resources/js/surfaces/home/vision-story/timeline.js';

function scene() {
    const previousWindow = globalThis.window;
    const desktop = { matches: true };
    globalThis.window = { matchMedia: () => desktop };
    let writes = 0;
    function element() {
        const values = {};
        const classes = new Set();
        return {
            dataset: {}, values, classes,
            classList: { toggle(name, active) { if (active) classes.add(name); else classes.delete(name); }, remove: name => classes.delete(name) },
            style: new Proxy({
                setProperty(name, value) { writes++; values[name] = value; },
                removeProperty(name) { delete values[name]; },
            }, { set(target, name, value) { writes++; values[name] = value; target[name] = value; return true; } }),
            removeAttribute() { for (const key of Object.keys(values)) delete values[key]; },
        };
    }
    const panels = [element(), element(), element()];
    const visuals = [element(), element(), element()];
    const media = visuals.map(visual => { const art = element(); visual.querySelector = () => art; return art; });
    const layers = [element(), element()];
    const root = Object.assign(new EventTarget(), {
        dataset: {}, querySelectorAll: selector => selector.includes('background') ? layers : selector.includes('panel') ? panels : visuals,
    });
    return { root, panels, visuals, media, layers, desktop, writes: () => writes,
        restore() { globalThis.window = previousWindow; } };
}

test('Vision retains its clip, media range and reverse story while settled renders write nothing', () => {
    const s = scene();
    try {
        const timeline = createVisionTimeline(s.root);
        timeline.setProgress(.25);
        assert.equal(s.visuals[0].values.clipPath, 'inset(0 0 50.000% 0)');
        assert.equal(s.media[0].values.transform, 'translate3d(0, 4.000%, 0) scale(1.08)');
        assert.equal(s.root.dataset.visionMediaRange, '0,1');
        const writes = s.writes(); timeline.setProgress(.25); assert.equal(s.writes(), writes);
        timeline.setProgress(1);
        assert.equal(s.root.dataset.visionMediaRange, '2,2');
        assert.ok(s.panels[2].classes.has('is-active'));
        timeline.setProgress(0);
        assert.equal(s.root.dataset.visionMediaRange, '0,0');
        assert.ok(s.panels[0].classes.has('is-active'));
        assert.equal(s.visuals[0].values.clipPath, 'inset(0 0 0.000% 0)');
        timeline.destroy();
        assert.equal(s.root.dataset.visionMediaRange, undefined);
        assert.ok(s.panels.every(panel => !panel.classes.has('is-active')));
    } finally { s.restore(); }
});

test('Vision palette adapts across 1280 without changing progress or remounting the timeline', () => {
    const s = scene();
    try {
        const timeline = createVisionTimeline(s.root);
        timeline.setProgress(.75);
        assert.equal(s.layers[0].values['--vision-state-color'], 'var(--vision-vision-color)');
        assert.equal(s.layers[1].values['--vision-state-color'], 'var(--vision-mission-color)');
        s.desktop.matches = false; timeline.setProgress(.75);
        assert.equal(s.layers[0].values['--vision-state-color'], 'var(--vision-background-default)');
        assert.equal(s.layers[1].values['--vision-state-pattern'], 'none');
        s.desktop.matches = true; timeline.setProgress(.75);
        assert.equal(s.layers[1].values['--vision-state-color'], 'var(--vision-mission-color)');
        const writes = s.writes(); timeline.setProgress(.75); assert.equal(s.writes(), writes);
    } finally { s.restore(); }
});
