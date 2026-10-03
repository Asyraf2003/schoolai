# Lusion Motion Probe

Status: RESEARCH ONLY

This probe measures the rendered motion of Lusion's Area of Expertise cards as a black box. It does not copy source code or assets and must never be imported by SchoolAI production code.

## Scope

- target URL: `https://lusion.co/about/`
- reference viewport: `841x878`
- primary target: one card label such as `Creative`
- output: local JSON traces under `traces/`
- production routes, Vite entries, Values motion, and root dependencies stay untouched

## Install

```bash
cd resources/labs/lusion-motion-probe
npm install
npx playwright install chromium
```

The lab pins Playwright locally. It does not modify the repository root package graph.

## 1. Discover the transform owner

```bash
npm run discover -- --target Creative --width 841 --height 878
```

The command prints the target text node and up to ten ancestors with geometry, transform, transform-origin, perspective, and position. The first large transformed ancestor is selected automatically during capture, but `--ancestor N` can override it when evidence shows a different owner.

## 2. Capture traces

Slow downward:

```bash
npm run capture -- --target Creative --delta 28 --samples 240 --output traces/creative-slow-down.json
```

Fast downward:

```bash
npm run capture -- --target Creative --delta 180 --samples 180 --output traces/creative-fast-down.json
```

Reverse:

```bash
npm run capture -- --target Creative --delta -28 --samples 240 --output traces/creative-reverse.json
```

Interrupted direction:

```bash
npm run capture -- --target Creative --delta 80 --interrupt-at 90 --samples 220 --output traces/creative-interrupt.json
```

Use `--headless` only after the headed run proves the target selection is correct.

## Recorded evidence

Each sampled frame records:

- `performance.now()` timestamp and `window.scrollY`;
- CDP content quad with all four rendered corners;
- derived center, top-edge angle, top/bottom width, and left/right height;
- bounding rectangle;
- computed transform and transform-origin;
- perspective and perspective-origin;
- up to six parent transform/perspective states;
- wheel input timestamps and deltas.

The trace is evidence of rendered geometry and timing, not proof of Lusion's private implementation source.

## Acceptance gate

Do not edit SchoolAI Values card motion from a single trace. First obtain slow, fast, reverse, and interruption traces for one card, verify the same trajectory on a second card, then derive the SchoolAI rail blueprint from measured evidence.
