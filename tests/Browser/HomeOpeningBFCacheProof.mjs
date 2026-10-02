import assert from 'node:assert/strict';
import {writeFileSync} from 'node:fs';
import {instrument,snapshot,waitComplete,nativeScroll,delay} from './home-opening-helpers.mjs';
const {chromium}=await import(process.env.SCHOOLAI_BROWSER_MODULE || '/tmp/schoolai-performance-tools/node_modules/playwright/index.mjs');
const base=process.env.SCHOOLAI_PROOF_URL || 'http://127.0.0.1:8018';
const browser=await chromium.launch({headless:true,executablePath:process.env.SCHOOLAI_CHROME_PATH || '/home/asus/.cache/ms-playwright/chromium-1243/chrome-linux64/chrome',ignoreDefaultArgs:['--disable-back-forward-cache'],args:['--disable-gpu']});
const rows=[];
try {
    for(const motion of ['reduce','no-preference']) {
        const context=await browser.newContext({viewport:{width:1440,height:900},reducedMotion:motion});await instrument(context);
        const page=await context.newPage(),notRestored=[];
        const client=await context.newCDPSession(page);await client.send('Page.enable');client.on('Page.backForwardCacheNotUsed',event=>notRestored.push(event));
        if(motion==='no-preference') {
            await page.route('**/*.mp4',route=>route.abort('failed'));
            await page.addInitScript(()=>window.holdOpeningHandoff=true);
        }
        await page.goto(base,{waitUntil:'domcontentloaded'});
        if(motion==='reduce')await waitComplete(page);
        else {await page.waitForFunction(()=>document.documentElement.dataset.homeOpeningPhase==='handoff');await delay(60);}
        await page.evaluate(()=>window.originalOpeningOwner=window.schoolaiHomeOpening);
        const before=await snapshot(page);
        if(motion==='no-preference')assert.equal(before.root.homeScrollGate,'locked');
        await page.goto(base+'/ppdb');
        // BFCache restore emits pageshow, not a second DOMContentLoaded.
        await page.evaluate(()=>history.back());await page.waitForURL(base+'/',{waitUntil:'commit'});
        await page.waitForFunction(()=>window.openingProof.events.some(event=>event.name==='pageshow'&&event.persisted));
        await waitComplete(page);const after=await snapshot(page);
        assert.equal(await page.evaluate(()=>window.originalOpeningOwner===window.schoolaiHomeOpening),true);
        assert.equal(after.duplicates,1);
        assert.equal(after.events.filter(event=>event.name==='schoolai:opening-adopted').length,1);
        assert.equal(after.events.filter(event=>event.name==='schoolai:first-journey-ready').length,1);
        assert.equal(after.root.homeScrollGate,'unlocked');
        const input=await nativeScroll(page,'chromium');assert.ok(input.after>0);
        rows.push({motion,browser:browser.version(),before,after,input,notRestored,
            profile:'Native regular Chromium BFCache; reduced motion and normal handoff with real media-error semantic fallback. Headless-shell delegate cannot supply native BFCache. Normal streaming history reload is tested separately.'});
        console.log(motion,'native BFCache PASS');await context.close();
    }
} finally {await browser.close();}
writeFileSync(process.env.SCHOOLAI_BFCACHE_PROOF || '/tmp/schoolai-map06b-bfcache-proof.json',JSON.stringify(rows,null,2));
