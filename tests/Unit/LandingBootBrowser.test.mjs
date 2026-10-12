import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';

const geometry = () => {
    const header = document.querySelector('[data-header]'), hero = document.querySelector('[data-hero]');
    return { pending: document.documentElement.hasAttribute('data-landing-pending'),
        enhanced: header.hasAttribute('data-enhanced'), header: header.getBoundingClientRect().toJSON(),
        hero: hero.getBoundingClientRect().toJSON(), menu: getComputedStyle(header.querySelector('nav')).display,
        overflow: document.documentElement.scrollWidth > innerWidth };
};
for (const engine of engines) test(`Landing first-paint geometry and failed bundle fallback in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine, { reducedMotion: 'reduce' });
    const cells = [];
    try {
        for (const language of ['en', 'id', 'ar']) {
            await locale(runtime.page, language);
            for (const width of [390, 768, 1440]) {
                await runtime.page.setViewportSize({ width, height: 900 });
                let release;
                const hold = new Promise(resolve => { release = resolve; });
                await runtime.page.route('**/build/assets/index-*.js', async route => { await hold; await route.continue(); });
                const reload = runtime.page.reload({ waitUntil: 'domcontentloaded' });
                await runtime.page.waitForFunction(() => document.querySelector('[data-header]') && document.documentElement.hasAttribute('data-landing-pending'));
                await runtime.page.evaluate(() => document.fonts.ready);
                const pending = await runtime.page.evaluate(geometry);
                assert.equal(pending.enhanced, false);
                assert.equal(pending.header.y, 0); assert.equal(pending.hero.y, 0);
                if (width < 1024) assert.equal(pending.menu, 'none');
                release(); await reload;
                await runtime.page.waitForFunction(() => document.querySelector('[data-header]').hasAttribute('data-enhanced'));
                const ready = await runtime.page.evaluate(geometry);
                assert.equal(ready.pending, false); assert.equal(ready.overflow, false);
                assert.equal(ready.hero.y, pending.hero.y, 'Hero never shifts when Header mounts');
                await runtime.page.unroute('**/build/assets/index-*.js');
                cells.push({ language, width, pending, ready });
            }
        }
        assert.deepEqual(runtime.errors, []);
        await runtime.page.route('**/build/assets/index-*.js', route => route.abort());
        await runtime.page.reload({ waitUntil: 'domcontentloaded' });
        const failed = await runtime.page.evaluate(geometry);
        assert.equal(failed.pending, false); assert.equal(failed.enhanced, false);
        assert.notEqual(failed.menu, 'none', 'failed JS restores native semantic navigation');
        assert.equal(await runtime.page.locator('[data-stories-demo]').count(), 4);
        await evidence(`landing-boot-${engine}`, { version: runtime.version, cells, failed });
    } finally { await runtime.close(); }
});
