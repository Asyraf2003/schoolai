import test from 'node:test';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';
import assert from 'node:assert/strict';
import { createHomepageScrollGate } from '../../resources/js/pages/welcome/scroll-gate.js';
import { prepareVisionPreview } from '../../resources/js/pages/welcome/video-readiness.js';
import { initVisionVideoPreviews } from '../../resources/js/pages/welcome/video-previews.js';
import { prepareVisionAssets } from '../../resources/js/surfaces/home/vision-story/preparation.js';

function deferred() { let resolve; const promise = new Promise(done => { resolve = done; }); return { promise, resolve }; }
function environment({ hash = '', scrollY = 0 } = {}) {
    const previous = { window: globalThis.window, document: globalThis.document, innerHeight: globalThis.innerHeight };
    class Element extends EventTarget {
        constructor() { super(); this.dataset = {}; this.attributes = new Map(); this.classList = { add() {}, remove() {} }; }
        setAttribute(key, value) { this.attributes.set(key, value); }
        removeAttribute(key) { this.attributes.delete(key); }
        closest() { return null; }
        getBoundingClientRect() { return { top: 0, height: 900 }; }
    }
    const html = new Element(); const hero = new Element(); const vision = new Element(); const timers = new Map();
    hero.contains = target => target === hero;
    const doc = Object.assign(new EventTarget(), {
        documentElement: html, hidden: false,
        querySelector: selector => selector.includes('slider') ? hero : vision,
        querySelectorAll: () => [],
    });
    const win = Object.assign(new EventTarget(), { scrollY, location: { hash },
        setTimeout(callback) { timers.set(callback, callback); return callback; }, scrollTo() {}, clearTimeout(id) { timers.delete(id); },
    });
    Object.assign(globalThis, { window: win, document: doc, innerHeight: 900 });
    const source = readFileSync(new URL('../../resources/views/home/partials/opening-bootstrap.blade.php', import.meta.url), 'utf8').match(/<script[^>]*>([\s\S]*?)<\/script>/)[1];
    runInNewContext(source, { window: win, document: doc, location: win.location, scrollY, performance: { mark() {}, getEntriesByType: () => [] }, setTimeout: win.setTimeout, clearTimeout: win.clearTimeout, AbortController, CustomEvent });
    return { html, hero, vision, doc, win, timers, Element,
        restore() { Object.assign(globalThis, previous); },
        event(target, name, properties = {}) {
            const event = new Event(name);
            Object.entries(properties).forEach(([key, value]) => Object.defineProperty(event, key, { value }));
            target.dispatchEvent(event);
        },
    };
}

test('critical gate is adopted once and release requires complete readiness, paint, handoff and an active lifecycle', () => {
    const e = environment();
    try {
        const instance = e.win.schoolaiHomeOpening;
        assert.equal(e.html.dataset.homeScrollGate, 'locked');
        const gate = createHomepageScrollGate(() => {});
        assert.equal(gate, instance); assert.equal(gate.adopt(() => {}), false);
        for (const proof of [{}, { complete: false, painted: true, handoff: true }, { complete: true, handoff: true }, { complete: true, painted: true }]) {
            assert.equal(gate.release(proof), false); assert.equal(e.html.dataset.homeScrollGate, 'locked');
        }
        e.doc.hidden = true; assert.equal(gate.release({ complete: true, painted: true, handoff: true }), false);
        e.doc.hidden = false; assert.equal(gate.release({ complete: true, painted: true, handoff: true }), true);
        assert.equal(gate.release({ complete: true, painted: true, handoff: true }), false);
        assert.equal(e.html.dataset.homeScrollGate, 'unlocked'); assert.equal(e.timers.size, 0);
    } finally { e.restore(); }
});

test('deadline, navbar intent, focus and Escape cannot unlock an incomplete homepage', () => {
    for (const reason of ['deadline', 'link', 'focus', 'escape']) {
        const e = environment();
        try {
            const gate = createHomepageScrollGate(() => {});
            const action = new e.Element();
            if (reason === 'deadline') [...e.timers.values()][0]();
            if (reason === 'link') e.event(e.doc, 'click', { target: action });
            if (reason === 'focus') e.event(e.doc, 'focusin', { target: action });
            if (reason === 'escape') e.event(e.doc, 'keydown', { key: 'Escape' });
            assert.equal(e.html.dataset.homeScrollGate, 'locked');
            assert.notEqual(e.html.dataset.homeExperienceState, 'prepared');
            assert.equal(gate.release({ complete: false, painted: true, handoff: true }), false);
        } finally { e.restore(); }
    }
});

test('hash and restored positions wait for the complete readiness barrier', () => {
    for (const options of [{ hash: '#program' }, { scrollY: 1200 }]) {
        const e = environment(options);
        try { assert.equal(e.html.dataset.homeScrollGate, 'locked'); }
        finally { e.restore(); }
    }
});

function preview(e, posterWork = Promise.resolve()) {
    const element = new e.Element(); element.dataset.visionVideoSrc = 'https://media.example/vision.mp4';
    const poster = { naturalWidth: 1920, src: 'poster.webp', decode: () => posterWork };
    element.closest = () => ({ querySelector: () => poster }); element.loads = 0; element.plays = 0; element.paused = true;
    element.readyState = 0; element.videoWidth = 1920; element.currentTime = 0; element.duration = 10;
    element.buffered = { length: 1, start: () => 0, end: () => 1 };
    element.load = () => { element.loads++; };  element.pause = () => { element.paused = true; };
    element.play = () => { element.plays++; element.paused = false; return Promise.resolve(); };
    return element;
}

