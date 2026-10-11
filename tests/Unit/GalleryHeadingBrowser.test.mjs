import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt } from './GalleryBrowserSupport.mjs';

for (const engine of engines) {
    test(`Gallery title waits for the actual Values card exit in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            for (const lang of ['id', 'en', 'ar']) {
                await locale(page, lang);
                await page.evaluate(() => {
                    window.galleryEntryWitness = null;
                    const heading = document.querySelector('[data-gallery-heading]');
                    const observer = new MutationObserver(() => {
                        if (heading.dataset.headingState !== 'revealing' || window.galleryEntryWitness) return;
                        const viewport = document.querySelector('[data-values-cards-viewport]');
                        const top = Math.max(0, viewport.getBoundingClientRect().top);
                        const cards = [...document.querySelectorAll('[data-values-card-inner]')].map(card => {
                            const r = card.getBoundingClientRect();
                            return { top: r.top, bottom: r.bottom, height: r.height,
                                visibleBody: Math.max(0, r.bottom - top) / r.height };
                        });
                        window.galleryEntryWitness = { cards, headingY: heading.getBoundingClientRect().top,
                            progress: document.querySelector('[data-gallery]').dataset.galleryProgress };
                        observer.disconnect();
                    });
                    observer.observe(heading, { attributes: true, attributeFilter: ['data-heading-state'] });
                });
                await galleryReady(page);
                const early = await galleryAt(page, .005);
                assert.equal(early.heading.visibility, 'hidden', 'no title while most card bodies remain');
                await galleryAt(page, .042);
                await page.waitForFunction(() => !!window.galleryEntryWitness);
                const witness = await page.evaluate(() => window.galleryEntryWitness);
                assert.ok(witness.cards.every(card => card.visibleBody <= .205), 'at most20% visible when reveal begins');
                await page.waitForFunction(() => document.querySelector('[data-gallery-heading]').dataset.headingState === 'complete');
                const shown = await galleryAt(page, .045);
                assert.equal(shown.heading.visibility, 'visible');
                await page.screenshot({ path: `${directory}/gallery-${engine}-${lang}-1440-delayed-title.jpg`, type: 'jpeg', quality: 85 });
                await galleryAt(page, .005);
                await page.waitForFunction(() => getComputedStyle(document.querySelector('[data-gallery-heading]')).visibility === 'hidden'
                    && +document.querySelector('[data-gallery]').dataset.galleryHeadingRemaining > .2);
                assert.ok(+await page.locator('[data-gallery]').getAttribute('data-gallery-heading-remaining') > .2, 'reverse closes gate as cards return');
                await page.emulateMedia({ reducedMotion: 'reduce' });
                await page.waitForFunction(() => !document.querySelector('[data-gallery]').hasAttribute('data-gallery-heading-waiting'));
                assert.equal(await page.locator('[data-gallery-heading]').evaluate(e => getComputedStyle(e).visibility), 'visible');
                await page.emulateMedia({ reducedMotion: 'no-preference' });
                cases.push({ lang, early, witness, shown, reverse: 'PASS', reduced: 'PASS' });
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`gallery-heading-${engine}`, { version: runtime.version, cases });
        } finally { await runtime.close(); }
    });
}
