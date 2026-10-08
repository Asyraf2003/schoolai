import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';

const inspect = () => {
    const root = document.querySelector('[data-values]');
    const cards = [...root.querySelectorAll('[data-values-card]')];
    const stage = root.querySelector('[data-values-cards-stage]');
    return {
        lang: document.documentElement.lang, width: innerWidth,
        cards: cards.length, titles: cards.map(card => card.querySelector('h3').textContent.trim()),
        numbers: cards.map(card => card.querySelector('.values__card-index').textContent.trim()),
        backs: cards.filter(card => card.querySelector('[aria-hidden="true"].values__card-back')).length,
        motion: root.dataset.cardsMotion === 'true',
        line: root.hasAttribute('data-line-ready'),
        columns: getComputedStyle(stage).gridTemplateColumns.split(' ').length,
        track: getComputedStyle(root.querySelector('[data-values-cards-track]')).position,
        overflow: document.documentElement.scrollWidth > innerWidth,
        transforms: cards.map(card => getComputedStyle(card.querySelector('[data-values-card-inner]')).transform),
        font: getComputedStyle(cards[0]).fontFamily,
        cardFit: cards.map(card => {
            const front = card.querySelector('.values__card-front');
            return { cardHeight: card.offsetHeight, frontHeight: front.scrollHeight,
                fits: front.scrollHeight <= card.offsetHeight + 2 };
        }),
    };
};

for (const engine of engines) {
    test(`V2 Values cards remain localized and scroll-reversible in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            for (const lang of ['id', 'en', 'ar']) {
                await locale(page, lang);
                for (const width of [360, 768, 1024, 1280, 1440, 1536]) {
                    await page.setViewportSize({ width, height: 900 });
                    await page.evaluate(() => {
                        const values = document.querySelector('[data-values]');
                        scrollTo(0, values.getBoundingClientRect().top + scrollY + innerHeight * 2.3);
                    });
                    await page.waitForTimeout(180);
                    const state = await page.evaluate(inspect);
                    assert.equal(state.cards, 4);
                    assert.deepEqual(state.numbers, ['01', '02', '03', '04']);
                    assert.equal(state.backs, 4);
                    assert.equal(state.titles.every(Boolean), true);
                    assert.equal(state.overflow, false, `${lang}/${width} horizontal overflow`);
                    assert.ok(state.cardFit.every(fit => fit.fits), `${lang}/${width} card content exceeds card bounds: ${JSON.stringify(state.cardFit)}`);
                    if (lang === 'ar') assert.match(state.font, /Cairo/);
                    if (state.line) {
                        assert.equal(state.track, 'absolute');
                        assert.equal(state.motion, true);
                        assert.equal(state.columns, width >= 1280 ? 4 : width >= 768 ? 2 : 1);
                    } else {
                        assert.equal(state.track, 'relative', 'no GSAP keeps cards in readable flow');
                    }
                    if (state.line && width === 1440) {
                        await page.evaluate(() => scrollBy(0, innerHeight * .7));
                        await page.waitForTimeout(100);
                        const later = await page.evaluate(inspect);
                        assert.notDeepEqual(later.transforms, state.transforms);
                        await page.evaluate(() => scrollBy(0, -innerHeight * .7));
                        await page.waitForTimeout(100);
                        const restored = await page.evaluate(inspect);
                        assert.deepEqual(restored.transforms, state.transforms, 'upward scroll reverses card flip');
                    }
                    cases.push(state);
                }
            }
            await page.emulateMedia({ reducedMotion: 'reduce' });
            await page.waitForTimeout(100);
            const reduced = await page.evaluate(inspect);
            assert.equal(reduced.motion, false);
            assert.equal(reduced.track, 'relative');
            assert.ok(reduced.transforms.every(transform => transform === 'none'));
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-cards-${engine}`, { cases, reduced });
        } finally { await runtime.close(); }
    });

    test(`V2 Values cards remain readable without JavaScript in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { javaScriptEnabled: false });
        try {
            await runtime.page.waitForLoadState('load');
            await runtime.page.waitForFunction(() => getComputedStyle(document.querySelector('[data-values-cards-track]')).position === 'relative');
            const state = await runtime.page.evaluate(inspect);
            assert.equal(state.cards, 4);
            assert.equal(state.track, 'relative');
            assert.equal(state.motion, false);
            assert.ok(state.transforms.every(transform => transform === 'none'));
        } finally { await runtime.close(); }
    });
}
