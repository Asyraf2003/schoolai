import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
const base = process.env.SCHOOLAI_BROWSER_URL;
const modulePath = process.env.PROGRAM_PLAYWRIGHT_MODULE;
const out = process.env.PROGRAM_PROOF_DIRECTORY ?? '/tmp/gallery-v3-proof';
const widths = [360,390,639,640,767,768,1023,1024,1180,1181,1279,1280,1440,1535,1536,1920];
const geometry = () => ({ width:innerWidth, lang:document.documentElement.lang,
    overflow:document.documentElement.scrollWidth > innerWidth + 1,
    heading:document.querySelectorAll('h1').length,
    controls:document.querySelectorAll('.gallery-header a, .gallery-header button').length,
    clips:[...document.querySelectorAll('.gallery-header a,.gallery-header button,h1')].some(e=>{
        const r=e.getBoundingClientRect(); return r.left < -1 || r.right > innerWidth + 1;
    }),
});
for (const engine of (process.env.PROGRAM_ENGINES ?? 'chromium,webkit').split(',')) {
    test(`Gallery V3 responsive, viewer and media in ${engine}`, {skip: !base || !modulePath}, async () => {
        const pw = await import(modulePath);
        const browser = await pw[engine].launch({headless:true, executablePath:engine === 'chromium'
            ? process.env.PROGRAM_CHROMIUM_PATH ?? '/usr/bin/chromium' : process.env.PROGRAM_WEBKIT_PATH});
        const context = await browser.newContext({viewport:{width:1440,height:1000}});
        const page = await context.newPage();
        const errors=[], cells=[];
        page.on('pageerror',e=>errors.push(e.message));
        await fs.mkdir(out,{recursive:true});
        try {
            await page.goto(base+'/galeri');
            for (const motion of ['no-preference','reduce']) {
                await page.emulateMedia({reducedMotion:motion});
                for (const locale of ['id','en','ar']) {
                    await Promise.all([page.waitForNavigation(),page.locator(`form[action$="/bahasa/${locale}"] button`).click()]);
                    await page.evaluate(()=>document.fonts.ready);
                    for (const width of widths) {
                        await page.setViewportSize({width,height:width<640?844:1000});
                        const cell=await page.evaluate(geometry);
                        assert.equal(cell.overflow,false,JSON.stringify(cell));
                        assert.equal(cell.clips,false,JSON.stringify(cell));
                        assert.equal(cell.lang,locale); assert.equal(cell.heading,1); assert.equal(cell.controls,3);
                        cells.push({...cell,motion});
                    }
                    await page.setViewportSize({width:390,height:844});
                    await page.locator('[data-viewer-link]').first().click();
                    assert.equal(await page.locator('dialog').evaluate(d=>d.open),true);
                    await page.keyboard.press('Escape');
                    assert.equal(await page.locator('dialog').evaluate(d=>d.open),false);
                    assert.equal(await page.evaluate(()=>document.activeElement.hasAttribute('data-viewer-link')),true);
                    assert.equal(await page.locator('html').getAttribute('data-gallery-viewing'),null);
                    for (const width of [390,1440]) {
                        await page.setViewportSize({width,height:width===390?844:1000});
                        // Hydrate actual lazy media, then capture after reveal completion.
                        for (const card of await page.locator('[data-gallery-card]').all()) {
                            await card.scrollIntoViewIfNeeded();
                        }
                        await page.evaluate(()=>Promise.all([...document.images].map(i=>i.decode().catch(()=>{}))));
                        await page.evaluate(()=>scrollTo(0,0));
                        await page.waitForTimeout(750);
                        if (motion==='no-preference') await page.screenshot({path:`${out}/${engine}-${locale}-${width}.png`,fullPage:true});
                    }
                }
            }
            await page.setViewportSize({width:768,height:360});
            await page.evaluate(()=>document.body.style.zoom='2');
            assert.equal((await page.evaluate(geometry)).overflow,false);
            await page.evaluate(()=>document.body.style.zoom='');
            const native = await browser.newContext({javaScriptEnabled:false,viewport:{width:360,height:780}});
            const fallback = await native.newPage(); await fallback.goto(base+'/galeri');
            assert.equal((await fallback.evaluate(geometry)).overflow,false);
            assert.ok(await fallback.locator('[data-viewer-link][href]').count()>0);
            await native.close();
            assert.deepEqual(errors,[]);
            await fs.writeFile(`${out}/${engine}-matrix.json`,JSON.stringify({engine,version:browser.version(),cells,errors},null,2));
        } finally { await browser.close(); }
    });
}
