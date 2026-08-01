import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { chromium } from 'playwright-core';

const baseURL = process.env.HERO_PROOF_BASE_URL || 'http://127.0.0.1:8000';
const chromePath = process.env.CHROME_PATH || '/usr/bin/google-chrome';
const outputDir = path.resolve('storage/app/hero-proof');
const widths = [360, 390, 640, 768, 1024, 1180, 1181, 1279, 1280, 1536, 1920];
const locales = [
  { code: 'id', dir: 'ltr' },
  { code: 'en', dir: 'ltr' },
  { code: 'ar', dir: 'rtl' },
];
const report = { browser: 'Chromium', matrix: [], interactions: {} };
const proofVideoBase64 = 'AAAAIGZ0eXBpc29tAAACAGlzb21pc28yYXZjMW1wNDEAAAOcbW9vdgAAAGxtdmhkAAAAAAAAAAAAAAAAAAAD6AAAC7gAAQAAAQAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgAAAsd0cmFrAAAAXHRraGQAAAADAAAAAAAAAAAAAAABAAAAAAAAC7gAAAAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAABAAAAAAKAAAABaAAAAAAAkZWR0cwAAABxlbHN0AAAAAAAAAAEAAAu4AAAAAAABAAAAAAI/bWRpYQAAACBtZGhkAAAAAAAAAAAAAAAAAAAoAAAAeABVxAAAAAAALWhkbHIAAAAAAAAAAHZpZGUAAAAAAAAAAAAAAABWaWRlb0hhbmRsZXIAAAAB6m1pbmYAAAAUdm1oZAAAAAEAAAAAAAAAAAAAACRkaW5mAAAAHGRyZWYAAAAAAAAAAQAAAAx1cmwgAAAAAQAAAapzdGJsAAAAunN0c2QAAAAAAAAAAQAAAKphdmMxAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAAAAKAAWgBIAAAASAAAAAAAAAABFUxhdmM2MS4xOS4xMDEgbGli eDI2NAAAAAAAAAAAAAAAGP//AAAAMGF2Y0MBQsAK/+EAGGdCwAraCjfkwEQAAAMABAAAAwBQPEiagAEABWjOA5yAAAAAEHBhc3AAAAABAAAAAQAAABRidHJ0AAAAAAAACsAAAAAAAAAAGHN0dHMAAAAAAAAAAQAAAB4AAAQAAAAAFHN0c3MAAAAAAAAAAQAAAAEAAAAcc3RzYwAAAAAAAAABAAAAAQAAAB4AAAABAAAAjHN0c3oAAAAAAAAAAAAAAB4AAAKcAAAAVAAAAAoAAAAKAAAACgAAAAoAAAAKAAAACgAAAAoAAAAKAAAACgAAAAoAAAAKAAAACgAAAAoAAAAKAAAACgAAAAoAAAAKAAAACgAAAAoAAAAKAAAACgAAAAoAAAAKAAAACgAAAAoAAAAKAAAACgAAAAoAAAAUc3RjbwAAAAAAAAABAAADzAAAAGF1ZHRhAAAAWW1ldGEAAAAAAAAAIWhkbHIAAAAAAAAAAG1kaXJhcHBsAAAAAAAAAAAAAAAALGlsc3QAAAAkqXRvbwAAABxkYXRhAAAAAQAAAABMYXZmNjEuNy4xMDAAAAAIZnJlZQAABBBtZGF0AAACVAYF//9Q3EXpvebZSLeWLNgg2SPu73gyNjQgLSBjb3JlIDE2NCByMzEwOCAzMWUxOWY5IC0gSC4yNjQvTVBFRy00IEFWQyBjb2RlYyAtIENvcHlsZWZ0IDIwMDMtMjAyMyAtIGh0dHA6Ly93d3cudmlkZW9sYW4ub3JnL3gyNjQuaHRtbCAtIG9wdGlvbnM6IGNhYmFjPTAgcmVmPTEgZGVibG9jaz0wOjA6MCBhbmFseXNlPTA6MCBtZT1kaWEgc3VibWU9MCBwc3k9MSBwc3lfcmQ9MS4wMDowLjAwIG1peGVkX3JlZj0wIG1lX3JhbmdlPTE2IGNocm9tYV9tZT0xIHRyZWxsaXM9MCA4eDhkY3Q9MCBjcW09MCBkZWFkem9uZT0yMSwxMSBmYXN0X3Bza2lwPTEgY2hyb21hX3FwX29mZnNldD0wIHRocmVhZHM9MyBsb29rYWhlYWRfdGhyZWFkcz0xIHNsaWNlZF90aHJlYWRzPTAgbnI9MCBkZWNpbWF0ZT0xIGludGVybGFjZWQ9MCBibHVyYXlfY29tcGF0PTAgY29uc3RyYWluZWRfaW50cmE9MCBiZnJhbWVzPTAgd2VpZ2h0cD0wIGtleWludD0yNTAga2V5aW50X21pbj0xMCBzY2VuZWN1dD0wIGludHJhX3JlZnJlc2g9MCByYz1jcmYgbWJ0cmVlPTAgY3JmPTQwLjAgcWNvbXA9MC42MCBxcG1pbj0wIHFwbWF4PTY5IHFwc3RlcD00IGlwX3JhdGlvPTEuNDAgYXE9MACAAAAAQGWIhDoRigACADHAAIxwABAWk5OTk5OTk5OTrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrwAAABQQZogPr17699e+vfXvr317699e+vYv9nXOudc651zrnXOudc/n8/n8/n8/n8/n8/n8/n8/n8/n8/n8/n8/n8/n8/n8/n8/n8/n8/n8AAAAGQZpAPoHsAAAABkGaYD6B7AAAAAZBmoAQoHsAAAAGQZqgEKB7AAAABkGawBCgewAAAAZBmuAQoHsAAAAGQZsAEKB7AAAABkGbIBCgewAAAAZBm0AQoHsAAAABkGbgBCgewAAAAZBm6AQoHsAAAABkGZvAEKB7AAAABkGb4BCgewAAAAZBmgAQoHsAAAAGQZogEKB7AAAABkGaQBCgewAAAAZBmmAQoHsAAAAGQZqAEKB7AAAABkGaoBCgewAAAAZBmsAQoHsAAAAGQZrgEKB7AAAABkGbABCgewAAAAZBmyAQoHsAAAAGQZtAEKB7AAAABkGbYBCgewAAAAZBm4AQoHsAAAAGQZugEKB7';

