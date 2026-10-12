import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';

const url = process.env.SCHOOLAI_BROWSER_URL;
const modulePath = process.env.PROGRAM_PLAYWRIGHT_MODULE;
const enabled = Boolean(url && modulePath);
const engines = (process.env.PROGRAM_ENGINES ?? 'chromium,webkit').split(',');
const out = process.env.PROGRAM_PROOF_DIRECTORY ?? '/tmp';

for (const engine of engines) test(`R2 public playback and SEO in ${engine}`, { skip: !enabled }, async () => {
    const playwright = await import(modulePath);
    const browser = await playwright[engine].launch({ headless: true,
        ...(engine === 'chromium' ? { executablePath: process.env.PROGRAM_CHROMIUM_PATH ?? '/usr/bin/chromium' }
            : process.env.PROGRAM_WEBKIT_PATH ? { executablePath: process.env.PROGRAM_WEBKIT_PATH } : {}),
    });
    const context = await browser.newContext({ viewport: { width: 390, height: 844 }, reducedMotion: 'reduce' });
    const page = await context.newPage();
    const proof = { engine, version: browser.version(), metadata: [], playback: [] };
    const mediaResponses = [];
    page.on('response', response => {
        if (response.request().resourceType() === 'media') mediaResponses.push({
            url: response.url(), status: response.status(), range: response.headers()['content-range'],
        });
    });
    try {
        for (const route of ['/', '/galeri', '/artikel', '/artikel/menjelajah-dunia-digital']) {
            assert.equal((await page.goto(url + route, { waitUntil: 'load' })).status(), 200);
            for (const locale of ['id', 'ar', 'en']) {
                await page.evaluate(async locale => {
                    const form = document.querySelector(`form[action$="/bahasa/${locale}"]`);
                    await fetch(form.action, { method: 'POST', body: new FormData(form) });
                }, locale);
                await page.reload({ waitUntil: 'load' });
                const head = await page.evaluate(() => ({
                    locale: document.documentElement.lang,
                    dir: document.documentElement.dir,
                    canonical: document.querySelector('link[rel="canonical"]')?.href,
                    image: document.querySelector('meta[property="og:image"]')?.content,
                    robots: document.querySelector('meta[name="robots"]')?.content ?? '',
                    graph: JSON.parse(document.querySelector('script[type="application/ld+json"]').textContent),
                    titles: document.querySelectorAll('title').length,
                    descriptions: document.querySelectorAll('meta[name="description"]').length,
                    media: [...document.querySelectorAll('img[src], video[src], source[src], [data-footer-preview-src]')]
                        .map(node => node.getAttribute('src') || node.dataset.footerPreviewSrc),
                }));
                assert.equal(head.locale, locale);
                assert.equal(head.dir, locale === 'ar' ? 'rtl' : 'ltr');
                assert.equal(new URL(head.canonical).pathname, route);
                assert.equal(head.titles, 1);
                assert.equal(head.descriptions, 1);
                assert.ok(!head.robots.includes('noindex'));
                assert.ok(head.image.startsWith('https://media.almustaqbal.sch.id/'));
                assert.ok(head.media.every(media => media.startsWith('https://media.almustaqbal.sch.id/')));
                assert.equal(head.graph['@graph'][0]['@type'], 'School');
                proof.metadata.push({ route, ...head });
            }
        }
        await page.goto(url + '/galeri', { waitUntil: 'load' });
        const video = page.locator('video[controls]').first();
        const sources = [await video.getAttribute('src')];
        if (process.env.R2_UPLOAD_PROOF) {
            const uploaded = JSON.parse(await fs.readFile(process.env.R2_UPLOAD_PROOF, 'utf8'));
            sources.push(...uploaded.uploads.filter(media => media.type === 'video/mp4').map(media => media.url));
        }
        for (const source of sources) {
            await video.scrollIntoViewIfNeeded();
            const range = await page.evaluate(async source => {
                const response = await fetch(source, { headers: { Range: 'bytes=0-31' } });
                return { status: response.status, range: response.headers.get('Content-Range'),
                    bytes: (await response.arrayBuffer()).byteLength };
            }, source);
            assert.equal(range.status, 206);
            assert.match(range.range, /^bytes 0-31\//);
            assert.equal(range.bytes, 32);
            await video.evaluate((node, source) => {
                node.pause(); node.src = source; node.muted = true; node.load();
            }, source);
            await video.evaluate(node => node.play());
            await page.waitForFunction(() => document.querySelector('video[controls]').currentTime > .15);
            const playing = await video.evaluate(node => ({ time: node.currentTime,
                width: node.videoWidth, paused: node.paused, error: node.error?.code ?? null }));
            assert.ok(playing.width > 0);
            assert.equal(playing.paused, false);
            assert.equal(playing.error, null);
            await video.evaluate(node => node.pause());
            proof.playback.push({ source, range, ...playing });
        }
        await fs.writeFile(`${out}/r2-media-${engine}.json`, JSON.stringify(proof, null, 2));
    } catch (error) {
        const diagnostic = await page.evaluate(() => {
            const node = document.querySelector('video[controls]');
            const rect = node?.getBoundingClientRect();
            return { hidden: document.hidden, viewport: [innerWidth, innerHeight],
                video: node ? { source: node.currentSrc, time: node.currentTime, paused: node.paused,
                    ready: node.readyState, network: node.networkState, error: node.error?.code,
                    width: node.videoWidth, top: rect.top, bottom: rect.bottom } : null };
        }).catch(() => null);
        console.error(JSON.stringify({ engine, diagnostic, mediaResponses }));
        await fs.writeFile(`${out}/r2-media-${engine}.json`, JSON.stringify({ ...proof,
            error: error.message, diagnostic, mediaResponses }, null, 2));
        throw error;
    } finally { await browser.close(); }
});
