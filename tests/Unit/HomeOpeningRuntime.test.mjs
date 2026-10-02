import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';
import { createOpeningLedger, REQUIRED_OPENING_UNITS } from '../../resources/js/pages/welcome/opening-ledger.js';
import { createOpeningProgress } from '../../resources/js/pages/welcome/opening-progress.js';
import { usableOpeningUnit, prepareHeroActiveMedia } from '../../resources/js/pages/welcome/opening-readiness.js';

const bootstrap = readFileSync(new URL('../../resources/views/home/partials/opening-bootstrap.blade.php', import.meta.url), 'utf8').match(/<script[^>]*>([\s\S]*?)<\/script>/)[1];
const turn = () => new Promise(setImmediate);
function deferred() { let resolve; const promise = new Promise(done => { resolve = done; }); return { promise, resolve }; }
function environment() {
    const previous = { window: globalThis.window, document: globalThis.document };
    const frames = new Map(), timers = new Map(), marks = []; let sequence = 0;
    class Element extends EventTarget {
        constructor() { super(); this.dataset = {}; this.textContent = 'Usable semantic content'; this.height = 900; }
        closest() { return null; }
        removeAttribute() {}
        toggleAttribute(name, enabled) { this.attributes ||= {}; this.attributes[name] = enabled; }
        getBoundingClientRect() { return { top: 0, bottom: this.height, height: this.height, width: 200 }; }
    }
    const root = new Element(), loader = new Element(), progress = new Element(), percent = new Element();
    const hero = new Element(), video = new Element(); video.readyState = 0;
    hero.querySelector = () => ({ querySelector: () => video });
    loader.querySelector = selector => selector.includes('percent') ? percent : progress;
    const animation = deferred(); let pauses = 0, plays = 0;
    loader.animate = () => ({ finished: animation.promise, pause() { pauses++; }, play() { plays++; }, cancel() { animation.resolve(); } });
    const doc = Object.assign(new EventTarget(), { hidden: false, documentElement: root,
        querySelector: selector => selector === '[data-home-opening]' ? loader : hero,
    });
    const win = Object.assign(new EventTarget(), {
        innerHeight: 844,
        matchMedia: () => Object.assign(new EventTarget(), { matches: false }),
        getComputedStyle: () => ({ display: 'block', visibility: 'visible' }),
        requestAnimationFrame: callback => { frames.set(++sequence, callback); return sequence; },
        setTimeout: callback => { timers.set(++sequence, callback); return sequence; }, clearTimeout: id => timers.delete(id),
    });
    Object.assign(globalThis, { window: win, document: doc });
    const execute = () => runInNewContext(bootstrap, { window: win, document: doc, location: { hash: '' }, scrollY: 0,
        performance: { mark: value => marks.push(value), getEntriesByType: () => [] },
        setTimeout: win.setTimeout, clearTimeout: win.clearTimeout, AbortController, CustomEvent,
    });
    execute();
    const fire = (target, name, properties = {}) => {
        const event = new Event(name, { cancelable: true });
        for (const [key, value] of Object.entries(properties)) Object.defineProperty(event, key, { value });
        target.dispatchEvent(event); return event;
    };
    const flush = () => { const work = [...frames.values()]; frames.clear(); work.forEach(callback => callback()); };
    return { root, doc, win, hero, video, loader, progress, percent, frames, timers, marks, animation, fire, flush, execute,
        get pauses() { return pauses; }, get plays() { return plays; }, restore() { Object.assign(globalThis, previous); } };
}

test('five real units settle out of order; failure/abort/invalid fallback never advances progress', () => {
    const ledger = createOpeningLedger();
    assert.equal(ledger.progress, 0);
    assert.equal(ledger.settle('footer', 'PREPARED', true), false);
    assert.equal(ledger.settle('hero', 'ABORTED', false), false);
    assert.equal(ledger.settle('hero', 'STATIC_FALLBACK', false), false);
    for (const unit of ['gallery', 'values', 'program', 'vision']) {
        assert.equal(ledger.settle(unit, 'PREPARED', true), true);
        assert.equal(ledger.settle(unit, 'PREPARED', true), false);
        assert.ok(ledger.progress < 100); assert.equal(ledger.complete, false);
    }
    assert.equal(ledger.progress, 80);
    assert.equal(ledger.settle('hero', 'STATIC_FALLBACK', true), true);
    assert.equal(ledger.progress, 100); assert.equal(ledger.complete, true);
});

test('critical bootstrap blocks wheel/touch/keyboard before any runtime adoption, while navigation is exempt', () => {
    const e = environment();
    try {
        const gate = e.win.schoolaiHomeOpening;
        for (const type of ['wheel', 'touchmove']) assert.equal(e.fire(e.doc, type).defaultPrevented, true);
        assert.equal(e.fire(e.doc, 'keydown', { key: 'PageDown' }).defaultPrevented, true);
        const navbar = { closest: selector => selector.includes('#navbar') ? navbar : null };
        assert.equal(e.fire(e.doc, 'wheel', { target: navbar }).defaultPrevented, false);
        e.execute(); assert.equal(e.win.schoolaiHomeOpening, gate);
        assert.equal(gate.adopt(() => {}), true); assert.equal(gate.adopt(() => {}), false);
        assert.equal(e.root.dataset.homeScrollGate, 'locked');
    } finally { e.restore(); }
});

