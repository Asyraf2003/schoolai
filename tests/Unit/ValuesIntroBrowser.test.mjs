import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const widths = [360, 640, 768, 1024, 1280, 1536, 1920];
const valueState = () => {
    const world = document.querySelector('[data-program-values]');
    const root = document.querySelector('[data-values]');
    const heading = root.querySelector('h2');
    const home = document.querySelector('[data-program-type-home]').getBoundingClientRect();
    const clips = [...heading.children].map(clip => {
        const box = clip.getBoundingClientRect();
        const text = clip.firstElementChild;
        const range = document.createRange(); range.selectNodeContents(text);
        const ink = range.getBoundingClientRect();
        return { shift: new DOMMatrix(getComputedStyle(clip).transform).m41,
            box: { x: box.x, top: box.top, right: box.right, bottom: box.bottom },
            ink: { x: ink.x, right: ink.right, top: ink.top, bottom: ink.bottom },
            opacity: getComputedStyle(text).opacity };
    });
    return { width: innerWidth, lang: document.documentElement.lang,
        overflow: document.documentElement.scrollWidth > innerWidth,
        color: getComputedStyle(world).backgroundColor,
        pct: world.style.getPropertyValue('--program-values-morph-pct'),
        clips, font: getComputedStyle(heading).fontFamily,
        planes: document.querySelectorAll('[data-program-type-home]').length,
        headerBottom: Math.max(0, document.querySelector('[data-header]').getBoundingClientRect().bottom),
        planeBottom: home.bottom, valuesBottom: root.getBoundingClientRect().bottom,
        animations: root.getAnimations({ subtree: true }).length };
};

