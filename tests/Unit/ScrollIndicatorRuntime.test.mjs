import test from 'node:test';
import assert from 'node:assert/strict';
import { mountScrollIndicator, scrollIndicatorOffset } from '../../resources/js/sections/scroll-indicator.js';

test('indicator progress is clamped to the rail, including short documents', () => {
    assert.equal(scrollIndicatorOffset(0, 1100, 100, 180, 30), 0);
    assert.equal(scrollIndicatorOffset(500, 1100, 100, 180, 30), 75);
    assert.equal(scrollIndicatorOffset(1000, 1100, 100, 180, 30), 150);
    assert.equal(scrollIndicatorOffset(5000, 1100, 100, 180, 30), 150);
    assert.equal(scrollIndicatorOffset(-500, 1100, 100, 180, 30), 0);
    assert.equal(scrollIndicatorOffset(0, 80, 100, 180, 30), 0);
});

test('the passive rail follows scroll, fades after idle, and cleans up', () => {
    const oldWindow = globalThis.window;
    const oldDocument = globalThis.document;
    let frame;
    let timer;
    const listeners = new Map();
    const attributes = new Set();
    const rootAttributes = new Set();
    const thumb = { clientHeight: 30, style: {} };
    const root = {
        hidden: false, clientHeight: 180,
        querySelector: () => thumb,
        setAttribute: name => rootAttributes.add(name),
        removeAttribute: name => rootAttributes.delete(name),
    };
    const html = {
        toggleAttribute: (name, enabled) => enabled ? attributes.add(name) : attributes.delete(name),
        removeAttribute: name => attributes.delete(name),
    };
    globalThis.window = {
        innerHeight: 100, scrollY: 0,
        requestAnimationFrame: callback => { frame = callback; return 1; },
        cancelAnimationFrame: () => { frame = null; },
        setTimeout: callback => { timer = callback; return 1; },
        clearTimeout: () => { timer = null; },
        addEventListener: (name, callback) => listeners.set(name, callback),
    };
    globalThis.document = {
        hidden: false, documentElement: html,
        scrollingElement: { scrollHeight: 1100 },
        body: {}, addEventListener() {},
    };
    try {
        const controller = mountScrollIndicator(root);
        frame();
        assert.equal(attributes.has('data-scroll-indicator-ready'), true);
        window.scrollY = 500;
        listeners.get('scroll')();
        frame();
        assert.equal(thumb.style.transform, 'translate3d(0, 75px, 0)');
        assert.equal(rootAttributes.has('data-scrolling'), true);
        timer();
        assert.equal(rootAttributes.has('data-scrolling'), false);
        controller.suspend();
        assert.equal(rootAttributes.has('data-scrolling'), false);
        controller.resume();
        frame();
        controller.dispose();
        assert.equal(attributes.has('data-scroll-indicator-ready'), false);
        assert.equal(root.hidden, true);
    } finally {
        globalThis.window = oldWindow;
        globalThis.document = oldDocument;
    }
});
