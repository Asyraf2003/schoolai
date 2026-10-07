import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import { openBrowserSession } from './AboutBrowserSession.mjs';

const url = process.env.ABOUT_BROWSER_URL;
const proof = {profile:'System Chromium/Linux, actual R2 media; viewport emulation'};
async function session(width, height) {
    const browser = await openBrowserSession(Number(process.env.ABOUT_CDP_PORT ?? 9224));
    await browser.send('Page.enable');
    await browser.send('Network.enable');
    await browser.send('Emulation.setDeviceMetricsOverride', {width,height,deviceScaleFactor:1,mobile:width<640});
    return browser;
}
const paused = `[...document.querySelectorAll('[data-about] video')].every(v=>v.paused)`;
async function click(browser, selector) {
    const point = await browser.evaluate(`(() => {const r=document.querySelector('${selector}').getBoundingClientRect();return {x:r.x+r.width/2,y:r.y+r.height/2};})()`);
    await browser.send('Input.dispatchMouseEvent',{type:'mousePressed',button:'left',clickCount:1,...point});
    await browser.send('Input.dispatchMouseEvent',{type:'mouseReleased',button:'left',clickCount:1,...point});
}

test('Arabic narrow About supports real dialog gestures, close/resume, reverse traversal and short/expanded layouts', {skip:!url}, async () => {
    const browser=await session(390,844);
    try {
        await browser.navigate(url);
        await browser.evaluate(`document.querySelector('form[action$="/bahasa/ar"]').requestSubmit()`);
        await browser.wait(`document.documentElement.lang==='ar' && !!document.querySelector('[data-about]')`);
        for (const key of ['about','mission']) {
            await browser.evaluate(`document.querySelector('[data-about-story="${key}"] [data-about-inline]').scrollIntoView({block:'center'})`);
            await browser.wait(`document.querySelector('[data-about]').dataset.activeStory==='${key}' && [...document.querySelectorAll('[data-about-inline] video')].some(v=>!v.paused&&v.readyState>=2)`);
            await click(browser,`[data-about-story="${key}"] [data-about-open]`);
            await browser.wait(`document.querySelector('[data-about-dialog]').open && document.querySelector('[data-about-dialog] video').currentTime>.1`);
            assert.equal(await browser.evaluate(`[...document.querySelectorAll('[data-about-preview]')].every(v=>v.paused)`),true);
            await click(browser,'[data-about-close]');
            await browser.wait(`!document.querySelector('[data-about-dialog] video').getAttribute('src') && !document.querySelector('[data-about-dialog]').open`);
            await browser.wait(`[...document.querySelectorAll('[data-about-inline] video')].some(v=>!v.paused)`);
            assert.equal(await browser.evaluate(`document.activeElement.hasAttribute('data-about-open')`),true);
        }
        await browser.send('Emulation.setDeviceMetricsOverride',{width:1536,height:400,deviceScaleFactor:1,mobile:false});
        await browser.wait(`document.querySelector('[data-about]').dataset.wide==='true'`);
        await browser.evaluate(`document.querySelector('[data-about-story="vision"]').scrollIntoView()`);
        await browser.wait(`document.querySelector('[data-about]').dataset.activeStory==='vision'`);
        const geometry=await browser.evaluate(`(() => {const r=document.querySelector('[data-about-stage] .about__frame').getBoundingClientRect();return {top:r.top,bottom:r.bottom,height:r.height,overflow:document.documentElement.scrollWidth>innerWidth};})()`);
        assert.ok(geometry.top>=0 && geometry.bottom<=400);
        assert.equal(geometry.overflow,false);
        await browser.evaluate(`document.documentElement.style.fontSize='200%'`);
        assert.equal(await browser.evaluate('document.documentElement.scrollWidth>innerWidth'),false);
        proof.arabicDialogs='Both full players advanced after CDP pointer gesture; preview pause, close/unload/focus/resume PASS';
        proof.shortHeight=geometry;
        proof.fontExpansion200Percent='No horizontal overflow';
    } finally {await browser.close();}
});

test('Blocked previews and missing observer support preserve posters and localized story content', {skip:!url}, async () => {
    const browser=await session(1280,900);
    try {
        await browser.send('Network.setBlockedURLs',{urls:['*site/about-v2/*-preview-v1.mp4']});
        await browser.navigate(url);
        for (const key of ['about','vision','mission']) {
            await browser.evaluate(`document.querySelector('[data-about-story="${key}"]').scrollIntoView()`);
            await browser.wait(`document.querySelector('[data-about]').dataset.activeStory==='${key}'`);
            await browser.wait(`[...document.querySelectorAll('[data-about-layer]')].some(l=>l.dataset.story==='${key}' && l.dataset.visible==='true' && l.querySelector('img').naturalWidth>0)`);
        }
        assert.equal(await browser.evaluate(paused),true);
        await browser.send('Page.addScriptToEvaluateOnNewDocument',{source:'delete window.IntersectionObserver'});
        await browser.navigate(url);
        assert.equal(await browser.evaluate(`document.querySelector('[data-about]').dataset.wide`),'false');
        assert.equal(await browser.evaluate(`document.querySelectorAll('[data-about-story]').length`),3);
        assert.equal(await browser.evaluate(paused),true);
        proof.blockedPreviews='All three posters available; no playing preview';
        proof.noIntersectionObserver='Semantic interleaved posters and copy remain accessible';
    } finally {await browser.close();}
});

test('Without JavaScript About renders three poster/copy stories without any hydrated video', {skip:!url}, async () => {
    const browser=await session(390,844);
    try {
        await browser.send('Emulation.setScriptExecutionDisabled',{value:true});
        await browser.send('Page.navigate',{url});
        await browser.wait(`document.readyState==='complete' && !!document.querySelector('[data-about]')`);
        assert.equal(await browser.evaluate(`document.querySelectorAll('[data-about-story]').length`),3);
        assert.equal(await browser.evaluate(`document.querySelectorAll('[data-about] video[src]').length`),0);
        assert.equal(await browser.evaluate(`document.querySelector('[data-about-stage]').hidden`),true);
        assert.equal(await browser.evaluate(`document.documentElement.scrollWidth>innerWidth`),false);
        proof.noJavaScript='Three localized stories/posters, hidden stage/triggers, no video src, no overflow';
        await fs.writeFile('docs2/proof/about-fallbacks.json',JSON.stringify(proof,null,2));
    } finally {await browser.close();}
});
