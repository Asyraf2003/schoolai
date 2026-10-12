import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';

const url = process.env.SCHOOLAI_BROWSER_URL;
const modulePath = process.env.PROGRAM_PLAYWRIGHT_MODULE;
const enabled = !!url && !!modulePath;
const engines = (process.env.PROGRAM_ENGINES ?? 'chromium,webkit').split(',');
const widths = [360, 390, 639, 640, 767, 768, 1023, 1024, 1180, 1181, 1279, 1280, 1440, 1535, 1536, 1920];
const statePath = process.env.SCHOOLAI_BROWSER_STATE ?? '/tmp/schoolai-browser-state.json';
const out = process.env.PROGRAM_PROOF_DIRECTORY ?? '/tmp';

const geometry = () => ({
    overflow: document.documentElement.scrollWidth > innerWidth + 1,
    lang: document.documentElement.lang, dir: document.documentElement.dir,
    menu: [...document.querySelectorAll('nav[aria-label="Menu admin"] a')].map(a => a.textContent),
});
async function launch(engine) {
    const playwright = await import(modulePath);
    return playwright[engine].launch({headless: true,
        ...(engine === 'chromium' ? {executablePath: process.env.PROGRAM_CHROMIUM_PATH ?? '/usr/bin/chromium'} : (process.env.PROGRAM_WEBKIT_PATH ? {executablePath:process.env.PROGRAM_WEBKIT_PATH} : {}))});
}

