import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, session, locale, field, evidence, directory } from './ProgramBrowserSupport.mjs';

const phase = process.env.PROGRAM_TYPE_PHASE ?? 'after';
const metrics = async client => Object.fromEntries((await client.send('Performance.getMetrics')).metrics.map(item => [item.name, item.value]));

test(`Program type ${phase} density, bounded scroll cost and JS heap`, { skip: !enabled }, async () => {
    const runtime = await session('chromium', { reducedMotion: 'reduce', hasTouch: true });
    const { page, context } = runtime;
    const client = await context.newCDPSession(page);
    const cases = [];
    try {
        await client.send('Performance.enable');
        for (const language of ['en', 'ar']) {
            await locale(page, language);
            for (const width of [390, 768, 1440]) {
                await page.setViewportSize({ width, height: width < 640 ? 844 : 900 });
                await field(page);
                await page.locator('[data-program-card]').nth(3).scrollIntoViewIfNeeded();
                await page.evaluate(() => document.fonts.ready);
                const density = await page.locator('[data-program-type]').evaluate(type => {
                    const lines = [...type.children];
                    const css = getComputedStyle(lines[0]);
                    return { width: innerWidth, height: innerHeight, locale: document.documentElement.lang,
                        lines: lines.length, characters: type.textContent.length,
                        leading: parseFloat(css.lineHeight) / parseFloat(css.fontSize),
                        font: css.fontFamily, opacity: css.opacity,
                        visibleLines: lines.filter(line => { const box = line.getBoundingClientRect(); return box.bottom > 0 && box.top < innerHeight; }).length,
                        idleAnimations: type.getAnimations({ subtree: true }).length,
                        canvas: type.querySelectorAll('canvas').length,
                    };
                });
                assert.equal(density.lines, 20);
                assert.equal(density.idleAnimations, 0);
                assert.equal(density.canvas, 0);
                if (phase === 'after') assert.ok(Math.abs(density.leading - .75) < .001);
                if (language === 'ar') assert.match(density.font, /Cairo/);
                await page.screenshot({ path: `${directory}/program-third-type-${phase}-${language}-${width}.png` });
                const samples = [];
                for (const hidden of [false, true]) {
                    await page.locator('[data-program-type-home]').evaluate((home, hide) => { home.hidden = hide; }, hidden);
                    for (let run = 0; run < 3; run++) {
                        await client.send('HeapProfiler.collectGarbage');
                        await page.evaluate(() => {
                            window.programLongTasks = [];
                            window.programLongObserver = new PerformanceObserver(list => {
                                window.programLongTasks.push(...list.getEntries().map(entry => ({ duration: entry.duration, startTime: entry.startTime })));
                            });
                            window.programLongObserver.observe({ type: 'longtask' });
                        });
                        const before = await metrics(client);
                        for (let index = 0; index < 8; index++) {
                            await page.mouse.wheel(0, index < 4 ? 80 : -80);
                            await page.evaluate(() => new Promise(resolve => requestAnimationFrame(resolve)));
                        }
                        const after = await metrics(client);
                        const longTasks = await page.evaluate(() => { window.programLongObserver.disconnect(); return window.programLongTasks; });
                        samples.push({ hidden, run, taskMs: (after.TaskDuration - before.TaskDuration) * 1000,
                            layoutMs: (after.LayoutDuration - before.LayoutDuration) * 1000,
                            scriptMs: (after.ScriptDuration - before.ScriptDuration) * 1000,
                            heapUsedBytes: after.JSHeapUsedSize, nodeCount: after.Nodes, longTasks });
                    }
                }
                await page.locator('[data-program-type-home]').evaluate(home => { home.hidden = false; });
                cases.push({ ...density, samples });
            }
        }
        assert.equal(runtime.requests.some(url => /program-(kinetic|gsap)|gsap@/.test(url)), false);
        assert.deepEqual(runtime.errors, []);
        await evidence(`program-third-type-${phase}`, { version: runtime.version, cases,
            profile: 'Linux headless localhost, reduced motion, fonts/media ready, no throttle; visible/hidden type control,3 repeats,8 native wheel steps',
            limitations: 'Task/JS heap include the page. JS heap is not total browser/GPU/Android/iOS RAM; timing is lab data, not field CWV.' });
    } finally { await runtime.close(); }
});
