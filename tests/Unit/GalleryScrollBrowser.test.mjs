import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt, galleryFits, galleryResize, galleryOpening } from './GalleryBrowserSupport.mjs';

for (const engine of engines) {
    test(`Gallery opening, formation, travel and testimonial handoff in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        const locales = (process.env.PROGRAM_GALLERY_LOCALES ?? 'en,id,ar').split(',');
        try {
            for (const lang of locales) {
                await locale(page, lang);
                assert.equal(await page.locator('[data-gallery-video][src]').count(), 0, 'no video transfer before gallery proximity');
                await galleryReady(page);
                const statement = await page.locator('[data-gallery-statement] > .sr-only').textContent();
                assert.ok((await page.locator('[data-gallery]').ariaSnapshot()).includes(statement), 'complete localized sentence in accessibility tree');
                for (const width of [360, 390, 640, 768, 1024, 1280, 1440, 1536, 1920]) {
                    const started = performance.now();
                    await galleryResize(page, width, 900);
                    const entryStart = +await page.locator('[data-gallery]').getAttribute('data-gallery-shrink-start');
                    const title = await galleryAt(page, entryStart * .2);
                    assert.equal(title.heading.size, title.heading.valuesSize);
                    assert.equal(title.heading.color, 'rgb(255, 255, 255)');
                    assert.ok(title.backgroundRGB[2] < 255 && title.backgroundRGB[2] > 11, 'entry darkens continuously');
                    assert.equal(title.heading.visibility, 'visible');
                    assert.ok(Math.abs(title.items[0].y - title.heading.bottom - title.items[0].x) < 2,
                        `arrival-only title/video gap equals opening side gutter: ${lang}/${width}`);
                    assert.ok(title.heading.y > 0 && title.heading.y < 900, 'large title precedes opening media');
                    const before = await galleryAt(page, 0);
                    assert.deepEqual(before.backgroundRGB, [32, 56, 255], 'reverse restores Values blue');
                    assert.equal(before.typeVisible, 'visible');
                    const gap = Math.max(6, Math.min(width * .011, 16));
                    assert.ok(Math.abs(title.items[0].y - title.header.bottom - gap) < 1, 'title and main media rise with a close fixed gap');
                    await galleryAt(page, entryStart * .65);
                    const opening = await galleryOpening(page);
                    assert.ok(Math.abs(opening.items[0].width - Math.min(width * 26 / 30, 900 * .84 / .5625)) < 1);
                    assert.deepEqual(opening.backgroundRGB, [8, 9, 11]);
                    assert.equal(opening.lineOpacity, 0);
                    assert.equal(opening.typeOpacity, 0);
                    assert.equal(opening.items[0].opacity, 1);
                    assert.ok(opening.items.slice(1).every(i => i.opacity === 1));
                    assert.ok(opening.words.every(w => w.opacity === 0));
                    assert.ok(Math.abs(opening.items[0].y - gap) < 1, 'shrink starts only at viewport top');
                    assert.ok(opening.items.slice(1).every(i => i.y > 900), 'secondary media are still far below at shrink start');
                    // The opening's equal-gutter spacing can move shrinkStart
                    // later on wide screens. Sample AFTER the actual word
                    // reveal threshold, without changing product choreography.
                    const revealStart = entryStart + (.52 - entryStart) * .6;
                    const entering = await galleryAt(page, Math.max(.40, revealStart + .012));
                    assert.ok(entering.items[0].width < opening.items[0].width);
                    assert.ok(entering.words[0].opacity > 0 && entering.words[0].blur > 0);
                    assert.ok(entering.words[0].opacity > entering.words.at(-1).opacity, 'word entrances overlap in order');
                    assert.ok(entering.items.slice(1).every((item, index) => Math.abs(item.width - opening.items[index + 1].width) < 1), 'secondary media keep their final width');
                    const full = await galleryAt(page, .52);
                    galleryFits(full);
                    assert.ok(full.items.every(i => i.opacity === 1));
                    assert.equal(full.triggers, 1);
                    assert.ok(Math.abs(full.items[0].y + full.items[0].height / 2 - 450) < 2, 'shrink finishes at viewport center');
                    assert.ok(full.words[0].opacity > .999 && full.words[0].blur < .05, 'first word is clear at assembly');
                    assert.ok(full.words[1].blur > 0 && full.words.at(-1).blur > full.words[1].blur, 'later words are progressively blurred');
                    {
                        assert.deepEqual(['left', 'center', 'right'].map(c => full.items.filter(i => i.column === c).length), [3, 4, 3]);
                        assert.ok(Math.abs(full.items[0].x + full.items[0].width / 2 - width / 2) < 1);
                        assert.ok(Math.abs(full.items[0].width * 3 + gap * 2 - width) < 1);
                        assert.ok(entering.items[4].y - entering.items[0].bottom > gap * 2, 'lower media approach from a distance');
                        assert.ok(Math.abs(full.items[4].y - full.items[0].bottom - gap) < 1, 'lower media attach at assembly');
                    }
                    const traveling = await galleryAt(page, .66);
                    galleryFits(traveling);
                    const assemblyBottom = Math.max(...full.items.map(i => i.bottom));
                    assert.ok(traveling.items.every((i, n) => i.y <= full.items[n].y + 1), 'a fitting composition never drifts downward');
                    if (assemblyBottom > 920) assert.ok(traveling.items.every((i, n) => i.y < full.items[n].y - 10));
                    const clear = await galleryAt(page, .74);
                    assert.ok(clear.words.every(w => w.opacity > .999 && w.blur < .02));
                    const edge = await galleryAt(page, .76);
                    const exitBottom = Math.min(900, assemblyBottom);
                    assert.ok(Math.abs(Math.max(...edge.items.map(i => i.bottom)) - exitBottom) < 2, 'exit begins after excess media have cleared the viewport');
                    assert.ok(edge.items.every(i => i.opacity > .999));
                    const exit = await galleryAt(page, .83);
                    assert.ok(exit.words.every(w => w.opacity < 1 && w.blur > 0));
                    assert.ok(exit.blur > 0, 'all media blur together');
                    assert.ok(exit.items.every(i => i.opacity < 1));
                    assert.ok(Math.abs(Math.max(...exit.items.map(i => i.bottom)) - (exitBottom - 225)) < 2);
                    const blackout = await galleryAt(page, .90);
                    assert.ok(Math.abs(Math.max(...blackout.items.map(i => i.bottom)) - (exitBottom - 450)) < 2, 'exit keeps the same half-viewport upward travel');
                    assert.ok(blackout.items.every(i => i.opacity < .01) && blackout.words.every(w => w.opacity < .01));
                    assert.ok(Math.abs(blackout.testimonial.y) <= 1, 'Testimonials begins in the finished Gallery black hold');
                    assert.equal(await page.locator('[data-gallery] button').count(), 0, 'automatic loop without pause UI');
                    await page.evaluate(() => {
                        const intro = document.querySelector('[data-testimonials-intro]').getBoundingClientRect();
                        scrollTo(0, intro.top + scrollY + (intro.height - innerHeight) * .5);
                    });
                    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
                    assert.ok(await page.locator('[data-testimonials]').evaluate(root => getComputedStyle(root).backgroundColor === 'rgb(8, 9, 11)'
                        && [...root.querySelectorAll('[data-testimonials-word]')].every(word => +getComputedStyle(word).opacity > .99 && parseFloat(getComputedStyle(word).filter.match(/[\d.]+/)[0]) < .01)), 'entire sharp white sentence reads on Gallery black before departure');
                    const end = await galleryAt(page, 1);
                    assert.ok(end.items.every(i => i.opacity < .01));
                    assert.ok(end.words.every(w => w.opacity < .01));
                    assert.ok(end.testimonial.y < 0, 'question shares the finished black hold');
                    assert.ok(await page.locator('[data-testimonials-word]').first().evaluate(word => +getComputedStyle(word).opacity > .5), 'sentence participates in the completed Gallery hold');
                    const reverse = await galleryAt(page, .52);
                    assert.ok(reverse.items.every((i, n) => Math.abs(i.x - full.items[n].x) < 1 && Math.abs(i.y - full.items[n].y) < 2));
                    if ([390, 1440].includes(width)) for (const [name, progress] of [['title', .005], ['rise', .075], ['opening', null], ['entering', .40], ['formation', .52], ['clear-text', .74], ['exit', .83], ['blackout', .90]]) {
                        if (progress === null) await galleryOpening(page); else await galleryAt(page, progress);
                        await page.screenshot({ path: `${directory}/gallery-${engine}-${lang}-${width}-${name}.jpg`, type: 'jpeg', quality: 85 });
                    }
                    cases.push({ lang, width, title, opening, full, traveling, edge, exit, blackout, end });
                    console.info(`Gallery ${engine}/${lang}/${width} PASS ${Math.round(performance.now() - started)}ms`);
                }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`gallery-scroll-${engine}${process.env.PROGRAM_GALLERY_LOCALES ? `-${locales.join('-')}` : ''}`, { version: runtime.version, cases });
        } finally { await runtime.close(); }
    });
}
