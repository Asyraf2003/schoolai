import test from 'node:test';
import assert from 'node:assert/strict';
import { chooseCharacter, cursorState } from '../../resources/js/sections/cursor.js';
import { enabled, engines, session } from './ProgramBrowserSupport.mjs';

const BASE = 'https://media.almustaqbal.sch.id/ui/cursor';
const EXPECTED = {
    cwo: { default: `${BASE}/cwo1.webp`, pointer: `${BASE}/cwo.webp` },
    cwe: { default: `${BASE}/cwe.webp`, pointer: `${BASE}/cwe1.webp` },
};
const cursorAssets = '**/ui/cursor/*.webp';
const image = Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aEq0AAAAASUVORK5CYII=', 'base64');

test('Cursor picks one of the existing identities from the supplied initialization sample', () => {
    let calls = 0;
    assert.equal(chooseCharacter(() => { calls++; return 0.25; }), 'cwo');
    assert.equal(calls, 1);
    assert.equal(chooseCharacter(() => 0.75), 'cwe');
});

test('Cursor maps native pointer-intent, nested targets, inline overrides, disabled and inert controls', () => {
    assert.equal(cursorState(null), 'default');
    const makeNode = ({ matchesFn = () => false, closestFn = () => null, style = {}, parentElement = null } = {}) => ({
        nodeType: 1,
        style,
        parentElement,
        matches: matchesFn,
        closest: closestFn,
    });
    const enabledButton = makeNode({ closestFn: sel => (sel === ':disabled, [aria-disabled="true"], [inert]' ? null : sel.includes('button') ? {} : null) });
    assert.equal(cursorState(enabledButton), 'pointer');
    const disabledControl = makeNode({ closestFn: sel => (sel === ':disabled, [aria-disabled="true"], [inert]' ? {} : null) });
    assert.equal(cursorState(disabledControl), 'default');
    const nestedInLink = makeNode({
        parentElement: makeNode({ matchesFn: sel => sel.includes(':any-link') }),
        closestFn: sel => (sel === '[style*="cursor"]' ? null : sel.includes(':any-link') ? {} : null),
    });
    assert.equal(cursorState(nestedInLink), 'pointer');
    const inlineSpanInsideDefault = makeNode({
        parentElement: makeNode({ style: { cursor: 'pointer' } }),
        closestFn: sel => (sel === '[style*="cursor"]' ? {} : null),
    });
    assert.equal(cursorState(inlineSpanInsideDefault), 'pointer');
});

for (const engine of engines) {
    test(`Cursor switches two-state assets for both characters across pointer, disabled, nested, dialog, fullscreen and BFCache in ${engine}`, { skip: !enabled }, async () => {
        for (const [sample, expectedCharacter] of [[0.1, 'cwo'], [0.9, 'cwe']]) {
            const runtime = await session(engine);
            const { page } = runtime;
            const assets = [];
            try {
                await page.addInitScript(val => { Math.random = () => val; }, sample);
                await page.route(cursorAssets, route => {
                    assets.push(route.request().url());
                    return route.fulfill({ contentType: 'image/png', body: image, headers: { 'cache-control': 'public, max-age=3600' } });
                });
                await page.reload({ waitUntil: 'domcontentloaded' });
                const cursor = page.locator('img.site-cursor');
                await page.mouse.move(10, 10);
                await cursor.waitFor({ state: 'visible' });
                const pair = EXPECTED[expectedCharacter];
                assert.equal(await cursor.getAttribute('data-character'), expectedCharacter);
                assert.equal(await cursor.getAttribute('data-state'), 'default');
                assert.equal(await cursor.getAttribute('src'), pair.default);
                assert.equal(await cursor.getAttribute('aria-hidden'), 'true');
                assert.equal((await cursor.boundingBox()).width, 96);
                assert.deepEqual([...new Set(assets)].sort(), [pair.default, pair.pointer].sort());

                await page.evaluate(() => {
                    document.body.insertAdjacentHTML('beforeend', `
                        <button id="c-btn" style="position:fixed;top:90px;left:220px;z-index:999"><span id="c-btn-child">Act</span></button>
                        <button id="c-dis" disabled style="position:fixed;top:140px;left:220px;z-index:999">Dis</button>
                        <a id="c-link" href="#demo" style="position:fixed;top:190px;left:220px;z-index:999"><span id="c-link-child">Link</span></a>
                        <div id="c-plain" style="position:fixed;top:240px;left:220px;width:80px;height:30px;z-index:999">Plain</div>
                        <dialog id="c-dialog"><button id="c-dialog-btn">Close</button></dialog>
                    `);
                    window.cursorWrites = 0;
                    window.srcWrites = 0;
                    new MutationObserver(r => { window.cursorWrites += r.length; }).observe(document.body, { attributes: true, attributeFilter: ['data-custom-cursor'] });
                    new MutationObserver(r => { window.srcWrites += r.length; }).observe(document.querySelector('img.site-cursor'), { attributes: true, attributeFilter: ['src'] });
                });

                await page.locator('#c-plain').hover();
                assert.equal(await cursor.getAttribute('data-state'), 'default');
                assert.equal(await cursor.getAttribute('src'), pair.default);
                assert.equal(await page.evaluate(() => window.srcWrites), 0);

                await page.locator('#c-btn-child').hover();
                await page.waitForFunction(s => document.querySelector('img.site-cursor').dataset.state === s, 'pointer');
                assert.equal(await cursor.getAttribute('src'), pair.pointer);
                await page.locator('#c-link-child').hover();
                assert.equal(await cursor.getAttribute('data-state'), 'pointer');
                assert.equal(await page.evaluate(() => window.srcWrites), 1);

                await page.locator('#c-dis').hover();
                await page.waitForFunction(s => document.querySelector('img.site-cursor').dataset.state === s, 'default');
                assert.equal(await cursor.getAttribute('src'), pair.default);
                assert.equal(await page.evaluate(() => window.cursorWrites), 0);

                await page.locator('#c-dialog').evaluate(d => d.showModal());
                await page.waitForFunction(() => document.querySelector('#c-dialog > .site-cursor'));
                await page.locator('#c-dialog-btn').hover();
                await page.waitForFunction(s => document.querySelector('img.site-cursor').dataset.state === s, 'pointer');
                await page.locator('#c-dialog').evaluate(d => d.close());
                await page.waitForFunction(() => document.querySelector('body > .site-cursor'));

                assert.equal(await cursor.count(), 1);
                await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true })));
                await cursor.waitFor({ state: 'hidden' });
                assert.equal(await page.locator('body').getAttribute('data-custom-cursor'), null);
                await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
                await page.mouse.move(10, 10);
                await cursor.waitFor({ state: 'visible' });
                assert.equal(await cursor.getAttribute('src'), pair.default);
                await page.evaluate(() => dispatchEvent(new PageTransitionEvent('pagehide', { persisted: false })));
                assert.equal(await cursor.count(), 0);
                assert.deepEqual(runtime.errors, []);
            } finally { await runtime.close(); }
        }
    });

    test(`Cursor uses the native pointer when either identity asset fails or input is coarse in ${engine}`, { skip: !enabled }, async () => {
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
