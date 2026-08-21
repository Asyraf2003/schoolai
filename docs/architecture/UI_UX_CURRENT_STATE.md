# UI/UX Engineering — Current State and Progress Ledger

Status: `BLOCKED_BY_MISSING_EVIDENCE / DESKTOP-HOMEPAGE-2026-08-21`
Updated: 2026-08-21
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Inspected source HEAD: `f8ae8150b5932c3d2a86d6607da990650de87028`

## Active owner-accepted blueprints

- `blueprints/2026-08-21-desktop-program-values-continuity.md`;
- `blueprints/2026-08-21-desktop-vision-background-foundation.md`;
- `blueprints/2026-08-21-desktop-gallery-heading-continuity.md`;
- `blueprints/2026-08-21-desktop-article-journey.md`.

The owner's 2026-08-21 desktop brief supersedes the former freeze on these
named surfaces only. Tablet/phone Values rail, Arabic/RTL visual tuning,
Gallery gateway art direction, and unrelated owners remain frozen.

## Current FACT

- Program geometry remains owned by `program-journey`; formation and dialog
  controllers remain separate.
- Program and Values now resolve final blue through
  `--program-values-final-color: #2038ff` on their shared visual world.
- Values uses only the existing mode-4 scene progress. Its protected
  `hidden -> deck -> fan -> preFlip -> flip` sequence and spatial/worm owners
  remain intact, while the deck collision pose now clamps at the center plane
  before fan travel begins.
- Vision uses its existing timeline progress to composite two configurable
  background layers. About, Vision, and Mission now have distinct desktop
  palette/pattern tokens and diffuse in both directions without a second scroll
  driver.
- Gallery heading owns a desktop-only normalized blur/scale/position entry and
  exit state. Values and Gallery share one resting-color token, while the
  `gallery-depth` data, renderer, trail, and route transition remain separate.
- Gallery ending now seeds the featured Article image behind the CTA and keeps a
  focused CTA visible during rapid reverse. The final Gallery CTA media itself
  remains blocked because no authoritative media field exists and the owner has
  not yet selected reuse versus a dedicated source.
- Article data remains database-only, newest-first, maximum four, and controller
  owned. Its presentation grows from a small 16:9 image, reveals copy from blur,
  uses one sticky desktop scene with vertical input, moves visually horizontal,
  finishes with a vertical roll plus clickable full-height media, then returns
  to native flow into the full-viewport Footer.

## Runtime proof — Chromium 1440 × 900

- Program heading-to-card-field gap: `-28.81px`.
- Cards-to-Program-end breathing interval: `225.59px` (`25.07%` viewport).
- Shared and Values tokens: exact `#2038ff`; handoff morph at Pondasi: `1.0000`.
- Program open/detail/back: open `true`, close `true`.
- Values heading-to-visual-deck gap: `70.79px` (`1.26/16` viewport height).
- Values visual center spread remains `15.97px` through collision progress
  `.096`, then opens to `72px` at `.12` and `496.78px` at `.18`; card Y remains
  below the collision plane rather than overshooting upward.
- Rapid forward/reverse leaves finite Values transforms and morph `1.0000`.
- Vision states resolve to distinct `rgb(239, 227, 202)`,
  `rgb(203, 223, 228)`, and `rgb(209, 223, 202)` pattern layers; intermediate
  opacity/diffusion proves gradual compositing.
- Gallery entry/rest/reverse opacity: `.692 / 1 / .692`; reverse focus remains
  tabbable, visible, and never receives `aria-hidden=true`.
- Article opening: `188.02 × 105.75` to `604.8 × 340.19`, ratio `1.78`.
- Opening copy progresses from opacity `0`/blur `14px` to opacity `.7117`/blur
  `4.04px` during image growth.
- Article title-center/corner and description-edge deltas: `0px`.
- Horizontal track: `-28.57px` to `-2851.12px`; roll: `-4.52px` to `-693px`.
- Final strip/full-media gap: `240px`, exactly `1/6` of the 1440px viewport.
- Final full-height media is clickable and resolves to `/artikel`.
- Article/Footer boundary shares one document coordinate; Footer is `900px`.
- Every checkpoint had `scrollWidth == clientWidth`; runtime errors: `0`.

## Automated proof

- `git diff --check`: `PASS`.
- affected Program/Values/Vision/Gallery/Article suite:
  `25 passed`, `636 assertions`, `0 failed` on temporary MariaDB.
- `npm run build`: `PASS`.
- `npm run check:structure`: baseline `FAIL`; unchanged HEAD already has
  224/202-line legacy controllers, two orphan Vision modules, and a stale Hero
  checksum. These unrelated owners were not mutated.
- full `php artisan test --compact` on temporary MariaDB: `201 passed` from
  212 with `1804` assertions; five errors require the unavailable GD extension,
  while six unrelated existing admin/article/security assertions fail in this
  fallback setup. The affected homepage suite remains fully green.

## Deferred and blocked

- Gallery CTA media owner is awaiting the owner's A/B/C decision: reuse a Gallery
  item, name an existing local asset, or add a dedicated configurable field.
- Tablet, phone, Arabic/RTL visual tuning, Values responsive rail, final
  motif naming, and Gallery cinematic gateway remain deferred.
- Safari/WebKit, Lighthouse/PageSpeed, RUM/CrUX, and responsive/locale proof are
  not claimed.

## NEXT VALID STEP

Obtain the exact Gallery CTA media owner decision, then complete that bounded
presentation without altering Gallery depth data or adding a cinematic gateway.
Global release proof also requires a test host with GD and the pre-existing
structure baseline repaired under separately authorized scope.
