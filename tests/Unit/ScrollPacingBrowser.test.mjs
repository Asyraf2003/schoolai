import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`homepage wheel scroll keeps cinematic sections inspectable in ${engine}`,
        { skip: !enabled }, async () => {
            const runtime = await session(engine);
            const { page } = runtime;
            const result = [];
            try {
                for (const lang of ['id', 'en', 'ar']) {
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
                    await page.mouse.wheel(0, 5000);
                    await page.waitForTimeout(180);
                    const after = await page.evaluate(() => scrollY);
                    const diagnostics = await page.evaluate(() => ({
                        events: window.wheelDiagnostics,
                        scroll: scrollY,
                        max: document.scrollingElement.scrollHeight - innerHeight,
                        visible: document.visibilityState,
                        reduce: matchMedia('(prefers-reduced-motion: reduce)').matches,
                    }));
                    assert.ok(after > before && after - before < 160,
                        `wheel cap ${lang}/${engine}: ${JSON.stringify({ before, after, diagnostics, errors: runtime.errors })}`);
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
                    const reducedBefore = await page.evaluate(() => scrollY);
                    await page.mouse.wheel(0, 750);
                    await page.waitForTimeout(120);
                    const reducedAfter = await page.evaluate(() => scrollY);
                    assert.ok(reducedAfter - reducedBefore > 160, 'reduced-motion uses native wheel');
                    await page.emulateMedia({ reducedMotion: 'no-preference' });
                    result.push({ lang, intercepted: after - before, direct, reduced: reducedAfter - reducedBefore });
                }
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
                await evidence(`scroll-pacing-${engine}`, { version: runtime.version, result, nested });
            } finally { await runtime.close(); }
        });
}
