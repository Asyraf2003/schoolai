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

fs.mkdirSync(outputDir, { recursive: true });

async function waitForEnhancedHero(page) {
  await page.locator('[data-hero-slider]').waitFor({ state: 'attached' });
  await page.waitForFunction(() => document.querySelector('[data-hero-slider]')?.dataset.enhanced === 'true');
}

async function waitForWebglReady(page) {
  await page.waitForFunction(() => {
    const state = document.querySelector('[data-hero-slider]')?.dataset.heroWebgl;
    return state === 'ready' || state === 'failed';
  }, null, { timeout: 5000 });
  assert.equal(
    await page.locator('[data-hero-slider]').getAttribute('data-hero-webgl'),
    'ready',
    'Hero WebGL renderer did not warm successfully'
  );
}

function viewportHeight(width) {
  if (width <= 390) return 844;
  return 900;
}

async function selectLocale(page, locale) {
  if (locale === 'id') return;
  const form = page.locator(`form[action*="/bahasa/${locale}"]`).first();
  await form.waitFor({ state: 'attached' });
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    form.evaluate((element) => element.submit()),
  ]);
  await waitForEnhancedHero(page);
}

async function inspectViewport(page, locale, width) {
  await page.setViewportSize({ width, height: viewportHeight(width) });
  await page.waitForTimeout(120);

  const metrics = await page.evaluate(() => {
    const active = document.querySelector('[data-hero-slide].is-active');
    const copy = active?.querySelector('.hero-cinema__copy')?.getBoundingClientRect();
    const controls = document.querySelector('[data-hero-controls]')?.getBoundingClientRect();
    const title = active?.querySelector('.hero-cinema__title')?.getBoundingClientRect();
    const navbar = document.getElementById('navbar')?.getBoundingClientRect();
    const hamburger = document.getElementById('hamburgerBtn');
    const desktop = document.getElementById('desktopNavMenu');
    const style = (node) => node ? getComputedStyle(node) : null;
    const hamburgerStyle = style(hamburger);
    const desktopStyle = style(desktop);
    const overlaps = copy && controls
      ? !(copy.right <= controls.left || controls.right <= copy.left || copy.bottom <= controls.top || controls.bottom <= copy.top)
      : false;

    return {
      overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
      overlaps,
      activeSlides: document.querySelectorAll('[data-hero-slide].is-active').length,
      transientSlides: document.querySelectorAll('.is-entering, .is-leaving').length,
      webglCanvases: document.querySelectorAll('.hero-cinema__webgl').length,
      titleInsideViewport: Boolean(title && title.left >= -1 && title.right <= innerWidth + 1),
      copyBelowNavbar: Boolean(copy && navbar && copy.top >= navbar.bottom - 1),
      hamburgerVisible: Boolean(hamburgerStyle && hamburgerStyle.display !== 'none' && hamburgerStyle.visibility !== 'hidden'),
      desktopVisible: Boolean(desktopStyle && desktopStyle.display !== 'none' && desktopStyle.visibility !== 'hidden' && desktopStyle.opacity !== '0'),
    };
  });

  assert.ok(metrics.overflow <= 1, `${locale} ${width}: horizontal overflow ${metrics.overflow}px`);
  assert.equal(metrics.overlaps, false, `${locale} ${width}: Hero copy overlaps controls`);
  assert.equal(metrics.activeSlides, 1, `${locale} ${width}: active slide count`);
  assert.equal(metrics.transientSlides, 0, `${locale} ${width}: stale transition classes`);
  assert.equal(metrics.webglCanvases, 0, `${locale} ${width}: stale WebGL canvas`);
  assert.equal(metrics.titleInsideViewport, true, `${locale} ${width}: title clips horizontally`);
  assert.equal(metrics.copyBelowNavbar, true, `${locale} ${width}: copy collides with navbar`);
  assert.equal(metrics.hamburgerVisible, width <= 1180, `${locale} ${width}: hamburger boundary`);
  assert.equal(metrics.desktopVisible, width >= 1181, `${locale} ${width}: desktop navigation boundary`);

  const screenshot = `${locale}-${width}x${viewportHeight(width)}.png`;
  await page.screenshot({ path: path.join(outputDir, screenshot), fullPage: false });
  report.matrix.push({ locale, width, ...metrics, screenshot });
}

