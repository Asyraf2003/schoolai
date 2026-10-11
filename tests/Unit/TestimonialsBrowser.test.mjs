import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt, galleryResize } from './GalleryBrowserSupport.mjs';

const paint = page => page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
const inspect = () => {
    const root = document.querySelector('[data-testimonials]');
    const h = root.querySelector('h2');
    const copy = root.querySelector('[data-testimonials-copy]');
    const rows = root.querySelector('[data-testimonials-rows]');
    const rect = e => { const b = e.getBoundingClientRect(); return { x: b.x, y: b.y, width: b.width, height: b.height, bottom: b.bottom }; };
    return {
        viewport: [innerWidth, innerHeight], scrollY, enabled: root.hasAttribute('data-testimonials-enabled'),
        background: getComputedStyle(root).backgroundColor, rowsBackground: getComputedStyle(rows).backgroundColor,
        heading: rect(h), align: getComputedStyle(h).textAlign, font: getComputedStyle(h).fontFamily,
        leading: parseFloat(getComputedStyle(h).lineHeight), color: getComputedStyle(h).color,
        opacity: +getComputedStyle(copy).opacity, rows: rect(rows), root: rect(root),
        overflow: document.documentElement.scrollWidth > innerWidth,
        offsets: [...root.querySelectorAll('[data-testimonials-track]')].map(t => new DOMMatrix(getComputedStyle(t).transform).m41),
        cards: [...root.querySelectorAll('article')].map(c => ({ ...rect(c), content: rect(c.querySelector('.testimonials__content')), quote: rect(c.querySelector('blockquote')), cite: rect(c.querySelector('footer')) })),
    };
};
async function at(page, progress) {
    await page.evaluate(p => {
        const intro = document.querySelector('[data-testimonials-intro]');
        const box = intro.getBoundingClientRect();
        scrollTo(0, box.top + scrollY + p * (box.height - innerHeight));
    }, progress);
    await paint(page);
    return page.evaluate(inspect);
}
const widths = [360, 390, 640, 768, 1024, 1280, 1440, 1536, 1920];
const boundaries = [639, 767, 1023, 1279, 1535];

for (const engine of engines) {
    test(`Gallery to OLD testimonial wall is continuous, readable and reversible in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            if (process.env.PROGRAM_TESTIMONIAL_FIXTURES) await page.route('**/site/testimonials/testi-*-v1.webp', route => route.fulfill({
                path: `${process.env.PROGRAM_TESTIMONIAL_FIXTURES}/${new URL(route.request().url()).pathname.split('/').at(-1)}`, contentType: 'image/webp',
            }));
            for (const lang of ['id', 'en', 'ar']) {
                await locale(page, lang); await galleryReady(page);
                for (const width of [...widths, ...boundaries]) {
                    await galleryResize(page, width, 900);
                    const black = await galleryAt(page, .9);
                    assert.ok(black.items.every(item => item.opacity < .01));
                    assert.ok(black.testimonial.y >= 900, 'ending Gallery remains untouched');
                    const start = await at(page, 0);
                    assert.equal(start.opacity, 0);
                    const reveal = await at(page, .5);
                    assert.ok(reveal.opacity > .45 && reveal.opacity < 1, `scroll reveal ${lang}/${width}`);
                    const channel = parseFloat(reveal.background.match(/[\d.]+/)[0]) / 255;
                    assert.ok((((channel + .055) / 1.055) ** 2.4 + .05) / .05 >= 7, 'actual black-title contrast');
                    const full = await at(page, 1);
                    assert.equal(full.background, 'rgb(255, 255, 255)');
                    assert.equal(full.opacity, 1); assert.equal(full.color, 'rgb(0, 0, 0)');
                    assert.equal(full.overflow, false); assert.equal(full.align, 'start');
                    const lines = full.heading.height / full.leading;
                    assert.ok(lines >= 1.9 && lines <= 3.1, `2–3 headline lines ${lang}/${width}: ${lines}`);
                    if (lang === 'ar') assert.match(full.font, /Cairo/);
                    assert.ok(full.cards.every(card => card.quote.bottom <= card.cite.y + 1 && card.cite.bottom <= card.bottom + 1), `quotes fit ${lang}/${width}: ${JSON.stringify(full.cards.filter(card => card.quote.bottom > card.cite.y + 1 || card.cite.bottom > card.bottom + 1))}`);
                    const reverse = await at(page, .5);
                    assert.ok(Math.abs(reverse.opacity - reveal.opacity) < .01);
                    await page.evaluate(() => {
                        const rows = document.querySelector('[data-testimonials-rows]');
                        scrollTo(0, rows.getBoundingClientRect().top + scrollY - innerHeight * .8);
                    });
                    await paint(page);
                    const wallStart = await page.evaluate(inspect);
                    await page.evaluate(() => scrollBy(0, 250)); await paint(page);
                    const wallEnd = await page.evaluate(inspect);
                    const sign = lang === 'ar' ? 1 : -1;
                    assert.ok((wallEnd.offsets[0] - wallStart.offsets[0]) * sign > 0);
                    assert.ok((wallEnd.offsets[1] - wallStart.offsets[1]) * sign < 0);
                    await page.evaluate(() => scrollTo(0, document.scrollingElement.scrollHeight)); await paint(page);
                    const end = await page.evaluate(inspect);
                    assert.equal(end.background, 'rgb(255, 255, 255)');
                    assert.ok(end.rows.bottom <= end.viewport[1] * .35 + 2, 'clean white ending');
                    cases.push({ lang, width, reveal, full, end });
                    if ([390, 1440].includes(width)) {
                        await at(page, .65);
                        await page.screenshot({ path: `${directory}/testimonials-${engine}-${lang}-${width}-title.jpg`, type: 'jpeg', quality: 85 });
                        await page.evaluate(() => { const rows = document.querySelector('[data-testimonials-rows]'); scrollTo(0, rows.getBoundingClientRect().top + scrollY - 150); });
                        await paint(page);
                        await page.screenshot({ path: `${directory}/testimonials-${engine}-${lang}-${width}-wall.jpg`, type: 'jpeg', quality: 85 });
                    }
                    console.info(`Testimonials ${engine}/${lang}/${width} PASS`);
                }
            }
            const row = page.locator('[data-testimonials-row]').first();
            await row.focus(); await paint(page);
            assert.equal(await row.getAttribute('data-testimonials-manual'), '');
            const before = await row.evaluate(e => e.scrollLeft);
            await page.keyboard.press('ArrowLeft'); await page.waitForTimeout(200);
            assert.notEqual(await row.evaluate(e => e.scrollLeft), before, 'RTL keyboard can browse every card');
            await page.emulateMedia({ reducedMotion: 'reduce' }); await paint(page);
            const reduced = await page.evaluate(inspect);
            assert.equal(reduced.enabled, false); assert.equal(reduced.opacity, 1);
            assert.deepEqual(reduced.offsets, [0, 0, 0]);
            assert.deepEqual(runtime.errors, []);
            await evidence(`testimonials-${engine}`, { version: runtime.version, cases, reduced });
        } finally { await runtime.close(); }
    });
    test(`testimonial content survives no-JS and short viewport in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { javaScriptEnabled: false, viewport: { width: 390, height: 500 } });
        try {
            await runtime.page.locator('[data-testimonials]').scrollIntoViewIfNeeded();
            const state = await runtime.page.evaluate(inspect);
            assert.equal(state.enabled, false); assert.equal(state.opacity, 1);
            assert.equal(state.cards.length, 21); assert.equal(state.overflow, false);
            assert.equal(state.background, 'rgb(255, 255, 255)');
        } finally { await runtime.close(); }
    });
}

