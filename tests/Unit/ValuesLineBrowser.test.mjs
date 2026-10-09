import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const ready = page => page.waitForFunction(() => !!window.ScrollTrigger?.getById('values-line'));
const sized = page => page.waitForFunction(() => {
    const scene = document.querySelector('[data-values-line]');
    const trigger = window.ScrollTrigger?.getById('values-line');
    return scene.querySelector('svg').viewBox.baseVal.width === innerWidth && trigger
        && Math.abs(trigger.start - (scene.getBoundingClientRect().top + scrollY - innerHeight * .85)) < .51;
});
const approach = page => page.evaluate(() => {
    scrollTo(0, document.querySelector('[data-values]').getBoundingClientRect().top + scrollY - innerHeight * 1.1);
});
const position = async (page, fraction) => {
    const target = await page.evaluate(p => {
        const trigger = window.ScrollTrigger.getById('values-line');
        const target = trigger.start + (trigger.end - trigger.start) * p;
        const rounded = p === 0 ? Math.floor(target) : p === 1 ? Math.ceil(target) : Math.round(target);
        scrollTo(0, rounded); return rounded;
    }, fraction);
    await page.waitForFunction(() => {
        const trigger = window.ScrollTrigger.getById('values-line');
        const expected = Math.max(0, Math.min(1, (scrollY - trigger.start) / (trigger.end - trigger.start)));
        return !trigger.enabled || Math.abs(trigger.progress - expected) < .000001;
    });
    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
    assert.ok(Math.abs(await page.evaluate(() => scrollY) - target) <= 1, 'drawing never moves native scroll');
};
const state = () => {
    const root = document.querySelector('[data-values]');
    const scene = root.querySelector('[data-values-line]');
    const path = scene.querySelector('path');
    const trigger = window.ScrollTrigger?.getById('values-line');
    const box = scene.getBoundingClientRect();
    const style = getComputedStyle(path);
    const length = path.getTotalLength();
    const offset = parseFloat(style.strokeDashoffset);
    const point = path.getPointAtLength(Math.max(0, length - offset));
    const start = path.getPointAtLength(0); const end = path.getPointAtLength(length);
    const view = scene.querySelector('svg').viewBox.baseVal;
    return { viewport: [innerWidth, innerHeight], lang: document.documentElement.lang,
        scene: { width: box.width, height: box.height, top: box.top },
        paths: scene.querySelectorAll('path').length, stroke: style.stroke, fill: style.fill,
        offset, length, progress: trigger?.progress, scrub: trigger?.vars.scrub,
        tip: { x: point.x / view.width * box.width, y: box.top + point.y / view.height * box.height },
        caps: [start, end].map(p => ({ x: p.x, y: p.y })),
        overflow: document.documentElement.scrollWidth > innerWidth, ready: root.hasAttribute('data-line-ready'),
        triggers: window.ScrollTrigger?.getAll().length ?? 0,
        gsapScripts: document.querySelectorAll('script[data-program-gsap]').length };
};

