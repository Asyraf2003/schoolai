import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import { openBrowserSession } from './AboutBrowserSession.mjs';

const url = process.env.ABOUT_BROWSER_URL;
const phase = process.env.ABOUT_COMPOSITION_PHASE ?? 'after';
const family = process.env.ABOUT_COMPOSITION_PREFIX ?? 'about-label';
const measure = `(() => {
    const box = selector => {const r=document.querySelector(selector).getBoundingClientRect();return {x:r.x,y:r.y,width:r.width,height:r.height,centerY:r.y+r.height/2};};
    const root=document.querySelector('[data-about]');
    const key=root.dataset.activeStory;
    const texture=getComputedStyle(root,'::before');
    const copy=document.querySelector('[data-about-story="'+key+'"] .about__copy');
    const label=copy.querySelector('.about__label'),range=document.createRange();range.selectNodeContents(label);
    return {locale:document.documentElement.lang,key,scrollY,header:box('.site-header__bar'),logo:box('.site-header__brand img'),
        frame:box('[data-about-stage] .about__frame'),stage:box('[data-about-stage]'),story:box('[data-about-story="'+key+'"]'),
        copy:box('[data-about-story="'+key+'"] .about__copy'),headlineFont:getComputedStyle(copy.querySelector('h3')).fontSize,
        bodyFont:getComputedStyle(copy.querySelector('p.about__body')??copy.querySelector('ol')).fontSize,
        mascot:document.querySelector('.about__mascot')?box('.about__mascot'):null,
        label:{font:getComputedStyle(label).fontSize,textWidth:range.getBoundingClientRect().width,lineWidth:parseFloat(getComputedStyle(label,'::after').width),direction:getComputedStyle(label).direction},
        titleWeight:getComputedStyle(copy.querySelector('h3')).fontWeight,
        bodyLeading:getComputedStyle(copy.querySelector('p.about__body')??copy.querySelector('ol')).lineHeight,
        bodyColor:getComputedStyle(copy.querySelector('p.about__body')??copy.querySelector('ol')).color,
        gap:getComputedStyle(copy.querySelector('p.about__body')??copy.querySelector('ol')).marginBlockStart,
        shadow:getComputedStyle(root.querySelector('.about__stage .about__frame')).boxShadow,
        navFont:getComputedStyle(document.querySelector('.site-header__bar')).fontSize,
        radius:getComputedStyle(root.querySelector('.about__stage .about__frame')).borderRadius,
        background:getComputedStyle(root).backgroundColor,
        motif:texture.backgroundImage==='none'?getComputedStyle(root).backgroundImage:texture.backgroundImage,
        motifOpacity:texture.opacity,overflow:document.documentElement.scrollWidth>innerWidth};
})()`;
const pause = ms => new Promise(resolve=>setTimeout(resolve,ms));

