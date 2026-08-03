# Homepage Values Rebuild Handoff

Date: 2026-08-04
Batch: `HOME-VALUES-001`
Repository: `Asyraf2003/schoolai`
Branch: `main`
Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`

## Published commits

- `48830e93645dc0d5a681a89546f4dc8bc1941dab`
  `docs(values): accept full rebuild blueprint`
- `fbbc83b6672053652ac4551aea3970b825df0fcc`
  `feat(values): rebuild responsive card story`

## Result

The homepage Values section now uses one localized semantic DOM and CSS Grid as
the final layout source. Desktop motion forms a stage-measured deck and fan,
then overlaps four perspective flips into the real Grid slots. XS is one column;
SM/MD are 2x2; LG+ uses the cinematic four-column sequence. Entry/exit layers,
the exact locale headings/honorifics, reduced-motion fallback, and one continuous
SVG route are included.

No Hero, Vision/Mission, Programs content, Gallery, Articles, navigation,
footer, DB/admin/routes, About, Testimonial, dependency, font, or asset owner was
intentionally changed.

## Local source evidence

| Check | Result |
|---|---|
| `node --check` for changed Values JS | `PASS` |
| Lightning CSS parse for six Values modules | `PASS` |
| changed enforced-root file line count | `PASS` (maximum 200; `lang/` is outside checker roots) |
| GitHub HEAD after ref update | `PASS` at `fbbc83b...` |
| GitHub implementation commit scope | `PASS` (19 expected files) |

## Evidence still required

The execution channel had no complete checkout, PHP runtime, or browser. These
remain explicitly unproven:

```text
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeValuesStoryTest
php artisan test
```

Browser proof must cover Chromium and WebKit, ID/EN/AR, the six responsive tiers
and boundary pairs, forward/reverse/fast scroll, resize/orientation, reload near
the section, BFCache, reduced motion, 200% zoom, keyboard/touch, and short-height
viewports. Capture heading entrance, centered deck, fan, edge-on trapezoid,
overlapping flips, final Grid, completed route, and white exit.

Desktop acceptance also requires measured stage/card-union center deltas within
8px horizontal and 12px vertical, or 2% of the corresponding stage dimension.

## First continuation step

```bash
git pull --ff-only origin main
```

Run the proof commands only after confirming the pulled HEAD. Do not promote the
batch from `BLOCKED_BY_MISSING_EVIDENCE` until the missing evidence is recorded.

## Rollback

The source rebuild is isolated in `fbbc83b6672053652ac4551aea3970b825df0fcc`.
Use a normal revert commit if rollback is required; do not force-push `main`.
