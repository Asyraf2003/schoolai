import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';
import { oldValuesReference } from './ValuesOldReference.mjs';
import { valuesCompactedProgress } from '../../resources/js/sections/values-card-keyframes.js';

const phases = [0, .025, .065, .118, .16, .237, .31, .65, .92, .98];
const inspect = legacy => {
    const cards = [...document.querySelectorAll('[data-values-card]')];
    const prefix = legacy ? '.values-card__' : '.values__card-';
    const fields = ['index', 'summary', 'title', 'body'];
    return cards.map(card => ({
        width: card.offsetWidth, height: card.offsetHeight,
        pose: getComputedStyle(card.querySelector('[data-values-card-pose]')).transform,
        flip: getComputedStyle(card.querySelector('[data-values-card-inner]')).transform,
        fields: fields.map(name => {
            const node = card.querySelector(prefix + name); const css = getComputedStyle(node);
            return { text: node.textContent.trim().replace(/\s+/g, ' '), size: css.fontSize, weight: css.fontWeight,
                family: css.fontFamily.split(',')[0].replaceAll('"', '').replace('Values Cairo', 'Cairo'),
                color: css.color, leading: css.lineHeight, tracking: css.letterSpacing };
        }),
    }));
};
async function desktopPosition(page, progress, legacy) {
    await page.bringToFront();
    const mappedProgress = legacy ? progress : valuesCompactedProgress(progress);
    await page.evaluate(({ progress, legacy, mappedProgress }) => {
        const track = document.querySelector(legacy ? '[data-values-timeline]' : '[data-values-cards-track]');
        const viewport = document.querySelector(legacy ? '[data-values-stage]' : '[data-values-cards-viewport]');
        let travel = track.offsetHeight - viewport.offsetHeight;
        let distance = mappedProgress * travel;
        if (legacy) {
            const base = travel - innerHeight;
            distance = progress <= .895 ? progress * base
                : .895 * base + (progress - .895) / .105 * (.105 * base + innerHeight * .5);
        }
        scrollTo(0, track.getBoundingClientRect().top + scrollY - 72 + distance);
    }, { progress, legacy, mappedProgress });
    if (!legacy) {
        try {
            await page.waitForFunction(mappedProgress => {
                const track = document.querySelector('[data-values-cards-track]');
                const viewport = document.querySelector('[data-values-cards-viewport]');
                const target = Math.max(0, Math.min(1, (72 - track.getBoundingClientRect().top) / (track.offsetHeight - viewport.offsetHeight)));
                // Allow one native scroll pixel; keep the card timeline
                // tightly synced to the actual measured scroll target.
                return Math.abs(target - mappedProgress) * (track.offsetHeight - viewport.offsetHeight) <= 1
                    && Math.abs(Number(document.querySelector('[data-values]').dataset.cardsProgress) - target) < .00002;
            }, mappedProgress);
        } catch (error) {
            const state = await page.evaluate(() => {
                const root = document.querySelector('[data-values]');
                const track = root.querySelector('[data-values-cards-track]');
                const viewport = root.querySelector('[data-values-cards-viewport]');
                return { scrollY, scrollHeight: document.documentElement.scrollHeight,
                    innerHeight, trackTop: track.getBoundingClientRect().top,
                    trackHeight: track.offsetHeight, viewportHeight: viewport.offsetHeight,
                    scrollTarget: (72 - track.getBoundingClientRect().top) / (track.offsetHeight - viewport.offsetHeight),
                    cardsProgress: root.dataset.cardsProgress, lineReady: root.hasAttribute('data-line-ready'),
                    flow: root.hasAttribute('data-cards-flow'), running: root.hasAttribute('data-cards-running') };
            });
            throw new Error(`Values fidelity scroll target ${mappedProgress} did not settle: ${JSON.stringify(state)}; ${error.message}`);
        }
    }
    await page.waitForTimeout(1500);
}
for (const engine of engines) {
    test(`Values preserves OLD card composition and native scroll choreography in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        runtime.page.setDefaultTimeout(15000);
        const cases = [];
        try {
            for (const lang of ['id', 'en', 'ar']) {
                await locale(runtime.page, lang);
                await runtime.page.evaluate(() => scrollTo(0, document.querySelector('[data-values]').getBoundingClientRect().top + scrollY));
                await runtime.page.waitForFunction(() => document.querySelector('[data-values]').hasAttribute('data-line-ready'));
                const old = await oldValuesReference(runtime.context, runtime.page);
                try {
                    for (const progress of phases) {
                        await desktopPosition(runtime.page, progress, false);
                        await desktopPosition(old, progress, true);
                        const before = await old.evaluate(inspect, true);
                        const after = await runtime.page.evaluate(inspect, false);
                        for (let index = 0; index < 4; index++) {
                            assert.ok(Math.abs(before[index].width - after[index].width) <= 1);
                            assert.ok(Math.abs(before[index].height - after[index].height) <= 2);
                            assert.deepEqual(after[index].fields, before[index].fields, `${lang}/${progress}/${index} content/type fidelity`);
                            // Rounded native scroll coordinates have subpixel differences in normalized progress.
                            const matrix = value => value.replace(/^matrix3?d?\(/, '').replace(')', '').split(',').map(Number);
                            // The exit now follows native scroll pixels rather than
                            // legacy eased translation; all pre-exit poses remain exact.
                            for (const key of (progress <= .895 ? ['pose', 'flip'] : ['flip'])) {
                                const a = matrix(after[index][key]); const b = matrix(before[index][key]);
                                assert.equal(a.length, b.length);
                                assert.ok(a.every((value, i) => Math.abs(value - b[i]) <= (i >= (a.length === 6 ? 4 : 12) ? 8 : .025)), `${lang}/${progress}/${key}: new=${a} old=${b}`);
                            }
                        }
                        if (lang === 'en' && [0, .065, .118, .237, .65, .92].includes(progress)) {
                            for (const [name, page] of [['old', old], ['v2', runtime.page]]) {
                                await page.evaluate(() => document.getAnimations().filter(a => a.animationName?.includes('float')).forEach(a => { a.pause(); a.currentTime = 0; }));
                                await page.screenshot({ path: `${directory}/values-fidelity-${engine}-${name}-${progress}.png` });
                            }
                        }
                        cases.push({ lang, progress, before, after });
                    }
                } finally { await old.evaluate(() => window.referenceDispose()); await old.context().close(); }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-fidelity-${engine}`, { version: runtime.version, reference: 'isolated archived component with unchanged OLD CSS/controller and shared localized content', cases });
        } finally { await runtime.close(); }
    });
}
