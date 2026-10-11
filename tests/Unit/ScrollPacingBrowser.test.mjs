import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';
import { settledValuesLayout } from './ValuesBrowserSupport.mjs';

for (const engine of engines) {
    test(`homepage wheel scroll keeps cinematic sections inspectable in ${engine}`,
        { skip: !enabled }, async () => {
            const runtime = await session(engine);
            const { page } = runtime;
            const result = [];
            try {
                for (const lang of ['id', 'en', 'ar']) {
                    console.info(`Pacing ${engine}/${lang}: locale`);
                    await locale(page, lang);
                    await page.evaluate(() => {
                        const root = document.querySelector('[data-values]');
                        scrollTo(0, root.getBoundingClientRect().top + scrollY + 350);
                    });
                    const before = await page.evaluate(() => {
                        window.wheelDiagnostics = [];
                        window.addEventListener('wheel', event => {
                            window.wheelDiagnostics.push({ delta: event.deltaY, cancelable: event.cancelable,
                                prevented: event.defaultPrevented, target: event.target?.tagName });
                        }, { capture: true, passive: true, once: true });
                        return scrollY;
                    });
                    // WebKit's initial pointer at (0,0) may hit a fixed
                    // overlay rather than the scrolling document.
                    await page.mouse.move(420, 470);
                    await page.mouse.wheel(0, 5000);
                    await page.waitForTimeout(180);
                    const after = await page.evaluate(() => scrollY);
                    console.info(`Pacing ${engine}/${lang}: moved ${after - before}`);
                    const diagnostics = await page.evaluate(() => ({
                        events: window.wheelDiagnostics,
                        scroll: scrollY,
                        max: document.scrollingElement.scrollHeight - innerHeight,
                        visible: document.visibilityState,
                        reduce: matchMedia('(prefers-reduced-motion: reduce)').matches,
                    }));
                    assert.ok(after > before,
                        `actual paced wheel ${lang}/${engine}: ${JSON.stringify({ before, after, diagnostics, errors: runtime.errors })}`);
                    await page.waitForTimeout(190);
                    assert.ok(Math.abs(await page.evaluate(() => scrollY) - after) < 2,
                        'no long queued inertial tail after input stops');
                    await page.mouse.wheel(0, -5000);
                    await page.waitForTimeout(180);
                    assert.ok(await page.evaluate(() => scrollY) < after, 'reverse changes direction');
                    const direct = await page.evaluate(() => {
                        const target = scrollY + 1300;
                        scrollTo(0, target);
                        return { target, actual: scrollY };
                    });
                    assert.ok(Math.abs(direct.target - direct.actual) < 2,
                        'programmatic navigation bypasses wheel pacing');
                    await page.emulateMedia({ reducedMotion: 'reduce' });
                    await settledValuesLayout(page);
                    const reducedBefore = await page.evaluate(() => {
                        // Reduced/static layout is shorter. Select real available
                        // travel rather than testing a clamped document endpoint.
                        scrollTo(0, 350);
                        window.reducedWheelPrevented = null;
                        window.addEventListener('wheel', event => { window.reducedWheelPrevented = event.defaultPrevented; }, { once: true });
                        return scrollY;
                    });
                    await page.mouse.wheel(0, 750);
                    await page.waitForFunction(before => scrollY - before > 160, reducedBefore);
                    const reducedAfter = await page.evaluate(() => scrollY);
                    assert.ok(reducedAfter - reducedBefore > 160, 'reduced-motion uses native wheel');
                    assert.equal(await page.evaluate(() => window.reducedWheelPrevented), false);
                    await page.emulateMedia({ reducedMotion: 'no-preference' });
                    await settledValuesLayout(page);
                    result.push({ lang, intercepted: after - before, direct, reduced: reducedAfter - reducedBefore });
                }
                await page.evaluate(() => scrollTo(0, 350));
                await settledValuesLayout(page);
                await page.mouse.move(700, 400);
                const glideStart = await page.evaluate(() => scrollY);
                await page.mouse.wheel(0, 112);
                const impulseEnd = await page.evaluate(() => scrollY);
                const glideFrames = await page.evaluate(() => new Promise(resolve => {
                    const frames = [];
                    const sample = () => {
                        frames.push(scrollY);
                        if (frames.length === 5) resolve(frames);
                        else requestAnimationFrame(sample);
                    };
                    requestAnimationFrame(sample);
                }));
                assert.ok(glideFrames.at(-1) > impulseEnd, 'released input still glides across subsequent real paints');
                assert.ok(glideFrames.at(-1) - glideStart <= 225, 'ordinary impulse has a short bounded glide');
                await page.waitForTimeout(220);
                const glideStopped = await page.evaluate(() => scrollY);
                await page.waitForTimeout(180);
                assert.ok(Math.abs(await page.evaluate(() => scrollY) - glideStopped) <= 1);
                const freeInertia = { glideStart, impulseEnd, glideFrames, glideStopped };
                await page.evaluate(() => {
                    scrollTo(0, 350);
                    document.body.insertAdjacentHTML('beforeend', '<div id="pacing-scrollbox" data-scroll-native style="position:fixed;top:100px;left:100px;width:240px;height:100px;overflow:auto;z-index:999999;background:white"><div style="height:900px"></div></div>');
                });
                await page.mouse.move(190, 145);
                const nativeBefore = await page.evaluate(() => scrollY);
                await page.mouse.wheel(0, 520);
                await page.waitForTimeout(160);
                const nested = await page.evaluate(() => ({
                    inner: document.getElementById('pacing-scrollbox').scrollTop,
                    outer: scrollY,
                }));
                assert.ok(nested.inner > 150, 'inner scrolling remains browser-native');
                assert.ok(Math.abs(nested.outer - nativeBefore) < 2, 'nested scroll never hijacked');
                assert.deepEqual(runtime.errors, []);
                await evidence(`scroll-pacing-${engine}`, { version: runtime.version, result, nested, freeInertia });
            } finally { await runtime.close(); }
        });
}
