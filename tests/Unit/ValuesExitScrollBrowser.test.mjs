import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';
import { settledValuesLayout } from './ValuesBrowserSupport.mjs';
import { valuesCompactedProgress, VALUES_OLD_EXIT } from '../../resources/js/sections/values-card-keyframes.js';

const exitStart = valuesCompactedProgress(VALUES_OLD_EXIT);
const state = () => {
    const root = document.querySelector('[data-values]');
    const track = root.querySelector('[data-values-cards-track]');
    const viewport = root.querySelector('[data-values-cards-viewport]');
    const pose = root.querySelector('[data-values-card-pose]');
    const stage = root.querySelector('[data-values-cards-stage]');
    const exitY = new DOMMatrix(getComputedStyle(pose).transform).m42;
    const stageTop = stage.getBoundingClientRect().top;
    return {
        scrollY, exitY, stageTop, visualTop: stageTop + exitY,
        scrollSpan: track.offsetHeight - viewport.offsetHeight,
        progress: (parseFloat(getComputedStyle(root).scrollMarginBlockStart)
            - track.getBoundingClientRect().top) / (track.offsetHeight - viewport.offsetHeight),
        reported: Number(root.dataset.cardsProgress),
        flow: root.hasAttribute('data-cards-flow'),
    };
};
const at = async (page, progress) => {
    await page.evaluate(progress => {
        const root = document.querySelector('[data-values]');
        const track = root.querySelector('[data-values-cards-track]');
        const viewport = root.querySelector('[data-values-cards-viewport]');
        const sticky = parseFloat(getComputedStyle(root).scrollMarginBlockStart);
        const absolute = track.getBoundingClientRect().top + scrollY - sticky
            + progress * (track.offsetHeight - viewport.offsetHeight);
        scrollTo(0, Math.round(absolute));
    }, progress);
    await page.waitForFunction(progress => {
        const root = document.querySelector('[data-values]');
        const track = root.querySelector('[data-values-cards-track]');
        const viewport = root.querySelector('[data-values-cards-viewport]');
        const span = track.offsetHeight - viewport.offsetHeight;
        const actual = (parseFloat(getComputedStyle(root).scrollMarginBlockStart)
            - track.getBoundingClientRect().top) / span;
        return Math.abs(actual - progress) * span <= 1
            && Math.abs(Number(root.dataset.cardsProgress) - actual) <= .00002;
    }, progress);
    return page.evaluate(state);
};
for (const engine of engines) {
    test(`Values card exit tracks native scroll at 1:1 in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const samples = [];
        try {
            for (const lang of ['id', 'en', 'ar']) {
                await locale(runtime.page, lang);
                for (const width of [1280, 1440, 1536]) {
                    await runtime.page.setViewportSize({ width, height: 900 });
                    await settledValuesLayout(runtime.page);
                    await runtime.page.evaluate(() => {
                        const root = document.querySelector('[data-values]');
                        scrollTo(0, root.getBoundingClientRect().top + scrollY);
                    });
                    await runtime.page.waitForFunction(() =>
                        document.querySelector('[data-values]').hasAttribute('data-line-ready'));
                    const first = await at(runtime.page, exitStart + .025);
                    const second = await at(runtime.page, exitStart + .075);
                    assert.equal(first.flow, false);
                    assert.equal(second.flow, false);
                    const scrolled = second.scrollY - first.scrollY;
                    const moved = first.exitY - second.exitY;
                    assert.ok(Math.abs(scrolled - moved) <= 2,
                        JSON.stringify({ lang, width, first, second }));
                    const reverse = await at(runtime.page, exitStart + .025);
                    assert.ok(Math.abs(reverse.exitY - first.exitY) <= 2,
                        JSON.stringify({ lang, width, first, reverse }));
                    const end = await at(runtime.page, 1);
                    await runtime.page.evaluate(() => scrollBy(0, 75));
                    await runtime.page.waitForFunction(endScroll => scrollY >= endScroll + 74, end.scrollY);
                    const released = await runtime.page.evaluate(state);
                    assert.ok(Math.abs((end.visualTop - released.visualTop)
                        - (released.scrollY - end.scrollY)) <= 3,
                        JSON.stringify({ lang, width, end, released }));
                    samples.push({ lang, width, first, second, reverse, end, released });
                }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-native-exit-${engine}`, { samples });
        } finally { await runtime.close(); }
    });
}
