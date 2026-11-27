import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt, galleryState, galleryOpening } from './GalleryBrowserSupport.mjs';

for (const engine of engines) {
    test(`Gallery media policy, failures, preferences and lifecycle in ${engine}`, { skip: !enabled }, async () => {
        const r = await session(engine);
        const p = r.page;
        try {
            await galleryReady(p);
            await galleryOpening(p);
            await p.waitForFunction(() => [...document.querySelectorAll('[data-gallery-video]')].some(v => !v.paused && v.currentTime > .1));
            const live = await p.evaluate(galleryState);
            assert.ok(live.videos.filter(v => v.playing).length <= 2);
            assert.equal(await p.locator('[data-gallery] button').count(), 0);
            assert.ok(await p.locator('[data-gallery-video]').evaluateAll(videos => videos.every(v => v.loop)));
            for (const progress of [.52, .64, .76]) {
                await galleryAt(p, progress);
                assert.ok(await p.locator('[data-gallery-video][src]').count() <= 2);
            }
            assert.ok((await p.evaluate(galleryState)).videos.filter(v => v.playing).length <= 2);
            await p.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true })));
            assert.equal(await p.locator('[data-gallery-video][src]').count(), 0);
            await p.evaluate(() => dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
            await galleryAt(p, 1);
            await p.waitForFunction(() => !document.querySelector('[data-gallery-video][src]'));
            await p.route('**/gallery/media/it-v1.mp4', route => route.abort());
            await galleryOpening(p);
            await p.waitForFunction(() => document.querySelector('[data-gallery-video]').dataset.failed);
            assert.equal(await p.locator('[data-gallery-video]').first().getAttribute('src'), null);
            assert.equal(await p.locator('[data-gallery-video]').first().getAttribute('data-ready'), null);
            assert.ok(await p.locator('[data-gallery-poster]').first().evaluate(i => i.complete && i.naturalWidth > 0));
            await p.emulateMedia({ reducedMotion: 'reduce' });
            await p.waitForFunction(() => !document.querySelector('[data-gallery]').hasAttribute('data-gallery-enabled'));
            assert.equal(await p.locator('[data-gallery-video][src]').count(), 0);
            assert.equal(await p.locator('[data-gallery-item]').count(), 34);
            await p.emulateMedia({ reducedMotion: 'no-preference' });
            await galleryReady(p);
            await galleryAt(p, .52);
            await p.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: false })));
            assert.equal((await p.evaluate(galleryState)).triggers, 0);
            assert.equal(await p.locator('[data-gallery-video][src]').count(), 0);
            assert.deepEqual(r.errors, []);
            await evidence(`gallery-lifecycle-${engine}`, { live, mediaFailure: 'poster retained', bfcache: 'synthetic events', dispose: 'PASS' });
        } finally { await r.close(); }
    });
    test(`Gallery static content and sample testimonial disclosure without JavaScript in ${engine}`, { skip: !enabled }, async () => {
        const r = await session(engine, { javaScriptEnabled: false });
        try {
            await r.page.waitForFunction(() => getComputedStyle(document.querySelector('[data-gallery]')).backgroundColor === 'rgb(255, 255, 255)');
            await r.page.evaluate(() => document.fonts.ready);
            for (const width of [390, 768, 1440]) {
                await r.page.setViewportSize({ width, height: 900 });
                assert.equal(await r.page.locator('[data-gallery-item]').count(), 34);
                assert.equal(await r.page.locator('[data-gallery-video][src]').count(), 0);
                assert.ok(await r.page.locator('[data-testimonials] blockquote').count() > 0);
                assert.equal(await r.page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false, `static overflow at ${width}`);
                assert.equal(await r.page.locator('[data-gallery] button').count(), 0);
            }
        } finally { await r.close(); }
    });
    test(`Gallery fails open when GSAP cannot load in ${engine}`, { skip: !enabled }, async () => {
        const r = await session(engine, { reducedMotion: 'reduce' });
        try {
            await r.page.route('**/gsap@*/**', route => route.abort());
            await locale(r.page, 'ar');
            await r.page.emulateMedia({ reducedMotion: 'no-preference' });
            await r.page.locator('[data-gallery]').scrollIntoViewIfNeeded();
            await r.page.waitForTimeout(600);
            assert.equal(await r.page.locator('[data-gallery]').getAttribute('data-gallery-enabled'), null);
            assert.equal(await r.page.locator('[data-gallery-item]').count(), 34);
        } finally { await r.close(); }
    });
}
