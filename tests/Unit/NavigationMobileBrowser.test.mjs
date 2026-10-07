import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, session, locale, phase, field, geometry, fits, evidence, directory } from './ProgramBrowserSupport.mjs';

const closed = page => page.waitForFunction(() => document.querySelector('[data-header]').dataset.mobileOpen === 'false');
const openPanel = page => page.waitForFunction(() => document.querySelector('[data-panel]').dataset.motion === 'open');
const rollSettled = page => page.waitForFunction(() => [...document.querySelectorAll('.menu-roll > span')].every(span => span.getAnimations().length === 0));

for (const engine of ['chromium', 'webkit']) {
    test(`V2 native touch, DPR and orientation in ${engine} mobile profile`, { skip: !enabled }, async () => {
        const options = {
            viewport: { width: 393, height: 873 }, isMobile: true, hasTouch: true, deviceScaleFactor: 2.75,
            userAgent: engine === 'chromium'
                ? 'Mozilla/5.0 (Linux; Android 13; 23053RN02A) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36'
                : (await import(process.env.PROGRAM_PLAYWRIGHT_MODULE)).devices['iPhone 13'].userAgent,
        };
        const cases = [];
        let version;
        for (const language of ['en', 'id', 'ar']) {
            // A fresh context distinguishes cold payload from native image-cache reuse.
            const runtime = await session(engine, options);
            const { page } = runtime;
            version = runtime.version;
            try {
                const requestStart = runtime.requests.length;
                await locale(page, language);
                await page.setViewportSize({ width: 393, height: 873 });
                const urls = await page.locator('[data-menu-media]').evaluateAll(images => images.map(image => image.src));
                const phoneRequests = runtime.requests.slice(requestStart).filter(url => urls.includes(url));
                const preparation = await page.locator('[data-menu-media]').evaluateAll(images => images.map(image => ({
                    loading: image.loading, phase: image.parentElement.dataset.mediaState,
                    width: innerWidth, capability: matchMedia('(min-width: 768px)').matches,
                })));
                assert.equal(phoneRequests.length, 0, JSON.stringify({ language, preparation, phoneRequests }));
                assert.ok(preparation.every(image => image.loading === 'lazy' && !image.capability));
                await page.evaluate(() => {
                    window.navTouchCount = 0;
                    document.addEventListener('touchend', () => { window.navTouchCount++; });
                });
                await page.locator('[data-menu-toggle]').tap();
                const trigger = page.locator('[data-panel] > summary').first();
                await trigger.tap(); await openPanel(page); await rollSettled(page);
                const state = await page.evaluate(() => ({
                    locale: document.documentElement.lang, dpr: devicePixelRatio, width: innerWidth,
                    overflow: document.documentElement.scrollWidth > innerWidth,
                    color: getComputedStyle(document.querySelector('[data-panel] summary')).color,
                    family: getComputedStyle(document.querySelector('[data-panel] summary')).fontFamily,
                    font: getComputedStyle(document.querySelector('.site-header__links strong')).fontSize,
                    mediaDisplay: getComputedStyle(document.querySelector('.site-header__media')).display,
                    touch: navigator.maxTouchPoints, touchEvents: window.navTouchCount,
                    mode: document.querySelector('[data-header]').dataset.mode,
                }));
                assert.equal(state.overflow, false);
                assert.equal(state.width, 393);
                assert.equal(state.mode, 'compact');
                assert.equal(state.color, 'rgb(17, 17, 17)');
                assert.equal(state.font, '16px', 'no unexpected mobile text inflation');
                assert.match(state.family, language === 'ar' ? /Cairo/ : /Inter/);
                assert.equal(state.mediaDisplay, 'none');
                assert.ok(state.touchEvents >= 2, 'native touch events drive burger and submenu');
                await page.screenshot({ path: `${directory}/program-third-${engine}-phone-${language}.png` });
                await trigger.tap();
                await page.waitForFunction(() => document.querySelector('[data-panel]').dataset.motion === 'closed');
                await trigger.tap(); await openPanel(page);
                await page.setViewportSize({ width: 873, height: 393 });
                await page.waitForFunction(() => [...document.querySelectorAll('.site-header__media')].every(frame => frame.dataset.mediaState === 'ready'));
                assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
                await page.screenshot({ path: `${directory}/program-third-${engine}-landscape-${language}.png` });
                await page.locator('[data-menu-toggle]').tap(); await closed(page);
                assert.equal(await page.evaluate(() => document.documentElement.hasAttribute('data-landing-menu-open')), false);
                await page.setViewportSize({ width: 393, height: 873 });
                await page.emulateMedia({ reducedMotion: 'reduce' });
                await page.locator('[data-menu-toggle]').tap();
                await trigger.tap(); await openPanel(page);
                assert.equal(await page.locator('[data-panel]').first().evaluate(group => group.getAnimations().length), 0);
                await page.locator('[data-menu-toggle]').tap(); await closed(page);
                await page.emulateMedia({ reducedMotion: 'no-preference' });
                const program = page.locator('[data-program-open]').first();
                await program.tap(); await phase(page, 'detail');
                fits(await page.evaluate(geometry));
                await page.screenshot({ path: `${directory}/program-third-${engine}-detail-${language}.png` });
                await page.locator('[data-program-back]:visible').tap(); await phase(page, 'idle');
                assert.equal(await program.evaluate(element => element === document.activeElement), true);
                await page.emulateMedia({ reducedMotion: 'reduce' });
                await field(page);
                fits(await page.evaluate(geometry));
                await page.locator('[data-program]').screenshot({ path: `${directory}/program-third-${engine}-program-${language}.png` });
                cases.push({ ...state, coldPhoneRequests: phoneRequests.length });
                assert.deepEqual(runtime.errors, []);
            } finally { await runtime.close(); }
        }
        await evidence(`program-third-mobile-${engine}`, { version, emulated: true,
            profile: '393x873 DPR2.75 touch/mobile; approximate Redmi12; WebKit mobile, not physical Android/iOS', cases });
    });
}

