# Homepage Values Runtime Correction Handoff

Date: 2026-08-04
Batch: `HOME-VALUES-001-R3`
Repository: `Asyraf2003/schoolai`
Branch: `main`
Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`

## Published commits

- `7b625935c555d379cb0a455bcb1164c682123856`
  `docs(values): accept runtime correction blueprint`
- `e56b00a455848772905def3c3ab63016dd24c303`
  `docs(values): classify tablet and desktop tiers`
- `c1c45381d7b1824ab72ca7d6d85fed77889e58ad`
  `fix(values): rebuild responsive card story motion`
- `0521c9aa1641c8d547099f8a7c758f943a32a0ae`
  `docs(values): accept focused runtime correction`
- `95b3b78f91d42d536881324eda6d453c7139a015`
  `fix(values): refine heading and card motion`

All three commits advanced `main` by non-force fast-forward. The failed runtime
baseline was `c24e4d73fd57659df9f18eeb732d3ec755031743`.

## Result

The Values surface keeps one localized semantic DOM and CSS Grid as the final
layout source. Its corrected six-tier contract is:

- XS/SM: one column, `8 / 84 / 8%`;
- MD/LG: two columns, `4.1667 / 43.75 / 4.1667 / 43.75 / 4.1667%`;
- XL/2XL: four columns, `5 / 21 / 2 / 21 / 2 / 21 / 2 / 21 / 5%`.

Only XL+ runs the cinematic opaque deck, fan, overlapping 3D flips, upright
Grid hold, completed trail, and intact sticky release. MD/LG remains a natural
2x2 tablet flow and XS/SM remains one column. Responsive cards begin flipping
at half-body visibility and settle at full-body visibility in one shared
direction.

The heading now reveals line one from below and line two from above. Line two
shifts inward only after reveal on tablet/desktop. Description is absent on
phones, below the title on portrait tablets, and at the logical side on
landscape tablets and desktops. The removed eyebrow and its three locale keys
no longer render.

Card geometry, DOM order, flip direction, and chronology are fixed LTR for both
document directions. Only card face content becomes RTL for Arabic; the header
continues to use logical RTL composition. Perspective is height-driven and the
browser's preserve-3d projection owns the natural near/far side asymmetry.

Cards no longer use entry or exit opacity. All four backs exist in the initial
deck, flips overlap when the preceding card has turned about `34.6deg`, the
front passes by `-18deg`, floating is stronger and phase-offset, and the final
row leaves upward intact with normal section movement.

R3 makes the desktop heading leave from the first scroll progress, starts both
title lines at logical inline-start before the second line shifts inline-end,
simplifies card fronts to readable content, replaces the back logo/orbit with an
Islamic geometric field, runs exactly three desktop bounce cycles before upward
exit, and uses a pure `180deg -> 0deg` responsive flip with card-relative type.

The live smoothness audit proved that Lusion uses native-scroll lock, a fixed UI,
and fixed canvases under one virtual-scroll pipeline. SchoolAI still uses native
scroll plus surface-local RAF controllers. That global difference is recorded
but intentionally not changed in R3.

No protected section, dependency, font, asset, route, database, backend, locale
copy, Vite entry, or global scroll owner was intentionally changed.

## Available source evidence

| Check | Result |
|---|---|
| GitHub HEAD/ref update | `PASS_SOURCE` at `95b3b78f`; fast-forward, `force:false` |
| R3 implementation scope | `PASS_SOURCE`; exactly 11 mapped Values/test files |
| Values JavaScript `node --check` | `PASS_LOCAL_PATCH` |
| Values CSS entry Lightning CSS bundle/parse | `PASS_LOCAL_PATCH` |
| ID/EN/AR lang + focused Pest source PHP parse | `PASS_LOCAL_PATCH`; syntax only, not Laravel execution |
| enforced Values source line count | `PASS_LOCAL_PATCH`; maximum 200 lines |
| six-tier ratio calculation | `PASS_LOCAL_PATCH` |
| flip/heading/exit numeric contract | `PASS_LOCAL_PATCH` |
| removed eyebrow/copy scan | `PASS_LOCAL_PATCH` |
| merge-conflict scan | `PASS_LOCAL_PATCH` |

The R3 numeric contract records responsive `180 / 90 / 0deg` at half-visible /
three-quarter-visible / fully-visible states, `34.59deg` retained desktop
inter-card overlap, exactly three alternating bounce cycles, heading Y travel
from the first positive progress, explicit full-row upward exit, and no
perpetual float keyframes.

## Evidence still required

This channel has no complete checkout, PHP binary, or local Chromium/WebKit
runtime. The following remain explicitly unproven:

```text
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeValuesStoryTest
php artisan test
```

Browser proof must cover Chromium and WebKit; ID/EN/AR; 360, 640, 768, 1024,
1280, and 1536 tiers plus boundary pairs; portrait/landscape tablet; normal,
reverse, and fast scroll; active resize; reload near section; BFCache; reduced
motion; 200% zoom; keyboard/touch; and short-height viewports.

Capture the heading reveal/settlement, opaque deck, fan, edge-on near/far
projection, inter-card overlap, front overshoot, final Grid, completed route,
intact upward exit, reverse re-entry, and both section seams. Desktop acceptance
still requires measured card-union/stage center deltas within 8px horizontal and
12px vertical, or 2% of the corresponding stage dimension.

## First continuation step

```bash
git pull --ff-only origin main
git rev-parse HEAD
```

Expected HEAD: `95b3b78f91d42d536881324eda6d453c7139a015`. Render that exact source and
collect owner feedback before promoting any runtime gate.

## Rollback

The focused R3 source correction is isolated in `95b3b78f91d42d536881324eda6d453c7139a015`.
Use a normal revert commit if rollback is required; do not force-push `main`.
