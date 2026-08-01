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
  await page.waitForFunction(() =>
    document.querySelector('[data-hero-slider]')?.dataset.enhanced === 'true'
  );
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

async function waitForWebglStart(page) {
  await page.waitForFunction(() =>
    document.querySelector('[data-hero-slider]')?.dataset.heroWebglActive === 'true'
  );
}

async function waitForWebglSettlement(page) {
  await page.waitForFunction(() => {
    const hero = document.querySelector('[data-hero-slider]');
    return hero?.dataset.heroWebglActive === 'false' &&
      !document.querySelector('.hero-cinema__webgl') &&
      !document.querySelector('.is-entering, .is-leaving');
  }, null, { timeout: 4500 });
}

function viewportHeight(width) {
  return width <= 390 ? 844 : 900;
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
    const title = active?.querySelector('.hero-cinema__title')?.getBoundingClientRect();
    const navbar = document.getElementById('navbar')?.getBoundingClientRect();
    const hamburger = document.getElementById('hamburgerBtn');
    const desktop = document.getElementById('desktopNavMenu');
    const style = (node) => node ? getComputedStyle(node) : null;
    const visibleArrows = Array.from(document.querySelectorAll('.hero-cinema__arrow'))
      .filter((arrow) => {
        const computed = style(arrow);
        return computed?.display !== 'none' && computed?.visibility !== 'hidden';
      });
    const arrowRects = visibleArrows.map((arrow) => arrow.getBoundingClientRect());
    const overlaps = copy ? arrowRects.some((rect) => !(
      copy.right <= rect.left || rect.right <= copy.left ||
      copy.bottom <= rect.top || rect.bottom <= copy.top
    )) : false;
    const hamburgerStyle = style(hamburger);
    const desktopStyle = style(desktop);

    return {
      overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
      overlaps,
      activeSlides: document.querySelectorAll('[data-hero-slide].is-active').length,
      transientSlides: document.querySelectorAll('.is-entering, .is-leaving').length,
      webglCanvases: document.querySelectorAll('.hero-cinema__webgl').length,
      arrowButtons: visibleArrows.length,
      rejectedControls: document.querySelectorAll(
        '[data-hero-controls], [data-hero-dot], [data-hero-playback]'
      ).length,
      titleInsideViewport: Boolean(title && title.left >= -1 && title.right <= innerWidth + 1),
      copyBelowNavbar: Boolean(copy && navbar && copy.top >= navbar.bottom - 1),
      hamburgerVisible: Boolean(hamburgerStyle && hamburgerStyle.display !== 'none' && hamburgerStyle.visibility !== 'hidden'),
      desktopVisible: Boolean(desktopStyle && desktopStyle.display !== 'none' && desktopStyle.visibility !== 'hidden' && desktopStyle.opacity !== '0'),
    };
  });

  assert.ok(metrics.overflow <= 1, `${locale} ${width}: horizontal overflow ${metrics.overflow}px`);
  assert.equal(metrics.overlaps, false, `${locale} ${width}: Hero copy overlaps arrows`);
  assert.equal(metrics.activeSlides, 1, `${locale} ${width}: active slide count`);
  assert.equal(metrics.transientSlides, 0, `${locale} ${width}: stale transition classes`);
  assert.equal(metrics.webglCanvases, 0, `${locale} ${width}: stale WebGL canvas`);
  assert.equal(metrics.arrowButtons, 2, `${locale} ${width}: visible arrow count`);
  assert.equal(metrics.rejectedControls, 0, `${locale} ${width}: rejected transport UI returned`);
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

async function activeIndex(page) {
  return page.locator('[data-hero-slide].is-active').getAttribute('data-slide-index');
}

async function goToSlideIndex(page, targetIndex) {
  const total = await page.locator('[data-hero-slide]').count();
  for (let step = 0; step < total; step += 1) {
    if (await activeIndex(page) === String(targetIndex)) return;
    await page.locator('[data-hero-next]').click();
    await waitForWebglStart(page);
    await waitForWebglSettlement(page);
  }
  assert.fail(`Could not reach Hero slide ${targetIndex}`);
}

async function provePhysicalDirections(page) {
  await waitForWebglReady(page);
  const hero = page.locator('[data-hero-slider]');
  const initial = await activeIndex(page);

  await page.locator('[data-hero-next]').click();
  await waitForWebglStart(page);
  assert.equal(await hero.getAttribute('data-hero-webgl-direction'), 'right-to-left', 'right arrow direction');
  assert.equal(await page.locator('.hero-cinema__webgl.is-active').count(), 1, 'right arrow WebGL canvas');
  assert.ok(await page.locator('.is-entering').count(), 'transition entering state missing');
  assert.ok(await page.locator('.is-leaving').count(), 'transition leaving state missing');
  assert.notEqual(await activeIndex(page), initial, 'right arrow did not change slide immediately');
  await waitForWebglSettlement(page);

  await page.locator('[data-hero-previous]').click();
  await waitForWebglStart(page);
  assert.equal(await hero.getAttribute('data-hero-webgl-direction'), 'left-to-right', 'left arrow direction');
  await page.waitForFunction((index) =>
    document.querySelector('[data-hero-slide].is-active')?.dataset.slideIndex === index,
  initial);
  await waitForWebglSettlement(page);
}

