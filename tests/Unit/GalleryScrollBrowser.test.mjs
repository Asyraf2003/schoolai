import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt, galleryFits, galleryResize, galleryOpening } from './GalleryBrowserSupport.mjs';

for (const engine of engines) {
    test(`White 34-tile Gallery 1:1 travel and 80% blur handoff to Testimonials in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            for (const lang of (process.env.PROGRAM_GALLERY_LOCALES ?? 'en,id,ar').split(',')) {
                await locale(page, lang);
                assert.equal(await page.locator('[data-gallery-video][src]').count(), 0, 'no video transfer before gallery proximity');
                await galleryReady(page);
                const statement = await page.locator('[data-gallery-statement] > .sr-only').textContent();
                assert.ok((await page.locator('[data-gallery]').ariaSnapshot()).includes(statement), 'complete localized sentence in accessibility tree');
                for (const width of (process.env.PROGRAM_GALLERY_WIDTHS ?? '360,390,640,768,1024,1280,1440,1536,1920').split(',').map(Number)) {
                    const started = performance.now();
                    await galleryResize(page, width, 900);
                    const opening = await galleryOpening(page);
                    const entryStart = +await page.locator('[data-gallery]').getAttribute('data-gallery-shrink-start');
                    const title = await galleryAt(page, Math.min(entryStart * .2, opening.pinStart * .2));
                    assert.equal(title.heading.size, title.heading.valuesSize);
                    assert.ok(title.backgroundRGB[0] >= 32 && title.backgroundRGB[2] === 255, 'entry lightens blue toward white');
                    assert.equal(title.heading.visibility, 'visible');
                    const gap = Math.max(6, Math.min(width * .011, 16));
                    assert.ok(Math.abs(title.items[0].y - title.header.bottom - gap) < 1, 'title and main media rise with a close fixed gap');
                    const before = await galleryAt(page, 0);
                    assert.deepEqual(before.backgroundRGB, [32, 56, 255], 'reverse restores Values blue');
                    assert.equal(before.typeVisible, 'visible');
                    const pinned = await galleryAt(page, opening.pinStart);
                    assert.deepEqual(pinned.backgroundRGB, [255, 255, 255], 'white settles before sticky pin');
                    assert.deepEqual(pinned.heading.colorRGB, [23, 23, 23]);
                    assert.equal(pinned.lineOpacity, 0);
                    assert.equal(pinned.typeOpacity, 0);
                    assert.equal(opening.items[0].opacity, 1);
                    assert.ok(opening.words.every(w => w.opacity === 0));
                    for (const fraction of [0, .2, .5, .8]) {
                        const shrinking = await galleryAt(page, entryStart + (opening.formation - entryStart) * fraction);
                        const main = shrinking.items[0];
                        assert.ok(Math.abs(main.y + main.height / 2 - 450) < 2, 'lead stays centered during shrink');
                        assert.ok(Math.abs(main.x + main.width / 2 - width / 2) < 2, 'lead stays horizontally centered');
                    }
                    const entering = await galleryAt(page, opening.pinStart + (opening.formation - opening.pinStart) * .72);
                    assert.ok(entering.items[0].width < opening.items[0].width);
                    assert.ok(entering.words[0].opacity > 0 && entering.words[0].blur > 0);
                    assert.ok(entering.statementMist > 0);
                    const full = await galleryAt(page, opening.formation);
                    galleryFits(full);
                    assert.ok(full.items.every(i => i.opacity === 1));
                    assert.equal(full.triggers, 1);
                    assert.ok(Math.abs(full.items[0].y + full.items[0].height / 2 - 450) < 2, 'shrink finishes at viewport center');
                    assert.deepEqual(['left', 'center', 'right'].map(c => full.items.filter(i => i.column === c).length), [11, 12, 11]);
                    const laneWidth = Math.min(width, 900 * 3.2) - gap * 2;
                    assert.ok(Math.abs(full.items[0].width - .4 * laneWidth) < 1);
                    assert.ok(Math.abs(full.items[1].width - .3 * laneWidth) < 1);
                    assert.ok(Math.abs(full.items[7].width - .3 * laneWidth) < 1);
                    assert.ok(Math.abs(full.items[4].y - full.items[0].bottom - gap) < 1);

                    const midRise = await galleryAt(page, (opening.formation + opening.exitStart) / 2);
                    galleryFits(midRise);
                    const scrollDelta = midRise.scroll - full.scroll;
                    const itemDelta = full.items[0].y - midRise.items[0].y;
                    assert.ok(Math.abs(itemDelta - scrollDelta) < 3, `1:1 visual rise (${itemDelta} vs ${scrollDelta})`);

                    const exitStart = await galleryAt(page, opening.exitStart);
                    assert.ok(exitStart.words.every(w => w.opacity > .999 && w.blur < .05));
                    assert.ok(exitStart.items.every(i => i.opacity > .999));

                    const eighty = await galleryAt(page, opening.exitPinned);
                    assert.ok(eighty.blur > 9 && eighty.blur < 13, `80% blur ${eighty.blur}`);
                    assert.ok(Math.abs(eighty.items[0].opacity - .2) < .05, `80% fade ${eighty.items[0].opacity}`);
                    assert.ok(Math.abs(eighty.testimonials.y - 900) < 3, `Testimonials begins rising from bottom at 80% blur (${eighty.testimonials.y})`);

                    const end = await galleryAt(page, 1);
                    assert.ok(end.items.every(i => i.opacity < .01));
                    assert.ok(end.words.every(w => w.opacity < .01));
                    assert.ok(end.testimonials.y < 800, `Testimonials rises smoothly during final 20% (${end.testimonials.y})`);
                    assert.ok(Math.abs(end.testimonials.y - end.gallery.bottom) < 2, 'Testimonials follows Gallery seamlessly');

                    const reverse = await galleryAt(page, opening.formation);
                    assert.ok(reverse.items.every((i, n) => Math.abs(i.x - full.items[n].x) < 1 && Math.abs(i.y - full.items[n].y) < 2));
                    if ([390, 1440].includes(width)) for (const [name, progress] of [['title', .01], ['opening', null], ['formation', opening.formation], ['exit80', opening.exitPinned], ['end', 1]]) {
                        if (progress === null) await galleryOpening(page); else await galleryAt(page, progress);
                        await page.screenshot({ path: `${directory}/gallery-${engine}-${lang}-${width}-${name}.jpg`, type: 'jpeg', quality: 85 });
                    }
                    cases.push({ lang, width, title, opening, full, midRise, exitStart, eighty, end });
                    console.info(`Gallery ${engine}/${lang}/${width} PASS ${Math.round(performance.now() - started)}ms`);
                }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`gallery-scroll-${engine}${process.env.PROGRAM_GALLERY_LOCALES ? `-${locales.join('-')}` : ''}`, { version: runtime.version, cases });
        } finally { await runtime.close(); }
    });
}
