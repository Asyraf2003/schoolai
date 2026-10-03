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

const url = String(args.get('url') ?? 'https://lusion.co/about/');
const targetText = String(args.get('target') ?? 'Creative');
const width = Number(args.get('width') ?? 841);
const height = Number(args.get('height') ?? 878);
const ancestor = Number(args.get('ancestor') ?? 3);
const delta = Number(args.get('delta') ?? 1200);
const frames = Number(args.get('frames') ?? 120);
const headed = !args.has('headless');

const browser = await chromium.launch({ headless: !headed });
const context = await browser.newContext({ viewport: { width, height }, deviceScaleFactor: 1 });
const page = await context.newPage();
await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 60_000 });
await page.waitForTimeout(1500);

const match = page.getByText(targetText, { exact: true }).first();
if (await match.count() === 0) throw new Error(`Target text not found: ${targetText}`);

const probeId = `schoolai-scroll-owner-${Date.now()}`;
await match.evaluate((node, data) => {
  let target = node;
  for (let index = 0; target && index < data.ancestor; index += 1) target = target.parentElement;
  if (!target) throw new Error('Requested ancestor does not exist');
  target.setAttribute('data-schoolai-scroll-owner', data.probeId);
}, { ancestor, probeId });

const selector = `[data-schoolai-scroll-owner="${probeId}"]`;
const sample = () => page.locator(selector).evaluate((node) => {
  const rect = node.getBoundingClientRect();
  const ancestors = [];
  let current = node;
  for (let level = 0; current && level < 12; level += 1, current = current.parentElement) {
    const style = getComputedStyle(current);
    const box = current.getBoundingClientRect();
    ancestors.push({
      level,
      tag: current.tagName,
      className: String(current.className ?? ''),
      y: box.y,
      height: box.height,
      transform: style.transform,
      position: style.position,
      overflowY: style.overflowY,
      scrollTop: current.scrollTop,
      scrollHeight: current.scrollHeight,
      clientHeight: current.clientHeight,
    });
  }

  const scrollables = [...document.querySelectorAll('*')]
    .filter((element) => element.scrollHeight > element.clientHeight + 2)
    .map((element) => {
      const style = getComputedStyle(element);
      const box = element.getBoundingClientRect();
      return {
        tag: element.tagName,
        className: String(element.className ?? ''),
        y: box.y,
        overflowY: style.overflowY,
        scrollTop: element.scrollTop,
        scrollHeight: element.scrollHeight,
        clientHeight: element.clientHeight,
        transform: style.transform,
      };
    })
    .filter((entry) => ['auto', 'scroll', 'hidden', 'clip'].includes(entry.overflowY))
    .slice(0, 20);

  return {
    t: performance.now(),
    windowScrollY: window.scrollY,
    documentScrollTop: document.scrollingElement?.scrollTop ?? null,
    bodyScrollTop: document.body.scrollTop,
    rect: { x: rect.x, y: rect.y, width: rect.width, height: rect.height },
    ancestors,
    scrollables,
  };
});

const before = await sample();
await page.mouse.move(width / 2, height / 2);
await page.mouse.wheel(0, delta);

const timeline = [];
for (let frame = 0; frame < frames; frame += 1) {
  await page.evaluate(() => new Promise((resolve) => requestAnimationFrame(resolve)));
  const state = await sample();
  timeline.push({ frame, ...state });
}
const after = timeline.at(-1);

const changedAncestors = before.ancestors.map((start, index) => {
  const end = after.ancestors[index];
  if (!end) return null;
  const yDelta = end.y - start.y;
  const scrollDelta = end.scrollTop - start.scrollTop;
  const transformChanged = start.transform !== end.transform;
  if (Math.abs(yDelta) < 0.1 && Math.abs(scrollDelta) < 0.1 && !transformChanged) return null;
  return {
    level: start.level,
    tag: start.tag,
    className: start.className,
    yBefore: start.y,
    yAfter: end.y,
    yDelta,
    scrollDelta,
    transformBefore: start.transform,
    transformAfter: end.transform,
  };
}).filter(Boolean);

const scrollableChanges = before.scrollables.map((start) => {
  const end = after.scrollables.find((candidate) => (
    candidate.tag === start.tag && candidate.className === start.className
  ));
  if (!end) return null;
  const scrollDelta = end.scrollTop - start.scrollTop;
  const yDelta = end.y - start.y;
  if (Math.abs(scrollDelta) < 0.1 && Math.abs(yDelta) < 0.1 && start.transform === end.transform) return null;
  return { ...start, scrollDelta, yDelta, transformAfter: end.transform };
}).filter(Boolean);

console.log('TARGET_RECT_BEFORE', before.rect);
console.log('TARGET_RECT_AFTER', after.rect);
console.log('TARGET_Y_DELTA', after.rect.y - before.rect.y);
console.log('WINDOW_SCROLL_RANGE', [before.windowScrollY, after.windowScrollY]);
console.log('DOCUMENT_SCROLL_RANGE', [before.documentScrollTop, after.documentScrollTop]);
console.log('CHANGED_ANCESTORS');
console.table(changedAncestors);
console.log('CHANGED_SCROLLABLES');
console.table(scrollableChanges);

const checkpoints = [0, 1, 2, 4, 8, 16, 32, 64, frames - 1]
  .filter((value, index, all) => value >= 0 && value < frames && all.indexOf(value) === index);
for (const index of checkpoints) {
  const state = timeline[index];
  console.log({
    frame: state.frame,
    targetY: state.rect.y,
    windowScrollY: state.windowScrollY,
    ancestorTransforms: state.ancestors
      .filter((entry) => entry.transform !== 'none')
      .map((entry) => ({ level: entry.level, transform: entry.transform, y: entry.y })),
  });
}

await browser.close();