test('Header media preparation survives a delayed response and keeps links accessible', { skip: !enabled }, async () => {
    const runtime = await session('chromium');
    const { page } = runtime;
    try {
        let release;
        const gate = new Promise(resolve => { release = resolve; });
        await page.route('**/site/navigation/**', async route => { await gate; await route.continue(); });
        await page.reload({ waitUntil: 'domcontentloaded' });
        await page.waitForFunction(() => !!document.querySelector('[data-header]').dataset.enhanced);
        await page.locator('[data-panel] > summary').first().click(); await openPanel(page);
        const pending = await page.locator('.site-header__media').first().evaluate(frame => ({
            phase: frame.dataset.mediaState, captionOpacity: getComputedStyle(frame.querySelector('figcaption')).opacity,
            loading: frame.querySelector('img').loading,
        }));
        assert.equal(pending.phase, 'preparing');
        assert.equal(pending.captionOpacity, '0', 'no title-only flash while preparing');
        assert.equal(pending.loading, 'eager');
        assert.equal(await page.locator('[data-panel]').first().locator('.site-header__links a').first().isVisible(), true);
        release();
        await page.waitForFunction(() => document.querySelector('.site-header__media').dataset.mediaState === 'ready');
        await page.keyboard.press('Escape');
        await page.waitForFunction(() => document.querySelector('[data-panel]').dataset.motion === 'closed');
        await page.route('**/site/navigation/**', route => route.abort());
        await page.reload({ waitUntil: 'domcontentloaded' });
        await page.waitForFunction(() => document.querySelector('.site-header__media').dataset.mediaState === 'failed');
        await page.locator('[data-panel] > summary').first().click(); await openPanel(page);
        assert.equal(await page.locator('.site-header__media figcaption').first().evaluate(element => getComputedStyle(element).opacity), '1');
        assert.deepEqual(runtime.errors, []);
        await evidence('program-third-media-fallback', { pending, decodedBeforeUse: true, failedCaptionVisible: true });
    } finally { await runtime.close(); }
});