test('Header and About desktop composition has a shared usable-viewport center and dominant display', {skip:!url}, async () => {
    const browser=await openBrowserSession();
    const proof={profile:'Chromium153 Linux, 1920x1080, EN, reduced-motion posters for repeatable comparison',phase,states:[],menus:[]};
    try {
        await browser.send('Page.enable');
        await browser.send('Emulation.setDeviceMetricsOverride',{width:1920,height:1080,deviceScaleFactor:1,mobile:false});
        await browser.send('Emulation.setEmulatedMedia',{features:[{name:'prefers-reduced-motion',value:'reduce'}]});
        await browser.navigate(url);
        if (await browser.evaluate('document.documentElement.lang')!=='en') {
            await browser.evaluate(`document.querySelector('form[action$="/bahasa/en"]').requestSubmit()`);
            await browser.wait(`document.documentElement.lang==='en' && !!document.querySelector('[data-about]')`);
        }
        await browser.evaluate('scrollTo(0,0)');
        await pause(150);
        proof.header=await browser.evaluate(measure);
        await browser.screenshot(`docs2/proof/${family}-${phase}-header.png`);
        await browser.evaluate('scrollTo(0,innerHeight*.6)');
        await pause(150);
        await browser.wait(`document.querySelector('[data-about-layer] img').naturalWidth>0`);
        await browser.screenshot(`docs2/proof/${family}-${phase}-entry.png`);
        for (const key of ['about','vision','mission']) {
            await browser.evaluate(`(() => {const r=document.querySelector('[data-about-story="${key}"]').getBoundingClientRect();const h=document.querySelector('.site-header__bar').getBoundingClientRect().height;scrollTo(0,scrollY+r.top-h);})()`);
            await browser.wait(`document.querySelector('[data-about]').dataset.activeStory==='${key}'`);
            await browser.wait(`[...document.querySelectorAll('[data-about-layer]')].some(l=>l.dataset.story==='${key}' && l.dataset.visible==='true' && l.querySelector('img').naturalWidth>0)`);
            await pause(150);
            const state=await browser.evaluate(measure);
            if (phase==='after') {
                assert.ok(state.header.height>=64&&state.header.height<=74);
                assert.ok(state.logo.height>=38&&state.logo.height<=48);
                assert.ok(state.frame.width>=860&&state.frame.width<=900);
                assert.ok(parseFloat(state.radius)<=4);
                assert.ok(Math.abs(state.frame.centerY-state.copy.centerY)<1,`${key}: aligned copy/media centers`);
                assert.ok(Math.abs(state.story.height-(1080-state.header.height))<1,`${key}: usable viewport story`);
                assert.equal(state.overflow,false);
                assert.match(state.motif,/ornament-33/);
                assert.ok(Number(state.motifOpacity)<=.2);
                assert.equal(state.mascot,null);
                assert.ok(parseFloat(state.headlineFont)>=64&&parseFloat(state.headlineFont)<=70);
                assert.ok(parseFloat(state.bodyFont)>=18&&parseFloat(state.bodyFont)<=19);
                assert.equal(state.titleWeight,'680');
                assert.ok(parseFloat(state.label.font)>=16&&parseFloat(state.label.font)<=18);
                assert.ok(Math.abs(state.label.lineWidth-state.label.textWidth)<1.5);
                const casts=[...state.shadow.matchAll(/\)\s+([-\d.]+)px\s+([-\d.]+)px/g)];
                assert.ok(casts.length===2&&casts.every(c=>Number(c[1])>0&&Number(c[2])>0));
            }
            proof.states.push(state);
            await browser.screenshot(`docs2/proof/${family}-${phase}-${key}.png`);
        }
        for (let index=0;index<3;index++) {
            await browser.evaluate(`document.querySelectorAll('.site-header__group>summary')[${index}].click()`);
            await pause(80);
            const menu=await browser.evaluate(`(() => {const p=document.querySelector('.site-header__group[open] .site-header__panel'); const m=p.querySelector('figure').getBoundingClientRect();const l=p.querySelector('.site-header__links').getBoundingClientRect();return {width:m.width,height:m.height,linkHeight:l.height,overflow:document.documentElement.scrollWidth>innerWidth};})()`);
            assert.equal(menu.overflow,false);
            assert.ok(Math.abs(menu.height-menu.linkHeight)<1);
            proof.menus.push(menu);
            if(index===0) await browser.screenshot(`docs2/proof/${family}-${phase}-menu.png`);
            await browser.evaluate(`document.querySelectorAll('.site-header__group>summary')[${index}].click()`);
        }
        if(phase==='after') {
            assert.equal(new Set(proof.states.map(s=>s.background)).size,3);
            assert.ok(proof.states.every(s=>Math.abs(s.frame.centerY-proof.states[0].frame.centerY)<1));
        }
        await fs.writeFile(`docs2/proof/${family}-${phase}.json`,JSON.stringify(proof,null,2));
    } finally {await browser.close();}
});

