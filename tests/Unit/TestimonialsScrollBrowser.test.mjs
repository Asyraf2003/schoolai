import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryResize } from './GalleryBrowserSupport.mjs';

for (const engine of engines) test(`aggressive scroll paints the complete testimonial journey in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine, { hasTouch: true });
    const { page } = runtime;
    const profiles = [];
    try {
        await locale(page, 'id'); await galleryReady(page);
        for (const width of [390, 1440]) {
            await galleryResize(page, width, 900);
            await page.evaluate(async () => {
                const intro = document.querySelector('[data-testimonials-intro]');
                scrollTo(0, intro.getBoundingClientRect().top + scrollY);
                await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
                window.testimonialFrames = []; window.testimonialRecording = true;
                const sample = () => {
                    if (!window.testimonialRecording) return;
                    const root = document.querySelector('[data-testimonials]');
                    const intro = root.querySelector('[data-testimonials-intro]').getBoundingClientRect();
                    const words = [...root.querySelectorAll('[data-testimonials-word]')];
                    window.testimonialFrames.push({ y: scrollY, p: -intro.top / (intro.height - innerHeight),
                        titleY: root.querySelector('h2').getBoundingClientRect().top,
                        rowsY: root.querySelector('[data-testimonials-rows]').getBoundingClientRect().top,
                        field: getComputedStyle(root).backgroundColor,
                        tone: root.style.getPropertyValue('--testimonials-tone'),
                        rendered: +root.dataset.testimonialsProgress,
                        departure: 2 * (.22 + .4 * (.52 - +document.querySelector('[data-gallery]').dataset.galleryShrinkStart)),
                        words: words.map(w => ({ opacity: +getComputedStyle(w).opacity, blur: parseFloat(getComputedStyle(w).filter.match(/[\d.]+/)[0]) })),
                    });
                    requestAnimationFrame(sample);
                };
                requestAnimationFrame(sample);
            });
            await page.mouse.move(width / 2, 420);
            for (let index = 0; index < 60; index++) {
                await page.mouse.wheel(0, 10000); await page.waitForTimeout(24);
                if (await page.evaluate(() => testimonialFrames.at(-1)?.p >= 1)) break;
            }
            await page.waitForTimeout(300);
            const frames = await page.evaluate(() => { window.testimonialRecording = false; return testimonialFrames; });
            await evidence(`testimonials-scroll-${engine}-${width}-diagnostic`, { frames });
            assert.ok(frames.every(f => {
                const exit = Math.max(0, Math.min(1, (f.p - f.departure) / (1 - f.departure)));
                const expected = Math.max(0, Math.min(1, f.p / .10)) * (1 - exit);
                return Math.abs(f.words[0].opacity - expected) < .001;
            }), 'first word renders actual native progress on the same delivery');
            assert.ok(frames.some(f => f.words[0].opacity > 0 && f.words.at(-1).opacity < 1), 'ordered opacity reveal is actually painted');
            assert.ok(frames.some(f => f.words[0].blur < f.words.at(-1).blur), 'Gallery-like ordered sharpening paints');
            const sharp = frames.find(f => f.words.every(w => w.opacity > .99 && w.blur < .01));
            assert.ok(sharp, 'complete sharp title paints before departing');
            assert.ok(frames.some(f => f.p > f.departure && f.titleY < sharp.titleY && f.rowsY < 900 && f.words.some(w => w.blur > 0)), 'upward blur and media arrival paint concurrently');
            assert.equal(frames.at(-1).tone, '255');
            const before = await page.evaluate(() => scrollY);
            await page.mouse.wheel(0, -10000);
            assert.ok(await page.evaluate(() => scrollY) < before, 'reverse responds immediately through the transition');
            for (let index = 0; index < 40; index++) {
                await page.mouse.wheel(0, -10000); await page.waitForTimeout(24);
                if (await page.evaluate(() => +document.querySelector('[data-testimonials]').dataset.testimonialsProgress < -.05)) break;
            }
            await page.waitForTimeout(300);
            const reverseGallery = await page.evaluate(() => ({
                field: getComputedStyle(document.querySelector('[data-testimonials]')).backgroundColor,
                words: [...document.querySelectorAll('[data-testimonials-word]')].map(w => +getComputedStyle(w).opacity),
                gallery: window.ScrollTrigger.getById('gallery-v2').progress,
            }));
            assert.equal(reverseGallery.field, 'rgba(0, 0, 0, 0)');
            assert.ok(reverseGallery.words.every(opacity => opacity === 0) && reverseGallery.gallery < .9, 'reverse restores active Gallery without question/field overlay');
            profiles.push({ width, frames, before, reverseGallery, afterReverse: await page.evaluate(() => scrollY) });
        }
        assert.deepEqual(runtime.errors, []);
        await evidence(`testimonials-scroll-${engine}`, { version: runtime.version, profiles });
    } finally { await runtime.close(); }
});
