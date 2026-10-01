import test from 'node:test';
import assert from 'node:assert/strict';
import { createHomepageScrollGate } from '../../resources/js/pages/welcome/scroll-gate.js';
import { prepareVisionPreview } from '../../resources/js/pages/welcome/video-readiness.js';
import { initVisionVideoPreviews } from '../../resources/js/pages/welcome/video-previews.js';
import { prepareVisionAssets } from '../../resources/js/surfaces/home/vision-story/preparation.js';

function deferred() { let resolve; const promise = new Promise(done => { resolve = done; }); return { promise, resolve }; }
function environment({ hash = '', scrollY = 0 } = {}) {
    const previous = { window: globalThis.window, document: globalThis.document };
    class Element extends EventTarget {
        constructor() { super(); this.dataset = {}; this.attributes = new Map(); this.classList = { add() {}, remove() {} }; }
        setAttribute(key, value) { this.attributes.set(key, value); }
        removeAttribute(key) { this.attributes.delete(key); }
        closest() { return null; }
    }
    const html = new Element(); const hero = new Element(); const vision = new Element(); const timers = new Map();
    hero.contains = target => target === hero;
    const doc = Object.assign(new EventTarget(), {
        documentElement: html, hidden: false,
        querySelector: selector => selector.includes('slider') ? hero : vision,
        querySelectorAll: () => [],
    });
    const win = Object.assign(new EventTarget(), { scrollY, location: { hash },
        setTimeout(callback) { timers.set(callback, callback); return callback; }, clearTimeout(id) { timers.delete(id); },
    });
    Object.assign(globalThis, { window: win, document: doc });
    return { html, hero, vision, doc, win, timers, Element,
        restore() { Object.assign(globalThis, previous); },
        event(target, name, properties = {}) {
            const event = new Event(name);
            Object.entries(properties).forEach(([key, value]) => Object.defineProperty(event, key, { value }));
            target.dispatchEvent(event);
        },
    };
}

test('gate stays pending until actual readiness; release retains semantic actions and cleans deadline', () => {
    const e = environment();
    try {
        let fallback = 0; const gate = createHomepageScrollGate(() => { fallback++; });
        assert.equal(e.html.dataset.homeScrollGate, 'locked'); assert.equal(e.vision.attributes.get('aria-busy'), 'true');
        e.event(e.doc, 'focusin', { target: e.hero }); assert.equal(fallback, 0);
        gate.release('prepared'); assert.equal(e.html.dataset.homeScrollGate, 'unlocked');
        assert.equal(e.html.dataset.homeExperienceState, 'prepared'); assert.equal(e.timers.size, 0);
        assert.equal(e.vision.attributes.has('aria-busy'), false);
        e.event(e.win, 'pagehide'); assert.equal(fallback, 0);
    } finally { e.restore(); }
});

test('deadline and access intent select actual static fallback exactly once without preventing the action', async () => {
    for (const reason of ['deadline', 'link', 'focus', 'escape', 'pagehide']) {
        const e = environment();
        try {
            let count = 0; const gate = createHomepageScrollGate(() => { count++; });
            const action = new e.Element(); action.closest = selector => reason === 'link' && selector.startsWith('a,') ? action : null;
            if (reason === 'deadline') [...e.timers.values()][0]();
            if (reason === 'link') e.event(e.doc, 'click', { target: action });
            if (reason === 'focus') e.event(e.doc, 'focusin', { target: action });
            if (reason === 'escape') e.event(e.doc, 'keydown', { key: 'Escape' });
            if (reason === 'pagehide') e.event(e.win, 'pagehide');
            assert.equal((await gate.fallbackReady).state, 'static-fallback');
            assert.equal(count, 1); assert.equal(e.html.dataset.homeScrollGate, 'unlocked'); assert.equal(e.timers.size, 0);
            gate.release('prepared'); assert.equal(e.html.dataset.homeExperienceState, 'static-fallback');
        } finally { e.restore(); }
    }
});

test('hash and restored position never acquire a scroll lock', async () => {
    for (const options of [{ hash: '#program' }, { scrollY: 1200 }]) {
        const e = environment(options);
        try { const gate = createHomepageScrollGate(() => {}); assert.equal(e.timers.size, 0); assert.equal(e.html.dataset.homeScrollGate, 'unlocked'); assert.equal((await gate.fallbackReady).reason, 'position-or-anchor'); }
        finally { e.restore(); }
    }
});

function preview(e, posterWork = Promise.resolve()) {
    const element = new e.Element(); element.dataset.visionVideoSrc = 'https://media.example/vision.mp4';
    const poster = { decode: () => posterWork };
    element.closest = () => ({ querySelector: () => poster }); element.loads = 0; element.plays = 0; element.paused = true;
    element.load = () => { element.loads++; }; element.pause = () => { element.paused = true; };
    element.play = () => { element.plays++; element.paused = false; return Promise.resolve(); };
    return element;
}

