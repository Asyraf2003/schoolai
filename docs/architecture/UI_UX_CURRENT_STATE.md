# UI/UX Engineering — Current State and Progress Ledger

Status: `FAIL`
Updated: 2026-08-09
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-VALUES-002-SPATIAL-LINES`
Source baseline: `196a2c02b606dba22db8e6bfbd838f00c6b7f152`
Source implementation head: uncommitted working tree
Active blueprint: `blueprints/2026-08-09-home-values-spatial-lines.md`

## Owner-accepted active goal

Create a Three.js pilot for three large solid white spatial lines behind the
existing Values DOM cards and connect Program's light-blue kinetic field to the
Values `#2038ff` field as one progressive scroll handoff.

## Accepted architecture

- Keep cards/content semantic DOM; canvas is decorative and behind them.
- Install Three.js by npm and use official Line2, LineGeometry, LineMaterial.
- Reuse the single Values scroll target, smoothing, observers, and RAF.
- Dynamically load one local renderer only when Values is near-active.
- Use three deterministic distinct curves with distinct phase/depth/ratio/offset.
- Use static CSS strokes for reduced motion, unsupported WebGL, and failure.
- Do not mirror the neutral scene merely because Arabic is RTL.
- Replace the losing SVG trail; do not add a duplicate visual/controller owner.

## Protected contract

- Program cards, detail, title, copy, and interaction remain unchanged.
- Values copy/cards remain unchanged on the first implementation step.
- Vision/Mission, Gallery, Articles, Navbar, About, and Testimonial remain
  unchanged.
- Existing unrelated local work remains untouched.

## Current FACT / GAP

- A fresh fetch proves local `main`, `origin/main`, and `FETCH_HEAD` remain
  `196a2c02...`.
- Three.js `0.185.1` is installed through npm and emitted only as a deferred
  Values scene chunk. The former Values SVG trail is removed.
- One Values controller still owns the scroll target, smoothing, observers,
  and RAF; the renderer bridge consumes that state without a second engine.
- Chromium proves the six required story states, deterministic reverse,
  reduced-motion fallback, inactive initial load, and stable offscreen render
  count. WebKitGTK proves desktop, mobile, RTL, reverse, and offscreen pause.
- Chromium width proof passes at 360, 390, 640, 768, 1024, 1180, 1181, 1280,
  1536, and 1920 pixels with one canvas and no horizontal overflow.
- `GAP-VALUES-SPATIAL-GATE-001`: the mandatory repository structure gate fails
  on three unchanged baseline findings. A clean `git archive HEAD` reproduction
  returns the same two unreachable Vision modules and Hero CSS checksum error.
- The full PHP suite has two unrelated failures: the existing Program source
  assertion rejects `dom.back`, and the user-modified analytics head produces a
  CSP assertion failure. The Values-focused suite passes.

## Proof state

Source implementation and browser proof for this bounded surface are complete.
Publication proof is not complete because mandatory repository-wide gates are
not green; no commit or push is permitted.

Required local proof:

```bash
git diff --check
git status --short
php artisan test --compact tests/Feature/HomeValuesStoryTest.php
php artisan test
npm run check:structure
npm run build
```

Runtime proof must cover the six owner-named desktop states, offscreen RAF and
context bounds, then tablet/mobile, AR RTL, reduced motion, and WebKit.

Proof results:

- `git diff --check`: PASS.
- `npm run build`: PASS; deferred `spatial-scene` is 548.96 kB minified and
  138.72 kB gzip.
- focused Values Pest: PASS, 2 tests and 124 assertions.
- Chromium visual/runtime: PASS for A-F, reverse, lifecycle, reduced motion,
  RTL, and all declared widths.
- WebKitGTK runtime: PASS for desktop/mobile/RTL/reverse/lifecycle.
- `npm run check:structure`: FAIL, reproduced unchanged on clean `HEAD`.
- full `php artisan test --compact`: FAIL, 201 of 203 tests pass; both failures
  are outside the Values patch.

STATUS: `FAIL`

NEXT EXECUTION CHANNEL: `Terminal Codex`

NEXT VALID STEP: obtain owner authorization for a separate baseline-gate repair
before changing Vision, Hero, Program, or analytics ownership.