async function proveLiveVideoWipe(browser) {
  const context = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await context.newPage();
  await page.goto(baseURL, { waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(page);
  await waitForWebglReady(page);

  const videoIndex = await page.evaluate(() =>
    Array.from(document.querySelectorAll('[data-hero-slide]'))
      .findIndex((slide) => slide.querySelector('[data-hero-video]'))
  );
  assert.ok(videoIndex >= 0, 'No native video slide exists for live texture proof');
  const total = await page.locator('[data-hero-slide]').count();
  const previousIndex = (videoIndex - 1 + total) % total;
  await goToSlideIndex(page, previousIndex);

  const video = page.locator(`[data-hero-slide][data-slide-index="${videoIndex}"] [data-hero-video]`);
  await page.locator('[data-hero-next]').click();
  await waitForWebglStart(page);
  await page.waitForFunction(() =>
    document.querySelector('[data-hero-slider]')?.dataset.heroWebglIncomingSource === 'video',
  null, { timeout: 1800 });

  const firstTime = await video.evaluate((node) => node.currentTime);
  await page.waitForTimeout(260);
  const secondTime = await video.evaluate((node) => node.currentTime);
  assert.equal(await video.evaluate((node) => node.paused), false, 'incoming video paused during wipe');
  assert.ok(secondTime > firstTime + 0.08, 'incoming video frame did not advance during wipe');

  await waitForWebglSettlement(page);
  const settledTime = await video.evaluate((node) => node.currentTime);
  assert.ok(settledTime >= secondTime, 'incoming video restarted or sought backward at settlement');
  report.interactions.liveVideoWipe = 'PASS';
  await context.close();
}

async function proveInteractions(browser) {
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await page.goto(baseURL, { waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(page);
  await provePhysicalDirections(page);

  for (let index = 0; index < 3; index += 1) {
    await page.locator('[data-hero-next]').click();
    await waitForWebglStart(page);
    await waitForWebglSettlement(page);
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

  await page.evaluate(() => {
    Object.defineProperty(document, 'hidden', { configurable: true, get: () => false });
    document.dispatchEvent(new Event('visibilitychange'));
  });
  const imageIndex = await page.evaluate(() =>
    Array.from(document.querySelectorAll('[data-hero-slide]'))
      .findIndex((slide) => !slide.querySelector('[data-hero-video]'))
  );
  assert.ok(imageIndex >= 0, 'No image slide exists for failure proof');
  await goToSlideIndex(page, imageIndex);
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
  const hiddenArrows = await noScriptPage.locator('.hero-cinema__arrow').evaluateAll((arrows) =>
    arrows.every((arrow) => getComputedStyle(arrow).display === 'none')
  );
  assert.equal(hiddenArrows, true, 'no-JS arrows should stay hidden');
  await noScriptPage.screenshot({ path: path.join(outputDir, 'no-js-390x844.png') });
  report.interactions.noJavaScript = 'PASS';
  await noScript.close();
}

async function waitForAutomaticChange(page, initialIndex, timeout = 10000) {
  await page.waitForFunction((current) => {
    const hero = document.querySelector('[data-hero-slider]');
    const active = document.querySelector('[data-hero-slide].is-active');
    return active?.dataset.slideIndex !== current && hero?.dataset.heroWebglActive === 'true';
  }, initialIndex, { timeout });
}

async function assertAutomaticDirection(page, locale, expectedDirection, origin) {
  assert.equal(
    await page.locator('[data-hero-slider]').getAttribute('data-hero-webgl-direction'),
    expectedDirection,
    `${locale}: ${origin} automatic transition direction`
  );
  await waitForWebglSettlement(page);
  report.interactions[`${origin}-${locale}`] = 'PASS';
}

async function proveAutomaticDirection(browser, locale, expectedDirection) {
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await page.goto(baseURL, { waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(page);
  await selectLocale(page, locale);
  await waitForWebglReady(page);

  const activeVideo = page.locator('[data-hero-slide].is-active [data-hero-video]');
  if (await activeVideo.count()) {
    const initialVideoIndex = await activeIndex(page);
    await activeVideo.dispatchEvent('ended');
    await waitForAutomaticChange(page, initialVideoIndex);
    await assertAutomaticDirection(page, locale, expectedDirection, 'video-ended');
  }

  const imageIndex = await page.evaluate(() =>
    Array.from(document.querySelectorAll('[data-hero-slide]'))
      .findIndex((slide) => !slide.querySelector('[data-hero-video]'))
  );
  assert.ok(imageIndex >= 0, `${locale}: no image slide available for timer proof`);
  await goToSlideIndex(page, imageIndex);

  const timerIndex = await activeIndex(page);
  await waitForAutomaticChange(page, timerIndex);
  await assertAutomaticDirection(page, locale, expectedDirection, 'timer');
  await context.close();
}

const browser = await chromium.launch({ executablePath: chromePath, headless: true, args: ['--no-sandbox'] });
try {
  for (const locale of locales) await proveLocaleMatrix(browser, locale);
  await proveInteractions(browser);
  await proveLiveVideoWipe(browser);
  await proveAutomaticDirection(browser, 'id', 'right-to-left');
  await proveAutomaticDirection(browser, 'ar', 'left-to-right');
} finally {
  await browser.close();
}

fs.writeFileSync(path.join(outputDir, 'runtime-report.json'), `${JSON.stringify(report, null, 2)}\n`);
console.log(JSON.stringify(report, null, 2));
