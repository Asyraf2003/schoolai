import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const fit = () => [...document.querySelectorAll('[data-values-card]')].map(card => {
    const front = card.querySelector('.values__card-front');
    const inner = card.querySelector('.values__card-inner');
    return { width: card.offsetWidth, height: card.offsetHeight,
        fits: front.scrollHeight <= inner.clientHeight + 2 && front.scrollWidth <= inner.clientWidth + 2,
        text: front.textContent.trim(), pose: card.querySelector('[data-values-card-pose]').style.transform };
});
for (const engine of engines) {
    test(`Values cards preserve content through short heights, text expansion and lifecycle in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        page.setDefaultTimeout(15000);
        const cases = [];
        try {
            for (const lang of ['id', 'en', 'ar']) {
                await locale(page, lang);
                for (const [width, height] of [[390, 844], [640, 360], [768, 1024], [1024, 600], [1440, 500], [1920, 1080]]) {
                    await page.setViewportSize({ width, height });
                    await page.evaluate(() => scrollTo(0, document.querySelector('[data-values]').getBoundingClientRect().top + scrollY + innerHeight));
                    await page.waitForTimeout(400);
                    const cards = await page.evaluate(fit);
                    assert.ok(cards.every(card => card.fits), JSON.stringify({ lang, width, height, cards }));
                    assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
                    cases.push({ lang, width, height, cards, flow: await page.locator('[data-values]').getAttribute('data-cards-flow') !== null });
                }
                await page.evaluate(() => document.querySelectorAll('.values__card :is(p,h3,span)').forEach(node => { node.style.fontSize = `${parseFloat(getComputedStyle(node).fontSize) * 2}px`; }));
                await page.waitForTimeout(300);
                const expanded = await page.evaluate(fit);
                assert.ok(expanded.every(card => card.fits), `200% text ${lang}: ${JSON.stringify(expanded)}`);
                assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
                cases.push({ lang, textExpansion: '200% CSS text; not native browser zoom', cards: expanded });
            }
            await locale(page, 'en');
            await page.setViewportSize({ width: 1440, height: 900 });
            await page.evaluate(() => scrollTo(0, document.querySelector('[data-values]').getBoundingClientRect().top + scrollY + 1500));
            await page.waitForFunction(() => document.querySelector('[data-values]').hasAttribute('data-cards-running'));
            await page.evaluate(() => window.dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true })));
            assert.equal(await page.locator('[data-values]').getAttribute('data-cards-running'), null);
            const paused = await page.evaluate(fit);
            await page.evaluate(() => scrollBy(0, 150));
            await page.waitForTimeout(100);
            assert.deepEqual(await page.evaluate(fit), paused);
            await page.evaluate(() => window.dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
            await page.waitForFunction(() => document.querySelector('[data-values]').hasAttribute('data-cards-running'));
            assert.equal(await page.locator('.values__card-float').evaluateAll(nodes => nodes.flatMap(n => n.getAnimations()).length), 4);
            await page.evaluate(() => scrollTo(0, 0));
            await page.waitForFunction(() => !document.querySelector('[data-values]').hasAttribute('data-cards-running'));
            assert.ok(await page.locator('.values__card-float').evaluateAll(nodes => nodes.every(n => n.getAnimations().every(a => a.playState === 'paused'))));
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-cards-lifecycle-${engine}`, { version: runtime.version, cases, float: 'offscreen and pagehide pause; one per card on resume' });
        } finally { await runtime.close(); }
    });

    test(`Values cards fall back without IO, CSS3D, GSAP or decorative media in ${engine}`, { skip: !enabled }, async () => {
        const cases = [];
        for (const failure of ['IO', 'CSS3D', 'GSAP', 'media']) {
            const runtime = await session(engine);
            const { page } = runtime;
            page.setDefaultTimeout(15000);
            try {
                if (failure === 'IO') await page.addInitScript(() => { delete window.IntersectionObserver; });
                if (failure === 'CSS3D') await page.addInitScript(() => {
                    const supports = CSS.supports.bind(CSS);
                    CSS.supports = (...args) => args[0] === 'transform-style' ? false : supports(...args);
                });
                if (failure === 'GSAP') await page.route('**/gsap@*/**', route => route.abort());
                if (failure === 'media') await page.route('**/gallery-ornament-32-v3.webp', route => route.abort());
                for (const lang of ['id', 'en', 'ar']) {
                    await locale(page, lang);
                    await page.evaluate(() => scrollTo(0, document.querySelector('[data-values]').getBoundingClientRect().top + scrollY + 1500));
                    await page.waitForTimeout(600);
                    const cards = await page.evaluate(fit);
                    assert.ok(cards.every(card => card.fits && card.text.length > 40));
                    if (failure !== 'media') {
                        assert.equal(await page.locator('[data-values]').getAttribute('data-cards-motion'), null);
                        assert.ok(cards.every(card => card.pose === ''));
                    }
                    cases.push({ failure, lang, cards });
                }
                if (failure === 'media') await page.screenshot({ path: `${directory}/values-media-fallback-${engine}.png` });
                assert.deepEqual(runtime.errors, []);
            } finally { await runtime.close(); }
        }
        await evidence(`values-cards-fallback-${engine}`, { cases });
    });
}
