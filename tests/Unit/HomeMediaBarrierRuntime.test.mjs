import test from 'node:test';
import assert from 'node:assert/strict';
import { preparePreview } from '../../resources/js/pages/welcome/preview-readiness.js';
import { finalizeHomepageMedia } from '../../resources/js/pages/welcome/homepage-media-finalization.js';

function environment() {
    const previous = { window: globalThis.window, document: globalThis.document };
    const previews = Array.from({ length: 2 }, () => Object.assign(new EventTarget(), {
        dataset: {}, readyState: 3, videoWidth: 1920, duration: 10, currentTime: 0, currentSrc: 'school.mp4',
        buffered: { length: 1, start: () => 0, end: () => 3 }, classList: { add() {} },
        paused: true, pause() { this.paused = true; }, play() { this.paused = false; return Promise.resolve(); },
        load() { throw new Error('Final barrier must reuse source'); },
        hasAttribute: selector => selector === 'data-vision-video-preview', matches: () => false,
    }));
    const window = new EventTarget();
    Object.assign(globalThis, { window, document: { querySelectorAll: () => previews } });
    return { previews, window, restore() { Object.assign(globalThis, previous); } };
}

test('final barrier holds every preview until all actual playback checks complete, then pauses them together', async () => {
    const e = environment();
    try {
        await Promise.all(e.previews.map(video => preparePreview(video, { source: 'school.mp4' })));
        e.previews[0].muted = false;
        e.previews[1].readyState = 2;
        let ready = false;
        const work = finalizeHomepageMedia().then(() => { ready = true; });
        await new Promise(setImmediate);
        assert.equal(ready, false); assert.deepEqual(e.previews.map(video => video.paused), [false, false]);
        assert.ok(e.previews.every(video => video.dataset.homeMediaPreparing === 'true'));
        e.previews[1].readyState = 3; e.previews[1].dispatchEvent(new Event('canplay')); await work;
        assert.deepEqual(e.previews.map(video => video.paused), [true, true]);
        assert.ok(e.previews.every(video => video.dataset.homeMediaPreparing === undefined));
        assert.ok(e.previews.every(video => video.dataset.visionVideoState === 'frame-ready'));
        assert.equal(e.previews[0].muted, false);
    } finally { e.restore(); }
});

test('page disposal aborts final media checks and releases their playback ownership', async () => {
    const e = environment();
    try {
        await Promise.all(e.previews.map(video => preparePreview(video, { source: 'school.mp4' })));
        e.previews.forEach(video => { video.readyState = 2; });
        const work = finalizeHomepageMedia(); await new Promise(setImmediate);
        const exit = new Event('pagehide'); Object.defineProperty(exit, 'persisted', { value: false }); e.window.dispatchEvent(exit);
        await assert.rejects(work, { name: 'AbortError' });
        assert.ok(e.previews.every(video => video.paused && video.dataset.homeMediaPreparing === undefined));
    } finally { e.restore(); }
});
