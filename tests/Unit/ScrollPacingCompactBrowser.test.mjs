import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';
import { settledValuesLayout } from './ValuesBrowserSupport.mjs';
import { galleryReady, galleryResize } from './GalleryBrowserSupport.mjs';

for (const engine of engines) test(`compact Values flips follow gradual native scrolling in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine);
    const { page } = runtime;
    const profiles = [];
    try {
        await locale(page, 'id'); await galleryReady(page);
        for (const width of [390, 768]) {
            await galleryResize(page, width, 900);
            await settledValuesLayout(page);
            await page.evaluate(() => {
                const card = document.querySelector('[data-values-card]'); const rect = card.getBoundingClientRect();
                scrollTo(0, rect.top + scrollY - innerHeight + rect.height * (innerWidth >= 768 ? .16 : .5));
            });
            await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
            await page.waitForFunction(() => document.querySelector('[data-values]').hasAttribute('data-cards-motion'));
            await page.evaluate(() => {
                const card = document.querySelector('[data-values-card]');
                const rect = card.getBoundingClientRect();
                scrollTo(0, rect.top + scrollY - innerHeight + rect.height * (innerWidth >= 768 ? .16 : .5));
                window.compactFrames = []; window.compactRecording = true;
                const sample = () => {
                    if (!window.compactRecording) return;
                    window.compactFrames.push({ y: scrollY,
                        visible: [...document.querySelectorAll('[data-values-card]')].map(e => { const b = e.getBoundingClientRect(); return b.bottom > 0 && b.top < innerHeight; }),
                        angles: [...document.querySelectorAll('[data-values-card-inner]')].map(e => Math.abs(parseFloat(e.style.transform.match(/rotateY\(([-\d.]+)/)?.[1] ?? 180))),
                    });
                    requestAnimationFrame(sample);
                };
                requestAnimationFrame(sample);
            });
            await page.mouse.move(width / 2, 450);
            await page.evaluate(() => new Promise(resolve => {
                const last = [...document.querySelectorAll('[data-values-card]')].at(-1).getBoundingClientRect();
                const end = last.bottom + scrollY - innerHeight;
                let steps = 0;
                const move = () => {
                    if (scrollY >= end || ++steps > 600) return resolve();
                    scrollBy(0, Math.min(12, end - scrollY));
                    requestAnimationFrame(move);
                };
                move();
            }));
            await page.waitForTimeout(300);
            const frames = await page.evaluate(() => { window.compactRecording = false; return compactFrames; });
            await evidence(`scroll-pacing-compact-${engine}-${width}-diagnostic`, { width, frames });
            for (let card = 0; card < 4; card++) {
                assert.ok(frames.some(f => f.angles[card] > 1 && f.angles[card] < 179), `card${card} has intermediate painted angles/${width}`);
                assert.ok(frames.at(-1).angles[card] < .1, `card${card} finishes/${width}`);
            }
            for (let index = 1; index < frames.length; index++) {
                assert.ok(frames[index].angles.every((angle, card) => Math.abs(angle - frames[index - 1].angles[card]) <= 48), JSON.stringify({ width, previous: frames[index - 1], current: frames[index] }));
            }
            const before = await page.evaluate(() => scrollY);
            await page.mouse.wheel(0, -240);
            await page.waitForFunction(before => scrollY < before, before);
            await page.waitForTimeout(300);
            profiles.push({ width, frames, before, reverse: await page.evaluate(() => scrollY) });
        }
        assert.deepEqual(runtime.errors, []);
        await evidence(`scroll-pacing-compact-${engine}`, { version: runtime.version, profiles });
    } finally { await runtime.close(); }
});
