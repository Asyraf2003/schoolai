import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, session, locale, phase, geometry, fits, evidence } from './ProgramBrowserSupport.mjs';

test('blocked GSAP preserves dialog access, selected media, Escape and focus restoration', { skip: !enabled }, async () => {
    const runtime = await session('chromium');
    const { page } = runtime;
    try {
        await page.route('**/gsap@*/**', route => route.abort());
        await locale(page, 'ar');
        await page.setViewportSize({ width: 390, height: 844 });
        const trigger = page.locator('[data-program-open]').nth(1);
        await trigger.click(); await phase(page, 'detail');
        assert.equal(await page.locator('[data-program]').getAttribute('data-program-animation'), 'fallback');
        fits(await page.evaluate(geometry));
        await page.keyboard.press('Escape'); await phase(page, 'idle');
        assert.equal(await trigger.evaluate(element => element === document.activeElement), true);
        assert.equal(await page.evaluate(() => document.documentElement.hasAttribute('data-program-detail-open')), false);
        await evidence('program-fallback', { state: await page.evaluate(geometry), errors: runtime.errors });
        assert.deepEqual(runtime.errors, []);
    } finally { await runtime.close(); }
});

test('no JavaScript keeps all Program disclosures and descriptions readable', { skip: !enabled }, async () => {
    const runtime = await session('chromium', { javaScriptEnabled: false, viewport: { width: 390, height: 844 } });
    const { page } = runtime;
    try {
        for (let index = 0; index < 8; index++) {
            const card = page.locator('[data-program-card]').nth(index);
            await card.locator('summary').click();
            assert.equal(await card.getAttribute('open'), '');
            assert.equal(await card.locator('.program__description').isVisible(), true);
            await card.locator('summary').click();
            assert.equal(await card.getAttribute('open'), null);
        }
        assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
        await evidence('program-no-js', { disclosures: 8, descriptions: 8, requests: runtime.requests });
    } finally { await runtime.close(); }
});

test('missing observer and dialog capabilities keep native access', { skip: !enabled }, async () => {
    const runtime = await session('chromium');
    const { page } = runtime;
    try {
        await page.addInitScript(() => { delete window.IntersectionObserver; HTMLDialogElement.prototype.showModal = undefined; });
        await page.reload();
        await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programState === 'idle');
        const trigger = page.locator('[data-program-open]').first();
        await trigger.click();
        assert.equal(await page.locator('[data-program-card]').first().getAttribute('open'), '');
        assert.equal(await page.locator('.program__description').first().isVisible(), true);
        assert.equal(await trigger.locator('.program__card-reveal').evaluate(element => getComputedStyle(element).opacity), '1');
        await evidence('program-capabilities', { nativeDisclosure: true, errors: runtime.errors });
        assert.deepEqual(runtime.errors, []);
    } finally { await runtime.close(); }
});

test('late preparation cannot reopen an Escape-cancelled selection', { skip: !enabled }, async () => {
    const runtime = await session('chromium');
    const { page } = runtime;
    try {
        let release;
        const delayed = new Promise(resolve => { release = resolve; });
        await page.route('**/gsap@*/**', async route => { await delayed; await route.abort(); });
        await page.locator('[data-program-open]').first().click();
        await phase(page, 'preparing');
        await page.keyboard.press('Escape'); await phase(page, 'idle');
        release();
        await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programAnimation === 'fallback');
        assert.equal(await page.locator('[data-program-dialog]').evaluate(dialog => dialog.open), false);
        await page.locator('[data-program-open]').last().click(); await phase(page, 'detail');
        await page.keyboard.press('Escape'); await phase(page, 'idle');
        assert.deepEqual(runtime.errors, []);
        await evidence('program-cancelled-load', { staleOpen: false, freshSelection: true });
    } finally { await runtime.close(); }
});

test('unavailable Program images retain composition, copy and detail controls', { skip: !enabled }, async () => {
    const runtime = await session('chromium', { reducedMotion: 'reduce', viewport: { width: 390, height: 844 } });
    const { page } = runtime;
    try {
        await page.route('**/site/school-life/**', route => route.abort());
        await locale(page, 'id');
        const trigger = page.locator('[data-program-open]').first();
        await trigger.click(); await phase(page, 'detail');
        await page.waitForFunction(() => document.querySelector('[data-program-detail-stage] img').complete);
        const image = await page.locator('[data-program-detail-stage] img').evaluate(image => ({
            width: image.getBoundingClientRect().width, height: image.getBoundingClientRect().height,
            naturalWidth: image.naturalWidth,
        }));
        assert.equal(image.naturalWidth, 0);
        assert.ok(image.width > 0 && image.height > 0, 'reserved media geometry remains');
        assert.equal(await page.locator('[data-program-detail-stage] .program__description').isVisible(), true);
        fits(await page.evaluate(geometry));
        await page.keyboard.press('Escape'); await phase(page, 'idle');
        assert.equal(await trigger.evaluate(element => element === document.activeElement), true);
        assert.deepEqual(runtime.errors, []);
        await evidence('program-media-failure', { image, copyAndControlsAvailable: true, focusRestored: true });
    } finally { await runtime.close(); }
});
