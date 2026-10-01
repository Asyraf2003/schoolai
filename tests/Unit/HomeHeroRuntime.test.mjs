import test from 'node:test';
import assert from 'node:assert/strict';
import { initOpeningHero } from '../../resources/js/pages/welcome-hero/opening.js';
import { armHeroReadySignal } from '../../resources/js/pages/welcome-hero/readiness.js';
import { createSliderMediaActions } from '../../resources/js/pages/welcome-hero/slider-media.js';

function environment({ reduced = false, hidden = false, rejectPlay = false } = {}) {
    const keys = ['window','document','IntersectionObserver'];
    const original = Object.fromEntries(keys.map(key=>[key,globalThis[key]]));
    const frames = new Map(); let sequence = 0; const observers = [];
    class Node extends EventTarget {
        constructor() { super(); this.dataset={}; this.attributes=new Map(); this.classes=new Set(); this.classList={add:c=>this.classes.add(c),remove:c=>this.classes.delete(c)}; }
        setAttribute(k,v) { this.attributes.set(k,v); }
        getAttribute(k) { return this.attributes.get(k); }
        removeAttribute(k) { this.attributes.delete(k); }
    }
    const source = new Node(); source.setAttribute('data-src','https://media.example/opening.mp4');
    const video = new Node(); video.paused=true; video.loads=0; video.plays=0; video.preload='none';
    video.pause=()=>{video.paused=true;}; video.load=()=>{video.loads++;};
    video.play=()=>{video.plays++;video.paused=false;return rejectPlay?Promise.reject(new Error('policy')):Promise.resolve();};
    video.querySelectorAll=()=>source.getAttribute('data-src')?[source]:[];
    const slide = new Node(); slide.querySelector=()=>video; slide.querySelectorAll=()=>[];
    const root = new Node(); const button = new Node();
    const motion = Object.assign(new EventTarget(),{matches:reduced});
    const doc = Object.assign(new EventTarget(),{hidden,documentElement:new Node(),querySelectorAll:()=>[button]});
    const win = Object.assign(new EventTarget(),{matchMedia:()=>motion,
        requestAnimationFrame:cb=>{frames.set(++sequence,cb);return sequence;},cancelAnimationFrame:id=>frames.delete(id),
        IntersectionObserver:true});
    Object.assign(globalThis,{window:win,document:doc,IntersectionObserver:class {
        constructor(callback){this.callback=callback;observers.push(this);} observe(){} disconnect(){this.disconnected=true;}
    }});
    return {video,root,slide,button,motion,doc,win,source,observers,frames,
        flush(){const work=[...frames.values()];frames.clear();work.forEach(cb=>cb());},
        fire(target,type,properties={}) {const event=new Event(type);for(const [key,value] of Object.entries(properties))Object.defineProperty(event,key,{value});target.dispatchEvent(event);},
        restore(){Object.assign(globalThis,original);},
    };
}

test('shell is ready after paint independently of media; no-input warm-up hydrates exactly once', () => {
    const e=environment();
    try {
        armHeroReadySignal(e.root);initOpeningHero(e.root,e.slide);
        assert.equal(e.video.loads,0);e.flush();assert.equal(e.root.dataset.heroReady,undefined);
        e.flush();assert.equal(e.root.dataset.heroShellReady,'true');assert.equal(e.video.loads,1);
        assert.equal(e.video.preload,'metadata');assert.equal(e.source.src,'https://media.example/opening.mp4');
        assert.equal(e.root.dataset.heroMediaState,'preparing');assert.equal(e.video.muted,true);
        e.fire(e.win,'scroll');e.fire(e.root,'pointermove');assert.equal(e.video.loads,1);
        e.fire(e.video,'loadeddata');assert.equal(e.root.dataset.heroMediaState,'frame-ready');
        e.fire(e.video,'playing');assert.equal(e.root.dataset.heroMediaState,'playing');
        e.fire(e.button,'click');assert.equal(e.video.muted,false);
        e.fire(e.video,'error');assert.equal(e.root.dataset.heroMediaState,'media-error');
        assert.equal(e.root.dataset.heroReady,'true');
        e.fire(e.win,'pagehide',{persisted:false});
    } finally {e.restore();}
});

