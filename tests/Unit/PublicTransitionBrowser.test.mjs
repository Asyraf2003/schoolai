import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
const base=process.env.SCHOOLAI_BROWSER_URL;
const modulePath=process.env.PROGRAM_PLAYWRIGHT_MODULE;
const out=process.env.PROGRAM_PROOF_DIRECTORY??'/tmp/gallery-v3-proof';
for(const engine of (process.env.PROGRAM_ENGINES??'chromium,webkit').split(',')) {
    test(`Public navigation menu, footer, Back and Article in ${engine}`,{skip:!base||!modulePath},async()=>{
        const pw=await import(modulePath);
        const browser=await pw[engine].launch({headless:true,executablePath:engine==='chromium'
            ? process.env.PROGRAM_CHROMIUM_PATH??'/usr/bin/chromium':process.env.PROGRAM_WEBKIT_PATH});
        await fs.mkdir(out,{recursive:true});
        const context=await browser.newContext({viewport:{width:1440,height:900},recordVideo:{dir:out,size:{width:1440,height:900}}});
        const page=await context.newPage(), errors=[],proof=[];
        page.on('pageerror',e=>errors.push(e.message));
        const ready=async()=>{await page.waitForTimeout(900);};
        const navigate=async(selector,path,name)=>{
            const target = page.locator(selector).first();
            const menu = target.locator('xpath=ancestor::details[1]');
            if (await menu.count() && !(await menu.getAttribute('open') !== null)) await menu.locator('summary').click();
            await target.click();
            assert.equal(await page.locator('html').getAttribute('data-public-transition'),'exit',name);
            await page.waitForURL(url=>url.pathname===path);
            assert.equal(await page.locator('.public-transition__slice').count(),10);
            assert.equal(await page.locator('html').getAttribute('data-public-transition'),'enter',name);
            await ready();
            assert.equal(await page.locator('.public-transition').evaluate(e=>getComputedStyle(e).visibility),'hidden');
            proof.push({name,path,result:'PASS'});
        };
        try {
            await page.goto(base);
            const originalLogo=await page.locator('.site-header__brand img').getAttribute('src');
            await navigate('[data-header] a[href$="/galeri"]','/galeri','landing menu');
            assert.equal(await page.locator('.public-transition__logo').getAttribute('src'),originalLogo);
            assert.equal(await page.locator('.gallery-header__logo').getAttribute('src'),originalLogo);
            await navigate('.gallery-back','/','Gallery Back');
            await navigate('[data-header] a[href$="/artikel"]','/artikel','landing Articles');
            await navigate('.public-nav a[href$="/galeri"]','/galeri','Article Gallery');
            await navigate('.gallery-article','/artikel','Gallery Article');
            await page.goBack(); await ready();
            assert.equal(new URL(page.url()).pathname,'/galeri');
            assert.equal(await page.locator('.public-transition').isVisible(),false);
            await page.goto(base);
            await page.waitForFunction(()=>document.querySelector('[data-footer-v2]').hasAttribute('data-footer-phase'));
            await page.evaluate(()=>{
                const white=document.querySelector('.site-footer-v2__white');
                scrollTo(0,white.getBoundingClientRect().top+scrollY+white.offsetHeight-innerHeight-100);
            });
            await page.mouse.move(900,500); await page.mouse.wheel(0,3000);
            await page.waitForFunction(()=>document.querySelector('[data-footer-v2]').dataset.footerPhase==='white-held');
            await page.waitForTimeout(300); await page.mouse.wheel(0,1200);
            await page.waitForFunction(()=>document.querySelector('[data-footer-v2]').dataset.footerPhase==='blue-held');
            await page.waitForTimeout(300); await page.mouse.wheel(0,1000);
            await page.waitForFunction(()=>document.documentElement.dataset.publicTransition==='exit');
            await page.waitForURL('**/galeri'); await ready();
            proof.push({name:'existing footer wheel gate',path:'/galeri',result:'PASS'});
            await page.setViewportSize({width:390,height:844});
            await navigate('.gallery-back','/','mobile Back');
            await page.locator('[data-menu-toggle]').click();
            await navigate('[data-header] a[href$="/galeri"]','/galeri','mobile menu');
            await page.emulateMedia({reducedMotion:'reduce'});
            await page.locator('.gallery-article').click(); await page.waitForURL('**/artikel');
            assert.equal(await page.locator('html').getAttribute('data-public-transition'),null);
            proof.push({name:'reduced motion immediate navigation',result:'PASS'});
            await page.goto(base+'/login');
            assert.equal(await page.locator('[data-public-transition]').count(),0);
            assert.deepEqual(errors,[]);
            await fs.writeFile(`${out}/${engine}-navigation.json`,JSON.stringify({engine,version:browser.version(),proof,errors},null,2));
        } finally {
            const video=page.video(); await context.close();
            await video.saveAs(`${out}/${engine}-navigation.webm`);
            await browser.close();
        }
    });
}
