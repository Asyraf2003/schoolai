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

async function waitForTransitionSettlement(page) {
  await page.waitForFunction(() =>
    !document.querySelector('.is-entering, .is-leaving')
  , null, { timeout: 2500 });
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
    const arrows = Array.from(document.querySelectorAll('.hero-cinema__arrow'))
      .filter((arrow) => {
        const computed = style(arrow);
        return computed?.display !== 'none' && computed?.visibility !== 'hidden';
      });
    const overlaps = copy ? arrows
      .map((arrow) => arrow.getBoundingClientRect())
      .some((rect) => !(
        copy.right <= rect.left || rect.right <= copy.left ||
        copy.bottom <= rect.top || rect.bottom <= copy.top
      )) : false;
    const hamburgerStyle = style(hamburger);
    const desktopStyle = style(desktop);
    const hero = document.querySelector('[data-hero-slider]');

    return {
      overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
      overlaps,
      activeSlides: document.querySelectorAll('[data-hero-slide].is-active').length,
      transientSlides: document.querySelectorAll('.is-entering, .is-leaving').length,
      arrowButtons: arrows.length,
      rejectedControls: document.querySelectorAll(
        '[data-hero-controls], [data-hero-dot], [data-hero-playback]'
      ).length,
      canvasCount: document.querySelectorAll('.hero-cinema canvas').length,
      webglStatePresent: Boolean(hero && Array.from(hero.attributes)
        .some((attribute) => attribute.name.startsWith('data-hero-webgl'))),
      titleInsideViewport: Boolean(title && title.left >= -1 && title.right <= innerWidth + 1),
      copyBelowNavbar: Boolean(copy && navbar && copy.top >= navbar.bottom - 1),
      hamburgerVisible: Boolean(
        hamburgerStyle && hamburgerStyle.display !== 'none' &&
        hamburgerStyle.visibility !== 'hidden'
      ),
      desktopVisible: Boolean(
        desktopStyle && desktopStyle.display !== 'none' &&
        desktopStyle.visibility !== 'hidden' && desktopStyle.opacity !== '0'
      ),
    };
  });

  assert.ok(metrics.overflow <= 1, `${locale} ${width}: horizontal overflow`);
  assert.equal(metrics.overlaps, false, `${locale} ${width}: copy overlaps arrows`);
  assert.equal(metrics.activeSlides, 1, `${locale} ${width}: active slide count`);
  assert.equal(metrics.transientSlides, 0, `${locale} ${width}: stale transition classes`);
  assert.equal(metrics.arrowButtons, 2, `${locale} ${width}: visible arrow count`);
  assert.equal(metrics.rejectedControls, 0, `${locale} ${width}: rejected controls returned`);
  assert.equal(metrics.canvasCount, 0, `${locale} ${width}: Hero canvas returned`);
  assert.equal(metrics.webglStatePresent, false, `${locale} ${width}: WebGL state returned`);
  assert.equal(metrics.titleInsideViewport, true, `${locale} ${width}: title clipping`);
  assert.equal(metrics.copyBelowNavbar, true, `${locale} ${width}: navbar collision`);
  assert.equal(metrics.hamburgerVisible, width <= 1180, `${locale} ${width}: hamburger boundary`);
  assert.equal(metrics.desktopVisible, width >= 1181, `${locale} ${width}: desktop boundary`);

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
  assert.equal(await page.locator('html').getAttribute('lang'), locale.code);
  assert.equal(await page.locator('html').getAttribute('dir'), locale.dir);
  for (const width of widths) await inspectViewport(page, locale.code, width);
  await context.close();
}

async function activeIndex(page) {
  return page.locator('[data-hero-slide].is-active').getAttribute('data-slide-index');
}

async function goToSlide(page, targetIndex) {
  const total = await page.locator('[data-hero-slide]').count();
  for (let step = 0; step < total; step += 1) {
    if (await activeIndex(page) === String(targetIndex)) return;
    await page.locator('[data-hero-next]').click();
    await waitForTransitionSettlement(page);
  }
  assert.fail(`Could not reach Hero slide ${targetIndex}`);
}

