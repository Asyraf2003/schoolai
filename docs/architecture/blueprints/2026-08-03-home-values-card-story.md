# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTING`
Owner: Asyraf Mubarak
Date: 2026-08-03
Source main before this correction: `6f44adb1438c5956a3269825e19ff0ea20a9e6c3`
Surface: homepage `#nilai`
Execution channel: Web AI with explicit direct-`main` authorization

## Owner goal and reference

Build a full-viewport Values scroll story informed by the owner-provided Lusion
About screenshots without copying Lusion code, assets, branding, card art, or
exact composition.

The latest accepted correction is:

- preserve the slow clipped heading reveal and scrubbed PC heading exit;
- make the Latin heading genuinely thin, not merely lighter than body text;
- keep the deck-to-fan visual preparation;
- prevent any nearest-card side swap during deck spread;
- straighten and vertically align all PC cards before the first face flip;
- keep cards upright in their individual slots through flip and final settle;
- preserve anticipation, overlap, overshoot, settle, float, phone/tablet modes,
  and the PC-only trail.

## Scope

In scope:

- Values Latin heading weight/tracking;
- PC card depth order;
- PC fan-to-upright staging and flip chronology;
- durable architecture state.

Protected and out of scope:

- Hero, Vision/Mission, Programs, Gallery, Articles, navigation, footer;
- About, Testimonial, DB/admin/routes, translations, WebGL, dependencies;
- phone and tablet card choreography;
- existing PC line and float art direction.

## Semantic and fallback contract

- One localized semantic `h2` and four semantic `article` cards remain.
- Card fronts keep all meaningful information; backs stay decorative.
- No JavaScript, reduced motion, or unsupported CSS 3D falls back to the readable
  static front-card grid.
- This correction changes display treatment and PC transforms only.

## Heading contract

- Inter is already loaded as a variable font covering weight `100..900`.
- ID/EN Values heading uses the real `100` axis, with synthesis disabled.
- Letter spacing is `-.045em` to retain a broad editorial display rhythm.
- Arabic remains on its current typography owner, weight `300`, natural tracking,
  and no inherited Inter variation override.
- Existing clipped reveal, line-two shift, reverse latch, and PC vertical exit
  remain unchanged.

## Six-tier composition

| Tier | Heading | Cards | Trail |
|---|---|---|---|
| XS 360–639 | thin heading only | one-card flip sequence | hidden |
| SM 640–767 | thin heading only | one-card flip sequence | hidden |
| MD 768–1023 | heading plus copy | stable 2x2 paired flips | hidden |
| LG 1024–1279 | heading plus copy | stable 2x2 paired flips | hidden |
| XL 1280–1535 | thin large heading and scrubbed exit | lead/deck/fan/upright/flip/settle | visible |
| 2XL >=1536 | largest bounded heading/cards | wider PC chronology | visible |

No breakpoint fork, duplicate DOM, renderer, or locale component is introduced.

## PC card chronology

```text
lead
-> layered deck
-> temporary fan spread
-> upright aligned row
-> shared anticipation
-> overlapping right-to-left drive
-> front overshoot
-> upright neutral settle
-> exit
```

### Geometry phases

- Lead enters during progress `0.07–0.19`.
- Deck forms during `0.22–0.33`.
- Fan spread forms during `0.31–0.41`.
- Upright row resolves during `0.40–0.50`.
- All upright cards share one base Y and `rotateZ(0deg)`.
- Horizontal slots retain spacing `0.88 * cardWidth`.

### Stable depth contract

Previous depth crossed from negative deck values to positive row values, causing
browser 3D compositing to exchange the visually nearest card.

Corrected depth remains ordered:

```text
deck/fan: -index * 22
upright:  -index * 4
```

The sign and index order never reverse during overlap.

### Flip contract

- Shared anticipation runs from progress `0.50` to `0.56`:
  `180deg -> 195deg`.
- Physical rightmost card begins first at `0.56`.
- Following starts use `0.045` progress offsets.
- Each drive lasts `0.17`: `195deg -> -15deg`.
- Each settle lasts `0.08`: `-15deg -> 0deg`.
- Fan rotation is already zero before drive begins.
- Final front cards remain in their individual upright row slots.
- Exit begins at progress `0.955`, after the last settle.
- The existing independent `-4px..4px` float remains PC-only and reduced-motion
  aware.

## Tablet and phone contracts

Tablet retains stable 2x2 geometry and paired flips. Phone retains one centered
card at a time. Neither receives fan, upright-row staging, continuous float, or
PC trail behavior from this correction.

## Locale and direction

- One DOM and one controller serve ID, EN, and AR.
- Locale changes family, copy, direction, and alignment only.
- Neutral Y-axis flip chronology is not mirrored for RTL.
- Existing second-line heading shift mirrors toward the visual center.

## Ownership

| Concern | Owner |
|---|---|
| semantic content | `resources/views/home/sections/school-values.blade.php` |
| heading treatment | `resources/css/surfaces/home/values/story-heading.css` |
| PC geometry/flip | `resources/js/surfaces/home/values/desktop-layout.js` |
| controller/inertia/paint | remaining Values JS modules |
| fallback/responsive | existing Values CSS modules |
| proof ledger | `docs/architecture/UI_UX_CURRENT_STATE.md` |

## Browser, performance, and accessibility

- Frequent movement remains transform/opacity based.
- No new observer, listener, loop, asset, dependency, or semantic node is added.
- Stable depth reduces compositor ambiguity during overlap.
- Reduced motion keeps the static readable grid.
- Chromium/WebKit still require runtime proof for variable font rendering,
  preserve-3d, backface visibility, sticky behavior, and reverse scroll.

## Proof state

Source publication is not runtime proof. Required gates remain:

```text
git diff --check
npm run check:structure
npm run build
php artisan test
```

Runtime proof must still cover all six tiers, ID/EN/AR, LTR/RTL,
Chromium/WebKit, reduced motion, resize/orientation, accessibility, and
PageSpeed.
