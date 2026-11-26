import test from 'node:test';
import assert from 'node:assert/strict';
import { chooseCharacter } from '../../resources/js/sections/cursor.js';
import { enabled, engines, session } from './ProgramBrowserSupport.mjs';

const cursorAssets = '**/ui/cursor/*.webp';
const image = Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aEq0AAAAASUVORK5CYII=', 'base64');

test('Cursor picks one of the existing identities from the supplied initialization sample', () => {
    let calls = 0;
    assert.equal(chooseCharacter(() => { calls++; return 0.25; }), 'cwo');
    assert.equal(calls, 1);
    assert.equal(chooseCharacter(() => 0.75), 'cwe');
});

for (const engine of engines) {
    test(`Cursor retains one image across hover, dialog and page lifecycle in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const assets = [];
        try {
            await page.route(cursorAssets, route => {
                assets.push(route.request().url());
                return route.fulfill({ contentType: 'image/png', body: image });
            });
            await page.reload({ waitUntil: 'domcontentloaded' });
            const cursor = page.locator('img.site-cursor');
            await page.mouse.move(600, 350);
            await cursor.waitFor({ state: 'visible' });
            const source = await cursor.getAttribute('src');
            assert.match(source, /\/cw[oe]1\.webp$/);
            assert.equal(await cursor.getAttribute('aria-hidden'), 'true');
            assert.equal((await cursor.boundingBox()).width, 96);
            await page.evaluate(() => {
                document.body.insertAdjacentHTML('beforeend', '<button id="cursor-action" style="position:fixed;top:100px;left:300px;z-index:999">Action</button><dialog id="cursor-dialog"><button>Close</button></dialog>');
                window.cursorWrites = 0;
                const changes = new MutationObserver(records => { window.cursorWrites += records.length; });
                changes.observe(document.body, { attributes: true, attributeFilter: ['data-custom-cursor'] });
            });
            await page.locator('#cursor-action').hover();
            await page.mouse.move(620, 360);
            assert.equal(await page.evaluate(() => window.cursorWrites), 0);
            assert.equal(await cursor.getAttribute('src'), source);
            for (let cycle = 0; cycle < 3; cycle++) {
                await page.locator('#cursor-dialog').evaluate(dialog => dialog.showModal());
                await page.waitForFunction(() => document.querySelector('#cursor-dialog > .site-cursor'));
                await page.locator('#cursor-dialog').evaluate(dialog => dialog.close());
                await page.waitForFunction(() => document.querySelector('body > .site-cursor'));
            }
            assert.deepEqual(assets, [source]);
            assert.equal(await cursor.count(), 1);
            await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true })));
            await cursor.waitFor({ state: 'hidden' });
            assert.equal(await page.locator('body').getAttribute('data-custom-cursor'), null);
            await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
            await page.mouse.move(640, 370);
            await cursor.waitFor({ state: 'visible' });
            assert.equal(await cursor.getAttribute('src'), source);
            await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: false })));
            assert.equal(await cursor.count(), 0);
            assert.deepEqual(runtime.errors, []);
        } finally { await runtime.close(); }
    });

    test(`Cursor uses the native pointer when its image fails or input is coarse in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        try {
            await runtime.page.route(cursorAssets, route => route.abort());
            await runtime.page.reload({ waitUntil: 'domcontentloaded' });
            await runtime.page.mouse.move(420, 420);
            assert.equal(await runtime.page.locator('body').getAttribute('data-custom-cursor'), null);
            assert.equal(await runtime.page.locator('.site-cursor').isVisible(), false);
            const touch = await session(engine, { viewport: { width: 390, height: 844 }, hasTouch: true, isMobile: true });
            try {
                assert.equal(await touch.page.evaluate(() => matchMedia('(hover: hover) and (pointer: fine)').matches), false);
                assert.equal(await touch.page.locator('.site-cursor').count(), 0);
                assert.equal(touch.requests.some(url => url.includes('/ui/cursor/')), false);
            } finally { await touch.close(); }
        } finally { await runtime.close(); }
    });
}