fs.mkdirSync(outputDir, { recursive: true });
const proofVideoPath = path.join(outputDir, 'runtime-proof-video.mp4');
fs.writeFileSync(proofVideoPath, Buffer.from(proofVideoBase64.replace(/\s+/g, ''), 'base64'));
assert.ok(fs.statSync(proofVideoPath).size > 0, 'Embedded proof video is empty');

async function installProofVideoRoute(context) {
  await context.route('**/media/cc0-videos/flower.mp4', async (route) => {
    await route.fulfill({ status: 200, contentType: 'video/mp4', path: proofVideoPath });
  });
}

async function waitForEnhancedHero(page) {
  await page.locator('[data-hero-slider]').waitFor({ state: 'attached' });
  await page.waitForFunction(() => document.querySelector('[data-hero-slider]')?.dataset.enhanced === 'true');
}

async function waitForWebglReady(page) {
  await page.waitForFunction(() => {
    const state = document.querySelector('[data-hero-slider]')?.dataset.heroWebgl;
    return state === 'ready' || state === 'failed';
  }, null, { timeout: 5000 });
  assert.equal(await page.locator('[data-hero-slider]').getAttribute('data-hero-webgl'), 'ready');
}

async function waitForWebglStart(page) {
  await page.waitForFunction(() => document.querySelector('[data-hero-slider]')?.dataset.heroWebglActive === 'true');
}

async function waitForWebglSettlement(page) {
  await page.waitForFunction(() => {
    const hero = document.querySelector('[data-hero-slider]');
    return hero?.dataset.heroWebglActive === 'false' && !document.querySelector('.hero-cinema__webgl') && !document.querySelector('.is-entering, .is-leaving');
  }, null, { timeout: 4500 });
}

function viewportHeight(width) { return width <= 390 ? 844 : 900; }

async function selectLocale(page, locale) {
  if (locale === 'id') return;
  const form = page.locator(`form[action*="/bahasa/${locale}"]`).first();
  await form.waitFor({ state: 'attached' });
  await Promise.all([page.waitForNavigation({ waitUntil: 'domcontentloaded' }), form.evaluate((element) => element.submit())]);
  await waitForEnhancedHero(page);
}

