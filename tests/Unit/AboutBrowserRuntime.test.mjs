import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import { openBrowserSession } from './AboutBrowserSession.mjs';

const url = process.env.ABOUT_BROWSER_URL;
const directory = 'docs2/proof';
const forbidden = /ad3344ec-86c3-42d6-98d0-4e35b3863888\.mp4|site\/vision\/(mission|vision)-video-v1\.mp4/;
const rect = selector => `(() => { const r = document.querySelector('${selector}').getBoundingClientRect(); return {x:r.x,y:r.y,width:r.width,height:r.height}; })()`;
const snapshot = `(() => {
    const root = document.querySelector('[data-about]');
    return {active:root.dataset.activeStory, wide:root.dataset.wide,
        playing:[...root.querySelectorAll('video')].filter(v=>!v.paused).map(v=>v.currentSrc),
        source:root.querySelector('dialog video').getAttribute('src'),
        overflow:document.documentElement.scrollWidth > innerWidth,
        background:getComputedStyle(root,'::before').backgroundImage,
        surface:getComputedStyle(root).backgroundColor,
        position:getComputedStyle(root,'::before').backgroundPosition,
        repeat:getComputedStyle(root,'::before').backgroundRepeat,
        direction:getComputedStyle(root).direction};
})()`;

async function setup() {
    const browser = await openBrowserSession(Number(process.env.ABOUT_CDP_PORT ?? 9224));
    await browser.send('Page.enable');
    await browser.send('Runtime.enable');
    await browser.send('Network.enable');
    return browser;
}
async function viewport(browser, width, height = 900) {
    await browser.send('Emulation.setDeviceMetricsOverride', { width, height, deviceScaleFactor: 1, mobile: width < 640 });
}
async function story(browser, key, wide = true) {
    await browser.evaluate(`document.querySelector('[data-about-story="${key}"]${wide ? '' : ' [data-about-inline]'}').scrollIntoView({block:'${wide ? 'start' : 'center'}'})`);
    await browser.wait(`document.querySelector('[data-about]').dataset.activeStory === '${key}'`);
}
async function switchLocale(browser, locale) {
    await browser.evaluate(`document.querySelector('form[action$="/bahasa/${locale}"]').requestSubmit()`);
    await browser.wait(`document.documentElement.lang === '${locale}' && !!document.querySelector('[data-about]')`);
}

test('About fits all six tiers and three locales with static backgrounds and poster reduced motion', { skip: !url }, async () => {
    const browser = await setup();
    const requests = [];
    const cells = [];
    browser.on('Network.requestWillBeSent', event => requests.push(event.request.url));
    try {
        await browser.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
        await viewport(browser, 1536);
        await browser.navigate(url);
        for (const locale of ['id', 'en', 'ar']) {
            await switchLocale(browser, locale);
            for (const width of [360, 390, 640, 768, 1023, 1024, 1180, 1181, 1280, 1536]) {
                await viewport(browser, width);
                await browser.wait(`document.querySelector('[data-about]').dataset.wide === '${width >= 1024}'`);
                await story(browser, 'about', width >= 1024);
                await browser.wait(`getComputedStyle(document.querySelector('[data-about]'),'::before').backgroundImage.includes('geometry') || getComputedStyle(document.querySelector('[data-about]'),'::before').backgroundImage.includes('ornament-33')`);
                const state = await browser.evaluate(snapshot);
                assert.equal(state.overflow, false, `${locale}/${width}: overflow`);
                assert.equal(state.direction, locale === 'ar' ? 'rtl' : 'ltr');
                assert.deepEqual(state.playing, []);
                assert.match(state.background, /gallery-ornament-33-v1.webp/);
                assert.equal(state.repeat, 'repeat');
                const interleaving = await browser.evaluate(`[...document.querySelectorAll('[data-about-story]')].every(s => s.children[0].hasAttribute('data-about-inline') && s.children[1].classList.contains('about__copy'))`);
                assert.equal(interleaving, true);
                cells.push({locale, width, ...state});
            }
        }
        assert.equal(requests.filter(value => forbidden.test(value)).length, 0);
        assert.equal(requests.filter(value => /site\/about-v2\/.*\.mp4/.test(value)).length, 0);
        await browser.screenshot(`${directory}/about-ar-desktop.png`);
        await viewport(browser, 390);
        await story(browser, 'mission', false);
        await browser.screenshot(`${directory}/about-ar-mobile.png`);
        await fs.writeFile(`${directory}/about-responsive.json`, JSON.stringify({profile:'System Chromium, Linux, emulated viewports, reduced motion', cells, requests}, null, 2));
    } finally { await browser.close(); }
});

