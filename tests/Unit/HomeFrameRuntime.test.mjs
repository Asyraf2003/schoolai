import test from 'node:test';
import assert from 'node:assert/strict';
import { subscribeHomepageFrame } from '../../resources/js/pages/welcome/scroll-frame.js';
import { mountProgramFormation } from '../../resources/js/surfaces/home/program-journey/formation.js';
import { readGalleryItems } from '../../resources/js/pages/welcome/gallery-story-frame.js';

function environment() {
    const saved = { window: globalThis.window, document: globalThis.document };
    const frames = new Map(); let nextId = 0; let time = 0;
    const doc = Object.assign(new EventTarget(), { hidden: false, querySelector: () => null });
    const reduced = Object.assign(new EventTarget(), { matches: false });
    const win = Object.assign(new EventTarget(), { scrollY: 0, innerWidth: 1440, innerHeight: 900,
        requestAnimationFrame(callback) { frames.set(++nextId, callback); return nextId; },
        cancelAnimationFrame(id) { frames.delete(id); }, matchMedia: () => reduced });
    Object.assign(globalThis, { window: win, document: doc });
    function tick(delta = 16.67) { time += delta; const pending = [...frames.values()]; frames.clear(); pending.forEach(callback => callback(time)); }
    function fire(target, type, props = {}) { const event = new Event(type); Object.assign(event, props); target.dispatchEvent(event); }
    return { win, doc, reduced, frames, tick, fire, restore: () => Object.assign(globalThis, saved) };
}

test('twenty scroll events queue one homepage frame, all reads precede writes, and viewport state is shared', () => {
    const e = environment();
    try {
        const order = [], snapshots = [];
        const first = subscribeHomepageFrame(viewport => { order.push('read:first'); snapshots.push(viewport); return 1; }, value => order.push('write:first:'+value));
        const second = subscribeHomepageFrame(viewport => { order.push('read:second'); snapshots.push(viewport); return 2; }, value => order.push('write:second:'+value));
        for (let i = 0; i < 20; i++) e.fire(e.win, 'scroll');
        assert.equal(e.frames.size, 1); assert.deepEqual(order, []); e.tick();
        assert.deepEqual(order, ['read:first','read:second','write:first:1','write:second:2']);
        assert.equal(snapshots[0], snapshots[1]); assert.equal(Object.isFrozen(snapshots[0]), true); assert.equal(e.frames.size, 0);
        first.remove(); second.remove(); e.fire(e.win,'scroll'); assert.equal(e.frames.size,0);
    } finally { e.restore(); }
});
test('reentrant work and unsettled animation schedule one next frame without executing new reads inside writes', () => {
    const e = environment();
    try {
        const order = []; let again = true; let second;
        const first = subscribeHomepageFrame(() => { order.push('read:first'); }, () => { order.push('write:first'); second.request(); const result = again; again = false; return result; });
        second = subscribeHomepageFrame(() => { order.push('read:second'); }, () => order.push('write:second'));
        e.tick(); assert.equal(e.frames.size, 1); assert.deepEqual(order, ['read:first','read:second','write:first','write:second']);
        e.tick(); assert.equal(e.frames.size, 1); first.remove(); e.tick(); assert.equal(e.frames.size,0); second.remove();
    } finally { e.restore(); }
});
test('hidden and persisted pagehide cancel work, pageshow resumes existing clients, permanent exit disposes', () => {
    const e = environment();
    try {
        let reads = 0; const client = subscribeHomepageFrame(() => { reads++; }, () => {});
        e.doc.hidden=true;e.fire(e.doc,'visibilitychange');assert.equal(e.frames.size,0);client.request();assert.equal(e.frames.size,0);
        e.doc.hidden=false;e.fire(e.doc,'visibilitychange');e.tick();assert.equal(reads,1);
        client.request();e.fire(e.win,'pagehide',{persisted:true});assert.equal(e.frames.size,0);
        e.fire(e.win,'scroll');assert.equal(e.frames.size,0);e.fire(e.win,'pageshow',{persisted:true});e.tick();assert.equal(reads,2);
        e.fire(e.win,'pagehide',{persisted:false});client.request();e.fire(e.win,'pageshow');assert.equal(e.frames.size,0);client.remove();
    } finally { e.restore(); }
});
test('Formation samples eight cards once per burst in read phase, retains 105ms settle, and reaches the same visible endpoint', () => {
    const e = environment();
    try {
        let reads=0; let top=1000;
        const cards=Array.from({length:8},()=>({ getBoundingClientRect:()=>{ reads++;return {top};} }));
        const triggers=Array.from({length:8},()=>({style:{removeProperty(){}}}));
        const root={classList:{add(){},remove(){}},querySelectorAll:selector=>selector.includes('card]')?cards:triggers};
        const cleanup=mountProgramFormation(root);e.tick();reads=0;
        top=0;for(let i=0;i<20;i++)e.fire(e.win,'scroll');assert.equal(reads,0);assert.equal(e.frames.size,1);e.tick();assert.equal(reads,8);
        assert.equal(triggers[0].style.opacity,(1-Math.exp(-16.67/105)).toFixed(4));
        for(let i=0;i<100&&e.frames.size;i++)e.tick();assert.equal(reads,8);assert.equal(e.frames.size,0);assert.equal(triggers[0].style.opacity,'1.0000');assert.equal(triggers[0].style.transform,'translate3d(0, 0.00px, 0)');
        top=1000;e.fire(e.win,'scroll');for(let i=0;i<100&&e.frames.size;i++)e.tick();assert.equal(triggers[0].style.opacity,'0.0000');
        cleanup();e.fire(e.win,'scroll');assert.equal(e.frames.size,0);
    } finally { e.restore(); }
});
test('Gallery snapshot reads every media before any paint and preserves mask geometry and nearest color', () => {
    const e = environment();
    try {
        const order=[];
        const items=[200,650].map((top,index)=>{
            const visual={offsetHeight:200,style:{setProperty(){throw new Error('write during read');}}};
            const media={getBoundingClientRect(){order.push(index);return {top,height:200};},querySelector:()=>visual};
            return {dataset:{galleryBackground:index?'sand':'green'},querySelector:()=>media};
        });
        const snapshot=readGalleryItems(items);assert.deepEqual(order,[0,1]);assert.equal(snapshot.nearestBackground,'green');
        assert.equal(snapshot.frames[0].mediaY,0);assert.equal(snapshot.frames[0].topInset,0);assert.ok(snapshot.frames[0].bottomInset>0);
        assert.ok(snapshot.frames[1].topInset>0);assert.equal(snapshot.frames[1].bottomInset,0);
    } finally { e.restore(); }
});