test('visible progress follows real settlement and 100 remains locked through two paints and handoff', async () => {
    const e = environment();
    try {
        const gate = e.win.schoolaiHomeOpening, ledger = createOpeningLedger();
        const controller = createOpeningProgress(gate, ledger);
        assert.equal(e.progress.value, 0);
        for (const unit of REQUIRED_OPENING_UNITS) { ledger.settle(unit, 'STATIC_FALLBACK', true); controller.update(); }
        assert.equal(e.progress.value, 100); assert.equal(e.percent.textContent, '100%');
        const completion = controller.complete(); await turn();
        assert.equal(e.root.dataset.homeScrollGate, 'locked');
        e.flush(); await turn(); assert.equal(e.root.dataset.homeScrollGate, 'locked');
        e.flush(); await turn(); assert.equal(e.root.dataset.homeOpeningPhase, 'handoff');
        assert.equal(e.root.dataset.homeScrollGate, 'locked');
        e.animation.resolve(); await completion;
        assert.equal(e.root.dataset.homeScrollGate, 'unlocked');
        await controller.complete(); assert.equal(e.marks.length, 1);
    } finally { e.restore(); }
});

test('hidden/BFCache suspends opening and restores the same owner without early unlock', async () => {
    const e = environment();
    try {
        const gate = e.win.schoolaiHomeOpening, ledger = createOpeningLedger();
        const controller = createOpeningProgress(gate, ledger);
        for (const unit of REQUIRED_OPENING_UNITS) ledger.settle(unit, 'PREPARED', true);
        const completion = controller.complete();
        for (let i = 0; i < 5 && e.root.dataset.homeOpeningPhase !== 'handoff'; i++) { await turn(); e.flush(); }
        await turn(); assert.equal(e.root.dataset.homeOpeningPhase, 'handoff');
        e.fire(e.win, 'pagehide', { persisted: true }); e.doc.hidden = true; e.fire(e.doc, 'visibilitychange');
        e.animation.resolve(); await turn();
        assert.equal(e.root.dataset.homeScrollGate, 'locked'); assert.ok(e.pauses > 0);
        e.doc.hidden = false; e.fire(e.win, 'pageshow', { persisted: true });
        e.fire(e.doc, 'schoolai:opening-active');
        await completion; assert.equal(e.win.schoolaiHomeOpening, gate); assert.equal(e.marks.length, 1);
    } finally { e.restore(); }
});

test('fallback usability checks actual semantic geometry, and Hero waits for a usable frame', async () => {
    const e = environment();
    try {
        assert.equal(usableOpeningUnit('hero'), true);
        e.hero.height = 0; assert.equal(usableOpeningUnit('hero'), false); e.hero.height = 900;
        e.hero.textContent = ''; assert.equal(usableOpeningUnit('hero'), false); e.hero.textContent = 'Hero';
        let settled = false;
        const work = prepareHeroActiveMedia().then(result => { settled = true; return result; });
        await turn(); assert.equal(settled, false);
        e.video.readyState = 2; e.fire(e.video, 'loadeddata'); assert.equal((await work).state, 'prepared');
    } finally { e.restore(); }
});

test('reduced motion paints truthful 100 before unlocking without an animation', async () => {
    const e = environment();
    try {
        e.win.matchMedia = () => ({ matches: true });
        e.loader.animate = () => { throw new Error('Reduced motion must not animate'); };
        const ledger = createOpeningLedger();
        const controller = createOpeningProgress(e.win.schoolaiHomeOpening, ledger);
        for (const unit of REQUIRED_OPENING_UNITS) ledger.settle(unit, 'STATIC_FALLBACK', true);
        const completion = controller.complete();
        for (let i = 0; i < 5; i++) { await turn(); e.flush(); }
        await completion; assert.equal(e.progress.value, 100); assert.equal(e.marks.length, 1);
    } finally { e.restore(); }
});

test('missing visible progress cannot satisfy the painted handoff proof', async () => {
    const e = environment();
    try {
        const ledger = createOpeningLedger();
        const controller = createOpeningProgress(e.win.schoolaiHomeOpening, ledger);
        for (const unit of REQUIRED_OPENING_UNITS) ledger.settle(unit, 'PREPARED', true);
        e.progress.height = 0;
        const completion = controller.complete();
        for (let i = 0; i < 5; i++) { await turn(); e.flush(); }
        await completion; assert.equal(e.root.dataset.homeScrollGate, 'locked'); assert.equal(e.marks.length, 0);
    } finally { e.restore(); }
});


test('opening keeps direct primary access when CTA geometry is outside the viewport', () => {
    const e = environment();
    try {
        const ledger = createOpeningLedger();
        createOpeningProgress(e.win.schoolaiHomeOpening, ledger);
        assert.equal(e.loader.attributes['data-primary-outside'], true);
        assert.equal(ledger.progress, 0);
        e.hero.height = 500; e.fire(e.win, 'resize');
        assert.equal(e.loader.attributes['data-primary-outside'], false);
        assert.equal(e.root.dataset.homeScrollGate, 'locked');
    } finally { e.restore(); }
});


test('Hero without usable media cannot report a prepared first frame', async () => {
    const e = environment();
    try {
        const image = { complete: false, naturalWidth: 0 };
        e.hero.querySelector = () => ({ querySelector: selector => selector === 'img' ? image : null });
        assert.equal((await prepareHeroActiveMedia()).state, 'static-fallback');
        image.complete = true; image.naturalWidth = 400;
        assert.equal((await prepareHeroActiveMedia()).state, 'prepared');
        e.hero.querySelector = () => ({ querySelector: () => null });
        assert.equal((await prepareHeroActiveMedia()).state, 'static-fallback');
    } finally { e.restore(); }
});