async function inspectViewport(page, locale, width) {
  await page.setViewportSize({ width, height: viewportHeight(width) });
  await page.waitForTimeout(120);
  const metrics = await page.evaluate(() => {
    const active = document.querySelector('[data-hero-slide].is-active');
    const copy = active?.querySelector('.hero-cinema__copy')?.getBoundingClientRect();
    const title = active?.querySelector('.hero-cinema__title')?.getBoundingClientRect();
    const navbar = document.getElementById('navbar')?.getBoundingClientRect();
    const hamburger = document.getElementById('hamburgerBtn');
    const desktop = document.getElementById('desktopNavMenu');
    const style = (node) => node ? getComputedStyle(node) : null;
    const visibleArrows = Array.from(document.querySelectorAll('.hero-cinema__arrow')).filter((arrow) => {
      const computed = style(arrow); return computed?.display !== 'none' && computed?.visibility !== 'hidden';
    });
    const overlaps = copy ? visibleArrows.map((arrow) => arrow.getBoundingClientRect()).some((rect) => !(copy.right <= rect.left || rect.right <= copy.left || copy.bottom <= rect.top || rect.bottom <= copy.top)) : false;
    const hamburgerStyle = style(hamburger); const desktopStyle = style(desktop);
    return {
      overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
      overlaps,
      activeSlides: document.querySelectorAll('[data-hero-slide].is-active').length,
      transientSlides: document.querySelectorAll('.is-entering, .is-leaving').length,
      webglCanvases: document.querySelectorAll('.hero-cinema__webgl').length,
      arrowButtons: visibleArrows.length,
      rejectedControls: document.querySelectorAll('[data-hero-controls], [data-hero-dot], [data-hero-playback]').length,
      titleInsideViewport: Boolean(title && title.left >= -1 && title.right <= innerWidth + 1),
      copyBelowNavbar: Boolean(copy && navbar && copy.top >= navbar.bottom - 1),
      hamburgerVisible: Boolean(hamburgerStyle && hamburgerStyle.display !== 'none' && hamburgerStyle.visibility !== 'hidden'),
      desktopVisible: Boolean(desktopStyle && desktopStyle.display !== 'none' && desktopStyle.visibility !== 'hidden' && desktopStyle.opacity !== '0'),
    };
  });
  assert.ok(metrics.overflow <= 1, `${locale} ${width}: overflow`);
  assert.equal(metrics.overlaps, false, `${locale} ${width}: overlap`);
  assert.equal(metrics.activeSlides, 1); assert.equal(metrics.transientSlides, 0); assert.equal(metrics.webglCanvases, 0);
  assert.equal(metrics.arrowButtons, 2); assert.equal(metrics.rejectedControls, 0);
  assert.equal(metrics.titleInsideViewport, true); assert.equal(metrics.copyBelowNavbar, true);
  assert.equal(metrics.hamburgerVisible, width <= 1180); assert.equal(metrics.desktopVisible, width >= 1181);
  const screenshot = `${locale}-${width}x${viewportHeight(width)}.png`;
  await page.screenshot({ path: path.join(outputDir, screenshot), fullPage: false });
  report.matrix.push({ locale, width, ...metrics, screenshot });
}

async function proveLocaleMatrix(browser, locale) {
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await page.goto(baseURL, { waitUntil: 'domcontentloaded' }); await waitForEnhancedHero(page); await selectLocale(page, locale.code);
  assert.equal(await page.locator('html').getAttribute('lang'), locale.code); assert.equal(await page.locator('html').getAttribute('dir'), locale.dir);
  for (const width of widths) await inspectViewport(page, locale.code, width);
  await context.close();
}

async function activeIndex(page) { return page.locator('[data-hero-slide].is-active').getAttribute('data-slide-index'); }

async function goToSlideIndex(page, targetIndex) {
  const total = await page.locator('[data-hero-slide]').count();
  for (let step = 0; step < total; step += 1) {
    if (await activeIndex(page) === String(targetIndex)) return;
    await page.locator('[data-hero-next]').click(); await waitForWebglStart(page); await waitForWebglSettlement(page);
  }
  assert.fail(`Could not reach slide ${targetIndex}`);
}