test('About plays one derivative, crossfades a stable sticky stage and unloads explicit full-video dialogs', { skip: !url }, async () => {
    const browser = await setup();
    const requests = [];
    const errors = [];
    const proof = {desktop:[], mobile:[], dialogs:[]};
    browser.on('Network.requestWillBeSent', event => requests.push(event.request.url));
    browser.on('Runtime.exceptionThrown', event => errors.push(event.exceptionDetails.text));
    try {
        await viewport(browser, 1536);
        await browser.navigate(url);
        await switchLocale(browser, 'en');
        await browser.evaluate(`window.__videoOverlap = false; document.querySelector('[data-about]').addEventListener('playing', () => {if ([...document.querySelectorAll('[data-about] video')].filter(v=>!v.paused).length > 1) window.__videoOverlap = true;}, true)`);
        assert.equal(requests.some(value => /site\/about-v2\/.*\.mp4/.test(value)), false, 'no preview on initial page load');
        let frame;
        for (const key of ['about', 'vision', 'mission']) {
            await story(browser, key);
            await browser.wait(`[...document.querySelectorAll('[data-about-layer] video')].some(v=>!v.paused && v.readyState >= 2 && v.currentSrc.includes('${key}-preview'))`);
            const geometry = await browser.evaluate(rect('[data-about-stage] .about__frame'));
            if (frame) assert.deepEqual(geometry, frame);
            frame = geometry;
            const state = await browser.evaluate(snapshot);
            assert.equal(state.playing.length, 1);
            assert.match(state.playing[0], new RegExp(`${key}-preview-v1.mp4`));
            assert.equal(state.source, null);
            assert.equal(state.position, '50% 50%');
            assert.equal(state.repeat, 'repeat');
            const layers = await browser.evaluate(`new Promise(resolve => setTimeout(() => resolve([...document.querySelectorAll('[data-about-layer]')].map(l => ({opacity:Number(getComputedStyle(l).opacity),posterReady:l.querySelector('img').complete && l.querySelector('img').naturalWidth>0,videoReady:l.querySelector('video').readyState>=2}))),100))`);
            assert.ok(layers.some(l => l.opacity > 0 && (l.posterReady || l.videoReady)), 'no blank transition frame');
            proof.desktop.push({key, geometry, layers, ...state});
        }
        assert.equal(requests.filter(value => forbidden.test(value)).length, 0);
        proof.ordinaryRequests = requests.slice();
        assert.equal(await browser.evaluate('window.__videoOverlap'), false);
        assert.equal(await browser.evaluate(`document.querySelector('[data-about-stage-open]').hidden`), false);
        for (const key of ['about', 'mission']) {
            await story(browser, key);
            await browser.wait(`!document.querySelector('[data-about-stage-open]').hidden`);
            await browser.evaluate(`document.querySelector('[data-about-stage-open]').click()`);
            await browser.wait(`document.querySelector('dialog[data-about-dialog]').open`);
            const state = await browser.evaluate(snapshot);
            assert.equal(state.playing.filter(value => /preview/.test(value)).length, 0);
            assert.match(state.source, key === 'about' ? /ad3344ec/ : /mission-video/);
            await browser.send('Input.dispatchKeyEvent', {type:'rawKeyDown', key:'Escape', code:'Escape', windowsVirtualKeyCode:27, nativeVirtualKeyCode:27});
            await browser.send('Input.dispatchKeyEvent', {type:'keyUp', key:'Escape', code:'Escape', windowsVirtualKeyCode:27, nativeVirtualKeyCode:27});
            await browser.wait(`!document.querySelector('dialog[data-about-dialog]').open && !document.querySelector('[data-about-dialog] video').getAttribute('src')`);
            assert.equal(await browser.evaluate(`document.activeElement.hasAttribute('data-about-stage-open')`), true);
            proof.dialogs.push({key, opened:state, closed:await browser.evaluate(snapshot)});
        }
        await story(browser, 'vision');
        assert.equal(await browser.evaluate(`document.querySelector('[data-about-stage-open]').hidden`), true);
        assert.equal(requests.some(value => /site\/vision\/vision-video/.test(value)), false);
        await browser.evaluate('new Promise(resolve => setTimeout(resolve,450))');
        await browser.screenshot(`${directory}/about-en-desktop.png`);
        const other = await setup();
        await other.send('Page.bringToFront');
        await browser.wait('document.hidden');
        await browser.wait(`[...document.querySelectorAll('[data-about] video')].every(v=>v.paused)`);
        proof.documentHidden = await browser.evaluate(snapshot);
        await browser.send('Page.bringToFront');
        await browser.wait('!document.hidden');
        await browser.wait(`[...document.querySelectorAll('[data-about] video')].some(v=>!v.paused)`);
        await other.close();
        await browser.evaluate('scrollTo(0,0)');
        await browser.wait(`[...document.querySelectorAll('[data-about] video')].every(v=>v.paused)`);
        await viewport(browser, 390, 844);
        for (const key of ['about', 'vision', 'mission']) {
            await story(browser, key, false);
            await browser.wait(`[...document.querySelectorAll('[data-about-inline] video')].some(v=>!v.paused && v.readyState>=2 && v.currentSrc.includes('${key}-preview'))`);
            const state = await browser.evaluate(snapshot);
            assert.equal(state.playing.length, 1);
            assert.equal(state.overflow, false);
            assert.equal(await browser.evaluate(`document.querySelector('[data-about-stage]').hidden`), true);
            proof.mobile.push({key, ...state});
        }
        await browser.send('Emulation.setEmulatedMedia', {features:[{name:'prefers-reduced-motion', value:'reduce'}]});
        await browser.wait(`[...document.querySelectorAll('[data-about] video')].every(v=>v.paused)`);
        assert.deepEqual(errors, []);
        await fs.writeFile(`${directory}/about-lifecycle.json`, JSON.stringify({profile:'System Chromium, Linux, real R2 media', ...proof, requests, errors}, null, 2));
    } finally { await browser.close(); }
});
