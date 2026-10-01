import assert from 'node:assert/strict';
import {localeContext,instrument,snapshot,waitComplete,nativeScroll,delay} from './home-opening-helpers.mjs';
export async function proveOpeningLifecycle(browser,engine,base,save) {
    for(const [from,to] of [['id','en'],['en','id'],['id','ar'],['en','ar'],['ar','id'],['ar','en']]) {
        const context=await localeContext(browser,base,from);await instrument(context);const page=await context.newPage();
        let release;const held=new Promise(resolve=>release=resolve);
        await page.route('**/program-cards-*.js',async route=>{await held;await route.continue().catch(()=>{});});
        await page.goto(base,{waitUntil:'domcontentloaded'});await page.waitForFunction(()=>document.querySelector('progress').value===40);
        await page.locator('#navbar [data-language-modal-open]').click();
        await page.locator(`[data-language-modal] button.language-modal__option[lang="${to}"]`).click();
        release();await waitComplete(page);const state=await snapshot(page);
        assert.equal(state.lang,to);assert.equal(state.dir,to==='ar'?'rtl':'ltr');assert.equal(state.duplicates,1);
        assert.equal(state.events.filter(e=>e.name==='schoolai:opening-adopted').length,1);
        save({engine,key:`locale-loading-${from}-${to}`,state});await context.close();
    }
    {
        const context=await localeContext(browser,base,'id');await instrument(context);const page=await context.newPage();
        let release;const held=new Promise(resolve=>release=resolve);
        await page.route('**/program-cards-*.js',async route=>{await held;await route.continue().catch(()=>{});});
        await page.goto(base,{waitUntil:'domcontentloaded'});await waitComplete(page);const state=await snapshot(page);
        assert.equal(state.value,100);assert.equal(state.program,'static-fallback');assert.equal(state.root.homeScrollGate,'unlocked');
        const trigger=page.locator('[data-program-open]').first();await trigger.scrollIntoViewIfNeeded();await trigger.click();
        assert.equal(await page.locator('[data-program-detail-layer]').isVisible(),true);
        await page.keyboard.press('Escape');assert.equal(await page.locator('[data-program-detail-layer]').isVisible(),false);
        release();await delay(250);assert.equal(await page.locator('[data-program-kinetic]').getAttribute('data-program-ready'),'static-fallback');
        save({engine,key:'real-deadline-usable-fallback',state,dialogOpenClose:true});await context.close();
    }
    {
        const context=await localeContext(browser,base,'id');await instrument(context);const page=await context.newPage();
        await page.addInitScript(()=>window.holdOpeningHandoff=true);
        await page.goto(base,{waitUntil:'domcontentloaded'});await page.waitForFunction(()=>document.documentElement.dataset.homeOpeningPhase==='handoff');
        await delay(60);await page.evaluate(()=>{
            window.originalGate=window.schoolaiHomeOpening;window.originalHidden=Object.getOwnPropertyDescriptor(document,'hidden');
            Object.defineProperty(document,'hidden',{configurable:true,get:()=>true});
            document.dispatchEvent(new Event('visibilitychange'));
            window.dispatchEvent(new PageTransitionEvent('pagehide',{persisted:true}));
        });
        const before=await snapshot(page);await delay(150);const pending=await snapshot(page);
        assert.equal(before.root.homeScrollGate,'locked');assert.equal(pending.root.homeScrollGate,'locked');
        await page.evaluate(()=>{
            delete document.hidden;window.dispatchEvent(new PageTransitionEvent('pageshow',{persisted:true}));
            document.dispatchEvent(new Event('visibilitychange'));
        });
        await waitComplete(page);const after=await snapshot(page);
        assert.equal(await page.evaluate(()=>window.originalGate===window.schoolaiHomeOpening),true);
        assert.equal(after.events.filter(e=>e.name==='schoolai:first-journey-ready').length,1);
        save({engine,key:'handoff-hidden-persisted-handlers',before,pending,after,profile:'Actual DOM/WAAPI with injected hidden and persisted lifecycle events; complements native history case.'});await context.close();
    }
    {
        const context=await localeContext(browser,base,'id','no-preference',390);await instrument(context);const page=await context.newPage();
        let release;const held=new Promise(resolve=>release=resolve);
        await page.route('**/program-cards-*.js',async route=>{await held;await route.continue().catch(()=>{});});
        await page.goto(base,{waitUntil:'domcontentloaded'});await page.waitForFunction(()=>document.querySelector('progress').value===40);
        await page.locator('#hamburgerBtn').click();await page.locator('#navMenu a[href$="/ppdb"]').last().click();
        await page.waitForURL('**/ppdb');assert.ok(await page.locator('h1').first().isVisible());release();
        save({engine,key:'mobile-navbar-ppdb-loading',url:page.url()});await context.close();
    }
    {
        const context=await localeContext(browser,base,'id');await instrument(context);const page=await context.newPage();
        let release;const held=new Promise(resolve=>release=resolve);
        await page.route('**/program-cards-*.js',async route=>{await held;await route.continue().catch(()=>{});});
        await page.goto(base,{waitUntil:'domcontentloaded'});await page.waitForFunction(()=>document.querySelector('progress').value===40);
        await page.locator('[data-vision-video-open]').first().click();await page.locator('[data-about-video-modal]').waitFor({state:'visible'});
        assert.equal(await page.locator('html').getAttribute('data-home-scroll-gate'),'unlocked');
        await page.locator('[data-about-video-fullscreen]').click();await delay(150);
        const fullscreen=await page.evaluate(()=>({native:!!document.fullscreenElement,fallback:document.querySelector('[data-about-video-modal]').className,body:document.body.style.overflow}));
        await page.keyboard.press('Escape');await delay(150);
        if(await page.locator('[data-about-video-modal]').isVisible())await page.locator('[data-about-video-close]').click();
        assert.equal(await page.locator('html').getAttribute('data-home-scroll-gate'),'unlocked');release();
        save({engine,key:'loading-video-modal-fullscreen',fullscreen,profile:engine==='webkit'?'WebKit fullscreen native/capability fallback recorded':'Chromium native fullscreen intent'});await context.close();
    }
    {
        const context=await localeContext(browser,base,'id');await instrument(context);const page=await context.newPage();
        await page.route('**/build/assets/*.js',route=>route.abort('failed'));
        await page.goto(base,{waitUntil:'domcontentloaded'});await page.locator('[data-home-opening-exit]').waitFor({state:'visible',timeout:15000});
        assert.equal(await page.locator('progress').getAttribute('value'),'0');
        const locked=await nativeScroll(page,engine);assert.equal(locked.after,0);
        await page.locator('[data-home-opening-exit]').click();assert.equal(await page.locator('html').getAttribute('data-home-scroll-gate'),'unlocked');
        const input=await nativeScroll(page,engine);assert.ok(input.after>0);
        save({engine,key:'failed-runtime-explicit-recovery',locked,input,profile:'No fabricated 100%; usable native recovery/navigation exits opening explicitly.'});await context.close();
    }
}