async function provePhysicalDirections(page) {
  await waitForWebglReady(page); const hero = page.locator('[data-hero-slider]'); const initial = await activeIndex(page);
  await page.locator('[data-hero-next]').click(); await waitForWebglStart(page);
  assert.equal(await hero.getAttribute('data-hero-webgl-direction'), 'right-to-left'); assert.equal(await page.locator('.hero-cinema__webgl.is-active').count(), 1);
  assert.ok(await page.locator('.is-entering').count()); assert.ok(await page.locator('.is-leaving').count()); assert.notEqual(await activeIndex(page), initial);
  await waitForWebglSettlement(page);
  await page.locator('[data-hero-previous]').click(); await waitForWebglStart(page);
  assert.equal(await hero.getAttribute('data-hero-webgl-direction'), 'left-to-right');
  await page.waitForFunction((index) => document.querySelector('[data-hero-slide].is-active')?.dataset.slideIndex === index, initial);
  await waitForWebglSettlement(page);
}

async function proveLiveVideoWipe(browser) {
  const context = await browser.newContext({ viewport: { width: 1280, height: 900 } }); await installProofVideoRoute(context);
  const page = await context.newPage(); await page.goto(baseURL, { waitUntil: 'domcontentloaded' }); await waitForEnhancedHero(page); await waitForWebglReady(page);
  const videoIndex = await page.evaluate(() => Array.from(document.querySelectorAll('[data-hero-slide]')).findIndex((slide) => slide.querySelector('[data-hero-video]')));
  assert.ok(videoIndex >= 0); const total = await page.locator('[data-hero-slide]').count(); await goToSlideIndex(page, (videoIndex - 1 + total) % total);
  const video = page.locator(`[data-hero-slide][data-slide-index="${videoIndex}"] [data-hero-video]`);
  await page.locator('[data-hero-next]').click(); await waitForWebglStart(page);
  await page.waitForFunction(() => document.querySelector('[data-hero-slider]')?.dataset.heroWebglIncomingSource === 'video');
  await page.waitForFunction((index) => {
    const hero = document.querySelector('[data-hero-slider]');
    const node = document.querySelector(`[data-hero-slide][data-slide-index="${index}"] [data-hero-video]`);
    return hero?.dataset.heroWebglActive === 'true' && node && !node.paused && node.readyState >= 2 && node.currentTime > 0;
  }, String(videoIndex), { timeout: 1800 });
  const firstTime = await video.evaluate((node) => node.currentTime); await page.waitForTimeout(260); const secondTime = await video.evaluate((node) => node.currentTime);
  assert.equal(await video.evaluate((node) => node.paused), false); assert.ok(secondTime > firstTime + 0.08);
  await waitForWebglSettlement(page); assert.ok(await video.evaluate((node) => node.currentTime) >= secondTime);
  report.interactions.liveVideoWipe = 'PASS'; await context.close();
}