test('first preview waits for decoded poster and real loadeddata, then pauses instead of running offscreen', async () => {
    const e = environment();
    try {
        const poster = deferred(); const v = preview(e, poster.promise); let ready = false;
        const result = prepareVisionPreview(v).then(state => { ready = true; return state; });
        await Promise.resolve(); assert.equal(v.loads, 0); poster.resolve(); await Promise.resolve(); await Promise.resolve();
        assert.equal(v.loads, 1); assert.equal(v.preload, 'metadata'); assert.equal(ready, false);
        e.event(v, 'loadeddata'); assert.equal(await result, 'frame-ready'); assert.equal(v.paused, true);
        assert.equal(await prepareVisionPreview(v), 'frame-ready'); assert.equal(v.loads, 1);
    } finally { e.restore(); }
});

test('reduced preview, media failure and cancellation settle a real poster/semantic fallback with no late hydrate', async () => {
    const e = environment();
    try {
        const reduced = preview(e); assert.equal(await prepareVisionPreview(reduced, { staticOnly: true }), 'poster-ready'); assert.equal(reduced.loads, 0);
        const failed = preview(e); const work = prepareVisionPreview(failed); await Promise.resolve(); await Promise.resolve(); e.event(failed, 'error');
        assert.equal(await work, 'poster-ready'); assert.equal(failed.paused, true);
        const poster = deferred(); const aborted = preview(e, poster.promise); const signal = new AbortController();
        const abortWork = prepareVisionPreview(aborted, { signal: signal.signal }); signal.abort(); assert.equal(await abortWork, 'semantic-fallback');
        const count = aborted.loads; poster.resolve(); await Promise.resolve(); await Promise.resolve(); assert.equal(aborted.loads, count); assert.equal(aborted.plays, 0);
    } finally { e.restore(); }
});

test('Vision assets await actual font readiness as well as first playable frame', async () => {
    const e = environment();
    try {
        const fonts = deferred(); e.doc.fonts = { ready: fonts.promise }; const v = preview(e); e.vision.querySelector = s => s.includes('preview') ? v : null;
        let ready = false; const work = prepareVisionAssets(e.vision, undefined, false).then(state => { ready = true; return state; });
        await Promise.resolve(); await Promise.resolve(); e.event(v, 'loadeddata'); await Promise.resolve(); assert.equal(ready, false);
        fonts.resolve(); assert.equal(await work, 'frame-ready');
    } finally { e.restore(); }
});


test('stacked Vision plays only the visible transition range and pauses hidden/disposed previews', async () => {
    const e = environment(); const previousObserver = globalThis.IntersectionObserver;
    try {
        e.html.dataset.homeScrollGate = 'unlocked'; e.html.dataset.homeExperienceState = 'prepared';
        e.vision.classList.contains = () => true; e.vision.dataset.visionMediaRange = '0,0';
        const videos = [0, 1, 2].map(index => {
            const v = preview(e); const visual = { dataset: { visionVisual: String(index) }, querySelector: () => ({ decode: () => Promise.resolve() }) };
            v.closest = selector => selector === '[data-vision-story]' ? e.vision : visual; return v;
        });
        for (const v of videos) { const work = prepareVisionPreview(v); await Promise.resolve(); await Promise.resolve(); e.event(v, 'loadeddata'); await work; }
        const observers = []; e.win.IntersectionObserver = true;
        globalThis.IntersectionObserver = class { constructor(callback) { this.callback = callback; observers.push(this); } observe() {} disconnect() { this.disconnected = true; } };
        e.doc.querySelectorAll = () => videos; e.win.matchMedia = () => Object.assign(new EventTarget(), { matches: false });
        initVisionVideoPreviews(); observers[0].callback(videos.map(target => ({ target, isIntersecting: true })));
        await Promise.resolve(); await Promise.resolve(); assert.deepEqual(videos.map(v => v.paused), [false, true, true]);
        e.vision.dataset.visionMediaRange = '0,1'; e.event(e.doc, 'schoolai:vision-media-range'); await Promise.resolve(); await Promise.resolve();
        assert.deepEqual(videos.map(v => v.paused), [false, false, true]);
        e.vision.dataset.visionMediaRange = '1,2'; e.event(e.doc, 'schoolai:vision-media-range'); await Promise.resolve(); await Promise.resolve();
        assert.deepEqual(videos.map(v => v.paused), [true, false, false]);
        e.doc.hidden = true; e.event(e.doc, 'visibilitychange'); assert.ok(videos.every(v => v.paused));
        e.event(e.win, 'pagehide', { persisted: false }); assert.ok(observers.every(o => o.disconnected));
    } finally { globalThis.IntersectionObserver = previousObserver; e.restore(); }
});
