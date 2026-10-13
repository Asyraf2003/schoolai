import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
const base = process.env.SCHOOLAI_BROWSER_URL;
const modulePath = process.env.PROGRAM_PLAYWRIGHT_MODULE;
const out = process.env.PROGRAM_PROOF_DIRECTORY ?? '/tmp/spatial-proof';
const widths = [360,390,639,640,767,768,1023,1024,1279,1280,1440,1535,1536,1920];
for (const engine of (process.env.PROGRAM_ENGINES ?? 'chromium,webkit').split(',')) {
    test(`Spatial stage geometry, input, fallback and locale in ${engine}`, { skip: !base || !modulePath }, async () => {
        const pw = await import(modulePath);
        const browser = await pw[engine].launch({ headless: true, executablePath: engine === 'chromium'
            ? process.env.PROGRAM_CHROMIUM_PATH ?? '/usr/bin/chromium' : process.env.PROGRAM_WEBKIT_PATH,
            args: engine === 'chromium' ? ['--enable-unsafe-swiftshader'] : [] });
        await fs.mkdir(out, { recursive: true });
        const context = await browser.newContext({ viewport: { width: 1440, height: 900 }, recordVideo: { dir: out, size: { width:1440,height:900 } } });
        const page = await context.newPage(), errors = [], cells = [], checks = [];
        page.on('pageerror', error => errors.push(error.message));
        const stage = page.locator('[data-spatial-gallery]');
        const position = () => stage.getAttribute('data-spatial-position').then(Number);
        const ready = async () => {
            await page.waitForFunction(() => +document.querySelector('[data-spatial-gallery]')?.dataset.spatialTextures >= 3);
            await page.waitForTimeout(350);
        };
        const settle = () => page.waitForTimeout(1100);
        try {
            await page.goto(base + '/galeri'); await ready();
            await page.screenshot({ path: `${out}/${engine}-1440.png` });
            const curve = await page.locator('[data-spatial-instance="1"]').evaluate(link => {
                const points = link.style.clipPath.match(/-?[\d.]+px/g).map(parseFloat);
                const top = Array.from({ length: 49 }, (_, i) => [points[i*2], points[i*2+1]]);
                return { midpointDeviation: Math.abs(top[24][1] - (top[0][1] + top[48][1]) / 2), outline: link.style.clipPath };
            });
            assert.ok(curve.midpointDeviation > 5, JSON.stringify(curve)); checks.push({ name:'curved projected mesh edge', ...curve });
            await page.mouse.move(720,400); const before = await position();
            await page.mouse.move(820,400,{steps:12}); await settle();
            assert.ok(Math.abs(await position() - before) > .01);
            const idle = await position(); await settle(); assert.ok(Math.abs(await position() - idle) < .005);
            checks.push({ name:'pointer displacement settles',result:'PASS' });
            await page.mouse.down(); await page.mouse.move(460,400,{steps:20}); await page.mouse.up();
            await page.screenshot({path:`${out}/${engine}-motion-01.png`});
            await page.waitForTimeout(150); await page.screenshot({path:`${out}/${engine}-motion-02.png`});
            await settle(); assert.equal(await page.locator('dialog').evaluate(d=>d.open),false);
            assert.ok(Math.abs(await position() - idle) > .1);
            await page.screenshot({path:`${out}/${engine}-motion-03.png`});
            checks.push({name:'drag inertia without click',result:'PASS'});
            const horizontal = await position(); await page.mouse.wheel(260,0); await settle();
            assert.ok(await position() > horizontal); assert.equal(await page.evaluate(()=>scrollY),0);
            const vertical = await position(); await page.mouse.wheel(0,620); await settle();
            assert.ok(await page.evaluate(()=>scrollY)>300); assert.ok(Math.abs(await position()-vertical)<.01);
            await page.locator('#stories').scrollIntoViewIfNeeded(); assert.equal(await page.locator('#stories h2').isVisible(),true);
            await page.locator('#all-posts [data-viewer-link]').first().click(); assert.equal(await page.locator('dialog').evaluate(d=>d.open),true);
            await page.keyboard.press('Escape');
            checks.push({name:'deltaX/deltaY independent and lower archive viewer',result:'PASS'});
            await page.evaluate(()=>scrollTo(0,0)); await settle();
            const modalPosition = await position();
            await page.mouse.click(720,430); assert.equal(await page.locator('dialog').evaluate(d=>d.open),true);
            await page.mouse.wheel(0,500); await page.waitForTimeout(100); assert.equal(await page.evaluate(()=>scrollY),0);
            await page.screenshot({path:`${out}/${engine}-modal.png`});
            await page.keyboard.press('Escape'); await settle();
            assert.equal(await page.locator('dialog').evaluate(d=>d.open),false);
            assert.ok(Math.abs(await position()-modalPosition)<.01,`modal restore ${modalPosition} -> ${await position()}`);
            checks.push({name:'whole-panel modal, Escape, scroll lock and position restoration',result:'PASS'});
            for (const locale of ['id','en','ar']) {
                await Promise.all([page.waitForNavigation(), page.locator(`form[action$="/bahasa/${locale}"] button`).click()]);
                await page.evaluate(()=>scrollTo(0,0)); await ready();
                for (const width of widths) {
                    await page.setViewportSize({width,height:width<640?844:900}); await page.waitForTimeout(100);
                    const cell=await page.evaluate(()=>({width:innerWidth,locale:document.documentElement.lang,dir:document.documentElement.dir,
                        overflow:document.documentElement.scrollWidth>innerWidth+1,
                        textures:+document.querySelector('[data-spatial-gallery]').dataset.spatialTextures,
                        ready:document.querySelector('[data-spatial-gallery]').hasAttribute('data-spatial-ready'),
                        canvas:document.querySelectorAll('.gallery-spatial__canvas').length}));
                    assert.equal(cell.overflow,false); assert.equal(cell.locale,locale); assert.equal(cell.ready,true);
                    assert.equal(cell.canvas,1); assert.ok(cell.textures<=5); cells.push(cell);
                    if ([390,640,768,1024,1440,1920].includes(width)) await page.screenshot({path:`${out}/${engine}-${locale}-${width}.png`});
                }
            }
            await page.setViewportSize({width:390,height:844}); await settle();
            await page.emulateMedia({reducedMotion:'reduce'});
            const reduced=await position(); await page.mouse.move(100,350);await page.mouse.move(290,350,{steps:10});await settle();
            assert.equal(await position(),reduced);
            await page.locator('[data-spatial-panel]').nth(5).focus(); await settle();
            await page.keyboard.press('Enter'); assert.equal(await page.locator('dialog').evaluate(d=>d.open),true);
            await page.locator('[data-viewer-close]').click();
            await page.setViewportSize({width:768,height:390}); await page.evaluate(()=>document.body.style.zoom='2');
            assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1),false);
            checks.push({name:'reduced pointer, keyboard modal, short height, 200% zoom',result:'PASS'});
            await page.evaluate(()=>document.body.style.zoom='');
            await page.evaluate(()=>document.querySelector('canvas.gallery-spatial__canvas').dispatchEvent(new Event('webglcontextlost',{cancelable:true})));
            assert.equal(await page.locator('.gallery-spatial__canvas').count(),0);
            await page.locator('[data-spatial-panel]').first().click(); assert.equal(await page.locator('dialog').evaluate(d=>d.open),true);
            checks.push({name:'context loss restores clickable HTML',result:'PASS'});
            const native = await browser.newContext({ javaScriptEnabled:false,viewport:{width:390,height:844} });
            const fallback = await native.newPage(); await fallback.goto(base+'/galeri');
            assert.equal(await fallback.locator('.gallery-spatial__canvas').count(),0);
            assert.ok(await fallback.locator('[data-spatial-panel][href]').count()>0);
            await fallback.screenshot({path:`${out}/${engine}-no-js.png`}); await native.close();
            assert.deepEqual(errors,[]);
            await fs.writeFile(`${out}/${engine}-stage.json`,JSON.stringify({engine,version:browser.version(),cells,checks,errors},null,2));
        } finally {
            const video=page.video();await context.close();await video.saveAs(`${out}/${engine}-stage.webm`);await browser.close();
        }
    });
}