async function proveInteractions(browser) {
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } }); const page = await context.newPage();
  await page.goto(baseURL, { waitUntil: 'domcontentloaded' }); await waitForEnhancedHero(page); await provePhysicalDirections(page);
  for (let index = 0; index < 3; index += 1) { await page.locator('[data-hero-next]').click(); await waitForWebglStart(page); await waitForWebglSettlement(page); }
  assert.equal(await page.locator('[data-hero-slide].is-active').count(), 1); assert.equal(await page.locator('.hero-cinema__webgl').count(), 0);
  await page.evaluate(() => { Object.defineProperty(document, 'hidden', { configurable: true, get: () => true }); document.dispatchEvent(new Event('visibilitychange')); });
  assert.equal(await page.locator('[data-hero-video]').first().evaluate((video) => video.paused), true);
  await page.evaluate(() => document.querySelector('[data-hero-video]')?.dispatchEvent(new Event('error'))); assert.ok(await page.locator('[data-hero-slide].has-media-error').count());
  await page.evaluate(() => { Object.defineProperty(document, 'hidden', { configurable: true, get: () => false }); document.dispatchEvent(new Event('visibilitychange')); });
  const imageIndex = await page.evaluate(() => Array.from(document.querySelectorAll('[data-hero-slide]')).findIndex((slide) => !slide.querySelector('[data-hero-video]')));
  await goToSlideIndex(page, imageIndex); const activeImage = page.locator('[data-hero-slide].is-active [data-hero-image]');
  await activeImage.evaluate((image) => image.dispatchEvent(new Event('error'))); await activeImage.evaluate((image) => image.dispatchEvent(new Event('error')));
  assert.ok(await page.locator('[data-hero-slide].is-active.has-media-error').count());
  await page.goto(`${baseURL}/ppdb`, { waitUntil: 'domcontentloaded' }); await page.goBack({ waitUntil: 'domcontentloaded' }); await waitForEnhancedHero(page);
  report.interactions.standard = 'PASS'; report.interactions.physicalDirections = 'PASS'; await context.close();
  const reduced = await browser.newContext({ viewport: { width: 390, height: 844 }, reducedMotion: 'reduce' }); const reducedPage = await reduced.newPage();
  await reducedPage.goto(baseURL, { waitUntil: 'domcontentloaded' }); await waitForEnhancedHero(reducedPage); await reducedPage.locator('[data-hero-next]').click(); await reducedPage.waitForTimeout(60);
  assert.equal(await reducedPage.locator('.is-entering, .is-leaving').count(), 0); assert.equal(await reducedPage.locator('.hero-cinema__webgl').count(), 0); report.interactions.reducedMotion = 'PASS'; await reduced.close();
  const noScript = await browser.newContext({ viewport: { width: 390, height: 844 }, javaScriptEnabled: false }); const noScriptPage = await noScript.newPage();
  await noScriptPage.goto(baseURL, { waitUntil: 'domcontentloaded' }); assert.equal(await noScriptPage.locator('[data-hero-slide].is-active').count(), 1);
  assert.equal(await noScriptPage.locator('.hero-cinema__arrow').evaluateAll((arrows) => arrows.every((arrow) => getComputedStyle(arrow).display === 'none')), true);
  report.interactions.noJavaScript = 'PASS'; await noScript.close();
}

async function waitForAutomaticChange(page, initialIndex, timeout = 10000) {
  await page.waitForFunction((current) => {
    const hero = document.querySelector('[data-hero-slider]'); const active = document.querySelector('[data-hero-slide].is-active');
    return active?.dataset.slideIndex !== current && hero?.dataset.heroWebglActive === 'true';
  }, initialIndex, { timeout });
}

async function assertAutomaticDirection(page, locale, expectedDirection, origin) {
  assert.equal(await page.locator('[data-hero-slider]').getAttribute('data-hero-webgl-direction'), expectedDirection);
  await waitForWebglSettlement(page); report.interactions[`${origin}-${locale}`] = 'PASS';
}

async function proveAutomaticDirection(browser, locale, expectedDirection) {
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } }); const page = await context.newPage();
  await page.goto(baseURL, { waitUntil: 'domcontentloaded' }); await waitForEnhancedHero(page); await selectLocale(page, locale); await waitForWebglReady(page);
  const activeVideo = page.locator('[data-hero-slide].is-active [data-hero-video]');
  if (await activeVideo.count()) { const initialVideoIndex = await activeIndex(page); await activeVideo.dispatchEvent('ended'); await waitForAutomaticChange(page, initialVideoIndex); await assertAutomaticDirection(page, locale, expectedDirection, 'video-ended'); }
  const imageIndex = await page.evaluate(() => Array.from(document.querySelectorAll('[data-hero-slide]')).findIndex((slide) => !slide.querySelector('[data-hero-video]')));
  await goToSlideIndex(page, imageIndex); const timerIndex = await activeIndex(page); await waitForAutomaticChange(page, timerIndex); await assertAutomaticDirection(page, locale, expectedDirection, 'timer'); await context.close();
}

const browser = await chromium.launch({ executablePath: chromePath, headless: true, args: ['--no-sandbox'] });
try {
  for (const locale of locales) await proveLocaleMatrix(browser, locale);
  await proveInteractions(browser); await proveLiveVideoWipe(browser);
  await proveAutomaticDirection(browser, 'id', 'right-to-left'); await proveAutomaticDirection(browser, 'ar', 'left-to-right');
} finally { await browser.close(); }

fs.writeFileSync(path.join(outputDir, 'runtime-report.json'), `${JSON.stringify(report, null, 2)}\n`);
console.log(JSON.stringify(report, null, 2));
