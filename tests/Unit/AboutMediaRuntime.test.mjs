import test from 'node:test';
import assert from 'node:assert/strict';
import { createAboutMedia } from '../../resources/js/sections/about-media.js';
import { selectVisibleStory } from '../../resources/js/sections/about-story.js';

class Video extends EventTarget {
    dataset = {};
    paused = true;
    readyState = 0;
    loads = 0;
    getAttribute() { return this.src ?? null; }
    removeAttribute() { delete this.src; }
    load() { this.loads++; this.readyState = 0; this.dispatchEvent(new Event('emptied')); }
    pause() { this.paused = true; }
    async play() {
        this.paused = false;
        this.readyState = 2;
        queueMicrotask(() => this.dispatchEvent(new Event('playing')));
    }
}
class Poster extends EventTarget {
    complete = false;
    naturalWidth = 0;
    ready() { this.complete = true; this.naturalWidth = 1280; this.dispatchEvent(new Event('load')); }
}
function fixture() {
    const layers = [0, 1].map(() => {
        const video = new Video();
        const image = new Poster();
        const layer = { dataset:{}, video, image, querySelector: selector => selector === 'video' ? video : image };
        video.parentElement = layer;
        return layer;
    });
    const stories = ['about', 'vision', 'mission'].map(key => ({
        dataset:{aboutStory:key, preview:`${key}-preview.mp4`, poster:`${key}-poster.webp`},
        video:new Video(),
        querySelector() { return this.video; },
    }));
    const root = {querySelectorAll: selector => selector === '[data-about-layer]' ? layers : stories.map(s=>s.video)};
    return {layers, stories, media:createAboutMedia(root, stories, new AbortController().signal)};
}

test('About waits for incoming media, pauses outgoing playback and reuses an already hydrated layer on reverse traversal', async () => {
    const {layers, stories, media} = fixture();
    media.update(stories[0], {wide:true, prepare:true, play:true});
    await Promise.resolve();
    const loads = layers[0].video.loads;
    assert.equal(layers[0].dataset.visible, 'true');

    media.update(stories[1], {wide:true, prepare:true, play:true});
    assert.equal(layers[0].video.paused, true);
    assert.equal(layers[0].dataset.visible, 'true', 'old frame persists until incoming readiness');
    await Promise.resolve();
    assert.equal(layers[1].dataset.visible, 'true');
    assert.equal(layers[0].dataset.visible, 'false');
    assert.equal(layers.filter(l=>!l.video.paused).length, 1);

    media.update(stories[0], {wide:true, prepare:true, play:true});
    await Promise.resolve();
    assert.equal(layers[0].video.loads, loads, 'reverse traversal does not reload the same source');
    assert.equal(layers[1].video.paused, true);
});

test('Narrow About prepares without playing, presents posters when paused and releases all media on disposal', async () => {
    const {layers, stories, media} = fixture();
    media.prepareInline(stories[0]);
    media.prepareInline(stories[1]);
    assert.ok(stories.every(s=>s.video.paused));
    assert.equal(stories[2].video.src, undefined, 'distant Mission stays unhydrated');

    media.update(stories[0], {wide:false, prepare:true, play:true});
    await Promise.resolve();
    assert.equal(stories[0].video.dataset.ready, 'true');
    media.update(stories[1], {wide:false, prepare:true, play:true});
    await Promise.resolve();
    assert.equal(stories[0].video.dataset.ready, 'false');
    assert.equal(stories.filter(s=>!s.video.paused).length, 1);
    media.pause();
    assert.ok(stories.every(s=>s.video.paused));
    assert.equal(stories[1].video.dataset.ready, 'false');
    media.dispose();
    assert.ok([...stories.map(s=>s.video),...layers.map(l=>l.video)].every(v=>v.paused && !v.src));
});

test('Rapid About story changes ignore stale readiness and reduced motion does not hydrate previews', async () => {
    const {layers, stories, media} = fixture();
    media.update(stories[0], {wide:true, prepare:false, play:false});
    layers[0].image.ready();
    media.update(stories[1], {wide:true, prepare:false, play:false});
    media.update(stories[2], {wide:true, prepare:false, play:false});
    layers[0].video.dispatchEvent(new Event('loadeddata'));
    assert.equal(layers[0].dataset.visible, 'true');
    assert.equal(layers[1].dataset.visible, 'false');
    layers[1].image.ready();
    assert.equal(layers[1].dataset.visible, 'true');
    assert.equal(layers[1].dataset.story, 'mission');
    assert.ok(layers.every(l=>l.video.paused && !l.video.src));
});

test('About chooses the most visible story and preserves the last story between observed regions', () => {
    const about = {}, vision = {}, mission = {};
    assert.equal(selectVisibleStory([
        {target:about,isIntersecting:false,intersectionRatio:0},
        {target:vision,isIntersecting:true,intersectionRatio:.5},
        {target:mission,isIntersecting:true,intersectionRatio:.1},
    ],about),vision);
    assert.equal(selectVisibleStory([{target:vision,isIntersecting:false,intersectionRatio:0}],mission),mission);
});
