import assert from 'node:assert/strict';
import { createHash } from 'node:crypto';
import { mkdirSync, writeFileSync } from 'node:fs';
import { localeContext } from './home-opening-helpers.mjs';
import { instrumentCompleteJourney, traverseCompleteHomepage } from './home-complete-journey.mjs';

const { chromium } = await import(process.env.SCHOOLAI_BROWSER_MODULE);
const browser = await chromium.connectOverCDP(process.env.SCHOOLAI_CHROME_CDP);
const base = process.env.SCHOOLAI_PROOF_URL || 'http://127.0.0.1:8019';
const directory = process.env.SCHOOLAI_COMPLETE_PROOF_DIR;
mkdirSync(directory, { recursive: true });
const records = [];
try {
    for (const width of process.env.SCHOOLAI_PROOF_WIDTHS?.split(',').map(Number) || [390,1440]) for (let run = 1; run <= Number(process.env.SCHOOLAI_PROOF_RUNS || 3); run++) {
        const context = await localeContext(browser, base, 'id', 'no-preference', width);
        await instrumentCompleteJourney(context);
        await context.addInitScript(() => {
            window.homeTiming = { lcp: [], shifts: [] };
            new PerformanceObserver(list => list.getEntries().forEach(entry => window.homeTiming.lcp.push({
                time: entry.startTime, size: entry.size, url: entry.url,
                element: entry.element?.tagName + '.' + entry.element?.className,
            }))).observe({ type: 'largest-contentful-paint', buffered: true });
            new PerformanceObserver(list => list.getEntries().forEach(entry => {
                if (!entry.hadRecentInput) window.homeTiming.shifts.push({ time: entry.startTime, value: entry.value });
            })).observe({ type: 'layout-shift', buffered: true });
        });
        const page = await context.newPage(), requests = [], errors = [], transfers = [];
        page.on('request', request => requests.push({ url: request.url(), type: request.resourceType() }));
        page.on('pageerror', error => errors.push(error.message));
        const client = await context.newCDPSession(page), resources = new Map();
        await client.send('Network.enable'); await client.send('Network.clearBrowserCache');
        client.on('Network.responseReceived', event => resources.set(event.requestId, { type: event.type, url: event.response.url }));
        client.on('Network.loadingFinished', event => transfers.push({ ...resources.get(event.requestId), bytes: event.encodedDataLength }));
        await client.send('Tracing.start', { categories: 'devtools.timeline,blink.user_timing', transferMode: 'ReturnAsStream' });
        await page.goto(base, { waitUntil: 'domcontentloaded' });
        await page.waitForFunction(() => document.documentElement.dataset.homePreparationState === 'complete', null, { timeout: 120000 });
        const firstInputIndex = requests.length;
        await page.mouse.move(width / 2, 500); await page.mouse.wheel(0, 400);
        const journey = await traverseCompleteHomepage(page, requests, firstInputIndex);
        const timing = await page.evaluate(() => ({ ...window.homeTiming,
            paints: performance.getEntriesByType('paint').map(entry => ({ name: entry.name, time: entry.startTime })),
            marks: performance.getEntriesByType('mark').map(entry => ({ name: entry.name, time: entry.startTime })),
        }));
        const traceDone = new Promise(resolve => client.once('Tracing.tracingComplete', resolve));
        await client.send('Tracing.end'); const { stream } = await traceDone;
        let raw = '';
        while (true) {
            const chunk = await client.send('IO.read', { handle: stream }); raw += chunk.data;
            if (chunk.eof) break;
        }
        await client.send('IO.close', { handle: stream });
        writeFileSync(`${directory}/${width}-${run}-trace.json`, raw);
        const events = JSON.parse(raw).traceEvents;
        const layout = events.filter(event => event.name === 'Layout' && event.dur).map(event => event.dur / 1000);
        assert.deepEqual(errors, []);
        const record = { width, run, browser: browser.version(), profile: process.env.SCHOOLAI_BROWSER_PROFILE,
            source: process.env.SCHOOLAI_PROOF_SOURCE || 'working tree', cache: 'browser HTTP cache cleared; new context; shared GPU process',
            traceHash: createHash('sha256').update(raw).digest('hex'), timing, transfers,
            layout: { count: layout.length, max: Math.max(0, ...layout), total: layout.reduce((sum, value) => sum + value, 0) }, journey, errors };
        records.push(record); writeFileSync(directory + '/cold-runs.json', JSON.stringify(records, null, 2));
        console.log(width, run, 'cold three journeys complete', journey.passes.map(pass => Math.max(...pass.gaps).toFixed(1)));
        await context.close();
    }
} finally { await browser.close(); }
