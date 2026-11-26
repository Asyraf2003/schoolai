import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const paint = page => page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));

function inspect() {
    const stories = document.querySelector('[data-stories]').getBoundingClientRect();
    const root = document.querySelector('[data-testimonials]');
    const wall = root.getBoundingClientRect();
    const header = root.querySelector('h2').getBoundingClientRect();
    const footer = document.querySelector('[data-footer-v2]').getBoundingClientRect();
    return {
        scroll: scrollY, storiesEnd: stories.bottom, wallStart: wall.top, wallEnd: wall.bottom,
        titleTop: header.top, titleBottom: header.bottom, footerStart: footer.top, viewport: innerHeight,
    };
}

async function seam(page, selector, edge, y) {
    await page.locator(selector).evaluate((element, [edge, y]) => {
        scrollTo(0, scrollY + element.getBoundingClientRect()[edge] - y);
    }, [edge, y]);
    await paint(page);
    return page.evaluate(inspect);
}

async function wallAt(page, progress) {
    await page.locator('[data-testimonials-scroll]').evaluate((scene, progress) => {
        const inner = scene.firstElementChild;
        const top = parseFloat(getComputedStyle(inner).insetBlockStart);
        scrollTo(0, scrollY + scene.getBoundingClientRect().top - top + progress * (scene.offsetHeight - inner.offsetHeight));
    }, progress);
    await paint(page);
    return page.locator('[data-testimonials-row]').evaluateAll(rows => rows.map(row => ({
        offset: row.scrollLeft,
        range: row.scrollWidth - row.clientWidth,
        top: row.getBoundingClientRect().top,
        visibleCards: [...row.children].filter(card => {
            const bounds = row.getBoundingClientRect();
            const box = card.getBoundingClientRect();
            return box.right > bounds.left && box.left < bounds.right;
        }).length,
    })));
}

for (const engine of engines) test(`Stories–Testimonials–Footer remains in native flow in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine);
    const { page } = runtime;
    const profiles = [];
    try {
        await locale(page, 'id');
        for (const width of [390, 1440]) {
            await page.setViewportSize({ width, height: 900 });
            await page.mouse.move(0, 0);
            const enter = await seam(page, '[data-stories]', 'bottom', 520);
            assert.ok(Math.abs(enter.storiesEnd - enter.wallStart) < 1, 'Stories ends exactly where Testimonials starts');
            assert.ok(enter.titleTop > enter.wallStart && enter.titleBottom < enter.viewport);
            await page.evaluate(() => Promise.allSettled([...document.querySelectorAll('.stories__feature-poster')].map(image => image.decode())));
            await page.evaluate(() => Promise.all([...document.querySelectorAll('[data-testimonial-card] img')]
                .filter(image => image.getBoundingClientRect().top < innerHeight).map(image => image.decode())));
            await page.screenshot({ path: `${directory}/testimonials-${engine}-${width}-from-stories.jpg`, type: 'jpeg', quality: 90 });
            await page.mouse.move(width / 2, 300);
            await page.mouse.wheel(0, width === 390 ? 1300 : 640);
            await page.waitForFunction(y => scrollY > y + 300, enter.scroll);
            const forward = await page.evaluate(inspect);
            assert.ok(forward.titleTop < enter.titleTop && forward.wallEnd > 0);
            await page.mouse.wheel(0, -900);
            await page.waitForFunction(y => scrollY < y, forward.scroll);
            const reverse = await page.evaluate(inspect);
            assert.ok(reverse.titleTop > forward.titleTop);
            await page.mouse.move(0, 0);
            const exit = await seam(page, '[data-testimonials]', 'bottom', 460);
            assert.ok(Math.abs(exit.wallEnd - exit.footerStart) < 1, 'footer starts at the end of the masonry');
            assert.ok(exit.footerStart > 0 && exit.footerStart < exit.viewport);
            await page.evaluate(() => Promise.allSettled([...document.querySelectorAll('[data-testimonial-card] img')]
                .filter(image => { const r = image.getBoundingClientRect(); return r.bottom > 0 && r.top < innerHeight; })
                .map(image => image.decode())));
            await page.screenshot({ path: `${directory}/testimonials-${engine}-${width}-to-footer.jpg`, type: 'jpeg', quality: 90 });
            assert.equal(new URL(page.url()).pathname, '/');
            profiles.push({ width, enter, forward, reverse, exit });
        }
        const rails = [];
        for (const language of ['id', 'ar']) {
            await locale(page, language);
            await page.setViewportSize({ width: 1440, height: 900 });
            const start = await wallAt(page, 0);
            const mid = await wallAt(page, .46);
            const end = await wallAt(page, 1);
            const reverse = await wallAt(page, .46);
            const sign = language === 'ar' ? -1 : 1;
            assert.ok(Math.abs(start[0].offset) <= 2 && Math.abs(start[1].offset - sign * start[1].range) <= 2);
            assert.ok(Math.abs(end[1].offset) <= 4 && Math.abs(end[0].offset - sign * end[0].range) <= 4);
            assert.ok(mid[0].offset * sign > start[0].offset * sign && mid[1].offset * sign < start[1].offset * sign);
            assert.ok(mid.every((row, index) => Math.abs(row.top - start[index].top) <= 2
                && Math.abs(row.offset - reverse[index].offset) <= 2 && row.visibleCards >= 3 && row.visibleCards <= 4));
            await page.evaluate(() => Promise.all([...document.querySelectorAll('[data-testimonial-card] img')]
                .filter(image => {
                    const r = image.getBoundingClientRect();
                    const row = image.closest('[data-testimonials-row]').getBoundingClientRect();
                    return r.right > row.left && r.left < row.right;
                }).map(image => image.decode())));
            await page.screenshot({ path: `${directory}/testimonials-${engine}-${language}-brick-mid.jpg`, type: 'jpeg', quality: 92 });
            rails.push({ language, start, mid, end, reverse });
        }
        await locale(page, 'id');
        const start = await wallAt(page, 0);
        const row = page.locator('[data-testimonials-row]').first();
        await row.evaluate(e => e.focus({ preventScroll: true }));
        await page.keyboard.press('ArrowRight');
        await page.waitForFunction(initial => document.querySelector('[data-testimonials-row]').scrollLeft > initial + 5, start[0].offset);
        assert.deepEqual(runtime.errors, []);
        await evidence(`testimonials-scroll-${engine}`, { version: runtime.version, profiles, rails });
    } finally { await runtime.close(); }
});
