import { settledValuesLayout } from './ValuesBrowserSupport.mjs';
import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, evidence } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Values SVG paints only the drawn stroke at desktop and phone scale in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine); const { page } = runtime;
        const cases = [];
        try {
            await page.evaluate(() => scrollTo(0, document.querySelector('[data-values]').getBoundingClientRect().top + scrollY - innerHeight));
            await page.waitForFunction(() => !!window.ScrollTrigger?.getById('values-line'));
            for (const width of [360, 768, 1440]) {
                await page.setViewportSize({ width, height: 900 });
                    await settledValuesLayout(page);

                await page.evaluate(() => {
                    const trigger = window.ScrollTrigger.getById('values-line');
                    scrollTo(0, Math.round((trigger.start + trigger.end) / 2));
                });
                await page.waitForFunction(() => Math.abs(window.ScrollTrigger.getById('values-line').progress - .5) < .001);
                await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
                const painted = await page.locator('[data-values-line] svg').evaluate(async svg => {
                    const box = svg.getBoundingClientRect();
                    const path = svg.querySelector('path');
                    const clone = svg.cloneNode(true);
                    clone.setAttribute('width', box.width); clone.setAttribute('height', box.height);
                    clone.querySelector('path').style.strokeWidth = getComputedStyle(path).strokeWidth;
                    const blob = new Blob([new XMLSerializer().serializeToString(clone)], { type: 'image/svg+xml' });
                    const url = URL.createObjectURL(blob);
                    const image = new Image(); image.src = url;
                    await image.decode();
                    const canvas = document.createElement('canvas'); canvas.width = box.width; canvas.height = box.height;
                    const context = canvas.getContext('2d'); context.drawImage(image, 0, 0); URL.revokeObjectURL(url);
                    const length = path.getTotalLength();
                    const drawn = length - parseFloat(getComputedStyle(path).strokeDashoffset);
                    const sample = (start, end) => {
                        const points = [];
                        for (let distance = start; distance < end; distance += length / 100) {
                            const point = path.getPointAtLength(distance);
                            if (point.x < 10 || point.x > box.width - 10 || point.y < 10 || point.y > box.height - 10) continue;
                            const pixels = context.getImageData(Math.round(point.x) - 1, Math.round(point.y) - 1, 3, 3).data;
                            const alpha = Array.from(pixels).filter((_, index) => index % 4 === 3);
                            points.push({ x: point.x, y: point.y, alpha: Math.max(...alpha) });
                        }
                        return points;
                    };
                    const behind = sample(drawn * .65, drawn * .95);
                    const ahead = sample(length * .85, length * .97);
                    clone.querySelector('path').style.strokeDashoffset = '0';
                    const fullUrl = URL.createObjectURL(new Blob([new XMLSerializer().serializeToString(clone)], { type: 'image/svg+xml' }));
                    image.src = fullUrl; await image.decode();
                    context.clearRect(0, 0, box.width, box.height); context.drawImage(image, 0, 0); URL.revokeObjectURL(fullUrl);
                    const walls = [];
                    for (const exit of [false, true]) {
                        let low = exit ? length * .9 : 0; let high = exit ? length : length * .1;
                        for (let pass = 0; pass < 30; pass++) {
                            const middle = (low + high) / 2;
                            if ((path.getPointAtLength(middle).x > box.width - 1) === exit) high = middle;
                            else low = middle;
                        }
                        const point = path.getPointAtLength((low + high) / 2);
                        const pixels = context.getImageData(box.width - 1, Math.round(point.y) - 1, 1, 3).data;
                        walls.push({ y: point.y, alpha: Math.max(...Array.from(pixels).filter((_, i) => i % 4 === 3)) });
                    }
                    return { width: box.width, length, drawn,
                        behind, ahead, walls };
                });
                assert.ok(painted.behind.length > 0 && painted.ahead.length > 0);
                assert.ok(painted.behind.every(point => point.alpha > 250), 'drawn path is visibly white');
                assert.ok(painted.ahead.every(point => point.alpha === 0), 'future path has no painted pixels');
                assert.ok(painted.walls.every(point => point.alpha > 250), 'entry and exit strokes reach the screen wall');
                cases.push(painted);
            }
            await page.evaluate(() => scrollTo(0, Math.ceil(window.ScrollTrigger.getById('values-line').end)));
            await page.waitForTimeout(80);
            await page.screenshot({ path: `${process.env.PROGRAM_PROOF_DIRECTORY ?? 'docs2/proof'}/values-line-wall-end.png` });
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-line-render-${engine}`, { cases });
        } finally { await runtime.close(); }
    });
}
