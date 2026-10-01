import test from 'node:test';
import assert from 'node:assert/strict';
import { mountProgramJourney } from '../../resources/js/surfaces/home/program-journey/controller.js';
import { prepareGalleryMedia } from '../../resources/js/pages/welcome/gallery-media-preparation.js';
import { mountGalleryVideoPreviews } from '../../resources/js/pages/welcome/gallery-video-preview.js';
import { fitGalleryStoryVisuals } from '../../resources/js/pages/welcome/gallery-story-visual.js';

function environment() {
    const keys = ['window', 'document', 'HTMLImageElement', 'HTMLVideoElement', 'IntersectionObserver'];
    const saved = Object.fromEntries(keys.map(key => [key, globalThis[key]]));
    class Element extends EventTarget {
        constructor() { super(); this.dataset = {}; this.classes = new Set(); this.props = new Map();
            this.style = { setProperty: (key, value) => this.props.set(key, value), removeProperty: key => this.props.delete(key) };
            this.classList = { add: (...names) => names.forEach(n => this.classes.add(n)), remove: (...names) => names.forEach(n => this.classes.delete(n)), toggle: (name, value) => value ? this.classes.add(name) : this.classes.delete(name) };
            this.nodes = {}; this.hidden = true;
        }
        querySelector(selector) { return this.nodes[selector] || null; }
        querySelectorAll(selector) { return this.nodes[selector] || []; }
        click() { this.dispatchEvent(new Event('click')); }
        focus() {}
        remove() { this.removed = true; }
    }
    class Video extends Element {
        constructor() { super(); this.dataset.galleryVideoSrc = 'school.mp4'; this.loads = 0; this.plays = 0; this.paused = true; }
        load() { this.loads++; }
        play() { this.plays++; this.paused = false; return Promise.resolve(); }
        pause() { this.paused = true; }
        removeAttribute(name) { if (name === 'src') this.src = ''; }
    }
    class Image extends Element { decode() { return this.work || Promise.resolve(); } }
    const reduced = Object.assign(new EventTarget(), { matches: false });
    const timers = new Map(); const observers = []; const scripts = [];
    class Observer {
        constructor(callback, options) { this.callback = callback; this.options = options; observers.push(this); }
        observe() {} disconnect() { this.disconnected = true; }
    }
    const win = Object.assign(new EventTarget(), { matchMedia: () => reduced, getComputedStyle: () => ({ opacity: '.16' }),
        setTimeout: callback => { timers.set(callback, callback); return callback; }, clearTimeout: key => timers.delete(key),
        IntersectionObserver: Observer });
    const doc = Object.assign(new EventTarget(), { hidden: false, documentElement: new Element(), body: new Element(),
        createElement: () => new Element(), head: { appendChild: script => scripts.push(script) } });
    Object.assign(globalThis, { window: win, document: doc, HTMLVideoElement: Video, HTMLImageElement: Image, IntersectionObserver: Observer });
    const fire = (target, type, props = {}) => { const event = new Event(type); Object.assign(event, props); target.dispatchEvent(event); };
    function program() {
        const root = new Element(), trigger = new Element(), back = new Element(), detail = new Element();
        trigger.dataset.programIndex = '0'; detail.nodes['[data-program-back]'] = back;
        root.nodes = { '[data-program-open]': [trigger], '[data-program-back]': [back], '[data-program-detail]': [detail],
            '[data-program-card]': [], '[data-program-type-line]': [new Element()], '[data-program-type]': new Element(),
            '[data-program-detail-layer]': new Element() };
        return { root, trigger, back, detail };
    }
    return { Element, Video, Image, timers, scripts, observers, win, doc, reduced, fire, program,
        restore: () => Object.assign(globalThis, saved) };
}
const flush = () => new Promise(setImmediate);

