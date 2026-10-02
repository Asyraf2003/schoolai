import assert from 'node:assert/strict';
import { localeContext, instrument, snapshot, waitComplete, nativeScroll, delay } from './home-opening-helpers.mjs';
import { instrumentCompleteJourney, traverseCompleteHomepage, completeSnapshot } from './home-complete-journey.mjs';
export async function proveOpeningLifecycle(browser, engine, base, save) {
    for (const mode of ['held-media','held-images','failed-image','media-error','play-denied','language','ppdb','anchor','hash','no-js','lifecycle','zoom','short']) {
        const context = await localeContext(browser,base,'id','no-preference',1440,mode==='no-js');
        if (mode !== 'no-js') { await instrument(context); await instrumentCompleteJourney(context); }
        if (mode === 'play-denied') await context.addInitScript(() => {
            HTMLMediaElement.prototype.play = () => Promise.reject(new DOMException('Autoplay denied', 'NotAllowedError'));
        });
        const page = await context.newPage(), requests = [], errors = [];
        page.on('request', request => requests.push({url:request.url(),type:request.resourceType()}));
        page.on('pageerror', error => errors.push(error.message));
        let release;
        const held = new Promise(resolve => { release = resolve; });
        if (mode === 'held-media') await page.route('**/homepage-opening-v2.mp4',async route => { await held; await route.continue().catch(()=>{}); });
        if (['held-images','language','ppdb','anchor','zoom','short'].includes(mode)) await page.route('**/testi-21-v1.webp',async route => { await held; await route.continue().catch(()=>{}); });
        if (mode === 'failed-image') await page.route('**/testi-21-v1.webp',route => route.abort('failed'));
        if (mode === 'media-error') await page.route('**/*.mp4',route => route.abort('failed'));
        if (mode === 'short') await page.setViewportSize({width:780,height:390});
        await page.goto(base+(mode==='hash'?'#program':''),{waitUntil:'domcontentloaded'});
        let detail;
        if (mode === 'held-media') {
            await page.waitForFunction(()=>document.documentElement.dataset.heroReady==='true');
            const pending = await snapshot(page);
            assert.equal(pending.value, 0); assert.equal(pending.root.homeScrollGate, 'locked');
            assert.equal(pending.hero, true); assert.equal(pending.navbar, true);
            await page.locator('#navbar [data-language-modal-open]').click();
            await page.waitForFunction(()=>document.activeElement.classList.contains('language-modal__dialog'));
            await page.keyboard.press('Escape');
            await page.locator('.hero-cinema__cta[href$="/ppdb"]').click(); await page.waitForURL('**/ppdb');
            detail = { pending, url: page.url() }; release();
        } else if (mode === 'no-js') {
            const input=await nativeScroll(page,engine); assert.ok(input.after>0);
            assert.equal(await page.locator('html').getAttribute('data-home-scroll-gate'),null); detail={input};
        } else if (mode === 'failed-image') {
            await page.waitForFunction(()=>document.documentElement.dataset.homePreparationState==='failed');
            detail=await completeSnapshot(page); assert.equal(detail.root.homeFailedDependency,'images');
            assert.equal(detail.root.homeScrollGate,'locked'); assert.ok(Number(detail.root.homeOpeningProgress)<100);
            assert.equal((await nativeScroll(page,engine)).after,0);
            assert.ok(await page.locator('[data-home-opening-exit]').isVisible());
            assert.equal(new URL(await page.locator('[data-home-opening-exit]').getAttribute('href')).href,new URL(base).href);
        } else if (['held-images','language','ppdb','anchor','zoom','short'].includes(mode)) {
            await page.waitForFunction(()=>document.documentElement.dataset.homePreparedThrough==='fonts'); await delay(150);
            const pending=await snapshot(page); assert.equal(pending.root.homeScrollGate,'locked'); assert.ok(pending.value<100);
            assert.equal((await nativeScroll(page,engine)).after,0); await page.keyboard.press('PageDown'); assert.equal(await page.evaluate(()=>scrollY),0);
            if (mode==='ppdb') {
                await page.locator('.hero-cinema__cta[href$="/ppdb"]').click(); await page.waitForURL('**/ppdb'); detail={url:page.url(),pending};
            } else if (mode==='zoom'||mode==='short') {
                if(mode==='zoom') await page.evaluate(()=>document.documentElement.style.zoom='2');
                await page.locator('[data-home-opening-direct]').click(); await page.waitForURL('**/ppdb'); detail={url:page.url(),pending};
            } else {
                await page.locator('#navbar [data-language-modal-open]').focus(); await page.keyboard.press('Enter');
                await page.waitForFunction(()=>document.activeElement.classList.contains('language-modal__dialog'));
                await page.keyboard.press('Escape'); assert.equal(await page.evaluate(()=>document.activeElement.matches('#navbar [data-language-modal-open]')),true);
                assert.equal(await page.locator('html').getAttribute('data-home-scroll-gate'),'locked');
                if(mode==='language') {
                    await page.locator('#navbar [data-language-modal-open]').click();
                    await page.locator('[data-language-modal] .language-modal__option[lang="ar"]').click();
                    release(); await page.waitForFunction(()=>document.documentElement.lang==='ar'); await waitComplete(page);
                    detail=await completeSnapshot(page);
                } else {
                    if(mode==='anchor') { await page.locator('#navbar [data-nav-mega-toggle]').first().click(); await page.locator('#navbar a[href="#program"]').first().click(); assert.equal(await page.evaluate(()=>scrollY),0); }
                    release(); await waitComplete(page); detail={pending,ready:await completeSnapshot(page)};
                    if(mode==='anchor') { await page.waitForFunction(()=>scrollY>900); detail.ready=await completeSnapshot(page); }
                    else detail.journey=await traverseCompleteHomepage(page,requests);
                }
            }
            release();
        } else {
            await waitComplete(page);
            if(mode==='hash') { await page.waitForFunction(()=>scrollY>900); detail=await completeSnapshot(page); }
            else if(mode==='lifecycle') {
                await page.evaluate(()=>{window.savedOwner=window.schoolaiHomeOpening;window.dispatchEvent(new PageTransitionEvent('pagehide',{persisted:true}));window.dispatchEvent(new PageTransitionEvent('pageshow',{persisted:true}));});
                assert.equal(await page.evaluate(()=>window.savedOwner===window.schoolaiHomeOpening),true); detail=await completeSnapshot(page);
            } else {
                detail=await traverseCompleteHomepage(page,requests);
                if (mode === 'play-denied') assert.ok(detail.start.previews.every(video => video.state === 'poster-ready'));
            }
        }
        assert.deepEqual(errors,[]); save({engine,key:mode,detail,errors}); await context.close();
    }
}