for (const engine of engines) {
    test(`Values line draws five full screens and reverses the same path in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            for (const language of ['id', 'en', 'ar']) {
                await locale(page, language); await approach(page); await ready(page);
                for (const width of [360, 640, 768, 1024, 1280, 1536, 1920]) {
                    await page.setViewportSize({ width, height: 900 });
                    await sized(page);
                    await position(page, 0);
                    const initial = await page.evaluate(state);
                    assert.equal(initial.paths, 1, 'no guide/duplicate stroke');
                    assert.equal(initial.stroke, 'rgb(255, 255, 255)'); assert.equal(initial.fill, 'none');
                    assert.equal(initial.scrub, true);
                    assert.ok(initial.offset >= initial.length, `future path completely hidden: ${JSON.stringify(initial)}`);
                    assert.equal(initial.scene.width, width); assert.equal(initial.scene.height, 4500);
                    assert.equal(initial.overflow, false);
                    assert.ok(initial.caps.every(p => p.x > width && p.y > 0 && p.y < initial.scene.height),
                        'both caps are beyond the right wall at every viewport/locale');
                    const forward = [];
                    for (const p of [.1, .2, .3, .4, .5, .6, .7, .8, .9, 1]) {
                        await position(page, p); forward.push(await page.evaluate(state));
                    }
                    assert.ok(forward.every((s, i) => i === 0 || s.offset < forward[i - 1].offset));
                    assert.ok(Math.abs(forward.at(-1).offset) < .01);
                    for (const i of [8, 6, 4, 2, 0]) {
                        await position(page, (i + 1) / 10);
                        const reverse = await page.evaluate(state);
                        assert.ok(Math.abs(reverse.offset - forward[i].offset) < .02, 'up restores exact drawn length');
                    }
                    cases.push({ language, width, initial, forward });
                }
            }
            await page.setViewportSize({ width: 1440, height: 900 });
            await sized(page);
            for (const [name, p] of [['entry', .03], ['one', .16], ['two', .36], ['three', .56], ['four', .76], ['five', .96]]) {
                await position(page, p); await page.screenshot({ path: `${directory}/values-line-${name}.png` });
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-line-drawing-${engine}`, { version: runtime.version, cases });
        } catch (error) {
            await evidence(`values-line-drawing-failure-${engine}`, { message: error.message, cases, state: await page.evaluate(state) });
            throw error;
        } finally { await runtime.close(); }
    });

    test(`Values line restores lifecycle, owns one trigger and reuses Program GSAP in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine); const { page } = runtime;
        try {
            await approach(page); await ready(page); await position(page, .45);
            const before = await page.evaluate(state);
            assert.ok(await page.locator('[data-program-type]').evaluate(type => {
                const rows = type.getAnimations({ subtree: true });
                return rows.length === 20 && rows.every(row => row.effect.getTiming().duration === 4500);
            }), 'Values uses the same faster ambient rows');
            await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true })));
            await position(page, .65);
            assert.equal((await page.evaluate(state)).offset, before.offset);
            await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
            await page.waitForTimeout(60);
            assert.ok((await page.evaluate(state)).offset < before.offset);
            await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
            await page.locator('[data-program-open]').first().click();
            await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programState === 'detail');
            assert.equal(await page.locator('[data-program-type]').evaluate(type => type.getAnimations({ subtree: true }).length), 0,
                'ambient rows stop during the full GSAP detail choreography');
            await page.keyboard.press('Escape');
            await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programState === 'idle');
            assert.equal(await page.locator('[data-program-type]').evaluate(type => type.getAnimations({ subtree: true }).length), 20);
            await approach(page); await position(page, .45);
            assert.equal((await page.evaluate(state)).gsapScripts, 1);
            assert.equal((await page.evaluate(state)).triggers, 1);
            await page.emulateMedia({ reducedMotion: 'reduce' });
            await page.waitForTimeout(60);
            const reduced = await page.evaluate(state);
            assert.equal(reduced.ready, false); assert.equal(reduced.triggers, 0);
            await page.emulateMedia({ reducedMotion: 'no-preference' });
            await approach(page); await ready(page);
            await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: false })));
            const disposed = await page.evaluate(state);
            assert.equal(disposed.ready, false); assert.equal(disposed.triggers, 0);
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-line-lifecycle-${engine}`, { before, reduced, disposed });
        } finally { await runtime.close(); }
    });

    test(`Values remains title-only when the drawing library cannot load in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine); const { page } = runtime;
        try {
            await page.route('**/gsap@3.7.1/dist/ScrollTrigger.min.js', route => route.abort());
            await page.reload({ waitUntil: 'domcontentloaded' });
            await approach(page);
            await page.waitForFunction(() => !!window.gsap);
            await page.waitForTimeout(100);
            const fallback = await page.evaluate(state);
            assert.equal(fallback.ready, false); assert.equal(fallback.triggers, 0);
            assert.equal(fallback.scene.height, 0);
            const readable = await page.locator('[data-values-heading]').evaluate(heading => {
                return heading.getAttribute('aria-label') && heading.textContent.trim().length > 0;
            });
            assert.equal(readable, true); assert.deepEqual(runtime.errors, []);
            await evidence(`values-line-network-fallback-${engine}`, { fallback });
        } finally { await runtime.close(); }
    });
}