test('All localized desktop stories share the media center and preserve readable menu geometry', {skip:!url||phase==='before'}, async () => {
    const browser=await openBrowserSession();
    const cells=[],menus=[];
    try {
        await browser.send('Page.enable');
        await browser.send('Emulation.setEmulatedMedia',{features:[{name:'prefers-reduced-motion',value:'reduce'}]});
        await browser.send('Emulation.setDeviceMetricsOverride',{width:1920,height:1080,deviceScaleFactor:1,mobile:false});
        await browser.navigate(url);
        for(const locale of ['id','en','ar']) {
            await browser.evaluate(`document.querySelector('form[action$="/bahasa/${locale}"]').requestSubmit()`);
            await browser.wait(`document.documentElement.lang==='${locale}'&&!!document.querySelector('[data-about]')`);
            for(const [width,height] of [[1024,900],[1024,1366],[1181,900],[1280,900],[1536,900],[1920,1080]]) {
                await browser.send('Emulation.setDeviceMetricsOverride',{width,height,deviceScaleFactor:1,mobile:false});
                await pause(100);
                for(const key of ['about','vision','mission']) {
                    await browser.evaluate(`(() => {const r=document.querySelector('[data-about-story="${key}"]').getBoundingClientRect();const h=document.querySelector('.site-header__bar').getBoundingClientRect().height;scrollTo(0,scrollY+r.top-h);})()`);
                    await browser.wait(`document.querySelector('[data-about]').dataset.activeStory==='${key}'`);
                    await pause(100);
                    const state=await browser.evaluate(measure);
                    assert.equal(state.mascot,null);
                    assert.ok(Math.abs(state.label.lineWidth-state.label.textWidth)<1.5);
                    assert.equal(state.label.direction,locale==='ar'?'rtl':'ltr');
                    cells.push({width,height,...state});
                }
                await browser.evaluate(`document.querySelector('.site-header__group>summary').click()`);
                await browser.wait(`document.querySelector('.site-header__group').open`);
                const menu=await browser.evaluate(`(() => {const p=document.querySelector('.site-header__group[open] .site-header__panel');const m=p.querySelector('figure').getBoundingClientRect();const l=p.querySelector('.site-header__links').getBoundingClientRect();const clipped=[...p.querySelectorAll('a')].some(a=>[...a.children].some(c=>c.getBoundingClientRect().bottom>a.getBoundingClientRect().bottom+1));return {width:m.width,height:m.height,links:l.height,clipped};})()`);
                assert.ok(Math.abs(menu.height-menu.links)<1&&!menu.clipped);
                menus.push({locale,width,height,...menu});
                await browser.evaluate(`document.querySelector('.site-header__group>summary').click()`);
            }
        }
        await fs.writeFile(`docs2/proof/${family}-locales.json`,JSON.stringify({profile:'Chromium, reduced motion, all three stories; six wide width/height cases x three locales',cells,menus},null,2));
        const scale=cells.filter(s=>s.locale==='en'&&s.key==='vision'&&s.height!==1366);
        assert.ok(scale.every((s,i)=>!i||(parseFloat(s.headlineFont)>parseFloat(scale[i-1].headlineFont)&&parseFloat(s.bodyFont)>parseFloat(scale[i-1].bodyFont))));
        const failures=cells.filter(s=>s.overflow||Math.abs(s.frame.centerY-s.copy.centerY)>1);
        assert.deepEqual(failures.map(s=>({locale:s.locale,width:s.width,height:s.height,key:s.key,delta:s.copy.centerY-s.frame.centerY,story:s.story.height})),[]);
    } finally {await browser.close();}
});

