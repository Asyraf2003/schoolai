import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const paint = page => page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
const visibleAvatarsReady = page => page.waitForFunction(() => [...document.querySelectorAll('[data-testimonial-card] img')]
    .filter(image => {
        const card = image.closest('[data-testimonial-card]');
        if (card.hidden) return false;
        const imageBox = image.getBoundingClientRect();
        const rowBox = card.closest('[data-testimonials-row]').getBoundingClientRect();
        return imageBox.bottom > 0 && imageBox.top < innerHeight && imageBox.right > rowBox.left && imageBox.left < rowBox.right;
    }).every(image => image.complete && image.naturalWidth > 0));

function inspect() {
    const gallery = document.querySelector('[data-gallery]').getBoundingClientRect();
    const root = document.querySelector('[data-testimonials]');
    const wall = root.getBoundingClientRect();
    const header = root.querySelector('h2').getBoundingClientRect();
    const stories = document.querySelector('[data-stories]').getBoundingClientRect();
    return {
        scroll: scrollY, galleryEnd: gallery.bottom, wallStart: wall.top, wallEnd: wall.bottom,
        titleTop: header.top, titleBottom: header.bottom, storiesStart: stories.top, viewport: innerHeight,
    };
}

async function seam(page, selector, edge, y) {
    await page.locator(selector).evaluate((element, [edge, y]) => {
        scrollTo(0, scrollY + element.getBoundingClientRect()[edge] - y);
    }, [edge, y]);
    await paint(page);
    return page.evaluate(inspect);
}

