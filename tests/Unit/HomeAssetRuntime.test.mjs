import test from 'node:test';
import assert from 'node:assert/strict';
import { decodeHomepageImage } from '../../resources/js/pages/welcome/homepage-assets.js';
import { preparePreview, releasePreviewFrames } from '../../resources/js/pages/welcome/preview-readiness.js';
import { prepareHeroCarouselMedia } from '../../resources/js/pages/welcome/hero-media-preparation.js';
import { scheduleHomepagePreparation } from '../../resources/js/pages/welcome/preparation.js';

function deferred() { let resolve, reject; const promise = new Promise((yes, no) => { resolve = yes; reject = no; }); return { promise, resolve, reject }; }
function video() {
    const element = Object.assign(new EventTarget(), {
        readyState: 0, videoWidth: 1920, currentTime: 0, duration: 10,
        buffered: { length: 1, start: () => 0, end: () => 2 },
        classList: { add() {} }, loads: 0, paused: true,
        load() { this.loads++; }, pause() { this.paused = true; }, play() { this.paused = false; return Promise.resolve(); },
    });
    return element;
}

test('required lazy images wait for decode and reject missing pixels without reporting readiness', async () => {
    const decode = deferred();
    const image = {
        dataset: { lazySrc: 'school.webp' }, complete: false, naturalWidth: 0,
        decode: () => decode.promise, classList: { add() {} }, removeAttribute() {},
    };
    let ready = false;
    const work = decodeHomepageImage(image).then(() => { ready = true; });
    await Promise.resolve();
    assert.equal(image.src, 'school.webp'); assert.equal(image.loading, 'eager');
    assert.equal(ready, false); assert.equal(image.dataset.homeImageReady, undefined);
    image.complete = true; image.naturalWidth = 1200; decode.resolve(); await work;
    assert.equal(ready, true); assert.equal(image.dataset.homeImageReady, 'true');
    image.naturalWidth = 0;
    await assert.rejects(decodeHomepageImage(image), /unavailable/);
});

test('a first video frame alone does not satisfy future playback readiness', async () => {
    const element = video(); let ready = false;
    const work = preparePreview(element, { source: 'school.mp4' }).then(state => { ready = true; return state; });
    element.readyState = 2; element.dispatchEvent(new Event('loadeddata')); await Promise.resolve();
    assert.equal(ready, false);
    element.readyState = 3; element.buffered.end = () => .3;
    element.dispatchEvent(new Event('canplay')); await Promise.resolve(); assert.equal(ready, false);
    element.buffered.end = () => 2; element.dispatchEvent(new Event('progress'));
    assert.equal(await work, 'frame-ready'); assert.equal(element.paused, true);
    assert.equal(await preparePreview(element, { source: 'school.mp4' }), 'frame-ready');
    assert.equal(element.loads, 1);
});

test('reduced media permanently selects decoded poster without a later source hydration', async () => {
    const element = video(); const poster = { src: 'poster.webp', naturalWidth: 1920, decode: () => Promise.resolve() };
    assert.equal(await preparePreview(element, { source: 'school.mp4', poster, staticOnly: true }), 'poster-ready');
    assert.equal(await preparePreview(element, { source: 'school.mp4', poster }), 'poster-ready');
    assert.equal(element.loads, 0); assert.equal(element.poster, 'poster.webp');
});

test('media with missing poster and unusable playback rejects instead of counting a blank fallback', async () => {
    const element = video();
    const work = preparePreview(element, { source: 'school.mp4' });
    element.dispatchEvent(new Event('error'));
    await assert.rejects(work, /no usable fallback/);
    assert.equal(element.paused, true);
});

test('aborting during poster decode settles cancellation and cannot hydrate late', async () => {
    const decode = deferred(), signal = new AbortController(), element = video();
    const work = preparePreview(element, { source: 'school.mp4', poster: { src: 'poster.webp', naturalWidth: 1920, decode: () => decode.promise }, signal: signal.signal });
    signal.abort(); await assert.rejects(work, { name: 'AbortError' });
    decode.resolve(); await Promise.resolve(); await Promise.resolve();
    assert.equal(element.loads, 0); assert.equal(element.paused, true);
});

test('carousel readiness includes the inactive video and preserves its non-looping playback', async () => {
    const previous = { document: globalThis.document, window: globalThis.window, Image: globalThis.Image };
    const active = video(), inactive = video(); active.readyState = 4;
    for (const item of [active, inactive]) {
        item.dataset = {}; item.poster = 'poster.webp'; item.loop = false;
        item.querySelector = () => ({ getAttribute: () => 'slide.mp4' });
        item.closest = () => ({ classList: { toggle() {} } });
    }
    globalThis.document = { querySelector: () => ({ dataset: { heroMode: 'carousel' }, querySelectorAll: () => [active, inactive] }) };
    globalThis.window = { matchMedia: () => ({ matches: false }) };
    globalThis.Image = class { naturalWidth = 1920; decode() { return Promise.resolve(); } };
    try {
        let ready = false;
        const work = prepareHeroCarouselMedia().then(() => { ready = true; });
        await new Promise(setImmediate); assert.equal(ready, false);
        assert.equal(active.dataset.heroVideoState, 'frame-ready'); assert.equal(inactive.loads, 1);
        inactive.readyState = 3; inactive.dispatchEvent(new Event('canplay')); await work;
        assert.equal(inactive.dataset.heroVideoState, 'frame-ready'); assert.equal(inactive.dataset.hydrated, 'true');
        assert.equal(inactive.loop, false); assert.equal(inactive.paused, true);
    } finally { Object.assign(globalThis, previous); }
});

