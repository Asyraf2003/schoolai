import process from 'node:process';
import { chromium } from 'playwright';

const args = new Map();
for (let i = 2; i < process.argv.length; i += 1) {
  const token = process.argv[i];
  if (!token.startsWith('--')) continue;
  const [key, inlineValue] = token.slice(2).split('=');
  const next = process.argv[i + 1];
  const value = inlineValue ?? (next && !next.startsWith('--') ? process.argv[++i] : true);
  args.set(key, value);
}

const targetText = String(args.get('target') ?? 'Creative');
const width = Number(args.get('width') ?? 841);
const height = Number(args.get('height') ?? 878);
const delta = Number(args.get('delta') ?? 1200);
const frames = Number(args.get('frames') ?? 120);
const headed = !args.has('headless');
const url = String(args.get('url') ?? 'https://lusion.co/about/');

const browser = await chromium.launch({ headless: !headed });
const context = await browser.newContext({ viewport: { width, height }, deviceScaleFactor: 1 });
const page = await context.newPage();
await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 60_000 });
await page.waitForTimeout(1500);

const target = page.getByText(targetText, { exact: true }).first();
if (await target.count() === 0) throw new Error(`Target text not found: ${targetText}`);

await page.evaluate(() => {
  let nextId = 1;
  for (const element of document.querySelectorAll('*')) {
    if (!element.__schoolaiLayoutProbeId) element.__schoolaiLayoutProbeId = nextId++;
  }
  window.__schoolaiLayoutShifts = [];
  const observer = new PerformanceObserver((list) => {
    for (const entry of list.getEntries()) {
      window.__schoolaiLayoutShifts.push({
        value: entry.value,
        hadRecentInput: entry.hadRecentInput,
        sources: (entry.sources ?? []).map((source) => ({
          id: source.node?.__schoolaiLayoutProbeId ?? null,
          tag: source.node?.tagName ?? null,
          className: String(source.node?.className ?? ''),
          previousRect: source.previousRect,
          currentRect: source.currentRect,
        })),
      });
    }
  });
  observer.observe({ type: 'layout-shift', buffered: false });
  window.__schoolaiLayoutObserver = observer;
});

const snapshot = () => page.evaluate((text) => {
  const match = [...document.querySelectorAll('*')].find((element) => element.textContent?.trim() === text);
  const targetRect = match?.getBoundingClientRect() ?? null;
  const rows = [...document.querySelectorAll('*')].map((element) => {
    const rect = element.getBoundingClientRect();
    const style = getComputedStyle(element);
    let depth = 0;
    for (let node = element.parentElement; node; node = node.parentElement) depth += 1;
    return {
      id: element.__schoolaiLayoutProbeId ?? null,
      tag: element.tagName,
      className: String(element.className ?? ''),
      domId: element.id,
      depth,
      y: rect.y,
      height: rect.height,
      width: rect.width,
      display: style.display,
      position: style.position,
      overflowY: style.overflowY,
      cssHeight: style.height,
      minHeight: style.minHeight,
      maxHeight: style.maxHeight,
      marginTop: style.marginTop,
      marginBottom: style.marginBottom,
      paddingTop: style.paddingTop,
      paddingBottom: style.paddingBottom,
    };
  });
  return {
    targetRect,
    rows,
    viewport: {
      innerHeight: window.innerHeight,
      clientHeight: document.documentElement.clientHeight,
      visualHeight: window.visualViewport?.height ?? null,
      scrollY: window.scrollY,
      scrollHeight: document.documentElement.scrollHeight,
    },
  };
}, targetText);

const before = await snapshot();
await page.mouse.move(width / 2, height / 2);
await page.mouse.wheel(0, delta);
for (let frame = 0; frame < frames; frame += 1) {
  await page.evaluate(() => new Promise((resolve) => requestAnimationFrame(resolve)));
}
const after = await snapshot();
const shifts = await page.evaluate(() => window.__schoolaiLayoutShifts ?? []);

const afterById = new Map(after.rows.map((row) => [row.id, row]));
const changes = before.rows.map((start) => {
  const end = afterById.get(start.id);
  if (!end) return null;
  const heightDelta = end.height - start.height;
  const yDelta = end.y - start.y;
  if (Math.abs(heightDelta) < 0.5 && Math.abs(yDelta) < 0.5) return null;
  return { ...start, yAfter: end.y, heightAfter: end.height, yDelta, heightDelta };
}).filter(Boolean);

const targetY = before.targetRect?.y ?? Infinity;
const upstreamHeightOwners = changes
  .filter((row) => row.y <= targetY && Math.abs(row.heightDelta) >= 0.5 && Math.abs(row.yDelta) < 0.5)
  .sort((a, b) => b.depth - a.depth || Math.abs(b.heightDelta) - Math.abs(a.heightDelta))
  .slice(0, 30);

const largestHeightChanges = [...changes]
  .filter((row) => Math.abs(row.heightDelta) >= 0.5)
  .sort((a, b) => Math.abs(b.heightDelta) - Math.abs(a.heightDelta))
  .slice(0, 30);

console.log('TARGET_BEFORE', before.targetRect);
console.log('TARGET_AFTER', after.targetRect);
console.log('VIEWPORT_BEFORE', before.viewport);
console.log('VIEWPORT_AFTER', after.viewport);
console.log('UPSTREAM_HEIGHT_OWNERS');
console.table(upstreamHeightOwners.map(({ tag, className, domId, depth, y, height, heightAfter, heightDelta }) => ({
  tag, className, domId, depth, y, heightBefore: height, heightAfter, heightDelta,
})));
console.log('LARGEST_HEIGHT_CHANGES');
console.table(largestHeightChanges.map(({ tag, className, domId, depth, y, yDelta, height, heightAfter, heightDelta }) => ({
  tag, className, domId, depth, y, yDelta, heightBefore: height, heightAfter, heightDelta,
})));
console.log('LAYOUT_SHIFT_ENTRIES', shifts.length);
for (const [index, shift] of shifts.entries()) {
  console.log(`SHIFT=${index} VALUE=${shift.value} RECENT_INPUT=${shift.hadRecentInput}`);
  console.table(shift.sources);
}

await browser.close();
