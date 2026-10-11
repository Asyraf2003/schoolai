import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt, galleryState } from './GalleryBrowserSupport.mjs';

for (const engine of engines) {
    test(`Gallery media policy, failures, preferences and lifecycle in ${engine}`, { skip: !enabled }, async () => {
        const r = await session(engine);
        const p = r.page;
        try {
            await galleryReady(p);
            await galleryAt(p, .05);
            await p.waitForFunction(() => [...document.querySelectorAll('[data-gallery-video]')].some(v => !v.paused && v.currentTime > .1));
            const live = await p.evaluate(galleryState);
            assert.equal(live.videos.filter(v => v.playing).length, 1);
            await p.locator('[data-gallery-pause]').click();
            assert.equal(await p.locator('[data-gallery-video][src]').count(), 0);
            await p.locator('[data-gallery-pause]').click();
            await galleryAt(p, .55);
            assert.ok((await p.evaluate(galleryState)).videos.filter(v => v.playing).length <= 2);
            await p.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true })));
            assert.equal(await p.locator('[data-gallery-video][src]').count(), 0);
            await p.evaluate(() => dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
            await galleryAt(p, .05);
            await p.locator('[data-gallery-video]').first().evaluate(v => v.dispatchEvent(new Event('error')));
            assert.equal(await p.locator('[data-gallery-video]').first().getAttribute('data-ready'), null);
            assert.ok(await p.locator('[data-gallery-poster]').first().evaluate(i => i.complete && i.naturalWidth > 0));
            await p.emulateMedia({ reducedMotion: 'reduce' });
            await p.waitForFunction(() => !document.querySelector('[data-gallery]').hasAttribute('data-gallery-enabled'));
            assert.equal(await p.locator('[data-gallery-video][src]').count(), 0);
            assert.equal(await p.locator('[data-gallery-item]').count(), 10);
            await p.emulateMedia({ reducedMotion: 'no-preference' });
            await galleryReady(p);
            await galleryAt(p, .46);
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
            for (const width of [390, 768, 1440]) {
                await r.page.setViewportSize({ width, height: 900 });
                assert.equal(await r.page.locator('[data-gallery-item]').count(), 10);
                assert.equal(await r.page.locator('[data-gallery-video][src]').count(), 0);
                assert.ok(await r.page.locator('[data-testimonials] blockquote').count() > 0);
                assert.equal(await r.page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
                assert.equal(await r.page.locator('[data-gallery-pause]').isVisible(), false);
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
            assert.equal(await r.page.locator('[data-gallery-item]').count(), 10);
        } finally { await r.close(); }
    });
}
