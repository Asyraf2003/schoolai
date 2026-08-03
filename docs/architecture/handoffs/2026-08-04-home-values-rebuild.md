# Homepage Values Runtime Correction Handoff

Date: 2026-08-04
Batch: `HOME-VALUES-001-R2`
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

No protected section, dependency, font, asset, route, database, or backend
owner was intentionally changed.

## Available source evidence

| Check | Result |
|---|---|
| GitHub HEAD/ref update | `PASS_SOURCE` at `c1c45381`; fast-forward, `force:false` |
| implementation scope | `PASS_SOURCE`; exactly 16 mapped Values files |
| Values JavaScript `node --check` | `PASS_LOCAL_PATCH` |
| Values CSS entry Lightning CSS bundle/parse | `PASS_LOCAL_PATCH` |
| ID/EN/AR lang + focused Pest source PHP parse | `PASS_LOCAL_PATCH`; syntax only, not Laravel execution |
| enforced Values source line count | `PASS_LOCAL_PATCH`; maximum 200 lines |
| six-tier ratio calculation | `PASS_LOCAL_PATCH` |
| flip/heading/exit numeric contract | `PASS_LOCAL_PATCH` |
| removed eyebrow/copy scan | `PASS_LOCAL_PATCH` |
| merge-conflict scan | `PASS_LOCAL_PATCH` |

The numeric contract recorded `34.6deg` prior-card turn at each following flip,
`-18deg` front overshoot, `0deg` final pose, responsive `rotateY` values of
`180 / 46.5 / 0deg` at `50 / 75 / 100%` visibility, heading mask starts of
`+108 / -108%`, and no final card-opacity property.

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

Expected HEAD: `c1c45381d7b1824ab72ca7d6d85fed77889e58ad`. Render that exact source and
collect owner feedback before promoting any runtime gate.

## Rollback

The source correction is isolated in `c1c45381d7b1824ab72ca7d6d85fed77889e58ad`.
Use a normal revert commit if rollback is required; do not force-push `main`.