test('Gallery keeps its semantic fallback until the first shared paint actually completes', async () => {
    const e = environment();
    try {
        const classes = new Set(), properties = new Map();
        const section = { dataset: {}, closest: () => null, querySelectorAll: () => [],
            classList: { add: name => classes.add(name), remove: name => classes.delete(name), toggle: (name, value) => value ? classes.add(name) : classes.delete(name) },
            style: { setProperty: (key, value) => properties.set(key, value) }, getBoundingClientRect: () => ({ top: 1000 }) };
        const root = { ...section, closest: () => section };
        e.doc.querySelector = selector => selector === '[data-gallery-story]' ? root : null;
        const { prepareHomepageDepthGallery } = await import('../../resources/js/pages/welcome-depth-gallery.js?first-paint-proof');
        const ready = prepareHomepageDepthGallery(); let settled = false; ready.then(() => { settled = true; });
        await Promise.resolve(); assert.equal(settled, false); assert.equal(classes.has('is-gallery-enhanced'), false);
        e.tick(); assert.equal((await ready).state, 'prepared'); assert.equal(classes.has('is-gallery-enhanced'), true);
        e.fire(e.win, 'pagehide', { persisted: false });
    } finally { e.restore(); }
});
test('Formation resumes its 105ms clock without counting hidden time and reduced mode paints static controls', () => {
    const e = environment();
    try {
        let top = 1000;
        const cards = Array.from({length:8}, () => ({getBoundingClientRect: () => ({top})}));
        const triggers = Array.from({length:8}, () => ({style: {removeProperty(key) { delete this[key === 'pointer-events' ? 'pointerEvents' : key]; }}}));
        const root = {classList: {add(){},remove(){}},querySelectorAll: selector => selector.includes('card]') ? cards : triggers};
        const cleanup = mountProgramFormation(root); e.tick(); top = 0; e.fire(e.win,'scroll'); e.tick();
        const before = Number(triggers[0].style.opacity); e.doc.hidden = true; e.fire(e.doc,'visibilitychange');
        e.doc.hidden = false; e.fire(e.doc,'visibilitychange'); e.tick(5000);
        assert.ok(Math.abs(Number(triggers[0].style.opacity) - (before + (1-before)*(1-Math.exp(-16.67/105)))) < 0.0001);
        e.reduced.matches = true; e.fire(e.reduced,'change'); e.tick(); assert.equal(triggers[0].style.opacity, undefined); assert.equal(e.frames.size,0);
        e.reduced.matches = false; e.fire(e.reduced,'change'); e.tick(); assert.ok(Number(triggers[0].style.opacity)>0); cleanup();
    } finally { e.restore(); }
});
