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
        flow: root.hasAttribute('data-cards-flow'),
        columns: getComputedStyle(stage).gridTemplateColumns.split(' ').length,
        track: getComputedStyle(root.querySelector('[data-values-cards-track]')).position,
        overflow: document.documentElement.scrollWidth > innerWidth,
        transforms: cards.map(card => getComputedStyle(card.querySelector('[data-values-card-inner]')).transform),
        faces: cards.map(card => ({ facing: card.dataset.cardFace,
            front: getComputedStyle(card.querySelector('.values__card-front')).opacity,
            back: getComputedStyle(card.querySelector('.values__card-back')).opacity })),
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
        page.setDefaultTimeout(15000);
        const cases = [];
        try {
            for (const lang of ['id', 'en', 'ar']) {
                await locale(page, lang);
                await page.evaluate(() => scrollTo(0, document.querySelector('[data-values]').getBoundingClientRect().top + scrollY));
                await page.waitForFunction(() => document.querySelector('[data-values]').hasAttribute('data-line-ready'));
                for (const width of [360, 390, 639, 640, 767, 768, 1023, 1024, 1279, 1280, 1440, 1535, 1536, 1920]) {
                    await page.setViewportSize({ width, height: 900 });
                    await page.evaluate(() => {
                        const track = document.querySelector('[data-values-cards-track]');
                        scrollTo(0, track.getBoundingClientRect().top + scrollY + (innerWidth >= 1280 ? 600 : 0));
                    });
                    await page.waitForFunction(() => {
                        const root = document.querySelector('[data-values]');
                        return !root.hasAttribute('data-line-ready') || root.hasAttribute('data-cards-flow') || root.dataset.cardsMotion === 'true';
                    });
                    await page.waitForTimeout(350);
                    const state = await page.evaluate(inspect);
                    assert.equal(state.cards, 4);
                    assert.deepEqual(state.numbers, ['01', '02', '03', '04']);
                    assert.equal(state.backs, 4);
                    assert.ok(state.faces.every(face => face.facing === 'back'
                        ? face.front === '0' && face.back === '1'
                        : face.front === '1' && face.back === '0'), 'only the facing side paints');
                    assert.equal(state.titles.every(Boolean), true);
                    assert.equal(state.overflow, false, `${lang}/${width} horizontal overflow`);
                    assert.ok(state.cardFit.every(fit => fit.fits), `${lang}/${width} card content exceeds card bounds: ${JSON.stringify(state.cardFit)}`);
                    if (lang === 'ar') assert.match(state.font, /Cairo/);
                    if (state.line && !state.flow) {
                        assert.equal(state.track, width >= 1280 ? 'absolute' : 'relative');
                        assert.equal(state.motion, true, JSON.stringify(state));
                        assert.equal(state.columns, width >= 1280 ? 4 : width >= 768 ? 2 : 1);
                    } else {
                        assert.equal(state.track, 'relative', 'missing motion capability or insufficient viewport height keeps cards readable');
                    }
                    if (state.line && width === 1440) {
                        const move = async progress => {
                            await page.evaluate(progress => {
                                const track = document.querySelector('[data-values-cards-track]');
                                const viewport = document.querySelector('[data-values-cards-viewport]');
                                scrollTo(0, track.getBoundingClientRect().top + scrollY - 72
                                    + progress * (track.offsetHeight - viewport.offsetHeight));
                            }, progress);
                            await page.waitForFunction(progress => {
                                const root = document.querySelector('[data-values]');
                                const track = root.querySelector('[data-values-cards-track]');
                                const viewport = root.querySelector('[data-values-cards-viewport]');
                                const target = Math.max(0, Math.min(1, (parseFloat(getComputedStyle(root).scrollMarginBlockStart) - track.getBoundingClientRect().top)
                                    / (track.offsetHeight - viewport.offsetHeight)));
                                return Math.abs(target - progress) < .0004
                                    && Math.abs(Number(root.dataset.cardsProgress) - target) < .00001;
                            }, progress);
                            return page.evaluate(inspect);
                        };
                        const early = await move(.08);
                        const later = await move(.2);
                        assert.notDeepEqual(later.transforms, early.transforms);
                        const restored = await move(.08);
                        assert.deepEqual(restored.transforms, early.transforms, 'upward scroll reverses card flip');
                    }
                    cases.push(state);
                }
            }
            await page.emulateMedia({ reducedMotion: 'reduce' });
            await page.waitForFunction(() => {
                const root = document.querySelector('[data-values]');
                return !root.hasAttribute('data-cards-motion')
                    && getComputedStyle(root.querySelector('[data-values-cards-track]')).position === 'relative';
            });
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
