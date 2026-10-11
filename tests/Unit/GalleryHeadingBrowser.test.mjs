import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt, galleryOpening, galleryResize } from './GalleryBrowserSupport.mjs';

for (const engine of engines) {
    test(`Gallery native title spacing and gradual dark handoff in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            for (const lang of ['id', 'en', 'ar']) {
                await locale(page, lang);
                await galleryReady(page);
                for (const width of [390, 640, 768, 1024, 1280, 1536]) {
                    await galleryResize(page, width, 900);
                    const start = +await page.locator('[data-gallery]').getAttribute('data-gallery-shrink-start');
                    const samples = [];
                    for (const amount of [.2, .4, .6]) {
                        const state = await galleryAt(page, start * amount);
                        const spacing = await page.evaluate(() => {
                            const root = document.querySelector('[data-gallery]');
                            const heading = root.querySelector('[data-gallery-heading]');
                            const line = root.querySelector('[data-gallery-heading-line]');
                            const cards = [...document.querySelectorAll('[data-values-card]')];
                            const bottom = Math.max(...cards.map(card => card.getBoundingClientRect().bottom));
                            return { gap: heading.getBoundingClientRect().top - bottom,
                                expected: +root.dataset.galleryEntryGap,
                                opacity: getComputedStyle(line).opacity, transform: getComputedStyle(line).transform,
                                animations: line.getAnimations().length };
                        });
                        assert.equal(state.heading.visibility, 'visible');
                        const left = state.items[0].x;
                        const right = state.width - (state.items[0].x + state.items[0].width);
                        const vertical = state.items[0].y - state.heading.bottom;
                        assert.ok(Math.abs(left - right) < 2,
                            `opening horizontal symmetry ${lang}/${width}: ${JSON.stringify({ left, right })}`);
                        assert.ok(Math.abs(vertical - left) < 2,
                            `opening title/media distance matches side inset ${lang}/${width}: ${JSON.stringify({ vertical, left, amount })}`);
                        assert.equal(state.lineOpacity, 0, 'white SVG is absent when the native title arrives');
                        assert.equal(spacing.opacity, '1');
                        assert.equal(spacing.transform, 'none');
                        assert.equal(spacing.animations, 0, 'title rises natively without an appearance animation');
                        assert.ok(Math.abs(spacing.gap - spacing.expected) < 2, `native spacing ${lang}/${width}: ${JSON.stringify(spacing)}`);
                        if (width === 1536 && amount === .4) await page.screenshot({ path: `${directory}/gallery-${engine}-${lang}-1536-native-title.jpg`, type: 'jpeg', quality: 85 });
                        samples.push({ amount, state, spacing });
                    }
                    assert.ok(samples[0].state.heading.y > samples[2].state.heading.y, 'title keeps rising');
                    assert.ok(samples[0].state.backgroundRGB[2] > samples[1].state.backgroundRGB[2]
                        && samples[1].state.backgroundRGB[2] > samples[2].state.backgroundRGB[2], 'blue progressively darkens');
                    const opening = await galleryOpening(page);
                    assert.deepEqual(opening.backgroundRGB, [8, 9, 11]);
                    assert.equal(opening.lineOpacity, 0, 'white decoration does not enter Gallery');
                    assert.equal(opening.typeOpacity, 0);
                    const reverse = await galleryAt(page, 0);
                    assert.deepEqual(reverse.backgroundRGB, [32, 56, 255]);
                    cases.push({ lang, width, samples, opening, reverse });
                }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`gallery-heading-${engine}`, { version: runtime.version, cases });
        } finally { await runtime.close(); }
    });
}
