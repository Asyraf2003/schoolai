import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/views/partials/site-navbar/behavior.blade.php', import.meta.url), 'utf8').match(/<script[^>]*>([\s\S]*)<\/script>/)[1];
function environment() {
    const frames = new Map(); let nextId = 0;
    function target() {
        const listeners = new Map();
        return { listeners, addEventListener(type, callback, options = {}) {
            const entries = listeners.get(type) || []; entries.push(callback); listeners.set(type, entries);
            options.signal?.addEventListener('abort', () => listeners.set(type, (listeners.get(type) || []).filter(item => item !== callback)), { once: true });
        }, fire(type, props = {}) {
            const event = { type, prevented: false, preventDefault() { this.prevented = true; }, stopPropagation() {}, ...props };
            [...listeners.get(type) || []].forEach(callback => callback(event)); return event;
        }, dispatchEvent(event) { this.fire(event.type, { detail: event.detail }); } };
    }
    const doc = target();
    function node(trigger = false) {
        const classes = new Set(), attributes = new Map();
        return { ...target(), connected: true, dataset: {}, style: {},
            classList: { add: (...names) => names.forEach(name => classes.add(name)), remove: (...names) => names.forEach(name => classes.delete(name)), contains: name => classes.has(name) },
            setAttribute: (key, value) => attributes.set(key,value), getAttribute: key => attributes.get(key),
            focus() { doc.activeElement = this; }, closest: () => trigger ? origin : null,
            getClientRects: () => [1], contains: value => value === dialog || options.includes(value) };
    }
    const origin = node(true), hamburger = node(), nav = node(), header = node(), modal = node(), dialog = node();
    const hiddenClose = node(); hiddenClose.getClientRects = () => [];
    const options = [node(),node()]; dialog.querySelectorAll = () => [hiddenClose,...options];
    const backdrop = node(); modal.querySelector = () => dialog; modal.querySelectorAll = () => [backdrop];
    doc.querySelector = () => modal; doc.getElementById = id => ({ hamburgerBtn: hamburger, navMenu: nav, navbar: header }[id]);
    doc.documentElement = { contains: value => value.connected };
    doc.body = { style: { overflow: 'clip' } }; doc.activeElement = origin;
    const win = { ...target(), requestAnimationFrame(callback) { frames.set(++nextId,callback); return nextId; }, cancelAnimationFrame(id) { frames.delete(id); } };
    const globals = { document: doc, window: win, AbortController, CustomEvent: class { constructor(type,init) { this.type=type;this.detail=init.detail; } } };
    const boot = () => runInNewContext(source,globals);
    const tick = () => { const callbacks=[...frames.values()];frames.clear();callbacks.forEach(callback=>callback()); };
    return { doc, win, modal, nav, header, origin, hamburger, dialog, options, backdrop, frames, boot, tick,
        open: () => doc.fire('click',{ target: origin }), close: () => doc.fire('keydown',{ key: 'Escape' }) };
}

test('canonical delegated owner opens before DOMContentLoaded and restores origin plus previous scroll ownership', () => {
    const e = environment();e.boot();
    assert.equal(e.doc.listeners.has('DOMContentLoaded'),false);
    assert.equal(e.open().prevented,true);assert.equal(e.modal.classList.contains('is-open'),true);
    assert.equal(e.doc.body.style.overflow,'hidden');e.tick();assert.equal(e.doc.activeElement,e.dialog);
    e.close();assert.equal(e.doc.activeElement,e.origin);assert.equal(e.doc.body.style.overflow,'clip');
    assert.equal(e.modal.getAttribute('aria-hidden'),'true');assert.equal(e.frames.size,0);
});
test('immediate Escape cancels stale dialog focus and repeated boot/open retains one owner and previous overflow', () => {
    const e=environment();e.boot();e.boot();assert.equal(e.doc.listeners.get('click').length,1);
    e.open();e.open();assert.equal(e.frames.size,1);e.close();e.tick();
    assert.equal(e.doc.activeElement,e.origin);assert.equal(e.doc.body.style.overflow,'clip');assert.equal(e.frames.size,0);
});
test('mobile language intent closes its navigation and restores focus to the hamburger with the original lock', () => {
    const e=environment();e.nav.classList.add('active');e.header.classList.add('has-open-menu');e.doc.body.style.overflow='hidden';
    e.doc.addEventListener('mobile-navigation:request-close',()=>{e.nav.classList.remove('active');e.hamburger.setAttribute('aria-expanded','false');e.doc.body.style.overflow='clip';});
    e.boot();e.open();e.tick();e.close();assert.equal(e.doc.activeElement,e.hamburger);assert.equal(e.doc.body.style.overflow,'clip');
    assert.equal(e.hamburger.getAttribute('aria-expanded'),'false');
});
test('Tab wraps actual visible choices and skips the hidden flag-only close control', () => {
    const e=environment();e.boot();e.open();e.tick();
    assert.equal(e.doc.fire('keydown',{key:'Tab'}).prevented,true);assert.equal(e.doc.activeElement,e.options[0]);
    e.doc.fire('keydown',{key:'Tab',shiftKey:true});assert.equal(e.doc.activeElement,e.options[1]);
    e.doc.fire('keydown',{key:'Tab'});assert.equal(e.doc.activeElement,e.options[0]);e.close();
});
test('BFCache suspends the pending focus frame, resumes one owner, and permanent exit disposes listeners and lock', () => {
    const e=environment();e.boot();e.open();e.win.fire('pagehide',{persisted:true});assert.equal(e.frames.size,0);
    assert.equal(e.modal.classList.contains('is-open'),true);e.win.fire('pageshow',{persisted:true});assert.equal(e.frames.size,1);
    e.tick();assert.equal(e.doc.activeElement,e.dialog);e.win.fire('pagehide',{persisted:false});
    assert.equal(e.doc.body.style.overflow,'clip');assert.equal(e.modal.classList.contains('is-open'),false);
    assert.equal(e.doc.listeners.get('click').length,0);e.open();assert.equal(e.frames.size,0);
});
