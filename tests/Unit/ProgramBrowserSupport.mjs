import fs from 'node:fs/promises';
import assert from 'node:assert/strict';

export const url = process.env.PROGRAM_BROWSER_URL;
export const enabled = !!url && !!process.env.PROGRAM_PLAYWRIGHT_MODULE;
export const engines = (process.env.PROGRAM_ENGINES ?? 'chromium,firefox,webkit').split(',');
export const directory = process.env.PROGRAM_PROOF_DIRECTORY ?? 'docs2/proof';

export async function session(engine, options = {}) {
    const playwright = await import(process.env.PROGRAM_PLAYWRIGHT_MODULE);
    const launch = { headless: true };
    if (engine === 'chromium') launch.executablePath = process.env.PROGRAM_CHROMIUM_PATH ?? '/usr/bin/chromium';
    if (engine === 'edge') launch.executablePath = process.env.PROGRAM_EDGE_PATH;
    if (engine === 'webkit' && process.env.PROGRAM_WEBKIT_PATH) launch.executablePath = process.env.PROGRAM_WEBKIT_PATH;
    const browser = await playwright[engine === 'edge' ? 'chromium' : engine].launch(launch);
    const context = await browser.newContext({ viewport: { width: 1440, height: 900 }, ...options });
    const page = await context.newPage();
    const errors = [];
    const requests = [];
    page.on('pageerror', error => errors.push(error.message));
    page.on('request', request => requests.push(request.url()));
    if (process.env.PROGRAM_LOGO_FIXTURE) await page.route('**/site/brand/logo-nav-v1.webp', route => route.fulfill({
        path: process.env.PROGRAM_LOGO_FIXTURE, contentType: 'image/webp',
    }));
    if (process.env.PROGRAM_SCROLLTRIGGER_FIXTURE) {
        await page.route('**/gsap@3.7.1/dist/gsap.min.js', route => route.fulfill({
            path: process.env.PROGRAM_GSAP_FIXTURE, contentType: 'application/javascript',
        }));
        await page.route('**/gsap@3.7.1/dist/ScrollTrigger.min.js', route => route.fulfill({
            path: process.env.PROGRAM_SCROLLTRIGGER_FIXTURE, contentType: 'application/javascript',
        }));
    }
    await page.goto(url, { waitUntil: 'domcontentloaded' });
    if (options.javaScriptEnabled !== false) await page.waitForFunction(() => !!document.querySelector('[data-program]')?.dataset.programState);
    return { browser, context, page, errors, requests, version: browser.version(), close: () => browser.close() };
}

export async function locale(page, value) {
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        page.locator(`form[action$="/bahasa/${value}"]`).evaluate(form => form.requestSubmit()),
    ]);
    await page.waitForFunction(value => document.documentElement.lang === value && !!document.querySelector('[data-program]')?.dataset.programState, value);
    await page.evaluate(() => document.fonts.ready);
}

export async function phase(page, value) {
    await page.waitForFunction(value => document.querySelector('[data-program]').dataset.programState === value, value);
}

export async function card(page, index = 0) {
    return page.locator('[data-program-open]').nth(index);
}

export async function field(page) {
    for (let index = 0; index < 8; index++) await page.locator('[data-program-card]').nth(index).scrollIntoViewIfNeeded();
    await page.waitForFunction(() => [...document.querySelectorAll('[data-program-card] summary img')].every(image => image.complete && image.naturalWidth > 0));
    await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
}

export const geometry = () => {
    const box = element => {
        const r = element.getBoundingClientRect();
        return { x: r.x, y: r.y, width: r.width, height: r.height, bottom: r.bottom, right: r.right };
    };
    const root = document.querySelector('[data-program]');
    const detail = document.querySelector('[data-program-detail-stage] article');
    return {
        locale: document.documentElement.lang, direction: getComputedStyle(root).direction,
        viewport: [innerWidth, innerHeight], overflow: document.documentElement.scrollWidth > innerWidth,
        programOverflow: root.scrollWidth > root.clientWidth + 1,
        columns: getComputedStyle(document.querySelector('[data-program-cards]')).gridTemplateColumns.split(' ').length,
        font: getComputedStyle(root).fontFamily,
        cards: [...document.querySelectorAll('[data-program-card]')].map(box),
        heading: box(document.querySelector('[data-program-heading]')),
        anchors: ['program-story-start', 'program-story-end', 'program-values-seam', 'values-entry-anchor'].map(name => box(document.querySelector(`[data-${name}]`))),
        detail: detail ? { box: box(detail), back: box(detail.querySelector('[data-program-back]')),
            title: box(detail.querySelector('h3')), description: box(detail.querySelector('p')),
            media: box(detail.querySelector('[data-program-image-wrap]')),
            titleScale: detail.querySelector('h3').dataset.titleScale,
            font: getComputedStyle(detail.querySelector('h3')).fontFamily,
            leading: parseFloat(getComputedStyle(detail.querySelector('h3')).lineHeight) / parseFloat(getComputedStyle(detail.querySelector('h3')).fontSize),
            scrollWidth: document.querySelector('[data-program-dialog]').scrollWidth,
        } : null,
    };
};

export function fits(state, scope = 'page') {
    const overflow = scope === 'program' ? state.programOverflow : state.overflow;
    assert.equal(overflow, false, `${state.locale}/${state.viewport}: ${scope} horizontal overflow`);
    assert.equal(state.direction, state.locale === 'ar' ? 'rtl' : 'ltr');
    if (state.locale === 'ar') assert.match(state.font, /Cairo/);
    if (state.detail) {
        if (state.locale === 'ar') assert.ok(state.detail.leading >= 1.3, 'Arabic title joining and leading');
        assert.ok(state.detail.back.bottom <= state.detail.title.y + 1, 'Back above title');
        assert.ok(state.detail.title.bottom <= state.detail.description.y + 1, 'copy follows actual title');
        assert.ok(state.detail.scrollWidth <= state.viewport[0], 'dialog inline overflow');
    }
}

export async function evidence(name, content) {
    await fs.writeFile(`${directory}/${name}.json`, JSON.stringify({ date: new Date().toISOString(), platform: process.platform, url, ...content }, null, 2));
}
