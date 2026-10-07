import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Program center split and progressive semantic formation in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const records = [];
        try {
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                await page.locator('[data-hero]').scrollIntoViewIfNeeded();
                await page.waitForFunction(() => [...document.querySelectorAll('[data-program-heading-line]')]
                    .every(line => getComputedStyle(line).opacity === '0'));
                const initial = await page.evaluate(() => ({
                    lines: [...document.querySelectorAll('[data-program-heading-line]')].map(line => ({
                        opacity: getComputedStyle(line).opacity,
                        y: new DOMMatrix(getComputedStyle(line).transform).m42,
                    })),
                    revealed: [...document.querySelectorAll('[data-program-card]')].filter(card => card.dataset.revealed === 'true').length,
                }));
                assert.equal(initial.revealed, 0);
                assert.equal(initial.lines.length, language === 'ar' ? 1 : 2);
                assert.ok(initial.lines[0].y > 0);
                if (initial.lines[1]) assert.ok(initial.lines[1].y < 0);
                await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.revealed === 'true');
                await page.locator('[data-program-card]').nth(3).scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelectorAll('[data-program-card][data-revealed="true"]').length >= 4);
                const firstRow = await page.locator('[data-program-card]').evaluateAll(cards => cards.map(card => card.dataset.revealed === 'true'));
                assert.ok(firstRow.slice(0, 4).every(Boolean));
                assert.ok(firstRow.slice(4).some(value => !value), 'second row waits for proximity');
                await page.locator('[data-program-card]').last().scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelectorAll('[data-program-card][data-revealed="true"]').length === 8);
                await page.locator('[data-hero]').scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelectorAll('[data-program-card][data-revealed="true"]').length === 0);
                assert.equal(await page.locator('[data-program-card]').count(), 8, 'fade retains the single semantic field');
                await page.locator('[data-program-card]').nth(3).scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelectorAll('[data-program-card][data-revealed="true"]').length >= 4);
                await page.emulateMedia({ reducedMotion: 'reduce' });
                await page.waitForFunction(() => !document.querySelector('[data-program]').hasAttribute('data-reveal-enabled'));
                assert.equal(await page.locator('[data-program-open]').evaluateAll(items => items.every(item => getComputedStyle(item.firstElementChild).opacity === '1')), true);
                await page.emulateMedia({ reducedMotion: 'no-preference' });
                await page.locator('[data-hero]').scrollIntoViewIfNeeded();
                records.push({ language, initial, firstRow, reverseFadesAndReenters: true, reducedVisible: true });
            }
            assert.equal(runtime.requests.some(request => /gsap@|program-kinetic/.test(request)), false);
            assert.deepEqual(runtime.errors, []);
            await evidence(`program-feedback-reveal-${engine}`, { version: runtime.version, records });
        } finally { await runtime.close(); }
    });
}