test('shared public bundle never adopts a homepage gate on PPDB or another non-home route', () => {
    const previous = globalThis.document;
    globalThis.document = { querySelector: () => null };
    try { assert.doesNotThrow(scheduleHomepagePreparation); }
    finally { globalThis.document = previous; }
});

test('buffered video waits for actual compositor delivery before preparation pauses it', async () => {
    const element=video();let frame,ready=false;
    element.requestVideoFrameCallback=callback=>{frame=callback;return 1;};
    element.cancelVideoFrameCallback=()=>{};
    const work=preparePreview(element,{source:'school.mp4'}).then(state=>{ready=true;return state;});
    element.readyState=3;element.dispatchEvent(new Event('canplay'));await Promise.resolve();
    assert.equal(ready,false);assert.equal(element.paused,false);
    frame();assert.equal(await work,'frame-ready');assert.equal(element.paused,true);
});

test('offscreen frames are copied only after future playback is buffered and all temporary resources close', async () => {
    const previous = { window: globalThis.window, OffscreenCanvas: globalThis.OffscreenCanvas };
    let canvas, copies = 0, closes = 0, cancellations = 0;
    globalThis.window = { innerHeight: 900, requestAnimationFrame: callback => setImmediate(callback), cancelAnimationFrame: clearImmediate };
    globalThis.OffscreenCanvas = class {
        constructor() { canvas = this; }
        getContext() { return { drawImage: source => { assert.equal(source.readyState, 3); copies++; } }; }
        transferToImageBitmap() { return { width: this.width, height: this.height, close() { closes++; } }; }
    };
    try {
        for (const buffered of [.3, 2]) {
            const element = video(); element.videoHeight = 1080;
            element.requestVideoFrameCallback = () => 1;
            element.cancelVideoFrameCallback = () => { cancellations++; };
            element.getBoundingClientRect = () => ({ top: 1000, bottom: 2000 });
            const work = preparePreview(element, { source: 'school.mp4' });
            element.readyState = 3; element.buffered.end = () => buffered;
            element.dispatchEvent(new Event('canplay'));
            if (buffered < 1) {
                assert.equal(copies, 0);
                element.buffered.end = () => 2; element.dispatchEvent(new Event('progress'));
            }
            assert.equal(await work, 'frame-ready'); assert.equal(element.paused, true);
        }
        assert.equal(copies, 2); assert.equal(closes, 2); assert.equal(cancellations, 2);
        releasePreviewFrames(); assert.equal(canvas.width, 1); assert.equal(canvas.height, 1);
    } finally { releasePreviewFrames(); Object.assign(globalThis, previous); }
});

test('a failed decoded frame copy rejects readiness and cancels pending callback', async () => {
    const previous = { window: globalThis.window, OffscreenCanvas: globalThis.OffscreenCanvas };
    globalThis.window = { innerHeight: 900 };
    globalThis.OffscreenCanvas = class { getContext() { return { drawImage() { throw new Error('decode copy failed'); } }; } };
    try {
        const element = video(); let cancelled = false;
        element.requestVideoFrameCallback = () => 1; element.cancelVideoFrameCallback = () => { cancelled = true; };
        element.getBoundingClientRect = () => ({ top: 1000, bottom: 2000 });
        const work = preparePreview(element, { source: 'school.mp4' });
        element.readyState = 3; element.dispatchEvent(new Event('canplay'));
        await assert.rejects(work, /decode copy failed/); assert.equal(cancelled, true); assert.equal(element.paused, true);
    } finally { releasePreviewFrames(); Object.assign(globalThis, previous); }
});

test('already buffered media awaits actual playback startup and reuses its hydrated source', async () => {
    const element = video(), playback = deferred(); let ready = false;
    element.dataset = {}; element.readyState = 3; element.currentSrc = 'school.mp4'; element.play = () => playback.promise;
    const work = preparePreview(element, { source: 'school.mp4' }).then(state => { ready = true; return state; });
    await Promise.resolve(); assert.equal(ready, false); assert.equal(element.loads, 0);
    playback.resolve(); assert.equal(await work, 'frame-ready'); assert.equal(element.dataset.homePlaybackWarmed, 'true');
});

test('autoplay rejection permanently selects decoded poster even when future data exists', async () => {
    const element = video(), playback = deferred(); element.readyState = 3;
    element.removeAttribute = () => {}; element.play = () => playback.promise;
    const poster = { src: 'poster.webp', naturalWidth: 1920, decode: () => Promise.resolve() };
    const work = preparePreview(element, { source: 'school.mp4', poster });
    await new Promise(setImmediate); playback.reject(new DOMException('Autoplay denied', 'NotAllowedError'));
    assert.equal(await work, 'poster-ready'); assert.equal(element.paused, true);
    assert.equal(await preparePreview(element, { source: 'school.mp4' }), 'poster-ready');
});
