import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';
import { oldValuesReference } from './ValuesOldReference.mjs';

const inspect = () => [...document.querySelectorAll('[data-values-card]')].map(card => ({
    width: card.offsetWidth, height: card.offsetHeight,
    pose: [...new DOMMatrix(getComputedStyle(card.querySelector('[data-values-card-pose]')).transform).toFloat64Array()],
    flip: [...new DOMMatrix(getComputedStyle(card.querySelector('[data-values-card-inner]')).transform).toFloat64Array()],
}));
const position = async (page, visible) => {
    await page.bringToFront();
    await page.evaluate(visible => {
        const card = document.querySelector('[data-values-card]');
        scrollTo(0, card.getBoundingClientRect().top + scrollY - innerHeight + card.offsetHeight * visible);
    }, visible);
    await page.waitForTimeout(500);
};
for (const engine of engines) {
    test(`Values matches OLD natural rows and visibility flips on compact tiers in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        runtime.page.setDefaultTimeout(15000);
        const cases = [];
        try {
            for (const lang of ['id', 'en', 'ar']) {
                await locale(runtime.page, lang);
                for (const width of [390, 768, 1024]) {
                    await runtime.page.setViewportSize({ width, height: 900 });
                    await runtime.page.evaluate(() => scrollTo(0, document.querySelector('[data-values]').getBoundingClientRect().top + scrollY));
                    await runtime.page.waitForFunction(() => document.querySelector('[data-values]').hasAttribute('data-line-ready'));
                    const old = await oldValuesReference(runtime.context, runtime.page);
                    await old.setViewportSize({ width, height: 900 });
                    try {
                        for (const visible of [.62, .9, 1]) {
                            await position(runtime.page, visible);
                            await position(old, visible);
                            const before = await old.evaluate(inspect);
                            const after = await runtime.page.evaluate(inspect);
                            for (let index = 0; index < 4; index++) {
                                assert.ok(Math.abs(before[index].width - after[index].width) <= 1);
                                assert.ok(Math.abs(before[index].height - after[index].height) <= 2);
                                for (const key of ['pose', 'flip']) assert.ok(after[index][key].every((v, i) => Math.abs(v - before[index][key][i]) < .03),
                                    `${lang}/${width}/${visible}/${index}/${key}: ${JSON.stringify({ before: before[index], after: after[index] })}`);
                            }
                            cases.push({ lang, width, visible, before, after });
                            if (visible === .9) {
                                for (const [name, page] of [['old', old], ['v2', runtime.page]]) {
                                    await page.evaluate(() => document.getAnimations().filter(a => a.animationName?.includes('float')).forEach(a => { a.pause(); a.currentTime = 0; }));
                                    await page.screenshot({ path: `${directory}/values-compact-${engine}-${lang}-${width}-${name}.png` });
                                }
                            }
                        }
                    } finally { await old.evaluate(() => window.referenceDispose()); await old.context().close(); }
                }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-compact-fidelity-${engine}`, { cases });
        } finally { await runtime.close(); }
    });
}