test('reduced motion retains poster until explicit audio intent and rehydrates only once', () => {
    const e=environment({reduced:true});
    try {
        armHeroReadySignal(e.root);initOpeningHero(e.root,e.slide);e.flush();e.flush();
        assert.equal(e.root.dataset.heroReady,'true');assert.equal(e.root.dataset.heroMediaState,'static-reduced');
        assert.equal(e.video.loads,0);assert.equal(e.video.plays,0);
        e.fire(e.button,'click');assert.equal(e.video.loads,1);assert.equal(e.video.muted,false);
        e.fire(e.button,'click');assert.equal(e.video.paused,true);
        e.motion.matches=false;e.fire(e.motion,'change');assert.equal(e.video.loads,1);assert.equal(e.video.paused,false);
        e.fire(e.win,'pagehide',{persisted:false});
    } finally {e.restore();}
});

test('autoplay rejection cannot block shell; hidden/offscreen/BFCache suspend and permanent exit dispose', async () => {
    const e=environment({rejectPlay:true});
    try {
        armHeroReadySignal(e.root);initOpeningHero(e.root,e.slide);e.flush();e.flush();await Promise.resolve();
        assert.equal(e.root.dataset.heroReady,'true');assert.equal(e.root.dataset.heroMediaState,'autoplay-blocked');
        e.observers[0].callback([{isIntersecting:false}]);assert.equal(e.video.paused,true);
        e.observers[0].callback([{isIntersecting:true}]);
        e.doc.hidden=true;e.fire(e.doc,'visibilitychange');assert.equal(e.video.paused,true);
        e.fire(e.win,'pagehide',{persisted:true});const count=e.video.plays;
        e.doc.hidden=false;e.fire(e.doc,'visibilitychange');assert.equal(e.video.plays,count);
        e.fire(e.win,'pageshow',{persisted:true});assert.equal(e.video.plays,count+1);assert.equal(e.video.loads,1);
        e.fire(e.win,'pagehide',{persisted:false});const final=e.video.plays;
        e.fire(e.button,'click');e.fire(e.win,'pageshow');assert.equal(e.video.plays,final);
        assert.equal(e.observers[0].disconnected,true);await Promise.resolve();
    } finally {e.restore();}
});

test('carousel media prepares only active video and respects reduced, hidden, suspended and inactive states', () => {
    const e=environment();
    try {
        const inactive=Object.assign(new EventTarget(),{paused:true,currentTime:0,removeAttribute(){},pause(){this.paused=true;},querySelectorAll(){throw new Error('inactive hydration');}});
        const slides=[e.slide,{querySelector:()=>inactive}];
        const state={currentIndex:0,videoHydrationReady:true,audioEnabled:false,reducedMotion:e.motion};
        const media=createSliderMediaActions({root:e.root,slides,state,statusTemplate:':current of :total'});
        media.syncVideos();assert.equal(e.video.loads,1);assert.equal(inactive.paused,true);
        e.motion.matches=true;media.syncVideos();assert.equal(e.video.paused,true);assert.equal(media.canAutoplay(),false);
        e.motion.matches=false;state.suspended=true;media.syncVideos();assert.equal(e.video.paused,true);
        state.suspended=false;state.inViewport=false;media.syncVideos();assert.equal(media.canAutoplay(),false);
        state.inViewport=true;e.doc.hidden=true;media.syncVideos();assert.equal(e.video.paused,true);
        e.doc.hidden=false;media.syncVideos();assert.equal(e.video.loads,1);assert.equal(e.video.muted,true);
    } finally {e.restore();}
});