test('About color fields interpolate with CSS while motif and media geometry remain static', {skip:!url||phase==='before'}, async () => {
    const browser=await openBrowserSession();
    try {
        await browser.send('Page.enable');
        await browser.send('Emulation.setDeviceMetricsOverride',{width:1920,height:1080,deviceScaleFactor:1,mobile:false});
        await browser.navigate(url);
        await browser.evaluate(`document.querySelector('[data-about-story="about"]').scrollIntoView()`);
        await browser.wait(`document.querySelector('[data-about]').dataset.activeStory==='about'`);
        await pause(750);
        const start=await browser.evaluate(measure);
        await browser.evaluate(`document.querySelector('[data-about-story="vision"]').scrollIntoView()`);
        await browser.wait(`document.querySelector('[data-about]').dataset.activeStory==='vision'`);
        const samples=await browser.evaluate(`(async () => {const root=document.querySelector('[data-about]');const samples=[];for(const delay of [50,140,250,400]) {await new Promise(resolve=>setTimeout(resolve,delay));const style=getComputedStyle(root);const tile=getComputedStyle(root,'::before');samples.push({color:style.backgroundColor,duration:style.transitionDuration,property:style.transitionProperty,position:tile.backgroundPosition,transform:tile.transform,filter:tile.filter});}return samples;})()`);
        assert.notEqual(samples[0].color,start.background);
        assert.notEqual(samples[0].color,'rgb(223, 243, 252)');
        assert.equal(samples.at(-1).color,'rgb(223, 243, 252)');
        assert.ok(samples.every(s=>s.duration==='0.7s'&&s.property==='background-color'&&s.position==='50% 50%'&&s.transform==='none'&&s.filter==='none'));
        await browser.send('Emulation.setEmulatedMedia',{features:[{name:'prefers-reduced-motion',value:'reduce'}]});
        assert.equal(await browser.evaluate(`getComputedStyle(document.querySelector('[data-about]')).transitionDuration`),'0s');
        await fs.writeFile(`docs2/proof/${family}-color-motion.json`,JSON.stringify({profile:'Chromium normal motion, actual scroll/IntersectionObserver activation',start,samples,reducedMotionDuration:'0s'},null,2));
    } finally {await browser.close();}
});

test('Scaled Header keeps dropdown, underline, language and sound interactions intact', {skip:!url||phase==='before'}, async () => {
    const browser=await openBrowserSession();
    const click=async selector => {
        const point=await browser.evaluate(`(() => {const r=document.querySelector('${selector}').getBoundingClientRect();return {x:r.x+r.width/2,y:r.y+r.height/2};})()`);
        await browser.send('Input.dispatchMouseEvent',{type:'mousePressed',button:'left',clickCount:1,...point});
        await browser.send('Input.dispatchMouseEvent',{type:'mouseReleased',button:'left',clickCount:1,...point});
    };
    try {
        await browser.send('Page.enable');
        await browser.send('Emulation.setDeviceMetricsOverride',{width:1920,height:1080,deviceScaleFactor:1,mobile:false});
        await browser.navigate(url);
        await click('.site-header__group:nth-of-type(1)>summary');
        await browser.wait(`document.querySelector('.site-header__group').open`);
        assert.equal(await browser.evaluate(`document.querySelectorAll('.site-header [data-highlighted]').length`),1);
        await click('[data-language]>summary');
        await browser.wait(`document.querySelector('#header-language-dialog').matches(':modal')`);
        assert.equal(await browser.evaluate(`document.querySelector('.site-header__group').open`),true);
        await browser.send('Input.dispatchKeyEvent',{type:'rawKeyDown',key:'Escape',code:'Escape',windowsVirtualKeyCode:27,nativeVirtualKeyCode:27});
        await browser.send('Input.dispatchKeyEvent',{type:'keyUp',key:'Escape',code:'Escape',windowsVirtualKeyCode:27,nativeVirtualKeyCode:27});
        await browser.wait(`!document.querySelector('#header-language-dialog').matches(':modal')`);
        assert.equal(await browser.evaluate(`document.querySelector('.site-header__group').open`),true);
        await click('.site-header__group:nth-of-type(1)>summary');
        await browser.wait(`!document.querySelector('.site-header__group').open`);
        await click('[data-audio]');
        await browser.wait(`document.querySelector('[data-audio]').getAttribute('aria-pressed')==='true'`);
        await click('[data-audio]');
        await browser.wait(`document.querySelector('[data-audio]').getAttribute('aria-pressed')==='false'`);
        await fs.writeFile(`docs2/proof/${family}-header-interactions.json`,JSON.stringify({profile:'Chromium1920x1080 normal motion, real CDP pointer gestures',dropdown:true,uniqueUnderline:true,languageModalIndependent:true,escape:true,soundOnOff:true},null,2));
    } finally {await browser.close();}
});
