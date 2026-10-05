import test from 'node:test';
import assert from 'node:assert/strict';
import { createHeroMedia } from '../../resources/js/sections/hero-media.js';

test('Hero focus and overlay renders never replay or pause unchanged media policy', () => {
    const source = Object.assign(new EventTarget(), { dataset: { src: '/opening.mp4' } });
    let loads = 0;
    let plays = 0;
    let pauses = 0;
    const video = Object.assign(new EventTarget(), {
        dataset: {}, paused: true, readyState: 4,
        querySelectorAll: () => [source],
        load() { loads++; },
        play() { plays++; this.paused = false; return Promise.resolve(); },
        pause() { pauses++; this.paused = true; },
    });
    let mutedWrites = 0;
    let loopWrites = 0;
    let muted = true;
    let loop = false;
    Object.defineProperties(video, {
        muted: { get: () => muted, set: value => { muted = value; mutedWrites++; } },
        loop: { get: () => loop, set: value => { loop = value; loopWrites++; } },
    });
    const slide = { dataset: {}, querySelector: () => video };
    const media = createHeroMedia([slide], { onEnded() {}, onAudioBlocked() {}, onFailure() {} });
    const policy = { index: 0, audio: false, canPlay: true };

    media.sync(policy);
    for (let index = 0; index < 10; index++) media.sync({ ...policy, focused: index % 2 === 0 });
    assert.equal(plays, 1);
    assert.equal(pauses, 0);
    media.sync({ ...policy, canPlay: false });
    assert.equal(pauses, 1);
    media.sync(policy);
    assert.equal(plays, 2);
    video.paused = true;
    media.sync(policy);
    assert.equal(plays, 3);
    assert.equal(loads, 1);
    assert.equal(source.src, '/opening.mp4');
    assert.equal(mutedWrites, 0);
    assert.equal(loopWrites, 1);
    media.dispose();
});
