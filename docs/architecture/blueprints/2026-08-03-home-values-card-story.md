# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTING`
Owner: Asyraf Mubarak
Date: 2026-08-03
Source main before this correction: `f440447cc153b47615b72d15b41617a2e15171ba`
Surface: homepage `#nilai`
Execution channel: Web AI with explicit direct-`main` authorization

## Owner goal and reference

Build a full-viewport Values scroll story informed by the owner-provided Lusion
About screenshots without copying Lusion code, assets, branding, card art, or
exact composition.

The latest accepted correction is:

- preserve the clipped heading entry, second-line shift, and PC heading exit;
- use an editorial Latin heading lighter than `300` but not hairline `100`;
- calculate all enhanced card positions from one shared stage center;
- keep the temporary deck/fan, then straighten cards before face flips;
- move the card group coherently under scroll momentum;
- flip visual cards first-to-fourth with overlapping motion;
- hold the complete upright information row until sticky release;
- preserve phone/tablet choreography families and the PC-only trail.

## Scope

In scope:

- Values Latin heading weight/tracking;
- enhanced card positioning origin;
- PC momentum, flip order, and final hold;
- durable architecture state.

Protected and out of scope:

- Hero, Vision/Mission, Programs, Gallery, Articles, navigation, footer;
- About, Testimonial, DB/admin/routes, translations, WebGL, dependencies;
- phone one-card chronology and tablet pair chronology;
- existing PC line art and independent subtle float.

## Semantic and fallback contract

- One localized semantic `h2` and four semantic `article` cards remain.
- Card fronts keep all meaningful information; backs stay decorative.
- Without JavaScript, with reduced motion, or without required CSS 3D support,
  the original readable static grid remains.
- Enhanced absolute positioning exists only under `.is-values-ready`.
- No control, focus path, content source, or semantic order changes.

## Heading contract

- Inter is loaded as a variable font covering weights `100..900`.
- ID/EN Values heading uses weight and variation axis `200`.
- Font synthesis remains disabled.
- Tracking is `-.04em`.
- Arabic retains Cairo, weight `300`, natural tracking, and normal variation
  settings.
- Existing clipped reveal, reverse latch, line-two shift, and scroll-driven PC
  exit remain unchanged.

## Six-tier composition

| Tier | Heading/copy | Cards | Trail |
|---|---|---|---|
| XS 360–639 | heading only | one centered card at a time | hidden |
| SM 640–767 | heading only | one centered card at a time | hidden |
| MD 768–1023 | heading plus copy | stable centered 2x2 pair flips | hidden |
| LG 1024–1279 | heading plus copy | stable centered 2x2 pair flips | hidden |
| XL 1280–1535 | large thin heading and scrubbed exit | centered lead/deck/fan/upright/flip/hold | visible |
| 2XL >=1536 | largest bounded heading/cards | wider centered PC choreography | visible |

One semantic DOM, controller, and coordinate owner serve every tier. Tier modes
change geometry and chronology, not component architecture.

## Shared coordinate contract

Enhanced cards do not use their static grid cells as animation origins.

```text
stage center
+ card-local X/Y/Z pose
= final enhanced position
```

Implementation contract:

- enhanced card container is one absolute layer covering the sticky stage;
- every card is physically anchored at `left: 50%`;
- X painting uses `calc(-50% + var(--values-x))`;
- static fallback keeps the ordinary grid;
- JavaScript pose functions may therefore assume a common center for phone,
  tablet, lead, deck, fan, and row geometry.

This removes the former double-origin defect:

```text
static grid column position + center-relative JS translation
```

## PC chronology

```text
heading reveal
-> centered lead back
-> layered centered deck
-> temporary fan spread
-> upright aligned row
-> shared anticipation
-> overlapping card 1 -> 2 -> 3 -> 4 drive
-> front overshoot and settle
-> stable four-front hold
-> normal sticky release
```

### Geometry phases

- Lead enters during progress `0.07–0.19`.
- Deck forms during `0.22–0.33`.
- Fan spread forms during `0.31–0.41`.
- Upright row resolves during `0.40–0.50`.
- Upright cards share one base Y and `rotateZ(0deg)`.
- Horizontal slot spacing remains `0.88 * cardWidth`.
- Depth stays negative and index-ordered from deck through upright row.

### Momentum contract

Scroll momentum is group motion, not an index multiplier.

```text
all card Y poses += momentum * 6px
```

The existing independent CSS float remains `-4px..4px`, time-based, PC-only,
and disabled under reduced motion. It may create subtle life but must not alter
slot geometry or generate a staircase.

### Flip contract

- Shared anticipation runs from progress `0.50` to `0.56`:
  `180deg -> 195deg`.
- Card one begins at `0.56`.
- Following visual cards begin with `0.045` progress offsets.
- Each drive lasts `0.17`: `195deg -> -15deg`.
- Each settle lasts the following `0.08`: `-15deg -> 0deg`.
- All cards are upright before the first drive begins.
- No indexed exit pose follows the final settle.
- Front cards retain their individual upright row slots while scrolling through
  the remaining section travel.

## Tablet and phone contracts

- Phone retains one centered card at a time, back to front, then yields to the
  next card.
- Tablet retains one centered 2x2 composition and paired face flips.
- Their chronology formulas are unchanged by this correction.
- Both modes benefit from the same correct center-relative enhanced origin.
- Neither receives deck/fan staging, continuous float, or PC trail behavior.

## Locale and direction

- One DOM and one controller serve ID, EN, and AR.
- Locale changes family, copy, direction, and natural alignment only.
- Card order remains semantic item order in every locale.
- Neutral Y-axis face rotation is not mirrored for RTL.
- Existing second-line heading shift mirrors toward the visual center.

## Ownership

| Concern | Owner |
|---|---|
| semantic content | `resources/views/home/sections/school-values.blade.php` |
| enhanced/static card positioning and treatment | `resources/css/surfaces/home/values/story-cards.css` |
| heading treatment | `resources/css/surfaces/home/values/story-heading.css` |
| PC geometry/flip | `resources/js/surfaces/home/values/desktop-layout.js` |
| controller/inertia/paint | remaining Values JS modules |
| fallback/responsive | existing Values CSS modules |
| proof ledger | `docs/architecture/UI_UX_CURRENT_STATE.md` |

## Browser, performance, and accessibility

- Frequent movement remains transform/opacity based.
- No new observer, listener, scheduler, asset, dependency, or semantic node is
  introduced.
- One center-relative plane reduces compositor and geometry ambiguity.
- Reduced motion keeps the readable static grid.
- Chromium and WebKit still require runtime proof for `calc()` inside 3D
  translate, preserve-3d, backface visibility, variable font weight, sticky,
  resize, and reverse scroll.

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