test('first preview waits for decoded poster and real loadeddata, then pauses instead of running offscreen', async () => {
    const e = environment();
    try {
        const poster = deferred(); const v = preview(e, poster.promise); let ready = false;
        const result = prepareVisionPreview(v).then(state => { ready = true; return state; });
        await Promise.resolve(); assert.equal(v.loads, 0); poster.resolve(); await Promise.resolve(); await Promise.resolve();
        assert.equal(v.loads, 1); assert.equal(v.preload, 'auto'); assert.equal(ready, false);
        v.readyState = 3; e.event(v, 'canplay'); assert.equal(await result, 'frame-ready'); assert.equal(v.paused, true);
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
        const abortWork = prepareVisionPreview(aborted, { signal: signal.signal }); signal.abort(); await assert.rejects(abortWork, { name: 'AbortError' });
        const count = aborted.loads; poster.resolve(); await Promise.resolve(); await Promise.resolve(); assert.equal(aborted.loads, count); assert.equal(aborted.plays, 0);
    } finally { e.restore(); }
});

test('Vision assets await actual font readiness as well as first playable frame', async () => {
    const e = environment();
    try {
        const fonts = deferred(); e.doc.fonts = { ready: fonts.promise }; const v = preview(e); e.vision.querySelector = s => s.includes('preview') ? v : null;
        let ready = false; const work = prepareVisionAssets(e.vision, undefined, false).then(state => { ready = true; return state; });
        await Promise.resolve(); await Promise.resolve(); v.readyState = 3; e.event(v, 'canplay'); await Promise.resolve(); assert.equal(ready, false);
        fonts.resolve(); assert.equal(await work, 'poster-ready');
    } finally { e.restore(); }
});


test('stacked Vision plays only the visible transition range and pauses hidden/disposed previews', async () => {
    const e = environment(); const previousObserver = globalThis.IntersectionObserver;
    try {
        e.html.dataset.homeScrollGate = 'unlocked'; e.html.dataset.homeExperienceState = 'prepared';
        e.vision.classList.contains = () => true; e.vision.dataset.visionMediaRange = '0,0';
        const videos = [0, 1, 2].map(index => {
            const v = preview(e); const visual = { dataset: { visionVisual: String(index) }, querySelector: () => ({ naturalWidth: 1920, src: 'poster.webp', decode: () => Promise.resolve() }) };
            v.closest = selector => selector === '[data-vision-story]' ? e.vision : visual; return v;
        });
        const observers = []; e.win.IntersectionObserver = true;
        globalThis.IntersectionObserver = class { constructor(callback) { this.callback = callback; observers.push(this); } observe() {} disconnect() { this.disconnected = true; } };
        e.doc.querySelectorAll = () => videos; e.win.matchMedia = () => Object.assign(new EventTarget(), { matches: false });
        videos.forEach(v => { v.paused = false; });
        initVisionVideoPreviews(); observers[0].callback(videos.map(target => ({ target, isIntersecting: false })));
        assert.deepEqual(videos.map(v => v.paused), [false, false, false]);
        assert.deepEqual(videos.map(v => v.loads), [0, 0, 0]);
        for (const v of videos) { const work = prepareVisionPreview(v); await Promise.resolve(); await Promise.resolve(); v.readyState = 3; e.event(v, 'canplay'); await work; }
        observers[0].callback(videos.map(target => ({ target, isIntersecting: true })));
        await Promise.resolve(); await Promise.resolve(); assert.deepEqual(videos.map(v => v.paused), [false, true, true]);
        e.vision.dataset.visionMediaRange = '0,1'; e.event(e.doc, 'schoolai:vision-media-range'); await Promise.resolve(); await Promise.resolve();
        assert.deepEqual(videos.map(v => v.paused), [false, false, true]);
        e.vision.dataset.visionMediaRange = '1,2'; e.event(e.doc, 'schoolai:vision-media-range'); await Promise.resolve(); await Promise.resolve();
        assert.deepEqual(videos.map(v => v.paused), [true, false, false]);
        e.doc.hidden = true; e.event(e.doc, 'visibilitychange'); assert.ok(videos.every(v => v.paused));
        e.event(e.win, 'pagehide', { persisted: false }); assert.ok(observers.every(o => o.disconnected));
    } finally { globalThis.IntersectionObserver = previousObserver; e.restore(); }
});


test('Vision scopes font readiness to its actual text when global font readiness remains pending', async () => {
    const e = environment();
    try {
        const globalFonts = deferred(); const localFonts = deferred(); let loads = 0;
        e.doc.fonts = { ready: globalFonts.promise, check: () => false, load(font, text) { loads++; assert.ok(font.includes('Inter')); assert.equal(text, 'Vision'); return localFonts.promise; } };
        e.win.getComputedStyle = () => ({ fontStyle: 'normal', fontWeight: '700', fontSize: '32px', fontFamily: 'Inter' });
        const v = preview(e); e.vision.querySelector = s => s.includes('preview') ? v : null; e.vision.querySelectorAll = () => [{ textContent: 'Vision' }];
        const work = prepareVisionAssets(e.vision, undefined, false); await new Promise(setImmediate); v.readyState = 3; e.event(v, 'canplay');
        assert.equal(loads, 1); localFonts.resolve(); assert.equal(await work, 'poster-ready');
        assert.equal(e.vision.dataset.visionFontsReady, 'true');
    } finally { e.restore(); }
});
