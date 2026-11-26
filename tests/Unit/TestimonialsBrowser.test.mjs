import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const widths = [360, 390, 639, 640, 767, 768, 1023, 1024, 1279, 1280, 1440, 1535, 1536, 1920];
const testimonial = '[data-testimonial-card]';
const fixtureURL = 'https://www.instagram.com/p/TEST_FIXTURE/?comment_id=4321';
const shot = (page, name) => page.screenshot({ path: `${directory}/testimonials-${name}.jpg`, type: 'jpeg', quality: 90 });
function inspect() {
    const root = document.querySelector('[data-testimonials]');
    const wall = root.querySelector('[data-testimonials-wall]');
    const bounds = wall.getBoundingClientRect();
    const rows = [...wall.querySelectorAll('[data-testimonials-row]')];
    const cards = rows.flatMap(row => [...row.children]);
    const first = rows.map(row => row.firstElementChild.getBoundingClientRect());
    const rowBoxes = rows.map(row => row.getBoundingClientRect());
    const edge = getComputedStyle(wall, '::before');
    const horizontal = wall.closest('[data-testimonials-scroll]') !== null;
    return {
        direction: getComputedStyle(wall).direction,
        font: getComputedStyle(root).fontFamily,
        horizontal, counts: rows.map(row => row.childElementCount),
        cardRatio: rowBoxes[0].width / (first[0].width + parseFloat(getComputedStyle(rows[0]).columnGap)),
        stagger: first[1].top - first[0].top,
        firstX: rowBoxes[0].left, lastX: rowBoxes[1].left,
        fits: rowBoxes.every(rect => rect.left >= bounds.left - 1 && rect.right <= bounds.right + 1),
        blur: edge.backdropFilter || edge.webkitBackdropFilter, edgeWidth: parseFloat(edge.width),
        edgeMask: edge.maskImage, edgePointer: edge.pointerEvents,
        titleFits: root.querySelector('h2').scrollWidth <= root.querySelector('h2').clientWidth + 1,
        quotesFit: cards.every(card => {
            const quote = card.querySelector('blockquote');
            return quote.scrollWidth <= quote.clientWidth + 1 && quote.getBoundingClientRect().bottom <= card.getBoundingClientRect().bottom;
        }),
        overflow: root.scrollWidth > root.clientWidth + 1 || document.documentElement.scrollWidth > innerWidth,
        links: wall.querySelectorAll('a, button').length,
        tabStops: wall.querySelectorAll('[tabindex="0"]').length,
    };
}
const firstCard = page => page.locator(testimonial).first();
const lighting = card => card.evaluate(e => {
    const light = e.querySelector('.testimonials__lighting');
    const style = getComputedStyle(e);
    const { width, height } = e.getBoundingClientRect();
    return {
        opacity: +getComputedStyle(light).opacity, clip: getComputedStyle(light).overflow,
        left: getComputedStyle(light, '::before').transform, right: getComputedStyle(light, '::after').transform,
        animation: getComputedStyle(light, '::before').animationName, background: style.backgroundColor,
        border: style.borderColor, width, height, rim: parseFloat(getComputedStyle(light, '::before').width),
    };
});
async function openSection(page) {
    await page.locator('[data-testimonials]').evaluate(root => {
        scrollTo(0, scrollY + root.getBoundingClientRect().top - 60);
    });
    await page.evaluate(() => Promise.all([...document.querySelectorAll('[data-testimonial-card] img')]
        .filter(img => {
            const box = img.getBoundingClientRect();
            const rail = img.closest('[data-testimonials-row]').getBoundingClientRect();
            return box.top < innerHeight && box.bottom > 0 && box.left < rail.right && box.right > rail.left;
        }).map(img => img.decode())));
}
async function linkFixture(page) {
    const card = firstCard(page);
    await card.evaluate((e, href) => {
        const icon = e.querySelector('.testimonials__source-icon');
        const link = document.createElement('a');
        link.className = 'testimonials__link';
        link.dataset.testimonialLink = '';
        link.href = href;
        link.setAttribute('aria-label', 'Open Instagram source for test fixture');
        link.append(...icon.childNodes);
        icon.replaceWith(link);
    }, fixtureURL);
    await page.route(fixtureURL, route => route.fulfill({ body: '<title>Instagram fixture</title>' }));
    return card.getByRole('link', { name: 'Open Instagram source for test fixture' });
}
for (const engine of engines) {
    test(`Testimonials layout, locale switches, zoom and hover in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const matrix = [];
        try {
            for (const language of ['id', 'en', 'ar', 'id', 'ar', 'en', 'id']) {
                await locale(page, language);
                const rtl = language === 'ar';
                for (const width of matrix.some(cell => cell.language === language) ? [390] : widths) {
                    await page.setViewportSize({ width, height: 900 });
                    const state = await page.evaluate(inspect);
                    assert.equal(state.direction, rtl ? 'rtl' : 'ltr');
                    assert.match(state.font, rtl ? /Cairo/ : /Inter/);
                    assert.equal(state.horizontal, width >= 1024);
                    assert.deepEqual(state.counts, [11, 10]);
                    assert.ok(state.fits && state.titleFits && state.quotesFit && !state.overflow, JSON.stringify({ language, width, state }));
                    assert.equal(state.links, 0);
                    assert.equal(state.tabStops, width >= 1024 ? 2 : 0);
                    if (state.horizontal) {
                        assert.ok(state.cardRatio > 2.4 && state.cardRatio < 2.6);
                        assert.ok(state.stagger > 100);
                    } else {
                        assert.ok(rtl ? state.firstX > state.lastX : state.firstX < state.lastX);
                        assert.ok(state.stagger >= 15);
                    }
                    if (width < 768) {
                        assert.equal(state.blur, 'blur(2px)');
                        assert.match(state.edgeMask, /linear-gradient/);
                        assert.ok(state.edgeWidth < width * .08 && state.edgePointer === 'none');
                    } else assert.equal(state.blur, 'none');
                    matrix.push({ language, width, ...state });
                    if ([390, 1440].includes(width) && matrix.filter(cell => cell.language === language && cell.width === width).length === 1) {
                        await openSection(page);
                        await shot(page, `${engine}-${language}-${width}-idle`);
                    }
                }
            }
            await page.setViewportSize({ width: 1440, height: 900 });
            await openSection(page);
            const heading = page.locator('#testimonials-title');
            assert.ok((await page.locator('[data-testimonials]').ariaSnapshot()).includes(await heading.textContent()));
            const card = page.locator(testimonial).nth(1);
            const idle = await lighting(card);
            assert.equal(idle.opacity, 0);
            assert.equal(idle.animation, 'none');
            assert.equal(idle.background, 'rgb(255, 255, 255)');
            await card.hover({ position: { x: idle.width / 2, y: 9 } });
            await page.waitForFunction(() => Number(getComputedStyle(document.querySelectorAll('.testimonials__lighting')[1]).opacity) > .64);
            const hover = await lighting(card);
            assert.equal(hover.animation, 'testimonial-light-drift');
            assert.equal(hover.clip, 'hidden');
            assert.ok(hover.rim <= hover.width * .13, 'the tint is confined to each side');
            assert.deepEqual([hover.width, hover.height, hover.border], [idle.width, idle.height, idle.border]);
            assert.notEqual(hover.left, hover.right);
            await shot(page, `${engine}-hover`);
            assert.notEqual((await lighting(card)).left, hover.left);
            await card.click({ position: { x: idle.width - 8, y: idle.height - 9 } });
            assert.equal(new URL(page.url()).pathname, '/');
            await page.emulateMedia({ reducedMotion: 'reduce' });
            await openSection(page);
            await card.hover({ position: { x: idle.width / 2, y: 9 } });
            const reduced = await lighting(card);
            assert.equal(reduced.opacity, 0);
            assert.equal(reduced.animation, 'none');
            assert.equal((await page.evaluate(inspect)).horizontal, false);
            await shot(page, `${engine}-reduced`);
            await page.setViewportSize({ width: 390, height: 500 });
            await heading.evaluate(e => { e.style.fontSize = `${parseFloat(getComputedStyle(e).fontSize) * 2}px`; });
            await page.locator('[data-testimonials-wall]').evaluate(wall => wall.querySelectorAll('blockquote').forEach(q => { q.style.fontSize = '2rem'; }));
            const expanded = await page.evaluate(inspect);
            assert.ok(expanded.titleFits && expanded.quotesFit && !expanded.overflow);
            const link = await linkFixture(page);
            await page.locator('[data-stories-copy] a').last().focus();
            await page.keyboard.press('Tab');
            assert.ok(await link.evaluate(e => e === document.activeElement));
            assert.equal(await link.evaluate(e => getComputedStyle(e, '::after').outlineStyle), 'solid');
            await page.keyboard.press('Enter');
            await page.waitForURL(fixtureURL);
            assert.deepEqual(runtime.errors, []);
            await evidence(`testimonials-layout-${engine}`, { version: runtime.version, matrix, idle, hover, reduced, expanded });
        } finally { await runtime.close(); }
    });
    test(`Testimonials touch effects, delayed source and lifecycle in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { hasTouch: true, isMobile: true, viewport: { width: 390, height: 844 } });
        const { page } = runtime;
        try {
            await locale(page, 'id');
            await openSection(page);
            const card = firstCard(page);
            const startURL = page.url();
            await card.tap();
            assert.equal(await card.getAttribute('data-testimonial-lit'), '');
            await shot(page, `${engine}-touch`);
            await page.waitForFunction(() => !document.querySelector('[data-testimonial-lit]'));
            assert.equal(page.url(), startURL);
            const link = await linkFixture(page);
            await link.tap();
            assert.equal(await card.getAttribute('data-testimonial-lit'), '');
            await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true })));
            assert.equal(await card.getAttribute('data-testimonial-lit'), null);
            await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
            await page.setViewportSize({ width: 844, height: 390 });
            assert.equal((await page.evaluate(inspect)).horizontal, false);
            const before = performance.now();
            await link.tap();
            assert.equal(page.url(), startURL);
            await page.waitForURL(fixtureURL);
            const touchElapsed = Math.round(performance.now() - before);
            assert.ok(touchElapsed >= 690, `observed ${touchElapsed} ms including Playwright and navigation`);
            assert.deepEqual(runtime.errors, []);
            await evidence(`testimonials-touch-${engine}`, { version: runtime.version, touchElapsed, syntheticPageTransitions: true });
        } finally { await runtime.close(); }
    });
    test(`Testimonials static content without JS in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { javaScriptEnabled: false, viewport: { width: 390, height: 500 } });
        try {
            assert.equal(await runtime.page.locator(`${testimonial} blockquote`).count(), 21);
            assert.ok((await runtime.page.evaluate(inspect)).quotesFit);
            assert.equal((await lighting(firstCard(runtime.page))).opacity, 0);
        } finally { await runtime.close(); }
    });
}
