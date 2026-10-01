import assert from 'node:assert/strict';
export const delay = ms => new Promise(resolve => setTimeout(resolve, ms));
export const widths = [360,390,639,640,767,768,1023,1024,1180,1181,1279,1280,1440,1535,1536,1920];
export async function localeContext(browser, base, locale, motion='no-preference', width=1440, noJs=false) {
    const context = await browser.newContext({viewport:{width,height:900},reducedMotion:motion,hasTouch:width<640,javaScriptEnabled:!noJs});
    if(locale!=='id') {
        const response=await context.request.get(base);
        const token=(await response.text()).match(/name="_token" value="([^"]+)"/)[1];
        await context.request.post(base+'/bahasa/'+locale,{form:{_token:token},headers:{referer:base}});
    }
    return context;
}
export async function instrument(context, extendedDeadline=false) {
    if(extendedDeadline)await context.addInitScript(()=>{const original=window.setTimeout;window.setTimeout=(callback,ms,...args)=>original(callback,ms===8000?60000:ms,...args);});
    await context.addInitScript(()=>{
        window.openingProof={events:[],reference:null};
        const snapshot=()=>({time:performance.now(),y:scrollY,gate:document.documentElement.dataset.homeScrollGate,
            phase:document.documentElement.dataset.homeOpeningPhase,value:document.querySelector('[data-home-opening] progress')?.value});
        for(const name of ['schoolai:opening-adopted','schoolai:home-preparation','schoolai:opening-progress','schoolai:opening-handoff','schoolai:first-journey-ready']) {
            document.addEventListener(name,event=>window.openingProof.events.push({name,detail:event.detail,...snapshot()}));
        }
        document.addEventListener('schoolai:opening-handoff',()=>{
            if(window.holdOpeningHandoff) {
                requestAnimationFrame(()=>document.querySelector('[data-home-opening]').getAnimations().forEach(animation=>animation.pause()));
            }
        });
        window.addEventListener('pageshow',event=>window.openingProof.events.push({name:'pageshow',persisted:event.persisted,...snapshot()}));
    });
}
export async function nativeScroll(page,engine,touch=false,amount=400) {
    const before=await page.evaluate(()=>scrollY);
    if(touch&&engine==='chromium') {
        const client=await page.context().newCDPSession(page);
        await client.send('Input.dispatchTouchEvent',{type:'touchStart',touchPoints:[{x:180,y:700}]});
        for(let i=1;i<=6;i++)await client.send('Input.dispatchTouchEvent',{type:'touchMove',touchPoints:[{x:180,y:700-i*60}]});
        await client.send('Input.dispatchTouchEvent',{type:'touchEnd',touchPoints:[]}); await client.detach();
    } else {
        const size=page.viewportSize();await page.mouse.move(size.width/2,Math.min(650,size.height-30));await page.mouse.wheel(0,amount);
    }
    await delay(120);
    return {input:touch&&engine==='chromium'?'native CDP touch':'native wheel',before,after:await page.evaluate(()=>scrollY)};
}
export async function snapshot(page) {
    return page.evaluate(()=>{
        const loader=document.querySelector('[data-home-opening]'),bar=loader.querySelector('progress'),r=loader.getBoundingClientRect();
        const visible=e=>{const rect=e.getBoundingClientRect(),style=getComputedStyle(e);return rect.width>0&&rect.height>0&&rect.top<innerHeight&&rect.bottom>0&&style.visibility==='visible'&&style.display!=='none';};
        const hero=document.querySelector('[data-hero-slider]'),ppdb=document.querySelector('.hero-cinema__cta[href$="/ppdb"]');
        return {root:{...document.documentElement.dataset},value:bar.value,percent:loader.innerText,loaderVisible:visible(loader),loader:{x:r.x,y:r.y,width:r.width,height:r.height},
            hero:visible(hero.querySelector('h1')),desktop:visible(document.querySelector('#desktopNavMenu')),navbar:visible(document.querySelector('#navbar')),heroRect:hero.getBoundingClientRect().toJSON(),ppdb:visible(ppdb),scrollY,
            horizontalOverflow:document.documentElement.scrollWidth-innerWidth,lang:document.documentElement.lang,dir:document.documentElement.dir,
            family:getComputedStyle(loader).fontFamily,program:document.querySelector('[data-program-kinetic]').dataset.programReady,
            values:document.querySelector('[data-values-story]').dataset.valuesReady,gallery:document.querySelector('[data-gallery-story]').dataset.galleryReady,
            duplicates:document.querySelectorAll('[data-home-opening]').length,events:window.openingProof.events};
    });
}
export async function waitComplete(page) {
    await page.waitForFunction(()=>document.documentElement.dataset.homePreparationState==='complete',null,{timeout:20000});
}
export function checkEvents(state,motion) {
    assert.equal(state.events.filter(e=>e.name==='schoolai:opening-adopted').length,1);
    const releases=state.events.filter(e=>e.name==='schoolai:first-journey-ready'); assert.equal(releases.length,1);
    assert.equal(releases[0].detail.state,'prepared');
    const steps=state.events.filter(e=>e.name==='schoolai:home-preparation'); assert.equal(steps.length,5);
    for(const event of state.events.filter(e=>e.name==='schoolai:opening-progress')) {
        const settled=Object.values(event.detail.units).filter(s=>['PREPARED','STATIC_FALLBACK'].includes(s)).length;
        assert.equal(event.detail.value,20*settled); assert.equal(event.gate,'locked');
    }
    const hundred=state.events.find(e=>e.name==='schoolai:opening-progress'&&e.value===100),handoff=state.events.find(e=>e.name==='schoolai:opening-handoff');
    assert.ok(hundred&&handoff);assert.equal(handoff.gate,'locked');assert.ok(handoff.time>=hundred.time);
    assert.ok(releases[0].time>=handoff.time);if(motion!=='reduce')assert.ok(releases[0].time-handoff.time>=140);
}