test('Program starts background loading immediately, stays pending, and installs functional fallback on deadline', async () => {
    const e = environment();
    try {
        const p = e.program(); const cleanup = mountProgramJourney(p.root); let settled = false;
        cleanup.ready.then(() => { settled = true; }); await flush();
        assert.equal(e.scripts.length, 1); assert.equal(settled, false); assert.equal(e.observers.length, 0);
        p.trigger.click(); [...e.timers.values()].forEach(callback => callback()); await flush();
        assert.equal((await cleanup.ready).state, 'static-fallback');
        assert.equal(e.scripts[0].removed, true); assert.equal(p.detail.hidden, false);
        e.fire(e.doc, 'keydown', { key: 'Escape' }); assert.equal(p.detail.hidden, true);
        cleanup(); p.trigger.click(); assert.equal(p.detail.hidden, true);
    } finally { e.restore(); }
});
test('Program actual GSAP readiness, hidden suspension and permanent disposal do not remount controls', async () => {
    const e = environment();
    try {
        let timelines = 0; e.win.gsap = { set() {}, timeline: () => { timelines++; return {}; } };
        const p = e.program(), cleanup = mountProgramJourney(p.root);
        assert.equal((await cleanup.ready).state, 'gsap'); assert.equal(timelines, 0);
        e.reduced.matches = true; e.fire(e.reduced, 'change'); assert.equal(p.root.dataset.programReady, 'static-fallback');
        p.trigger.click(); assert.equal(p.detail.hidden, false); p.back.click();
        e.reduced.matches = false; e.fire(e.reduced, 'change'); await flush(); assert.equal(p.root.dataset.programReady, 'gsap');
        e.fire(e.win, 'pagehide', { persisted: true }); e.fire(e.win, 'pageshow', { persisted: true });
        e.fire(e.win, 'pagehide', { persisted: false }); assert.equal(p.root.classes.has('has-gsap'), false);
        p.trigger.click(); assert.equal(timelines, 0);
    } finally { e.restore(); }
});
test('reduced Program installs static controls without requesting GSAP', async () => {
    const e = environment();
    try {
        e.reduced.matches = true; const p = e.program(), cleanup = mountProgramJourney(p.root);
        assert.equal((await cleanup.ready).state, 'static-fallback'); assert.equal(e.scripts.length, 0);
        p.trigger.click(); assert.equal(p.detail.hidden, false); p.back.click(); assert.equal(p.detail.hidden, true); cleanup();
    } finally { e.restore(); }
});
test('Gallery first-frame preparation pauses offscreen, and abort/error/deadline remove pending source', async () => {
    for (const mode of ['ready', 'error', 'deadline', 'abort', 'reduced']) {
        const e = environment();
        try {
            const video = new e.Video(), controller = new AbortController();
            if (mode === 'reduced') e.reduced.matches = true;
            const work = prepareGalleryMedia(video, controller.signal);
            if (mode === 'ready') { e.fire(video, 'loadeddata'); }
            if (mode === 'error') e.fire(video, 'error');
            if (mode === 'deadline') [...e.timers.values()].forEach(callback => callback());
            if (mode === 'abort') controller.abort();
            assert.equal(await work, mode === 'ready' ? 'frame-ready' : mode === 'reduced' ? 'poster-ready' : 'semantic-fallback');
            assert.equal(video.paused, true); assert.equal(e.timers.size, 0);
            const loads = video.loads; e.fire(video, 'loadeddata'); assert.equal(video.loads, loads);
            if (mode === 'reduced') assert.equal(video.loads, 0);
        } finally { e.restore(); }
    }
});
test('Gallery ahead window prepares sequentially; only visible video plays; persisted/hidden/reduced and disposal pause', async () => {
    const e = environment();
    try {
        const first = new e.Video(), second = new e.Video(), distant = new e.Video(); const root = new e.Element();
        root.nodes['[data-gallery-story-visual]'] = [first, second, distant]; const cleanup = mountGalleryVideoPreviews(root);
        const ahead = e.observers.find(o => o.options.rootMargin === '100% 0px');
        const visible = e.observers.find(o => o.options.rootMargin === '0px');
        ahead.callback([first, second].map(target => ({ target, isIntersecting: true })));
        assert.equal(first.loads, 1); assert.equal(second.loads, 0); assert.equal(distant.loads, 0);
        e.fire(first, 'loadeddata'); await flush(); assert.equal(first.paused, true); assert.equal(second.loads, 1);
        e.fire(second, 'loadeddata'); await flush(); visible.callback([{ target: first, isIntersecting: true }]); assert.equal(first.paused, false);
        e.doc.hidden = true; e.fire(e.doc, 'visibilitychange'); assert.equal(first.paused, true);
        e.doc.hidden = false; e.fire(e.doc, 'visibilitychange'); assert.equal(first.paused, false);
        e.fire(e.win, 'pagehide', { persisted: true }); assert.equal(first.paused, true);
        e.fire(e.win, 'pageshow', { persisted: true }); assert.equal(first.paused, false);
        e.reduced.matches = true; e.fire(e.reduced, 'change'); assert.equal(first.paused, true);
        cleanup(); assert.ok(e.observers.every(o => o.disconnected)); assert.equal(distant.loads, 0);
    } finally { e.restore(); }
});
test('Gallery repeated resize before image load retains one fitting callback', () => {
    const e = environment();
    try {
        const image = new e.Image(), media = new e.Element(), item = new e.Element(); let callbacks = 0;
        image.complete = false; media.nodes['[data-gallery-story-visual]'] = image; item.nodes['[data-gallery-story-media]'] = media;
        for (let i = 0; i < 10; i++) fitGalleryStoryVisuals([item], () => callbacks++);
        e.fire(image, 'load'); assert.equal(callbacks, 1);
    } finally { e.restore(); }
});

test('cancelled GSAP preparation removes its script and ignores late load', async () => {
    const e = environment();
    try {
        const { loadGsap } = await import('../../resources/js/surfaces/home/program-journey/motion.js?cancel-proof');
        const controller = new AbortController(); const work = loadGsap({ signal: controller.signal });
        const script = e.scripts[0]; controller.abort();
        await assert.rejects(work, /aborted/); assert.equal(script.removed, true);
        assert.equal(script.onload, null); assert.equal(e.timers.size, 0);
    } finally { e.restore(); }
});
