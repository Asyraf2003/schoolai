import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import { openBrowserSession } from './AboutBrowserSession.mjs';

const url=process.env.ABOUT_BROWSER_URL;
const pause=ms=>new Promise(resolve=>setTimeout(resolve,ms));
async function setup(width=1920,height=1080) {
    const b=await openBrowserSession();
    await b.send('Page.enable');
    await b.send('Emulation.setDeviceMetricsOverride',{width,height,deviceScaleFactor:1,mobile:width<640});
    return b;
}
async function click(b,selector='[data-audio]',fraction=.5) {
    const p=await b.evaluate(`(() => {const r=document.querySelector('${selector}').getBoundingClientRect();return {x:r.x+r.width*${fraction},y:r.y+r.height/2};})()`);
    await b.send('Input.dispatchMouseEvent',{type:'mousePressed',button:'left',clickCount:1,...p});
    await b.send('Input.dispatchMouseEvent',{type:'mouseReleased',button:'left',clickCount:1,...p});
}
const count=`document.querySelectorAll('.site-header__audio-ripple').length`;
const metrics=`(() => {
    const info=e=>{const r=e.getBoundingClientRect(),s=getComputedStyle(e);return {y:r.y,height:r.height,font:s.fontSize,weight:s.fontWeight,line:s.lineHeight,borderColor:s.borderBottomColor};};
    const a=document.querySelector('[data-audio]');
    return {locale:document.documentElement.lang,mode:document.querySelector('[data-header]').dataset.mode,
        sound:info(a.querySelector('[data-audio-label]')),menu:info(document.querySelector('.site-header__items>li>a [data-menu-label]')),
        soundButton:info(a),waveDisplay:getComputedStyle(a.querySelector('canvas')).display,
        overflow:document.documentElement.scrollWidth>innerWidth};
})()`;

test('Desktop Sound shares menu typography and vertical alignment across locales and navigation boundaries', {skip:!url}, async () => {
    const b=await setup();const cases=[];
    try {
        await b.send('Emulation.setEmulatedMedia',{features:[{name:'prefers-reduced-motion',value:'reduce'}]});
        await b.navigate(url);
        for(const locale of ['id','en','ar']) {
            await b.evaluate(`document.querySelector('form[action$="/bahasa/${locale}"]').requestSubmit()`);
            await b.wait(`document.documentElement.lang==='${locale}'&&!!document.querySelector('[data-header]')`);
            for(const [width,height] of [[360,844],[390,844],[640,900],[768,1024],[1024,900],[1024,1366],[1180,1366],[1181,1366],[1280,900],[1536,900],[1920,1080]]) {
                const desktop=width>=1181||(width>=1024&&width>height);
                await b.send('Emulation.setDeviceMetricsOverride',{width,height,deviceScaleFactor:1,mobile:width<640});
                await b.wait(`document.querySelector('[data-header]').dataset.mode==='${desktop?'desktop':'compact'}'`);
                await b.evaluate('scrollTo(0,0)');
                await pause(50);
                const s=await b.evaluate(metrics);
                assert.equal(s.overflow,false);
                if(desktop) {
                    assert.equal(s.sound.font,s.menu.font);
                    assert.equal(s.sound.weight,s.menu.weight);
                    assert.equal(s.sound.line,s.menu.line);
                    assert.ok(Math.abs(s.sound.y-s.menu.y)<.5&&Math.abs(s.sound.height-s.menu.height)<.5);
                    assert.equal(s.sound.borderColor,'rgba(0, 0, 0, 0)');
                    assert.equal(s.waveDisplay,'none');
                } else assert.equal(s.waveDisplay,'block');
                cases.push({width,height,...s});
            }
        }
        await fs.writeFile('docs2/proof/header-sound-alignment.json',JSON.stringify({profile:'System Chromium153/Linux, reduced motion, three locales/11 viewport cases',cases},null,2));
    } finally {await b.close();}
});

