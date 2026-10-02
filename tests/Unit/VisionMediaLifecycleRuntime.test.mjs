import test from 'node:test';
import assert from 'node:assert/strict';
import { prepareVisionPreview } from '../../resources/js/pages/welcome/video-readiness.js';

function fixture() {
    const original = globalThis.document;
    globalThis.document = Object.assign(new EventTarget(), { hidden: false });
    const media = Object.assign(new EventTarget(), {
        dataset: { visionVideoSrc: 'https://example.test/vision.mp4' },
        readyState: 3, videoWidth: 1920, duration: 10, currentTime: 0,
        buffered: { length: 1, start: () => 0, end: () => 2 },
        classList: { add() {}, remove() {} },
        closest: () => ({ querySelector: () => ({ decode: () => Promise.resolve() }) }),
        loads: 0, plays: 0, paused: true,
        load() { this.loads++; }, pause() { this.paused = true; }, removeAttribute() {},
        play() { this.plays++; this.paused = false; return Promise.resolve(); },
    });
    return { media, restore() { globalThis.document = original; } };
}

test('hidden Vision preparation waits for visibility then warms and pauses without consuming its buffer', async () => {
    const f = fixture();
    try {
        document.hidden = true; let ready = false;
        const work = prepareVisionPreview(f.media).then(state => { ready = true; return state; });
        await new Promise(setImmediate);
        assert.equal(ready, false); assert.equal(f.media.loads, 0);
        document.hidden = false; document.dispatchEvent(new Event('visibilitychange'));
        assert.equal(await work, 'frame-ready'); assert.equal(f.media.paused, true);
        const plays = f.media.plays;
        document.dispatchEvent(new Event('visibilitychange'));
        assert.equal(f.media.plays, plays, 'settled preparation removes visibility listener');
    } finally { f.restore(); }
});

test('a reduced-motion change cancels pending media and later motion restores a fresh preparation', async () => {
    const f = fixture();
    try {
        f.media.readyState = 1;
        const pending = prepareVisionPreview(f.media);
        await new Promise(setImmediate);
        const reduced = prepareVisionPreview(f.media, { staticOnly: true });
        assert.equal(await pending, 'semantic-fallback');
        assert.equal(await reduced, 'poster-ready'); assert.equal(f.media.paused, true);
        f.media.readyState = 3;
        assert.equal(await prepareVisionPreview(f.media), 'frame-ready');
        assert.equal(f.media.plays, 2);
    } finally { f.restore(); }
});

test('cancelled preparation can be retried without reusing an aborted promise', async () => {
    const f = fixture();
    try {
        f.media.readyState = 1;
        const controller = new AbortController();
        const pending = prepareVisionPreview(f.media, { signal: controller.signal });
        await new Promise(setImmediate); controller.abort();
        assert.equal(await pending, 'semantic-fallback');
        f.media.readyState = 3;
        assert.equal(await prepareVisionPreview(f.media), 'frame-ready');
        assert.equal(f.media.paused, true);
    } finally { f.restore(); }
});