for (const engine of engines) test(`Gallery–Testimonials–Stories remains in native flow in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine);
    const { page } = runtime;
    const profiles = [];
    try {
        await locale(page, 'id');
        for (const width of [390, 1440]) {
            await page.setViewportSize({ width, height: 900 });
            await page.waitForFunction(mode => document.querySelector('[data-testimonials-wall]').dataset.testimonialsMode === mode, width === 390 ? 'rotation' : 'loop');
            await page.mouse.move(0, 0);
            const enter = await seam(page, '[data-gallery]', 'bottom', 390);
            assert.ok(Math.abs(enter.galleryEnd - enter.wallStart) < 1, 'Gallery ends exactly where Testimonials starts');
            assert.ok(enter.titleTop > enter.wallStart && enter.titleBottom < enter.viewport, JSON.stringify({ width, enter }));
            await visibleAvatarsReady(page);
            await page.screenshot({ path: `${directory}/testimonials-${engine}-${width}-from-gallery.jpg`, type: 'jpeg', quality: 90 });
            await page.mouse.move(width / 2, 300);
            await page.mouse.wheel(0, width === 390 ? 480 : 640);
            await page.waitForFunction(y => scrollY > y + 300, enter.scroll);
            const forward = await page.evaluate(inspect);
            assert.ok(forward.titleTop < enter.titleTop && forward.wallEnd > 0);
            await page.mouse.wheel(0, -900);
            await page.waitForFunction(y => scrollY < y, forward.scroll);
            const reverse = await page.evaluate(inspect);
            assert.ok(reverse.titleTop > forward.titleTop);
            await page.mouse.move(0, 0);
            const exit = await seam(page, '[data-testimonials]', 'bottom', 460);
            assert.ok(Math.abs(exit.wallEnd - exit.storiesStart) < 1, 'Stories starts at the end of Testimonials');
            assert.ok(exit.storiesStart > 0 && exit.storiesStart < exit.viewport);
            await visibleAvatarsReady(page);
            await page.screenshot({ path: `${directory}/testimonials-${engine}-${width}-to-stories.jpg`, type: 'jpeg', quality: 90 });
            assert.equal(new URL(page.url()).pathname, '/');
            profiles.push({ width, enter, forward, reverse, exit });
        }
        assert.deepEqual(runtime.errors, []);
        await evidence(`testimonials-scroll-${engine}`, { version: runtime.version, profiles });
    } finally { await runtime.close(); }
});

function railState() {
    return [...document.querySelectorAll('[data-testimonials-track]')].map(track => ({
        offsets: [...track.children].map(c => c.getBoundingClientRect().left - track.getBoundingClientRect().left),
        position: new DOMMatrix(getComputedStyle(track).transform).m41,
        leading: [...track.children].findIndex(c => c.style.order === '0'),
    }));
}

for (const engine of engines) {
    test(`Testimonials loops in opposite directions and supports dragging and keyboard in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        try {
            await locale(page, 'en');
            await seam(page, '[data-testimonials-wall]', 'top', 340);
            await page.mouse.move(0, 0);
            const initial = await page.evaluate(railState);
            await page.waitForFunction(initial => {
                const tracks = [...document.querySelectorAll('[data-testimonials-track]')];
                const positions = tracks.map(track => new DOMMatrix(getComputedStyle(track).transform).m41);
                return positions[0] > initial[0].position + 9 && positions[1] < initial[1].position - 9 && positions[2] > initial[2].position + 9;
            }, initial);
            const row = page.locator('[data-testimonials-row]').first();
            const box = await row.boundingBox();
            await page.mouse.move(box.x + 410, box.y + 85);
            const before = await page.evaluate(railState);
            await page.mouse.down();
            await page.mouse.move(box.x + 555, box.y + 85, { steps: 8 });
            const during = await page.evaluate(railState);
            await page.mouse.up();
            assert.ok(Math.abs(during[0].position - before[0].position - 145) < 4
                || during[0].leading !== before[0].leading, 'horizontal drag moves the actual rail');
            await row.focus();
            const start = (await page.evaluate(railState))[0];
            const count = await row.locator('[data-testimonial-card]').count();
            await page.keyboard.press('ArrowRight');
            const moved = (await page.evaluate(railState))[0];
            assert.notEqual(moved.leading, start.leading);
            for (let index = 1; index < count; index++) await page.keyboard.press('ArrowRight');
            const wrapped = (await page.evaluate(railState))[0];
            assert.equal(wrapped.leading, start.leading);
            assert.ok(Math.abs(wrapped.position - start.position) < 1);
            assert.deepEqual(runtime.errors, []);
            await evidence(`testimonials-loop-${engine}`, { version: runtime.version, initial, before, during, start, moved, wrapped });
        } finally { await runtime.close(); }
    });

    test(`Phone displays six slots with alternating, overlapping replacements in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { hasTouch: true, isMobile: true, viewport: { width: 390, height: 844 } });
        const { page } = runtime;
        const active = () => page.locator('[data-testimonial-card]:not([hidden]):not([inert]) bdi').allTextContents();
        try {
            await locale(page, 'en');
            const title = page.locator('#testimonials-title');
            assert.equal(await title.evaluate(heading => {
                const range = document.createRange();
                range.selectNodeContents(heading);
                return range.getClientRects().length;
            }), 1);
            await seam(page, '[data-testimonials-wall]', 'top', 196);
            await page.waitForFunction(() => {
                const visible = [...document.querySelectorAll('[data-testimonial-card]')].filter(c => !c.hidden && !c.hasAttribute('data-testimonial-pending'));
                const sides = visible.flatMap(c => c.getAnimations().map(animation => animation.effect.getKeyframes()[0].transform));
                return sides.includes('translateX(-85%)') && sides.includes('translateX(85%)');
            });
            assert.equal((await active()).length, 6);
            await page.waitForFunction(() => !document.querySelector('[data-testimonial-pending]'));
            const first = await active();
            await visibleAvatarsReady(page);
            await page.screenshot({ path: `${directory}/testimonials-${engine}-phone-six.jpg`, type: 'jpeg', quality: 92 });
            await page.waitForFunction(() => document.querySelectorAll('[data-testimonial-card][inert]').length >= 2);
            const intermediate = await active();
            assert.notDeepEqual(intermediate, first);
            assert.equal(intermediate.length, 6);
            await page.screenshot({ path: `${directory}/testimonials-${engine}-phone-chain.jpg`, type: 'jpeg', quality: 92 });

            const beforeSwipe = await active();
            const bounds = await page.locator('[data-testimonials-wall]').boundingBox();
            const x = bounds.x + bounds.width * .75;
            const y = bounds.y + 56;
            if (engine === 'chromium') {
                const touch = await runtime.context.newCDPSession(page);
                await touch.send('Input.dispatchTouchEvent', { type: 'touchStart', touchPoints: [{ x, y }] });
                for (const distance of [18, 44, 76, 115]) {
                    await touch.send('Input.dispatchTouchEvent', { type: 'touchMove', touchPoints: [{ x: x - distance, y }] });
                }
                await touch.send('Input.dispatchTouchEvent', { type: 'touchEnd', touchPoints: [] });
            } else {
                await page.mouse.move(x, y);
                await page.mouse.down();
                await page.mouse.move(x - 115, y, { steps: 6 });
                await page.mouse.up();
            }
            await page.waitForFunction(names => [...document.querySelectorAll('[data-testimonial-card]:not([hidden]):not([inert]) bdi')]
                .some(item => !names.includes(item.textContent)), beforeSwipe);
            assert.equal((await active()).length, 6);
            assert.deepEqual(runtime.errors, []);
            await evidence(`testimonials-rotation-${engine}`, { version: runtime.version, first, intermediate, beforeSwipe, afterSwipe: await active() });
        } finally { await runtime.close(); }
    });
}
