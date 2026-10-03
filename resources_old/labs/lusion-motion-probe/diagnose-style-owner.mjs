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

const probeId = `schoolai-style-owner-${Date.now()}`;
await match.evaluate((node, data) => {
  let target = node;
  for (let index = 0; target && index < data.ancestor; index += 1) target = target.parentElement;
  if (!target) throw new Error('Requested ancestor does not exist');
  target.setAttribute('data-schoolai-style-owner', data.probeId);
}, { ancestor, probeId });

const selector = `[data-schoolai-style-owner="${probeId}"]`;
const selectedProps = [
  'transform', 'translate', 'rotate', 'scale', 'top', 'right', 'bottom', 'left',
  'inset-block-start', 'inset-block-end', 'margin-top', 'margin-bottom',
  'position', 'will-change', 'transform-origin', 'perspective', 'perspective-origin',
];

const snapshot = (full = false) => page.locator(selector).evaluate((node, data) => {
  const rows = [];
  let current = node;
  for (let level = 0; current && level < 20; level += 1, current = current.parentElement) {
    const style = getComputedStyle(current);
    const rect = current.getBoundingClientRect();
    const picked = Object.fromEntries(data.selectedProps.map((name) => [name, style.getPropertyValue(name)]));
    const properties = {};
    if (data.full) {
      for (const name of style) properties[name] = style.getPropertyValue(name);
    }
    rows.push({
      level,
      tag: current.tagName,
      className: String(current.className ?? ''),
      rect: { x: rect.x, y: rect.y, width: rect.width, height: rect.height },
      inlineStyle: current.getAttribute('style') ?? '',
      picked,
      properties,
    });
  }
  return {
    t: performance.now(),
    windowScrollY: window.scrollY,
    documentScrollTop: document.scrollingElement?.scrollTop ?? null,
    rows,
  };
}, { full, selectedProps });

const before = await snapshot(true);
await page.mouse.move(width / 2, height / 2);
await page.mouse.wheel(0, delta);

const timeline = [];
for (let frame = 0; frame < frames; frame += 1) {
  await page.evaluate(() => new Promise((resolve) => requestAnimationFrame(resolve)));
  const state = await snapshot(false);
  timeline.push({ frame, ...state });
}
const after = await snapshot(true);

const diffs = [];
for (let index = 0; index < Math.min(before.rows.length, after.rows.length); index += 1) {
  const start = before.rows[index];
  const end = after.rows[index];
  const changed = [];
  const keys = new Set([...Object.keys(start.properties), ...Object.keys(end.properties)]);
  for (const key of keys) {
    const from = start.properties[key] ?? '';
    const to = end.properties[key] ?? '';
    if (from !== to) changed.push({ property: key, before: from, after: to });
  }
  const yDelta = end.rect.y - start.rect.y;
  const inlineChanged = start.inlineStyle !== end.inlineStyle;
  if (changed.length || Math.abs(yDelta) > 0.1 || inlineChanged) {
    diffs.push({
      level: start.level,
      tag: start.tag,
      className: start.className,
      yBefore: start.rect.y,
      yAfter: end.rect.y,
      yDelta,
      inlineBefore: start.inlineStyle,
      inlineAfter: end.inlineStyle,
      changed,
    });
  }
}

console.log('WINDOW_SCROLL', [before.windowScrollY, after.windowScrollY]);
console.log('DOCUMENT_SCROLL', [before.documentScrollTop, after.documentScrollTop]);
console.log('STYLE_OWNER_DIFFS');
for (const diff of diffs) {
  console.log(`LEVEL=${diff.level} TAG=${diff.tag} CLASS=${diff.className || '(none)'} Y_DELTA=${diff.yDelta}`);
  if (diff.inlineBefore !== diff.inlineAfter) {
    console.log('  INLINE_BEFORE', diff.inlineBefore);
    console.log('  INLINE_AFTER ', diff.inlineAfter);
  }
  for (const change of diff.changed) {
    console.log(`  ${change.property}: ${change.before} -> ${change.after}`);
  }
}

const checkpoints = [0, 1, 2, 4, 8, 16, 24, 32, 48, 64, 96, frames - 1]
  .filter((value, index, all) => value >= 0 && value < frames && all.indexOf(value) === index);
console.log('TIMELINE_SELECTED_PROPERTIES');
for (const index of checkpoints) {
  const state = timeline[index];
  console.log(`FRAME=${state.frame}`);
  for (const row of state.rows) {
    const active = Object.values(row.picked).some((value) => value && value !== 'none' && value !== 'auto' && value !== 'normal' && value !== '0px');
    if (active || Math.abs(row.rect.y - before.rows[row.level].rect.y) > 0.1) {
      console.log({ level: row.level, tag: row.tag, className: row.className, y: row.rect.y, ...row.picked });
    }
  }
}

await browser.close();
