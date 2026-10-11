import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, session, evidence, url } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt } from './GalleryBrowserSupport.mjs';

const baseline = process.env.PROGRAM_BASELINE_URL;
const percentile = (values, ratio) => [...values].sort((a, b) => a - b)[Math.floor((values.length - 1) * ratio)] ?? 0;

test('three comparable Chromium scroll samples expose frame cost and idle work', { skip: !enabled || !baseline }, async () => {
    const profiles = [];
    for (const [name, entryURL] of [['PR97+PR99', baseline], ['continuation', url]]) {
        const samples = [];
        for (let run = 0; run < 3; run++) {
            const runtime = await session('chromium', { baseURL: entryURL });
            const { page } = runtime;
            try {
                await galleryReady(page); await galleryAt(page, .52);
                await page.mouse.move(420, 470);
                await page.evaluate(() => {
                    window.scrollProfile = { frames: [], longTasks: [], running: true, writes: 0 };
                    window.profileObserver = new PerformanceObserver(list => scrollProfile.longTasks.push(...list.getEntries().map(entry => ({ duration: entry.duration, start: entry.startTime }))));
                    profileObserver.observe({ type: 'longtask' });
                    const sample = now => {
                        if (!scrollProfile.running) return;
                        scrollProfile.frames.push({ now, y: scrollY }); requestAnimationFrame(sample);
                    };
                    requestAnimationFrame(sample);
                });
                for (let index = 0; index < 24; index++) {
                    await page.mouse.wheel(0, 112); await page.waitForTimeout(24);
                }
                await page.waitForTimeout(320);
                await page.evaluate(() => {
                    scrollProfile.running = false;
                    profileObserver.disconnect();
                    window.idleObserver = new MutationObserver(entries => { scrollProfile.writes += entries.length; });
                    for (const selector of ['[data-testimonials]', '[data-gallery]']) idleObserver.observe(document.querySelector(selector), { attributes: true });
                });
                await page.waitForTimeout(180);
                const sample = await page.evaluate(() => { idleObserver.disconnect(); return scrollProfile; });
                const intervals = sample.frames.slice(1).map((frame, index) => frame.now - sample.frames[index].now);
                assert.equal(sample.writes, 0, 'no added section writes while idle');
                samples.push({ run, frameP95: percentile(intervals, .95), longTasks: sample.longTasks,
                    travel: sample.frames.at(-1).y - sample.frames[0].y, idleWrites: sample.writes });
                assert.deepEqual(runtime.errors, []);
            } finally { await runtime.close(); }
        }
        profiles.push({ name, entryURL, samples, medianP95: percentile(samples.map(s => s.frameP95), .5), worstP95: Math.max(...samples.map(s => s.frameP95)) });
    }
    // Local automated rendering permits at most one additional 60Hz frame at
    // the median. This is an incremental regression gate, not 60fps or PSI.
    assert.ok(profiles[1].medianP95 <= profiles[0].medianP95 + 17, JSON.stringify(profiles));
    await evidence('scroll-pacing-performance', { profiles, viewport: [1440, 900], engine: 'Chromium', mode: 'headless Linux, real wheel112, fresh contexts; GPU path not certified', claims: 'local incremental frame/idle evidence only' });
});
