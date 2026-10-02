import { mkdirSync, writeFileSync } from 'node:fs';
import { proveOpeningLifecycle } from './home-opening-lifecycle.mjs';
const { chromium, webkit } = await import(process.env.SCHOOLAI_BROWSER_MODULE || '/tmp/schoolai-performance-tools/node_modules/playwright/index.mjs');
const base = process.env.SCHOOLAI_PROOF_URL || 'http://127.0.0.1:8019';
const directory = process.env.SCHOOLAI_COMPLETE_PROOF_DIR || '/tmp/schoolai-homepage-lifecycle';
mkdirSync(directory, { recursive: true });
const results = [];
for (const engine of process.env.SCHOOLAI_PROOF_ENGINES?.split(',') || ['chromium','webkit']) {
    const browser = await (engine === 'chromium'
        ? (process.env.SCHOOLAI_CHROME_CDP ? chromium.connectOverCDP(process.env.SCHOOLAI_CHROME_CDP) : chromium.launch({ headless: true, args: ['--disable-gpu'] }))
        : webkit.launch({ headless: true, executablePath: process.env.SCHOOLAI_WEBKIT_PATH || '/tmp/schoolai-webkit.sh' }));
    try {
        await proveOpeningLifecycle(browser, engine, base, row => {
            results.push({ ...row, browser: browser.version(), source: process.env.SCHOOLAI_PROOF_SOURCE || 'working tree' });
            writeFileSync(directory + '/lifecycle.json', JSON.stringify(results, null, 2));
            console.log(engine, row.key, 'PASS');
        });
    } finally { await browser.close(); }
}
