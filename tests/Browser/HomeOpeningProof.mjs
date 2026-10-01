import assert from 'node:assert/strict';
import {proveOpeningLifecycle} from './home-opening-lifecycle.mjs';
import {writeFileSync,mkdirSync} from 'node:fs';
import {localeContext,instrument,nativeScroll,snapshot,waitComplete,checkEvents,widths,delay} from './home-opening-helpers.mjs';
const {chromium,webkit}=await import(process.env.SCHOOLAI_BROWSER_MODULE || '/tmp/schoolai-performance-tools/node_modules/playwright/index.mjs');
const base=process.env.SCHOOLAI_PROOF_URL || 'http://127.0.0.1:8018';
const dir=process.env.SCHOOLAI_PROOF_DIR || '/tmp/schoolai-map06b-corrected';mkdirSync(dir,{recursive:true});
const rows=[];const save=row=>{rows.push(row);writeFileSync(dir+'/proof.json',JSON.stringify({date:new Date().toISOString(),base,fixture:'Held-module tier matrix extends the 8s fallback deadline to60s solely for deterministic screenshots; readiness/progress remains real. Other cases retain production deadline.',rows},null,2));console.log(row.engine,row.key,'PASS');};
for(const [engine,type] of Object.entries({chromium,webkit})) {
    const browser=await type.launch({headless:true,ignoreDefaultArgs:['--disable-back-forward-cache'],...(engine==='webkit'?{executablePath:process.env.SCHOOLAI_WEBKIT_WRAPPER || '/tmp/schoolai-webkit.sh'}:{args:['--disable-gpu']})});
    try {
        for(const locale of ['id','en','ar'])for(const motion of ['no-preference','reduce']) {
            const context=await localeContext(browser,base,locale,motion);await instrument(context,true);
            const page=await context.newPage(),errors=[],requests=[];page.on('pageerror',e=>errors.push(e.message));
            let stage='preparation';page.on('request',r=>requests.push({stage,url:r.url(),type:r.resourceType()}));
            let releaseProgram;const held=new Promise(resolve=>releaseProgram=resolve);
            await page.route('**/program-cards-*.js',async route=>{await held;await route.continue().catch(()=>{});});
            await page.goto(base,{waitUntil:'domcontentloaded'});
            await page.waitForFunction(()=>document.querySelector('progress')?.value===40);
            const initial=await snapshot(page);assert.equal(initial.root.homeScrollGate,'locked');assert.ok(initial.loaderVisible&&initial.hero&&initial.ppdb);
            await page.evaluate(()=>{window.openingProof.reference=window.schoolaiHomeOpening;window.holdOpeningHandoff=true;});
            for(const width of widths) {
                await page.setViewportSize({width,height:900});await page.waitForFunction(expected=>{const s=getComputedStyle(document.querySelector('#desktopNavMenu'));return(s.display!=='none'&&s.visibility!=='hidden')===expected},width>=1181);
                const state=await snapshot(page);assert.ok(state.loaderVisible&&state.hero&&state.ppdb,JSON.stringify(state));
                assert.equal(state.value,40);assert.equal(state.lang,locale);assert.equal(state.dir,locale==='ar'?'rtl':'ltr');
                assert.ok(state.horizontalOverflow<=2);assert.equal(state.duplicates,1);assert.equal(state.desktop,width>=1181);
                const input=await nativeScroll(page,engine,width===390);assert.equal(input.after,0);
                if([360,390,640,768,1024,1280,1440,1536,1920].includes(width))await page.screenshot({path:`${dir}/${engine}-${locale}-${motion}-${width}-pending.png`});
                save({engine,version:browser.version(),key:`${locale}-${motion}-${width}-pending`,state,input});
            }
            await page.setViewportSize({width:780,height:390});await delay(100);const short=await snapshot(page);assert.ok(short.loaderVisible&&short.navbar);save({engine,key:`${locale}-${motion}-landscape`,state:short});
            await page.setViewportSize({width:390,height:844});await page.evaluate(()=>document.documentElement.style.zoom='2');await delay(100);
            const zoom=await snapshot(page);writeFileSync(dir+'/debug-zoom.json',JSON.stringify(zoom,null,2));assert.ok(zoom.loaderVisible&&zoom.hero&&zoom.navbar);save({engine,key:`${locale}-${motion}-zoom`,state:zoom});
            await page.evaluate(()=>document.documentElement.style.zoom='');await page.setViewportSize({width:1440,height:900});
            releaseProgram();
            if(motion!=='reduce') {
                await page.waitForFunction(()=>document.documentElement.dataset.homeOpeningPhase==='handoff');await delay(60);
                const hundred=await snapshot(page);assert.equal(hundred.value,100);assert.equal(hundred.root.homeScrollGate,'locked');
                assert.equal((await nativeScroll(page,engine)).after,0);
                await page.screenshot({path:`${dir}/${engine}-${locale}-${motion}-100-locked.png`});
                await page.evaluate(()=>document.querySelector('[data-home-opening]').getAnimations().forEach(a=>a.play()));
            }
            await page.waitForFunction(()=>document.documentElement.dataset.homeScrollGate==='unlocked');
            stage='immediate-first-scroll';const input=await nativeScroll(page,engine);await delay(500);
            await waitComplete(page);const completed=await snapshot(page);writeFileSync(dir+'/debug-complete.json',JSON.stringify(completed,null,2));checkEvents(completed,motion);assert.ok(input.after>0);
            const required=requests.filter(r=>r.stage==='immediate-first-scroll'&&r.type==='script'&&/program-cards|program-values-world|controller-|welcome-depth-gallery|gsap|preparation-/.test(r.url));
            assert.deepEqual(required,[]);assert.equal(await page.evaluate(()=>window.openingProof.reference===window.schoolaiHomeOpening),true);
            assert.deepEqual(errors,[]);save({engine,key:`${locale}-${motion}-complete`,state:completed,input,firstScrollRequests:requests.filter(r=>r.stage==='immediate-first-scroll'),requiredRequests:required,errors});
            await context.close();
        }
        for(const locale of ['id','en','ar']) {
            const context=await localeContext(browser,base,locale,'no-preference',390);await instrument(context);const page=await context.newPage();
            let release;const held=new Promise(resolve=>release=resolve);
            await page.route('**/welcome-*.js',async route=>{if(/\/welcome-[^/]+\.js$/.test(route.request().url())){await held;}await route.continue().catch(()=>{});});
            await page.goto(base,{waitUntil:'commit'});await page.locator('[data-home-opening]').waitFor({state:'visible'});
            assert.equal(await page.evaluate(()=>document.documentElement.dataset.homeOpeningOwner),'bootstrap');
            const input=await nativeScroll(page,engine,true);assert.equal(input.after,0);
            const wheel=await nativeScroll(page,engine,false);assert.equal(wheel.after,0);
            release();await waitComplete(page);const state=await snapshot(page);checkEvents(state,'no-preference');
            const afterUnlockInput=await nativeScroll(page,engine,true);if(engine==='chromium')assert.ok(afterUnlockInput.after>0);
            save({engine,key:`${locale}-earliest`,input,wheel,afterUnlockInput,state,limitation:engine==='webkit'?'Native wheel at390; Playwright WebKit has tap but no native drag protocol. Chromium supplies actual native touch drag.':null});await context.close();
        }
        for(const mode of ['no-js','hash','ppdb','language','navigation','history','lifecycle','media-error']) {
            const context=await localeContext(browser,base,'id','no-preference',1440,mode==='no-js');if(mode!=='no-js')await instrument(context);
            const page=await context.newPage();let release;let detail={};
            if(['ppdb','language','navigation'].includes(mode)) {const held=new Promise(resolve=>release=resolve);await page.route('**/program-cards-*.js',async route=>{await held;await route.continue().catch(()=>{});});}
            if(mode==='media-error')await page.route('**/*.mp4',route=>route.abort('failed'));
            await page.goto(base+(mode==='hash'?'#program':''),{waitUntil:'domcontentloaded'});
            if(mode==='no-js') {const input=await nativeScroll(page,engine);assert.ok(input.after>0);assert.equal(await page.locator('html').getAttribute('data-home-scroll-gate'),null);detail={input,ppdb:await page.locator('.hero-cinema__cta[href$="/ppdb"]').isVisible()};}
            else if(mode==='hash') {await waitComplete(page);assert.equal(await page.locator('html').getAttribute('data-home-scroll-gate'),'unlocked');detail={scrollY:await page.evaluate(()=>scrollY),state:await snapshot(page)};assert.ok(detail.scrollY>0);}
            else if(mode==='ppdb') {await page.waitForFunction(()=>document.querySelector('progress').value===40);await page.locator('.hero-cinema__cta[href$="/ppdb"]').click();await page.waitForURL('**/ppdb');assert.ok(await page.locator('h1').first().isVisible());detail={url:page.url()};release();}
            else if(mode==='language') {await page.waitForFunction(()=>document.querySelector('progress').value===40);await page.locator('#navbar [data-language-modal-open]').focus();await page.keyboard.press('Enter');await page.waitForFunction(()=>document.activeElement?.classList.contains('language-modal__dialog'));await page.keyboard.press('Escape');assert.equal(await page.evaluate(()=>document.activeElement.matches('#navbar [data-language-modal-open]')),true);assert.equal(await page.locator('html').getAttribute('data-home-scroll-gate'),'unlocked');detail={bodyOverflow:await page.evaluate(()=>document.body.style.overflow)};release();}
            else if(mode==='navigation') {await page.waitForFunction(()=>document.querySelector('progress').value===40);await page.locator('#navbar [data-nav-mega-toggle]').first().click();await page.locator('#navbar a[href="#program"]').first().click();assert.equal(await page.locator('html').getAttribute('data-home-scroll-gate'),'unlocked');detail=await snapshot(page);release();}
            else if(mode==='history') {await waitComplete(page);await page.evaluate(()=>scrollTo(0,900));await page.waitForFunction(()=>scrollY>=890);await delay(100);await page.goto(base+'/ppdb');await page.goBack({waitUntil:'domcontentloaded'});await waitComplete(page);detail=await snapshot(page);assert.equal(detail.root.homeScrollGate,'unlocked');assert.ok(detail.scrollY>0);assert.equal(detail.duplicates,1);}
            else if(mode==='lifecycle') {await waitComplete(page);await page.evaluate(()=>{window.savedGate=window.schoolaiHomeOpening;window.dispatchEvent(new PageTransitionEvent('pagehide',{persisted:true}));window.dispatchEvent(new PageTransitionEvent('pageshow',{persisted:true}));window.dispatchEvent(new Event('resize'));});detail=await snapshot(page);assert.equal(await page.evaluate(()=>window.savedGate===window.schoolaiHomeOpening),true);assert.equal(detail.duplicates,1);assert.equal(detail.events.filter(e=>e.name==='schoolai:first-journey-ready').length,1);}
            else {await waitComplete(page);detail=await snapshot(page);assert.equal(detail.value,100);assert.equal(detail.root.homeScrollGate,'unlocked');}
            save({engine,key:mode,detail});await context.close();
        }
        await proveOpeningLifecycle(browser,engine,base,save);
    } finally {await browser.close();}
}