async function proveInteractions(browser) {
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await page.goto(baseURL, { waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(page);

  const initial = await activeIndex(page);
  await page.locator('[data-hero-next]').focus();
  await page.keyboard.press('Enter');
  await page.waitForTimeout(60);
  assert.ok(await page.locator('.is-entering').count(), 'CSS entering state missing');
  assert.ok(await page.locator('.is-leaving').count(), 'CSS leaving state missing');
  assert.equal(await page.locator('.hero-cinema canvas').count(), 0, 'canvas appeared during CSS transition');
  await waitForTransitionSettlement(page);
  assert.notEqual(await activeIndex(page), initial, 'keyboard did not change slide');

  for (let index = 0; index < 4; index += 1) {
    await page.locator('[data-hero-next]').click();
    await waitForTransitionSettlement(page);
  }
  assert.equal(await page.locator('[data-hero-slide].is-active').count(), 1);
  assert.equal(await page.locator('.hero-cinema canvas').count(), 0);

  const imageIndex = await page.evaluate(() =>
    Array.from(document.querySelectorAll('[data-hero-slide]'))
      .findIndex((slide) => slide.querySelector('[data-hero-image]'))
  );
  assert.ok(imageIndex >= 0, 'No image Hero slide exists');
  await goToSlide(page, imageIndex);
  const activeImage = page.locator('[data-hero-slide].is-active [data-hero-image]');
  await activeImage.evaluate((image) => image.dispatchEvent(new Event('error')));
  await activeImage.evaluate((image) => image.dispatchEvent(new Event('error')));
  assert.ok(await page.locator('[data-hero-slide].is-active.has-media-error').count());

  await page.evaluate(() =>
    document.querySelector('[data-hero-video]')?.dispatchEvent(new Event('error'))
  );
  assert.ok(await page.locator('[data-hero-slide].has-media-error').count());

  await page.evaluate(() => {
    Object.defineProperty(document, 'hidden', { configurable: true, get: () => true });
    document.dispatchEvent(new Event('visibilitychange'));
  });
  assert.equal(
    await page.locator('[data-hero-video]').first().evaluate((video) => video.paused),
    true
  );

  await page.goto(`${baseURL}/ppdb`, { waitUntil: 'domcontentloaded' });
  await page.goBack({ waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(page);
  assert.equal(await page.locator('.hero-cinema canvas').count(), 0);
  report.interactions.standard = 'PASS';
  await context.close();

  const reduced = await browser.newContext({
    viewport: { width: 390, height: 844 },
    reducedMotion: 'reduce',
  });
  const reducedPage = await reduced.newPage();
  await reducedPage.goto(baseURL, { waitUntil: 'domcontentloaded' });
  await waitForEnhancedHero(reducedPage);
  await reducedPage.locator('[data-hero-next]').click();
  await reducedPage.waitForTimeout(60);
  assert.equal(await reducedPage.locator('.is-entering, .is-leaving').count(), 0);
  assert.equal(await reducedPage.locator('.hero-cinema canvas').count(), 0);
  report.interactions.reducedMotion = 'PASS';
  await reduced.close();

  const noScript = await browser.newContext({
    viewport: { width: 390, height: 844 },
    javaScriptEnabled: false,
  });
  const noScriptPage = await noScript.newPage();
  await noScriptPage.goto(baseURL, { waitUntil: 'domcontentloaded' });
  assert.equal(await noScriptPage.locator('[data-hero-slide].is-active').count(), 1);
  assert.equal(await noScriptPage.locator('[data-hero-slider]').getAttribute('data-enhanced'), null);
  assert.equal(await noScriptPage.locator('.hero-cinema canvas').count(), 0);
  await noScriptPage.screenshot({ path: path.join(outputDir, 'no-js-390x844.png') });
  report.interactions.noJavaScript = 'PASS';
  await noScript.close();
}

const browser = await chromium.launch({
  executablePath: chromePath,
  headless: true,
  args: ['--no-sandbox'],
});

try {
  for (const locale of locales) await proveLocaleMatrix(browser, locale);
  await proveInteractions(browser);
} finally {
  await browser.close();
}

fs.writeFileSync(
  path.join(outputDir, 'runtime-report.json'),
  `${JSON.stringify(report, null, 2)}\n`
);
console.log(JSON.stringify(report, null, 2));
