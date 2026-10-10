import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const ready = page => page.waitForFunction(() => !!window.ScrollTrigger?.getById('values-line'));
const approach = page => page.evaluate(() => {
    const section = document.querySelector('[data-values]');
    scrollTo(0, section.getBoundingClientRect().top + scrollY - innerHeight);
});
const at = async (page, fraction) => {
    await page.evaluate(p => {
        const trigger = window.ScrollTrigger.getById('values-line');
        scrollTo(0, Math.round(trigger.start + (trigger.end - trigger.start) * p));
    }, fraction);
    await page.waitForFunction(p => Math.abs(window.ScrollTrigger.getById('values-line').progress - p) < .0015, fraction);
    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
};
const inspect = () => {
    const scene = document.querySelector('[data-values-line]');
    const trigger = window.ScrollTrigger?.getById('values-line');
    return {
        paths: [...scene.querySelectorAll('path')].map(path => {
            const length = path.getTotalLength();
            const offset = parseFloat(getComputedStyle(path).strokeDashoffset);
            const caps = [path.getPointAtLength(0), path.getPointAtLength(length)];
            return { length, offset, caps: caps.map(p => ({ x: p.x, y: p.y })),
                stroke: getComputedStyle(path).stroke, fill: getComputedStyle(path).fill };
        }),
        size: { width: scene.getBoundingClientRect().width, height: scene.getBoundingClientRect().height },
        ready: document.querySelector('[data-values]').hasAttribute('data-line-ready'),
        scrub: trigger?.vars.scrub, progress: trigger?.progress,
        triggers: window.ScrollTrigger?.getAll().length ?? 0,
        overflow: document.documentElement.scrollWidth > innerWidth,
    };
};

for (const engine of engines) {
    test('Three independent Values strokes draw/reverse with one scrubbed trigger in ' + engine,
        { skip: !enabled }, async () => {
            const runtime = await session(engine);
            const cases = [];
            try {
                for (const lang of ['id', 'en', 'ar']) {
                    await locale(runtime.page, lang);
                    await approach(runtime.page);
                    await ready(runtime.page);
                    for (const width of [390, 768, 1440]) {
                        await runtime.page.setViewportSize({ width, height: 900 });
                        await runtime.page.waitForFunction(w => document.querySelector('[data-values-line] svg').viewBox.baseVal.width === w, width);
                        await at(runtime.page, 0);
                        const initial = await runtime.page.evaluate(inspect);
                        assert.equal(initial.paths.length, 3);
                        assert.equal(initial.scrub, true);
                        assert.equal(initial.triggers, 1);
                        assert.equal(initial.overflow, false);
                        assert.equal(initial.size.height, 4500);
                        assert.ok(initial.paths.every(p => p.offset >= p.length
                            && p.stroke === 'rgb(255, 255, 255)' && p.fill === 'none'));
                        assert.ok(initial.paths.every(p => p.caps.every(c => c.x < 0 || c.x > width)), 'every line ends at a side wall');
                        await at(runtime.page, .4);
                        const first = await runtime.page.evaluate(inspect);
                        await at(runtime.page, .8);
                        const later = await runtime.page.evaluate(inspect);
                        assert.ok(later.paths.every((p, i) => p.offset <= first.paths[i].offset + .1));
                        assert.ok(later.paths[0].offset < 1 && first.paths[2].offset >= first.paths[2].length,
                            JSON.stringify({ lang, width, first: first.paths.map(p => [p.length, p.offset]),
                                later: later.paths.map(p => [p.length, p.offset]),
                                firstProgress: first.progress, laterProgress: later.progress }));
                        await at(runtime.page, 1);
                        const full = await runtime.page.evaluate(inspect);
                        assert.ok(full.paths.every(p => Math.abs(p.offset) < .1));
                        await at(runtime.page, .4);
                        const back = await runtime.page.evaluate(inspect);
                        assert.ok(back.paths.every((p, i) => Math.abs(p.offset - first.paths[i].offset) < .5));
                        cases.push({ lang, width, initial, first, later, full });
                    }
                }
                await runtime.page.setViewportSize({ width: 1440, height: 900 });
                await at(runtime.page, .7);
                await runtime.page.screenshot({ path: `${directory}/values-three-line-${engine}.png` });
                assert.deepEqual(runtime.errors, []);
                await evidence(`values-three-line-${engine}`, { cases });
            } finally { await runtime.close(); }
        });

    test('Values line falls back with reduced motion and resumes after restore in ' + engine,
        { skip: !enabled }, async () => {
            const runtime = await session(engine);
            try {
                await approach(runtime.page); await ready(runtime.page);
                await runtime.page.emulateMedia({ reducedMotion: 'reduce' });
                await runtime.page.waitForFunction(() => !document.querySelector('[data-values]').hasAttribute('data-line-ready'));
                const reduced = await runtime.page.evaluate(inspect);
                assert.equal(reduced.triggers, 0);
                await runtime.page.emulateMedia({ reducedMotion: 'no-preference' });
                await approach(runtime.page); await ready(runtime.page);
                assert.equal((await runtime.page.evaluate(inspect)).paths.length, 3);
                await runtime.page.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: false })));
                const disposed = await runtime.page.evaluate(inspect);
                assert.equal(disposed.ready, false);
                assert.equal(disposed.triggers, 0);
                assert.deepEqual(runtime.errors, []);
            } finally { await runtime.close(); }
        });
}
