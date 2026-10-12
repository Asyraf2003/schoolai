import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';

for (const engine of engines) test(`Phone Stories have no side gradient in any locale in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine, { reducedMotion: 'reduce' });
    const cells = [];
    try {
        for (const language of ['id', 'en', 'ar']) {
            await locale(runtime.page, language);
            for (const width of [360, 390, 639, 640, 767, 768, 1440]) {
                await runtime.page.setViewportSize({ width, height: 900 });
                const gradients = await runtime.page.locator('[data-stories-feature]').evaluateAll(features => features.map(element => getComputedStyle(element, '::after').backgroundImage));
                for (const gradient of gradients) {
                    const named = { 'to top': 0, 'to right': 90, 'to bottom': 180, 'to left': 270 };
                    const angle = named[gradient.match(/linear-gradient\((to \w+)/)?.[1]]
                        ?? Number(gradient.match(/linear-gradient\((\d+)deg/)?.[1] ?? 180);
                    assert.equal(angle, width < 768 ? 180 : language === 'ar' ? 90 : 270, `${language}/${width}: ${gradient}`);
                }
                assert.equal(await runtime.page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
                cells.push({ language, width, gradients });
            }
        }
        assert.deepEqual(runtime.errors, []);
        await evidence(`phone-stories-shade-${engine}`, { version: runtime.version, cells });
    } finally { await runtime.close(); }
});
