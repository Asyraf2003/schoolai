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
        assert.deepEqual(runtime.errors, []);
        await evidence(`testimonials-scroll-${engine}`, { version: runtime.version, profiles });
    } finally { await runtime.close(); }
});
