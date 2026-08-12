import fs from 'node:fs/promises';
import path from 'node:path';
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

const mode = args.has('discover') ? 'discover' : 'capture';
const url = String(args.get('url') ?? 'https://lusion.co/about/');
const targetText = String(args.get('target') ?? 'Creative');
const width = Number(args.get('width') ?? 841);
const height = Number(args.get('height') ?? 878);
const requestedAncestor = args.has('ancestor') ? Number(args.get('ancestor')) : null;
const sampleCount = Number(args.get('samples') ?? 180);
const wheelDelta = Number(args.get('delta') ?? 28);
const interruptAt = args.has('interrupt-at') ? Number(args.get('interrupt-at')) : null;
const headed = !args.has('headless');
const output = String(args.get('output') ?? `traces/${targetText.toLowerCase()}-${Date.now()}.json`);

function quadMetrics(flat) {
  if (!Array.isArray(flat) || flat.length !== 8) return null;
  const points = Array.from({ length: 4 }, (_, index) => ({
    x: flat[index * 2],
    y: flat[index * 2 + 1],
  }));
  const [tl, tr, br, bl] = points;
  const distance = (a, b) => Math.hypot(b.x - a.x, b.y - a.y);
  return {
    points,
    center: {
      x: points.reduce((sum, point) => sum + point.x, 0) / 4,
      y: points.reduce((sum, point) => sum + point.y, 0) / 4,
    },
    topAngleDeg: Math.atan2(tr.y - tl.y, tr.x - tl.x) * 180 / Math.PI,
    topWidth: distance(tl, tr),
    bottomWidth: distance(bl, br),
    leftHeight: distance(tl, bl),
    rightHeight: distance(tr, br),
  };
}

const browser = await chromium.launch({ headless: !headed });
const context = await browser.newContext({ viewport: { width, height }, deviceScaleFactor: 1 });
const page = await context.newPage();
const cdp = await context.newCDPSession(page);
await cdp.send('DOM.enable');
await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 60_000 });
await page.waitForTimeout(1500);

const matches = page.getByText(targetText, { exact: true });
if (await matches.count() === 0) {
  await browser.close();
  throw new Error(`Target text not found: ${targetText}`);
}

const textNode = matches.first();
await textNode.scrollIntoViewIfNeeded();
await page.waitForTimeout(400);

const candidates = await textNode.evaluate((node) => {
  const rows = [];
  let current = node;
  for (let level = 0; current && level < 10; level += 1, current = current.parentElement) {
    const style = getComputedStyle(current);
    const rect = current.getBoundingClientRect();
    rows.push({
      level,
      tag: current.tagName,
      className: String(current.className ?? ''),
      width: rect.width,
      height: rect.height,
      transform: style.transform,
      transformOrigin: style.transformOrigin,
      perspective: style.perspective,
      perspectiveOrigin: style.perspectiveOrigin,
      position: style.position,
    });
  }
  return rows;
});

if (mode === 'discover') {
  console.table(candidates);
  await browser.close();
  process.exit(0);
}

const autoAncestor = candidates.find((candidate) => (
  candidate.level > 0
  && candidate.transform !== 'none'
  && candidate.width > 120
  && candidate.height > 120
))?.level ?? 1;
const ancestor = Number.isFinite(requestedAncestor) ? requestedAncestor : autoAncestor;
const probeId = `schoolai-lusion-probe-${Date.now()}`;

await textNode.evaluate((node, data) => {
  let target = node;
  for (let index = 0; target && index < data.ancestor; index += 1) target = target.parentElement;
  if (!target) throw new Error('Requested ancestor does not exist');
  target.setAttribute('data-schoolai-motion-probe', data.probeId);
}, { ancestor, probeId });

const documentNode = await cdp.send('DOM.getDocument', { depth: 1, pierce: true });
const selected = await cdp.send('DOM.querySelector', {
  nodeId: documentNode.root.nodeId,
  selector: `[data-schoolai-motion-probe="${probeId}"]`,
});
if (!selected.nodeId) throw new Error('Unable to resolve selected probe node through CDP');

const setupDirection = wheelDelta >= 0 ? -1 : 1;
await page.evaluate(({ viewportHeight, direction }) => {
  window.scrollBy(0, direction * viewportHeight * 0.9);
}, { viewportHeight: height, direction: setupDirection });
await page.waitForTimeout(500);

const trace = [];
const inputs = [];
for (let frame = 0; frame < sampleCount; frame += 1) {
  if (frame % 3 === 0) {
    const eventTime = await page.evaluate(() => performance.now());
    const deltaY = Number.isFinite(interruptAt) && frame >= interruptAt ? -wheelDelta : wheelDelta;
    inputs.push({ t: eventTime, kind: 'wheel', deltaY });
    await page.mouse.wheel(0, deltaY);
  }
  await page.evaluate(() => new Promise((resolve) => requestAnimationFrame(() => resolve())));
  const [quadResult, state] = await Promise.all([
    cdp.send('DOM.getContentQuads', { nodeId: selected.nodeId }).catch(() => ({ quads: [] })),
    page.locator(`[data-schoolai-motion-probe="${probeId}"]`).evaluate((node) => {
      const rect = node.getBoundingClientRect();
      const style = getComputedStyle(node);
      const ancestors = [];
      let current = node.parentElement;
      for (let level = 1; current && level <= 6; level += 1, current = current.parentElement) {
        const parentStyle = getComputedStyle(current);
        ancestors.push({
          level,
          transform: parentStyle.transform,
          transformOrigin: parentStyle.transformOrigin,
          perspective: parentStyle.perspective,
          perspectiveOrigin: parentStyle.perspectiveOrigin,
          position: parentStyle.position,
        });
      }
      return {
        t: performance.now(),
        scrollY: window.scrollY,
        rect: { x: rect.x, y: rect.y, width: rect.width, height: rect.height },
        style: {
          transform: style.transform,
          transformOrigin: style.transformOrigin,
          perspective: style.perspective,
          perspectiveOrigin: style.perspectiveOrigin,
        },
        ancestors,
      };
    }),
  ]);
  trace.push({ frame, ...state, quad: quadMetrics(quadResult.quads?.[0]) });
}

const payload = {
  meta: {
    url,
    targetText,
    viewport: { width, height, deviceScaleFactor: 1 },
    ancestor,
    samples: sampleCount,
    wheelDelta,
    interruptAt,
    capturedAt: new Date().toISOString(),
  },
  candidates,
  inputs,
  trace,
};
await fs.mkdir(path.dirname(output), { recursive: true });
await fs.writeFile(output, `${JSON.stringify(payload, null, 2)}\n`, 'utf8');
console.log(`TRACE_SAVED=${output}`);
console.log(`SAMPLES=${trace.length}`);
console.log(`ANCESTOR=${ancestor}`);
await browser.close();
