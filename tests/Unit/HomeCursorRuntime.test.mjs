import test from 'node:test';
import assert from 'node:assert/strict';
import { initHomepageCursor } from '../../resources/js/pages/welcome/cursor.js';

function environment({ fine = true, random = 0 } = {}) {
    const original = Object.fromEntries(['window','document','Element','Image','MutationObserver'].map(k => [k, globalThis[k]]));
    const originalRandom = Math.random;
    Math.random = () => random;
    const frames = new Map();
    const images = [];
    const observers = [];
    let next = 0;
    class Element extends EventTarget {
        constructor() {
            super();
            this.dataset = {};
            this.properties = new Map();
            this.classes = new Set();
            this.children = [];
            this.style = { setProperty: (name, value) => this.properties.set(name, value) };
            this.classList = {
                contains: c => this.classes.has(c), add: c => this.classes.add(c), remove: c => this.classes.delete(c),
                toggle: (c, active) => active ? this.classes.add(c) : this.classes.delete(c),
            };
        }
        append(child) { child.remove(); this.children.push(child); child.parentElement = this; }
        remove() {
            if (this.parentElement) this.parentElement.children = this.parentElement.children.filter(c => c !== this);
            this.parentElement = null;
        }
        setAttribute() {}
        removeAttribute(name) { if (name === 'data-cursor-character') delete this.dataset.cursorCharacter; }
        closest(selector) { return selector.includes('[disabled]') ? (this.disabled ? this : null) : (this.interactive ? this : null); }
        matches() { return this.modal; }
    }
    const body = new Element();
    body.classes.add('site-cursor-page');
    const root = new Element();
    const capability = new EventTarget();
    capability.matches = fine;
    const doc = Object.assign(new EventTarget(), {
        body, documentElement: root, hidden: false, fullscreenElement: null, dialogs: [],
        createElement: () => new Element(), querySelectorAll: () => doc.dialogs,
    });
    const win = Object.assign(new EventTarget(), {
        matchMedia: () => capability,
        requestAnimationFrame: cb => { frames.set(++next, cb); return next; },
        cancelAnimationFrame: id => frames.delete(id),
    });
    Object.assign(globalThis, {
        Element, document: doc, window: win,
        Image: class { constructor() { images.push(this); } decode() { return Promise.resolve(); } },
        MutationObserver: class {
            constructor(cb) { this.callback = cb; observers.push(this); }
            observe() { this.active = true; }
            disconnect() { this.active = false; }
        },
    });
    return {
        body, doc, win, root, frames, images, observers, Element, capability,
        async load(image = images[0]) { image.onload(); await Promise.resolve(); },
        flush() { const work = [...frames.values()]; frames.clear(); work.forEach(cb => cb()); },
        fire(target, type, properties = {}) {
            const event = new Event(type);
            for (const [key,value] of Object.entries(properties)) Object.defineProperty(event,key,{value});
            target.dispatchEvent(event);
        },
        restore() { Object.assign(globalThis, original); Math.random = originalRandom; },
    };
}

for (const [random,character] of [[0,'cwo'],[0.99,'cwe']]) {
    test(`${character}: coalesces movement, loads useful hover on intent and preserves native disabled pointer`, async () => {
        const env = environment({random});
        try {
            const destroy = initHomepageCursor();
            const cursor = env.body.children[0];
            assert.equal(env.images.length,1);
            assert.ok(env.images[0].src.endsWith(`${character}1.webp`));
            assert.equal(env.body.dataset.cursorCharacter,undefined);
            await env.load(); env.flush();
            for (let x=0;x<100;x++) env.fire(env.doc,'pointermove',{clientX:x,clientY:22});
            assert.equal(env.frames.size,1);
            env.flush();
            assert.equal(cursor.properties.get('--home-cursor-x'),'99px');
            assert.equal(env.body.dataset.cursorCharacter,character);
            env.fire(env.doc,'pointermove',{clientX:99,clientY:22,pointerType:'touch'});
            assert.equal(env.body.dataset.cursorCharacter,undefined);
            env.fire(env.doc,'pointermove',{clientX:99,clientY:22}); env.flush();
            const link = new env.Element(); link.interactive = true;
            env.fire(env.doc,'pointerover',{target:link}); env.flush();
            assert.equal(env.images.length,2);
            assert.ok(env.images[1].src.endsWith(`${character}2.webp`));
            assert.equal(cursor.dataset.state,'default');
            await env.load(env.images[1]); env.flush();
            assert.equal(cursor.dataset.state,'interactive');
            link.disabled = true;
            env.fire(env.doc,'pointerover',{target:link}); env.flush();
            assert.equal(cursor.dataset.state,'disabled');
            assert.equal(cursor.classes.has('is-visible'),false);
            destroy();
            assert.equal(env.frames.size,0);
            assert.equal(env.body.children.length,0);
            assert.equal(env.observers[0].active,false);
            env.fire(env.doc,'pointermove',{clientX:5,clientY:5});
            assert.equal(env.frames.size,0);
        } finally { env.restore(); }
    });
}

test('cursor suspends across hidden/BFCache, follows modal/fullscreen and disposes on permanent exit', async () => {
    const env = environment();
    try {
        initHomepageCursor(); await env.load(); env.flush();
        const cursor = env.body.children[0];
        const dialog = new env.Element(); dialog.modal = true; env.doc.dialogs = [dialog];
        env.observers[0].callback(); assert.equal(cursor.parentElement,dialog);
        const fullscreen = new env.Element(); env.doc.fullscreenElement = fullscreen;
        env.fire(env.doc,'fullscreenchange'); assert.equal(cursor.parentElement,fullscreen);
        env.fire(env.doc,'pointermove',{clientX:2,clientY:3}); env.flush();
        env.doc.hidden = true; env.fire(env.doc,'visibilitychange');
        assert.equal(env.body.dataset.cursorCharacter,undefined);
        assert.equal(env.frames.size,0);
        env.doc.hidden = false; env.fire(env.doc,'visibilitychange');
        env.fire(env.win,'pagehide',{persisted:true});
        env.fire(env.doc,'pointermove',{clientX:3,clientY:4}); assert.equal(env.frames.size,0);
        env.fire(env.win,'pageshow',{persisted:true});
        env.fire(env.doc,'pointermove',{clientX:4,clientY:5}); env.flush();
        assert.equal(env.body.dataset.cursorCharacter,'cwo');
        assert.equal(env.images.length,1);
        env.fire(env.win,'pagehide',{persisted:false});
        assert.equal(cursor.parentElement,null);
        assert.equal(env.observers[0].active,false);
    } finally { env.restore(); }
});

test('coarse pointer requests no assets; failed default asset leaves native pointer available', () => {
    let env = environment({fine:false});
    try { assert.equal(initHomepageCursor(),undefined); assert.equal(env.images.length,0); } finally { env.restore(); }
    env = environment();
    try {
        const destroy = initHomepageCursor(); env.images[0].onerror();
        env.fire(env.doc,'pointermove',{clientX:3,clientY:4}); env.flush();
        assert.equal(env.body.dataset.cursorCharacter,undefined);
        destroy();
    } finally { env.restore(); }
});
