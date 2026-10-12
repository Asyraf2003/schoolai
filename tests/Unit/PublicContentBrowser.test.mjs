import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';

const url = process.env.SCHOOLAI_BROWSER_URL;
const modulePath = process.env.PROGRAM_PLAYWRIGHT_MODULE;
const enabled = !!url && !!modulePath;
const engines = (process.env.PROGRAM_ENGINES ?? 'chromium,webkit').split(',');
const widths = [360, 390, 639, 640, 767, 768, 1023, 1024, 1180, 1181, 1279, 1280, 1440, 1535, 1536, 1920];
const out = process.env.PROGRAM_PROOF_DIRECTORY ?? '/tmp';
const geometry = () => ({
    overflow: document.documentElement.scrollWidth > innerWidth + 1,
    lang: document.documentElement.lang, dir: document.documentElement.dir,
    headings: document.querySelectorAll('h1').length,
    badMediaRatio: [...document.querySelectorAll('.public-card img, .public-card video')].some(media => {
        const box = media.getBoundingClientRect();
        return Math.abs(box.width / box.height - 16 / 9) > .02;
    }),
    unnamedLinks: [...document.querySelectorAll('main a')].filter(a => !a.textContent.trim()
        && !a.getAttribute('aria-label') && !a.querySelector('img[alt]:not([alt=""])')).length,
});

for (const engine of engines) test(`Public gallery and real Stories in ${engine}`, {skip: !enabled}, async () => {
    const playwright = await import(modulePath);
    const browser = await playwright[engine].launch({headless:true,
        ...(engine === 'chromium' ? {executablePath:process.env.PROGRAM_CHROMIUM_PATH ?? '/usr/bin/chromium'}
            : (process.env.PROGRAM_WEBKIT_PATH ? {executablePath:process.env.PROGRAM_WEBKIT_PATH} : {}))});
    const context = await browser.newContext({viewport:{width:390,height:844}});
    const page = await context.newPage();
    const errors = [];
    const cells = [];
    page.on('pageerror', e => errors.push(e.message));
    try {
        for (const motion of ['reduce', 'no-preference']) {
            await page.emulateMedia({reducedMotion:motion});
            for (const route of ['/galeri', '/artikel', '/artikel/menjelajah-dunia-digital']) {
                assert.equal((await page.goto(url + route, {waitUntil:'domcontentloaded'})).status(), 200);
                for (const locale of ['id', 'en', 'ar']) {
                    await Promise.all([page.waitForNavigation({waitUntil:'domcontentloaded'}),
                        page.locator(`form[action$="/bahasa/${locale}"] button`).click()]);
                    assert.equal(new URL(page.url()).pathname, route);
                    await page.evaluate(() => document.fonts.ready);
                    for (const width of widths) {
                        await page.setViewportSize({width,height:width < 640 ? 780 : 900});
                        const cell = await page.evaluate(geometry);
                        assert.equal(cell.overflow, false, `${route}/${locale}/${width}/${motion}`);
                        assert.equal(cell.lang, locale);
                        assert.equal(cell.dir, locale === 'ar' ? 'rtl' : 'ltr');
                        assert.equal(cell.headings, 1);
                        assert.equal(cell.badMediaRatio, false, 'reserved media geometry');
                        assert.equal(cell.unnamedLinks, 0);
                        cells.push({route,locale,width,motion,...cell});
                    }
                    assert.equal(await page.locator('a[href="https://forms.gle/BrowserFixture"]').count(), 1);
                    if (route === '/galeri') {
                        assert.equal(await page.locator('[data-public-gallery] figure').count(), 10);
                        assert.equal(await page.locator('video[controls][preload="none"][poster]').count(), 5);
                    } else if (route === '/artikel') {
                        assert.equal(await page.locator('[data-public-articles] article').count(), 4);
                        assert.equal(await page.locator('[data-public-articles] img').count(), 4);
                        assert.equal(await page.locator('[data-public-articles] video').count(), 0);
                    } else {
                        assert.equal(await page.locator('video').getAttribute('poster'), 'https://media.almustaqbal.sch.id/site/school-life/labit-v1.webp');
                        assert.equal(await page.locator('.article-content img, .article-content video').count(), 0);
                        assert.ok((await page.locator('.article-content').textContent()).trim().length > 0);
                    }
                }
                await page.setViewportSize({width:768,height:360});
                assert.equal((await page.evaluate(geometry)).overflow, false, 'landscape');
                await page.evaluate(() => {document.body.style.zoom = '2';});
                assert.equal((await page.evaluate(geometry)).overflow, false, '200% zoom');
                await page.evaluate(() => {document.body.style.zoom = '';});
                if (route.includes('/artikel/')) {
                    const title = page.locator('h1');
                    const original = await title.textContent();
                    await title.evaluate(node => {node.textContent = 'A'.repeat(200);});
                    await page.setViewportSize({width:360,height:780});
                    assert.equal((await page.evaluate(geometry)).overflow, false, 'long optional title');
                    await title.evaluate((node, text) => {node.textContent = text;}, original);
                }
                await page.locator('body').click({position:{x:1,y:1}});
                await page.keyboard.press('Tab');
                assert.equal(await page.evaluate(() => document.activeElement.tagName), 'A');
                assert.notEqual(await page.evaluate(() => getComputedStyle(document.activeElement).outlineStyle), 'none');
                await page.setViewportSize({width:390,height:844});
                await page.screenshot({path:`${out}/public-content-${engine}-${route === '/galeri' ? 'gallery' : route === '/artikel' ? 'articles' : 'article'}.png`, fullPage:true});
            }
        }
        const staticContext = await browser.newContext({javaScriptEnabled:false,viewport:{width:360,height:780},isMobile:true,hasTouch:true});
        const staticPage = await staticContext.newPage();
        for (const route of ['/galeri', '/artikel', '/artikel/menjelajah-dunia-digital']) {
            await staticPage.goto(url + route, {waitUntil:'domcontentloaded'});
            await Promise.all([staticPage.waitForNavigation({waitUntil:'domcontentloaded'}),
                staticPage.locator('form[action$="/bahasa/ar"] button').tap()]);
            assert.equal((await staticPage.evaluate(geometry)).overflow, false);
            assert.equal((await staticPage.evaluate(geometry)).dir, 'rtl');
            assert.equal(await staticPage.locator('.public-nav a').count(), 4);
        }
        await staticContext.close();
        assert.deepEqual(errors, []);
        await fs.writeFile(`${out}/public-content-${engine}.json`, JSON.stringify({engine,version:browser.version(),cells,errors}, null, 2));
    } finally { await browser.close(); }
});
