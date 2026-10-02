import assert from 'node:assert/strict';
import { writeFileSync, mkdirSync } from 'node:fs';
import { localeContext, delay } from './home-opening-helpers.mjs';
import { instrumentCompleteJourney, completeSnapshot, traverseCompleteHomepage } from './home-complete-journey.mjs';
const { chromium, webkit } = await import(process.env.SCHOOLAI_BROWSER_MODULE || '/tmp/schoolai-performance-tools/node_modules/playwright/index.mjs');
const base = process.env.SCHOOLAI_PROOF_URL || 'http://127.0.0.1:8019';
const directory = process.env.SCHOOLAI_COMPLETE_PROOF_DIR || '/tmp/schoolai-homepage-complete';
mkdirSync(directory, { recursive: true });
const records = [];
const widths = process.env.SCHOOLAI_PROOF_WIDTHS?.split(',').map(Number) || [360,390,640,768,1024,1180,1181,1280,1440,1536,1920];
const engines = process.env.SCHOOLAI_PROOF_ENGINES?.split(',') || ['chromium','webkit'];
const locales = process.env.SCHOOLAI_PROOF_LOCALES?.split(',') || ['id','en','ar'];
const motions = process.env.SCHOOLAI_PROOF_MOTIONS?.split(',') || ['no-preference','reduce'];
for (const engine of engines) {
    const browser = await (engine === 'chromium'
        ? (process.env.SCHOOLAI_CHROME_CDP ? chromium.connectOverCDP(process.env.SCHOOLAI_CHROME_CDP) : chromium.launch({ headless: true, args: ['--disable-gpu'] }))
        : webkit.launch({ headless: true, executablePath: process.env.SCHOOLAI_WEBKIT_PATH || '/tmp/schoolai-webkit.sh' }));
    try {
        for (const locale of locales) for (const motion of motions) {
            const context = await localeContext(browser, base, locale, motion); await instrumentCompleteJourney(context);
            for (const width of widths) {
                const page = await context.newPage(); await page.setViewportSize({ width, height: 900 });
                const requests = [], errors = [];
                page.on('request', request => requests.push({ url: request.url(), type: request.resourceType() }));
                page.on('pageerror', error => errors.push(error.message));
                await page.goto(base, { waitUntil: 'domcontentloaded' });
                await page.waitForFunction(() => document.documentElement.dataset.homePreparationState === 'complete', null, { timeout: 120000 }).catch(async error => {
                    writeFileSync(directory+'/failure.json', JSON.stringify(await completeSnapshot(page), null, 2)); throw error;
                });
                // Native input immediately after unlock must use already prepared runtime.
                const firstInputIndex = requests.length;
                await page.mouse.move(width / 2, 500); await page.mouse.wheel(0, 400); await delay(120);
                const journey = await traverseCompleteHomepage(page, requests, firstInputIndex);
                assert.deepEqual(errors, []);
                const record = { engine, browser: browser.version(), source: process.env.SCHOOLAI_PROOF_SOURCE || 'working tree', profile: process.env.SCHOOLAI_BROWSER_PROFILE || 'Linux WSL2 software graphics', locale, motion, width, height: 900, errors, journey };
                records.push(record); writeFileSync(directory+'/journeys.json', JSON.stringify(records, null, 2));
                if ([390,1440].includes(width)) await page.screenshot({ path: `${directory}/${engine}-${locale}-${motion}-${width}-footer.png` });
                console.log(engine, locale, motion, width, 'three journeys: dependency readiness PASS; frame data recorded');
                await page.close();
            }
            await context.close();
        }
    } finally { await browser.close(); }
}