test('Sound water tap responds once to pointer/keyboard and cancels for reduced motion, hidden state and compact mode', {skip:!url}, async () => {
    const b=await setup();const proof={};
    try {
        await b.navigate(url);
        if(await b.evaluate('document.documentElement.lang')!=='en') {
            await b.evaluate(`document.querySelector('form[action$="/bahasa/en"]').requestSubmit()`);
            await b.wait(`document.documentElement.lang==='en'&&!!document.querySelector('[data-audio]')`);
        }
        await b.wait(`!document.querySelector('[data-audio]').hidden`);
        await click(b,'.site-header__group>summary');
        await b.wait(`document.querySelector('[data-header]').dataset.open==='true'`);
        await click(b,'[data-audio]',.25);
        await b.wait(`${count}===1&&document.querySelector('[data-audio]').getAttribute('aria-pressed')==='true'`);
        assert.equal(await b.evaluate(`document.querySelector('.site-header__audio-ripple').getAttribute('aria-hidden')`),'true');
        assert.equal(await b.evaluate(`document.querySelector('[data-hero] video').muted`),false);
        proof.after=await b.evaluate(metrics);
        await b.send('Input.dispatchMouseEvent',{type:'mouseMoved',x:20,y:300});
        await pause(80);
        await b.screenshot('docs2/proof/header-sound-ripple.png');
        proof.ripple=await b.evaluate(`(() => {const r=document.querySelector('.site-header__audio-ripple'),s=getComputedStyle(r);return {opacity:s.opacity,transform:s.transform,duration:s.animationDuration,iterations:s.animationIterationCount,pointer:s.pointerEvents,originX:r.style.getPropertyValue('--sound-ripple-x')};})()`);
        assert.equal(proof.ripple.iterations,'1');
        assert.equal(proof.ripple.pointer,'none');
        await b.wait(`${count}===0`);
        await b.evaluate(`document.querySelector('[data-audio]').focus()`);
        await b.send('Input.dispatchKeyEvent',{type:'keyDown',key:'Enter',code:'Enter',text:'\r',unmodifiedText:'\r',windowsVirtualKeyCode:13,nativeVirtualKeyCode:13});
        await b.send('Input.dispatchKeyEvent',{type:'keyUp',key:'Enter',code:'Enter',windowsVirtualKeyCode:13,nativeVirtualKeyCode:13});
        await b.wait(`${count}===1&&document.querySelector('[data-audio]').getAttribute('aria-pressed')==='false'`);
        proof.keyboard=true;
        for(let i=0;i<4;i++) {await click(b);assert.equal(await b.evaluate(count),1);}
        proof.repeatedTapSingleNode=true;
        const other=await setup();await other.send('Page.bringToFront');
        await b.wait('document.hidden');await b.wait(`${count}===0`);
        await b.send('Page.bringToFront');await other.close();
        proof.hiddenCleanup=true;
        await b.send('Emulation.setEmulatedMedia',{features:[{name:'prefers-reduced-motion',value:'reduce'}]});
        const before=await b.evaluate(`document.querySelector('[data-audio]').getAttribute('aria-pressed')`);
        await click(b);
        assert.equal(await b.evaluate(count),0);
        assert.notEqual(await b.evaluate(`document.querySelector('[data-audio]').getAttribute('aria-pressed')`),before);
        proof.reducedMotionKeepsAudio=true;
        await b.send('Emulation.setEmulatedMedia',{features:[]});
        await click(b);assert.equal(await b.evaluate(count),1);
        await b.send('Emulation.setDeviceMetricsOverride',{width:390,height:844,deviceScaleFactor:1,mobile:true});
        await b.wait(`document.querySelector('[data-header]').dataset.mode==='compact'`);
        assert.equal(await b.evaluate(count),0);
        await click(b);assert.equal(await b.evaluate(count),0);
        proof.compactWaveUnchanged=true;
        await fs.writeFile('docs2/proof/header-sound-interaction.json',JSON.stringify({profile:'System Chromium153/Linux, actual pointer and Enter clicks/real Hero audio',...proof},null,2));
    } finally {await b.close();}
});

test('Desktop Sound ripple remains usable without a canvas context and cleanup handles pagehide', {skip:!url}, async () => {
    const b=await setup();
    try {
        await b.send('Page.addScriptToEvaluateOnNewDocument',{source:'HTMLCanvasElement.prototype.getContext=()=>null'});
        await b.navigate(url);
        await click(b);await b.wait(`${count}===1`);
        assert.equal(await b.evaluate(`document.querySelector('[data-audio]').hasAttribute('data-wave-ready')`),false);
        await b.evaluate(`dispatchEvent(new PageTransitionEvent('pagehide',{persisted:true}))`);
        assert.equal(await b.evaluate(count),0);
        await b.evaluate(`dispatchEvent(new PageTransitionEvent('pageshow',{persisted:true}))`);
        await click(b);await b.wait(`${count}===1`);
        await b.evaluate(`dispatchEvent(new PageTransitionEvent('pagehide',{persisted:false}))`);
        assert.equal(await b.evaluate(count),0);
        await b.evaluate(`document.querySelector('[data-audio]').hidden=false;document.querySelector('[data-audio]').click()`);
        assert.equal(await b.evaluate(count),0);
        await fs.writeFile('docs2/proof/header-sound-fallback.json',JSON.stringify({canvasUnavailable:true,rippleWorks:true,syntheticPersistedPagehideResumes:true,syntheticDisposeNoNewFeedback:true},null,2));
    } finally {await b.close();}
});