for (const engine of engines) {
    test(`Values shares Program's field, morphs to blue and fits all tiers in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            for (const language of ['id', 'en', 'ar']) {
                await locale(page, language);
                for (const width of widths) {
                    await page.setViewportSize({ width, height: 900 });
                    await page.locator('[data-values]').scrollIntoViewIfNeeded();
                    await page.waitForFunction(() => document.querySelector('[data-program-values]').style.getPropertyValue('--program-values-morph-pct') === '100.00%');
                    await page.waitForFunction(() => document.querySelector('[data-values]').getAnimations({ subtree: true }).length === 0);
                    const state = await page.evaluate(valueState);
                    assert.equal(state.overflow, false);
                    assert.equal(state.planes, 1);
                    assert.ok(Math.abs(state.planeBottom - state.valuesBottom) < 1, 'same background plane spans Values');
                    assert.ok(state.clips.every(clip => clip.opacity === '1'));
                    for (const clip of state.clips) {
                        assert.ok(clip.ink.x >= clip.box.x - 1 && clip.ink.right <= clip.box.right + 1, 'title ink fits its mask');
                        assert.ok(clip.box.x >= -1 && clip.box.right <= width + 1, 'shifted title fits the viewport');
                        assert.ok(clip.box.top >= state.headerBottom - 1, `${language}/${width}: visible line mask remains below Header`);
                    }
                    const expected = width < 768 ? 0 : ({ 768: 38.4, 1024: 61.44, 1280: 89.6, 1536: 122.88, 1920: 153.6 }[width]);
                    assert.ok(Math.abs(state.clips[1].shift - expected * (language === 'ar' ? -1 : 1)) < .02);
                    if (language === 'ar') assert.match(state.font, /Cairo/);
                    cases.push(state);
                    if (width === 1440 || width === 1536 || width === 360) await page.screenshot({ path: `${directory}/values-${engine}-${language}-${width}.png` });
                }
            }
            await page.setViewportSize({ width: 1440, height: 900 });
            const colors = [];
            for (const ratio of [1.2, .94, .68, .94, 1.2]) {
                await page.evaluate(ratio => {
                    const bottom = document.querySelector('[data-program]').getBoundingClientRect().bottom + scrollY;
                    scrollTo(0, bottom - innerHeight * ratio);
                }, ratio);
                await page.waitForTimeout(80);
                colors.push(await page.evaluate(valueState));
            }
            assert.equal(colors[0].pct, '0.00%');
            assert.ok(Math.abs(parseFloat(colors[1].pct) - 50) < .35, 'halfway color tolerates native scroll pixel rounding');
            assert.equal(colors[2].pct, '100.00%');
            assert.equal(colors[3].color, colors[1].color, 'reverse scroll restores the same color');
            assert.equal(colors[4].color, colors[0].color);
            await page.evaluate(() => window.dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true })));
            await page.locator('[data-values]').scrollIntoViewIfNeeded();
            assert.equal((await page.evaluate(valueState)).pct, colors[4].pct, 'suspended pages do not paint scroll changes');
            await page.evaluate(() => window.dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
            await page.waitForFunction(() => document.querySelector('[data-program-values]').style.getPropertyValue('--program-values-morph-pct') === '100.00%');
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-intro-layout-${engine}`, { cases, colors });
        } finally { await runtime.close(); }
    });

    test(`Values reveals complete lines before its old-style shift and restores Program detail in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            const fixture = process.env.PROGRAM_GSAP_FIXTURE;
            const script = fixture ? await fs.readFile(fixture, 'utf8') : null;
            await page.route('**/gsap@3.7.1/**', route => script
                ? route.fulfill({ contentType: 'application/javascript', body: script }) : route.abort());
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelector('[data-values-heading]').dataset.revealed === 'false');
                await page.waitForFunction(() => [...document.querySelectorAll('[data-values-heading-line]')].every(line => getComputedStyle(line).opacity === '0'));
                await page.locator('[data-values]').scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelector('[data-values-heading]').dataset.revealed === 'true');
                const frames = await page.locator('[data-values-heading]').evaluate(heading => {
                    const animations = heading.getAnimations({ subtree: true });
                    animations.forEach(animation => animation.pause());
                    return [0, 450, 900, 1340, 1780].map(time => {
                        animations.forEach(animation => { animation.currentTime = time; });
                        return { time, x: new DOMMatrix(getComputedStyle(heading.lastElementChild).transform).m41,
                            lines: [...heading.querySelectorAll('[data-values-heading-line]')].map(line => ({
                                y: new DOMMatrix(getComputedStyle(line).transform).m42, opacity: Number(getComputedStyle(line).opacity),
                            })) };
                    });
                });
                assert.equal(frames[1].x, 0);
                assert.equal(frames[2].x, 0);
                assert.ok(frames[2].lines.every(line => Math.abs(line.y) < .01 && line.opacity === 1));
                assert.ok(Math.abs(frames.at(-1).x - (language === 'ar' ? -100.8 : 100.8)) < .02);
                await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
                await page.locator('[data-program-open]').first().click();
                await page.waitForFunction(() => document.querySelector('[data-program-dialog]').open);
                await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programState === 'detail');
                assert.equal(await page.locator('[data-program-dialog] [data-program-type-viewport]').count(), 1);
                assert.equal(await page.locator('[data-program-type]').evaluate(type => getComputedStyle(type).translate), 'none');
                await page.keyboard.press('Escape');
                await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programState === 'idle');
                assert.equal(await page.locator('[data-program-type-home] [data-program-type-viewport]').count(), 1);
                cases.push({ language, frames, detailRestored: true, detailMode: fixture ? 'gsap-3.7.1-fixture' : 'fallback' });
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-intro-sequence-${engine}`, { cases });
        } finally { await runtime.close(); }
    });

    test(`Values heading remains readable without JavaScript, reduced motion or IO in ${engine}`, { skip: !enabled }, async () => {
        const cases = [];
        for (const options of [{ javaScriptEnabled: false }, { reducedMotion: 'reduce' }, { missingIO: true }]) {
            const runtime = await session(engine, options.missingIO ? {} : options);
            const { page } = runtime;
            try {
                if (options.missingIO) {
                    await page.addInitScript(() => { delete window.IntersectionObserver; });
                    await page.reload({ waitUntil: 'domcontentloaded' });
                }
                for (const language of ['id', 'en', 'ar']) {
                    if (options.javaScriptEnabled === false) await Promise.all([
                        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
                        page.locator(`form[action$="/bahasa/${language}"]`).evaluate(form => form.requestSubmit()),
                    ]);
                    else await locale(page, language);
                    await page.evaluate(() => document.fonts.ready);
                    for (const width of [360, 768, 1536]) {
                        await page.setViewportSize({ width, height: 900 });
                        await page.locator('[data-values]').scrollIntoViewIfNeeded();
                        const state = await page.evaluate(valueState);
                        assert.equal(state.overflow, false);
                        assert.ok(state.clips.every(clip => clip.opacity === '1' && clip.shift === 0));
                        assert.equal(state.animations, 0);
                        assert.equal(await page.locator('[data-program-values][data-values-enhanced]').count(), 0);
                        cases.push({ options, ...state });
                    }
                }
                assert.deepEqual(runtime.errors, []);
            } finally { await runtime.close(); }
        }
        await evidence(`values-intro-fallback-${engine}`, { cases });
    });
}
