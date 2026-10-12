import test from 'node:test';
import assert from 'node:assert/strict';
import { mountScrollMomentum, wheelPixels } from '../../resources/js/sections/scroll-momentum.js';

test('wheel deltas are normalized across wheel, line and page devices', () => {
    assert.equal(wheelPixels({ deltaY: 4, deltaMode: 0 }, 800), 4);
    assert.equal(wheelPixels({ deltaY: -3, deltaMode: 1 }, 800), -48);
    assert.equal(wheelPixels({ deltaY: 1, deltaMode: 2 }, 800), 800);
});

test('wheel inertia follows input strength and yields to native controls', () => {
    const oldWindow = globalThis.window;
    const oldDocument = globalThis.document;
    const oldStyle = globalThis.getComputedStyle;
    const listeners = new Map();
    let scheduled;
    let reduced = false;
    const html = { scrollHeight: 3000, hasAttribute: () => false };
    const media = { get matches() { return reduced; }, addEventListener() {} };
    globalThis.document = {
        hidden: false, body: {}, documentElement: html, scrollingElement: html,
        querySelector: () => null,
    };
    globalThis.window = {
        scrollY: 0, innerHeight: 800,
        scrollTo: (_, y) => { window.scrollY = y; },
        matchMedia: () => media,
        requestAnimationFrame: fn => { scheduled = fn; return 1; },
        cancelAnimationFrame: () => { scheduled = null; },
        addEventListener: (name, fn) => listeners.set(name, fn),
    };
    globalThis.getComputedStyle = () => ({ overflowY: 'visible' });
    const target = { nodeType: 1, parentElement: document.body, closest: () => null };
    const fire = (delta, extra = {}) => {
        const event = { target, deltaY: delta, deltaX: 0, deltaMode: 0, cancelable: true,
            defaultPrevented: false, preventDefault() { this.defaultPrevented = true; }, ...extra };
        listeners.get('wheel')(event);
        return event;
    };
    try {
        const controller = mountScrollMomentum();
        assert.equal(fire(60).defaultPrevented, true);
        scheduled(16);
        const slow = window.scrollY;
        assert.ok(slow > 0 && slow < 60);
        assert.equal(fire(180).defaultPrevented, true);
        scheduled(32);
        assert.ok(window.scrollY - slow > slow, 'faster wheel input moves farther per frame');
        const current = window.scrollY;
        assert.equal(fire(-35).defaultPrevented, true);
        scheduled(48);
        assert.ok(window.scrollY < current, 'reverse input immediately reverses direction');
        assert.equal(fire(50, { ctrlKey: true }).defaultPrevented, false);
        reduced = true;
        assert.equal(fire(100).defaultPrevented, false);
        reduced = false;
        controller.suspend();
        assert.equal(fire(100).defaultPrevented, false);
        controller.resume();
        assert.equal(fire(80).defaultPrevented, true);
        controller.dispose();
        assert.equal(scheduled, null);
    } finally {
        globalThis.window = oldWindow;
        globalThis.document = oldDocument;
        globalThis.getComputedStyle = oldStyle;
    }
});
