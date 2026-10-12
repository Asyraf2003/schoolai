import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';

const inspectWheel = async (page, delta = 112) => {
    await page.evaluate(() => {
        scrollTo(0, 350);
        window.lastWheelPrevented = null;
        window.addEventListener('wheel', event => { window.lastWheelPrevented = event.defaultPrevented; }, { once: true });
    });
    await page.mouse.move(200, 300);
    if (await page.evaluate(() => matchMedia('(pointer: coarse)').matches)) {
        return page.evaluate(delta => !document.body.dispatchEvent(new WheelEvent('wheel', { deltaY: delta, bubbles: true, cancelable: true })), delta);
    }
    await page.mouse.wheel(0, delta);
    await page.waitForFunction(() => window.lastWheelPrevented !== null);
    return page.evaluate(() => lastWheelPrevented);
};
for (const engine of engines) test(`Cinematic wheel is desktop-only and phone input stays native in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine);
    const cells = [];
    try {
        for (const language of ['id', 'en', 'ar']) {
            await locale(runtime.page, language);
            for (const width of [360, 390, 640, 768, 1023, 1024, 1280, 1440, 1536, 1920]) {
                await runtime.page.setViewportSize({ width, height: 900 });
                const prevented = await inspectWheel(runtime.page);
                assert.equal(prevented, width >= 1024, `${language}/${width}`);
                cells.push({ language, width, prevented });
            }
        }
        await runtime.page.setViewportSize({ width: 1440, height: 900 });
        await inspectWheel(runtime.page);
        const afterInput = await runtime.page.evaluate(() => scrollY);
        await runtime.page.waitForTimeout(100);
        assert.ok(await runtime.page.evaluate(() => scrollY) > afterInput, 'desktop coast retained');
        await runtime.page.setViewportSize({ width: 390, height: 844 });
        await runtime.page.evaluate(() => scrollTo(0, 350));
        await runtime.page.waitForTimeout(200);
        assert.equal(await runtime.page.evaluate(() => scrollY), 350, 'resize cancels queued desktop travel');
        await runtime.page.emulateMedia({ reducedMotion: 'reduce' });
        await runtime.page.setViewportSize({ width: 1440, height: 900 });
        assert.equal(await inspectWheel(runtime.page), false, 'reduced motion remains native');
        assert.deepEqual(runtime.errors, []);
        await evidence(`scroll-input-capability-${engine}`, { version: runtime.version, cells });
    } finally { await runtime.close(); }
});
for (const engine of engines.filter(engine => engine !== 'firefox')) test(`Wide touch input avoids artificial coast in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine, { hasTouch: true, isMobile: true, viewport: { width: 1280, height: 900 } });
    try {
        for (const language of ['en', 'id', 'ar']) {
            await locale(runtime.page, language);
            assert.equal(await inspectWheel(runtime.page), false);
        }
        assert.deepEqual(runtime.errors, []);
    } finally { await runtime.close(); }
});
