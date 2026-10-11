import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryResize, galleryAt } from './GalleryBrowserSupport.mjs';

const frame = () => {
    const cards = [...document.querySelectorAll('[data-values-card]')].map(card => card.getBoundingClientRect());
    const title = document.querySelector('[data-gallery-heading]').getBoundingClientRect();
    const top = Math.max(...cards.map(card => card.bottom));
    const line = document.querySelector('[data-values-line]');
    const root = document.querySelector('[data-gallery]');
    const release = document.querySelector('[data-values-cards-release]');
    return {
        scroll: scrollY, cardTop: Math.min(...cards.map(card => card.top)), cardBottom: top,
        titleTop: title.top, gap: title.top - top, visible: top > 0 && title.top < innerHeight,
        expected: +root.dataset.galleryEntryGap,
        lineOpacity: +getComputedStyle(line).opacity,
        release: !!release && document.querySelector('[data-values-cards-viewport]').parentElement === release,
        progress: +root.dataset.galleryProgress,
    };
};
const shifted = async (page, delta) => {
    await page.evaluate(delta => scrollBy(0, delta), delta);
    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
    return page.evaluate(frame);
};

for (const engine of engines) {
    test(`Values cards and Gallery title share one physical scroll handoff in ${engine}`,
        { skip: !enabled }, async () => {
            const runtime = await session(engine);
            const cases = [];
            try {
                for (const lang of ['id', 'en', 'ar']) {
                    await locale(runtime.page, lang);
                    await galleryReady(runtime.page);
                    for (const width of [1280, 1440, 1536]) {
                        await galleryResize(runtime.page, width, 900);
                        const start = +await runtime.page.locator('[data-gallery]').getAttribute('data-gallery-shrink-start');
                        await galleryAt(runtime.page, start * .2);
                        const before = await runtime.page.evaluate(frame);
                        assert.equal(before.release, true, 'native sticky release wraps the viewport');
                        assert.equal(before.visible, true, `both cards and Gallery title on screen: ${lang}/${width} ${JSON.stringify(before)}`);
                        assert.ok(Math.abs(before.gap - before.expected) < 3,
                            `card-body/title physical gap: ${lang}/${width} ${JSON.stringify(before)}`);
                        assert.ok(before.lineOpacity < .01, 'white strand is gone before Gallery title arrives');
                        const next = await shifted(runtime.page, 48);
                        const distance = next.scroll - before.scroll;
                        assert.ok(Math.abs(distance - 48) < 2);
                        assert.ok(Math.abs((before.cardBottom - next.cardBottom) - distance) < 3,
                            `cards move at 1px/scroll: ${JSON.stringify({ before, next })}`);
                        assert.ok(Math.abs((before.titleTop - next.titleTop) - distance) < 3,
                            `Gallery title rises at native speed: ${JSON.stringify({ before, next })}`);
                        assert.ok(Math.abs(next.gap - before.gap) < 3,
                            `no drift: ${JSON.stringify({ before, next })}`);
                        const reverse = await shifted(runtime.page, -48);
                        assert.ok(Math.abs(reverse.cardBottom - before.cardBottom) < 3);
                        assert.ok(Math.abs(reverse.titleTop - before.titleTop) < 3);
                        if (width === 1440) {
                            await runtime.page.screenshot({
                                path: `${directory}/values-gallery-handoff-${engine}-${lang}.jpg`,
                                type: 'jpeg', quality: 85,
                            });
                        }
                        cases.push({ lang, width, before, next, reverse });
                    }
                }
                assert.deepEqual(runtime.errors, []);
                await evidence(`values-gallery-native-handoff-${engine}`, { version: runtime.version, cases });
            } finally { await runtime.close(); }
        });
}