for (const engine of engines) test(`Simple admin, localized articles and direct PPDB in ${engine}`, {skip: !enabled}, async () => {
    const browser = await launch(engine);
    const context = await browser.newContext({storageState: statePath, reducedMotion: 'reduce'});
    const page = await context.newPage();
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    const cells = [];
    try {
        for (const route of ['/admin/akun', '/admin/galeri', '/admin/galeri/create', '/admin/artikel', '/admin/artikel/create', '/admin/ppdb']) {
            const response = await page.goto(url + route, {waitUntil: 'domcontentloaded'});
            assert.equal(response.status(), 200, route);
            assert.equal(new URL(page.url()).pathname, route);
            for (const width of widths) {
                await page.setViewportSize({width, height: width <= 640 ? 780 : 900});
                const cell = await page.evaluate(geometry);
                assert.equal(cell.overflow, false, `${route}/${width}`);
                assert.deepEqual(cell.menu, ['Akun', 'Galeri', 'Artikel', 'Link PPDB']);
                assert.equal(cell.lang, 'id');
                cells.push({route, width, ...cell});
            }
        }
        await page.goto(url + '/admin/artikel/create');
        await page.locator('[data-editor-surface]').first().fill('Teks browser');
        const surface = page.locator('[data-editor-surface]').first();
        await surface.evaluate(node => {
            const range = document.createRange(); range.selectNodeContents(node);
            getSelection().removeAllRanges(); getSelection().addRange(range);
            node.dispatchEvent(new MouseEvent('mouseup', {bubbles:true}));
        });
        await page.locator('[data-command="bold"]').first().click();
        assert.match(await surface.innerHTML(), /<(?:b|strong)>Teks browser<\/(?:b|strong)>/);
        const source = await page.locator('textarea[name="content_id"]').inputValue();
        assert.match(source, /Teks browser/);
        assert.equal(await page.locator('[data-editor-surface]').nth(2).evaluate(node => getComputedStyle(node).fontFamily.includes('Cairo')), true);
        await page.goto(url + '/admin/ppdb');
        await page.locator('input[name="registration_url"]').fill('https://forms.gle/BrowserChanged');
        await Promise.all([page.waitForNavigation(), page.getByRole('button', {name:'Simpan link'}).click()]);
        await page.goto(url + '/');
        for (const locale of ['id', 'en', 'ar']) {
            await Promise.all([page.waitForNavigation({waitUntil:'domcontentloaded'}),
                page.locator(`form[action$="/bahasa/${locale}"]`).evaluate(form => form.requestSubmit())]);
            for (const width of widths) {
                await page.setViewportSize({width, height:900});
                const snapshot = await page.evaluate(() => ({...({
                    overflow: document.documentElement.scrollWidth > innerWidth + 1,
                    lang: document.documentElement.lang, dir:document.documentElement.dir,
                    ppdb: [...document.querySelectorAll('[data-header] a, [data-hero] a')]
                        .filter(a => a.href.includes('forms.gle')).map(a => a.href),
                    articles: document.querySelectorAll('[data-stories-feature] h3 a').length,
                })}));
                assert.equal(snapshot.overflow, false, `${locale}/${width}`);
                assert.equal(snapshot.dir, locale === 'ar' ? 'rtl' : 'ltr');
                assert.ok(snapshot.ppdb.length >= 2);
                assert.ok(snapshot.ppdb.every(href => href === 'https://forms.gle/BrowserChanged'));
                assert.equal(snapshot.articles, 4);
                cells.push({route:'/', locale, width, ...snapshot});
            }
            const articleLink = await page.locator('[data-stories-feature] h3 a').first().getAttribute('href');
            await page.goto(articleLink);
            assert.equal(await page.locator('.article-content').count(), 1);
            assert.equal(await page.locator('.article-content img, .article-content video').count(), 0);
            assert.equal((await page.evaluate(geometry)).overflow, false);
            await page.goto(url + '/');
        }
        await page.goto(url + '/admin/ppdb');
        await page.locator('input[name="registration_url"]').fill('');
        await Promise.all([page.waitForNavigation(), page.getByRole('button', {name:'Simpan link'}).click()]);
        await page.goto(url + '/');
        assert.equal(await page.locator('[data-header] a[href*="forms.gle"], [data-hero] a[href*="forms.gle"]').count(), 0);
        await page.emulateMedia({reducedMotion:'no-preference'});
        await page.reload({waitUntil:'domcontentloaded'});
        await page.waitForFunction(() => document.querySelector('[data-stories-sequence-enabled]'));
        for (const width of [360, 640, 768, 1024, 1280, 1536]) {
            await page.setViewportSize({width, height:780});
            await page.locator('[data-stories]').evaluate(root => scrollTo(0, root.offsetTop));
            await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
            assert.equal((await page.evaluate(geometry)).overflow, false, `normal motion/${width}`);
            cells.push({route:'/', motion:'normal', width, ...await page.evaluate(geometry)});
        }
        await page.goto(url + '/admin/artikel/create');
        await page.setViewportSize({width:768, height:900});
        await page.evaluate(() => {document.body.style.zoom = '2';});
        assert.equal((await page.evaluate(geometry)).overflow, false, '200% zoom');
        await page.evaluate(() => {document.body.style.zoom = '';});
        await page.keyboard.press('Tab');
        assert.equal(await page.evaluate(() => document.activeElement.tagName === 'A'), true);
        assert.deepEqual(errors, []);
        const staticContext = await browser.newContext({storageState:statePath, javaScriptEnabled:false, viewport:{width:360,height:780}});
        const staticPage = await staticContext.newPage();
        await staticPage.goto(url + '/admin/artikel/create');
        assert.equal(await staticPage.locator('textarea[name="content_id"]').isVisible(), true);
        assert.equal((await staticPage.evaluate(geometry)).overflow, false);
        await staticContext.close();
        await page.setViewportSize({width:390,height:844});
        await page.screenshot({path:`${out}/simple-admin-${engine}.png`, fullPage:true});
        await fs.writeFile(`${out}/simple-admin-${engine}.json`, JSON.stringify({engine, version:browser.version(), cells, errors}, null, 2));
    } catch (error) {
        console.error(error.stack);
        throw error;
    } finally {
        try {
        // Leave shared browser fixtures consistent for other regression jobs.
        await page.goto(url + '/admin/ppdb');
        await page.locator('input[name="registration_url"]').fill('https://forms.gle/BrowserFixture');
        await Promise.all([page.waitForNavigation(), page.getByRole('button', {name:'Simpan link'}).click()]);
        } finally { await browser.close(); }
    }
});