async function proveLocaleMatrix(browser, locale) {
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await page.goto(baseURL, { waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(page);
  await selectLocale(page, locale.code);

  assert.equal(await page.locator('html').getAttribute('lang'), locale.code, `${locale.code}: html lang`);
  assert.equal(await page.locator('html').getAttribute('dir'), locale.dir, `${locale.code}: html dir`);

  for (const width of widths) await inspectViewport(page, locale.code, width);
  await context.close();
}

async function provePhysicalDirections(page) {
  await waitForWebglReady(page);
  const hero = page.locator('[data-hero-slider]');
  const activeIndex = async () => page.locator('[data-hero-slide].is-active').getAttribute('data-slide-index');
  const initial = await activeIndex();

  await page.locator('[data-hero-next]').click();
  await page.waitForFunction(() => document.querySelector('[data-hero-slider]')?.dataset.heroWebglActive === 'true');
  assert.equal(await hero.getAttribute('data-hero-webgl-direction'), 'right-to-left', 'right arrow direction');
  assert.equal(await page.locator('.hero-cinema__webgl.is-active').count(), 1, 'right arrow WebGL canvas');
  assert.ok(await page.locator('.is-entering').count(), 'transition entering state missing');
  assert.ok(await page.locator('.is-leaving').count(), 'transition leaving state missing');
  await page.waitForTimeout(1450);
  assert.notEqual(await activeIndex(), initial, 'right arrow did not change slide');
  assert.equal(await page.locator('.is-entering, .is-leaving').count(), 0, 'right transition did not settle');
  assert.equal(await page.locator('.hero-cinema__webgl').count(), 0, 'right transition canvas remained');

  await page.locator('[data-hero-previous]').click();
  await page.waitForFunction(() => document.querySelector('[data-hero-slider]')?.dataset.heroWebglActive === 'true');
  assert.equal(await hero.getAttribute('data-hero-webgl-direction'), 'left-to-right', 'left arrow direction');
  await page.waitForTimeout(1450);
  assert.equal(await activeIndex(), initial, 'left arrow did not restore slide');
}

async function proveInteractions(browser) {
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await page.goto(baseURL, { waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(page);
  await provePhysicalDirections(page);

  for (let index = 0; index < 3; index += 1) {
    await page.locator('[data-hero-next]').click();
    await page.waitForTimeout(1450);
  }
  assert.equal(await page.locator('[data-hero-slide].is-active').count(), 1, 'repeated changes lost single-active invariant');
  assert.equal(await page.locator('.hero-cinema__webgl').count(), 0, 'repeated changes left a canvas');

  await page.evaluate(() => {
    Object.defineProperty(document, 'hidden', { configurable: true, get: () => true });
    document.dispatchEvent(new Event('visibilitychange'));
  });
  assert.equal(await page.locator('[data-hero-video]').first().evaluate((video) => video.paused), true, 'visibility did not pause video');

  await page.evaluate(() => document.querySelector('[data-hero-video]')?.dispatchEvent(new Event('error')));
  assert.ok(await page.locator('[data-hero-slide].has-media-error').count(), 'video error fallback state missing');

  await page.locator('[data-hero-dot][data-slide-index="1"]').click();
  await page.waitForTimeout(1450);
  const activeImage = page.locator('[data-hero-slide].is-active [data-hero-image]');
  await activeImage.evaluate((image) => image.dispatchEvent(new Event('error')));
  await activeImage.evaluate((image) => image.dispatchEvent(new Event('error')));
  assert.ok(await page.locator('[data-hero-slide].is-active.has-media-error').count(), 'image error fallback state missing');

  await page.goto(`${baseURL}/ppdb`, { waitUntil: 'domcontentloaded' });
  await page.goBack({ waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(page);
  assert.equal(await page.locator('[data-hero-slider]').getAttribute('data-enhanced'), 'true', 'back navigation did not restore Hero');
  report.interactions.standard = 'PASS';
  report.interactions.physicalDirections = 'PASS';
  await context.close();

  const reduced = await browser.newContext({ viewport: { width: 390, height: 844 }, reducedMotion: 'reduce' });
  const reducedPage = await reduced.newPage();
  await reducedPage.goto(baseURL, { waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(reducedPage);
  assert.equal(await reducedPage.locator('[data-hero-playback]').isDisabled(), true, 'reduced motion playback must be disabled');
  await reducedPage.locator('[data-hero-next]').click();
  await reducedPage.waitForTimeout(60);
  assert.equal(await reducedPage.locator('.is-entering, .is-leaving').count(), 0, 'reduced motion still animates');
  assert.equal(await reducedPage.locator('.hero-cinema__webgl').count(), 0, 'reduced motion created WebGL canvas');
  report.interactions.reducedMotion = 'PASS';
  await reduced.close();

  const noScript = await browser.newContext({ viewport: { width: 390, height: 844 }, javaScriptEnabled: false });
  const noScriptPage = await noScript.newPage();
  await noScriptPage.goto(baseURL, { waitUntil: 'domcontentloaded' });
  assert.equal(await noScriptPage.locator('[data-hero-slide].is-active').count(), 1, 'no-JS first slide missing');
  assert.equal(await noScriptPage.locator('[data-hero-controls]').evaluate((node) => getComputedStyle(node).display), 'none', 'no-JS controls should stay hidden');
  await noScriptPage.screenshot({ path: path.join(outputDir, 'no-js-390x844.png') });
  report.interactions.noJavaScript = 'PASS';
  await noScript.close();
}

async function proveAutomaticDirection(browser, locale, expectedDirection) {
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await page.goto(baseURL, { waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(page);
  await selectLocale(page, locale);
  await waitForWebglReady(page);

  const initial = await page.locator('[data-hero-slide].is-active').getAttribute('data-slide-index');
  await page.waitForFunction((current) => {
    const hero = document.querySelector('[data-hero-slider]');
    const active = document.querySelector('[data-hero-slide].is-active');
    return active?.dataset.slideIndex !== current && hero?.dataset.heroWebglActive === 'true';
  }, initial, { timeout: 10000 });

  assert.equal(
    await page.locator('[data-hero-slider]').getAttribute('data-hero-webgl-direction'),
    expectedDirection,
    `${locale}: automatic transition direction`
  );
  await page.waitForTimeout(1450);
  assert.equal(await page.locator('.hero-cinema__webgl').count(), 0, `${locale}: automatic canvas remained`);
  report.interactions[`automatic-${locale}`] = 'PASS';
  await context.close();
}

const browser = await chromium.launch({ executablePath: chromePath, headless: true, args: ['--no-sandbox'] });
try {
  for (const locale of locales) await proveLocaleMatrix(browser, locale);
  await proveInteractions(browser);
  await proveAutomaticDirection(browser, 'id', 'right-to-left');
  await proveAutomaticDirection(browser, 'ar', 'left-to-right');
} finally {
  await browser.close();
}

fs.writeFileSync(path.join(outputDir, 'runtime-report.json'), `${JSON.stringify(report, null, 2)}\n`);
console.log(JSON.stringify(report, null, 2));