for (const engine of engines) test(`Testimonials adapts during locale, orientation, text expansion and lifecycle in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine, { hasTouch: true });
    const { page } = runtime;
    const switches = [];
    try {
        for (const [from, to] of [['id', 'en'], ['en', 'id'], ['id', 'ar'], ['ar', 'id'], ['en', 'ar'], ['ar', 'en']]) {
            await locale(page, from); await galleryReady(page); await at(page, .5);
            await locale(page, to); await galleryReady(page); await at(page, .5);
            const state = await page.evaluate(inspect);
            assert.equal(state.overflow, false);
            assert.equal(await page.locator('html').getAttribute('dir'), to === 'ar' ? 'rtl' : 'ltr');
            switches.push({ from, to, background: state.background, font: state.font });
        }
        for (const [width, height] of [[390, 500], [768, 600], [1536, 500], [639, 700], [640, 700], [1023, 600], [1024, 600]]) {
            await galleryResize(page, width, height);
            await at(page, .6);
            const state = await page.evaluate(inspect);
            assert.equal(state.overflow, false);
            assert.ok(state.heading.width <= width && state.heading.height > 0);
        }
        await galleryResize(page, 390, 844); await at(page, .7);
        await page.locator('[data-testimonials] h2').evaluate(h => { h.style.fontSize = `${parseFloat(getComputedStyle(h).fontSize) * 2}px`; });
        await paint(page);
        assert.equal((await page.evaluate(inspect)).overflow, false, '200% title expansion remains in semantic flow');
        await page.locator('[data-testimonials] h2').evaluate(h => h.style.removeProperty('font-size'));
        const row = page.locator('[data-testimonials-row]').first();
        await row.scrollIntoViewIfNeeded(); await row.tap({ position: { x: 50, y: 30 } });
        assert.equal(await row.getAttribute('data-testimonials-manual'), '', 'touch retains native access to complete quotes');
        await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true })));
        assert.equal(await page.locator('[data-testimonials]').getAttribute('data-testimonials-active'), null);
        await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
        await at(page, .7);
        assert.equal((await page.evaluate(inspect)).opacity, 1);
        await page.emulateMedia({ reducedMotion: 'reduce' }); await paint(page);
        assert.equal((await page.evaluate(inspect)).enabled, false);
        await page.emulateMedia({ reducedMotion: 'no-preference' }); await paint(page); await at(page, .7);
        assert.equal((await page.evaluate(inspect)).opacity, 1);
        await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: false })));
        const disposed = await page.evaluate(inspect);
        assert.equal(disposed.enabled, false); assert.equal(disposed.opacity, 1);
        assert.deepEqual(runtime.errors, []);
        await evidence(`testimonials-lifecycle-${engine}`, { version: runtime.version, switches, disposed, syntheticPageTransitions: true });
    } finally { await runtime.close(); }
});
