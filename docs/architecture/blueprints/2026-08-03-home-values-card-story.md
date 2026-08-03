# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTING`
Owner: Asyraf Mubarak
Date: 2026-08-03
Source main before this correction: `e23d1f220ca76aa8782bb6c5cfd8f3b2aa4ba298`
Source head after bounded implementation: `0db23a9a450b94bcabdd5261bade505be3098767`
Surface: homepage `#nilai`
Execution channel: Web AI with explicit direct-`main` authorization

## Owner goal and reference

Build a full-viewport Values story informed by the owner-provided Lusion About
screenshots and measured transform samples without copying Lusion code, assets,
branding, card art, or exact composition.

Latest owner-accepted correction:

- heading entry is a time-based section-entry animation, not scroll scrubbing;
- the two clipped title lines emerge toward their resting positions from
  opposite vertical directions;
- after the reveal completes, line two shifts toward visual center only on PC;
- reverse entry from the following section shows the already-revealed static
  heading instead of replaying the entry;
- the lead card begins below the heading, then the four-card deck rises while
  the heading starts its scroll-driven exit;
- the deck locks near the stage center before the measured fan/flip sequence;
- fan `rotateZ` is preserved while per-card `rotateY` begins with overlap;
- each card straightens during its flip, not in a separate upright phase;
- all four information fronts settle into one stable centered row;
- phone remains one-card flip only, tablet remains paired 2x2 flip only, and the
  white trail remains PC-only.

## Scope

In scope:

- Values heading entry state and RAF lifecycle;
- PC heading exit timing relative to deck formation;
- PC lead/deck vertical staging;
- measured PC card interpolation for X, Y, `rotateZ`, and `rotateY`;
- durable blueprint and current-state records.

Protected and out of scope:

- Hero, Vision/Mission, Programs, Gallery, Articles, navigation, footer;
- About, Testimonial, DB/admin/routes, translations, WebGL, dependencies;
- card semantic content, back illustration, card size tokens, Latin/Cairo font
  ownership, phone chronology, tablet chronology, and locale content.

## Semantic and fallback contract

- One localized semantic `h2` and four semantic `article` cards remain.
- Card fronts keep all meaningful information; backs remain decorative.
- Without JavaScript, with reduced motion, or without required CSS 3D support,
  the readable static front-card grid remains.
- No control, focus path, content source, semantic order, or accessible name
  changes.

## Heading state contract

```text
before section
-> idle clipped lines
-> section enters from above
-> 1600ms eased reveal runs independently of scroll
-> PC line two shifts using its CSS transition
-> revealed/static
-> PC heading exits upward only when card deck formation begins
```

Reverse/re-entry rules:

- entering from the following section resolves instantly to the static revealed
  state;
- scrolling fully above the entry threshold resets the next forward entry;
- the RAF continues until both scroll inertia and the heading reveal settle;
- CSS owns the second-line shift transition; JavaScript owns only the state.

Responsive heading/copy:

- XS/SM: heading only, no description/eyebrow, no line-two shift;
- MD/LG: heading plus copy, no line-two shift or scroll exit;
- XL/2XL: heading plus copy, PC line-two shift and scroll-driven exit.

ID/EN retain Inter weight `200`; AR retains Cairo weight `300`, natural
tracking, and normal variation settings.

## PC spatial chronology

```text
heading entry
-> one lower lead back
-> four-card centered deck rises
-> heading begins upward exit
-> deck opens into measured fan
-> overlapping measured card flips
-> rotateZ converges toward zero during flip
-> overshoot
-> stable four-front centered row
-> normal sticky release
```

### Vertical anchors

All Y values are card-top transforms derived from stage and card geometry:

```text
lead center:  78% stage height
 deck center: 60% stage height
 final center: 56% stage height
```

The card half-height is subtracted from each center. This keeps the first card
below the heading and the active deck/row near the viewport center instead of
using one arbitrary top offset.

### Horizontal normalization

The measured reference X values are converted into a shared center-relative row:

- final slot spacing is bounded by `1.03 * cardWidth` and available viewport
  width;
- the early fan uses approximately `93%` of final row spread;
- measured X factors progress through
  `0.93, 0.955, 0.975, 0.988, 0.996, 1.0`;
- every card remains anchored to the same physical stage center.

## Measured reference transform samples

The owner supplied six reference samples. Raw reference coordinates remain
recorded here as art-direction evidence; production uses normalized geometry,
not fixed 1600px coordinates.

| Sample | Container Y | Card | X | Y | rotateZ | rotateY |
|---|---:|---|---:|---:|---:|---:|
| 1 | 471.417 | 1 | 46.398 | 4.191 | -12.639 | 123.539 |
| 1 | 471.417 | 2 | 456.846 | 7.161 | -4.213 | 150.520 |
| 1 | 471.417 | 3 | 867.294 | 3.547 | 4.213 | 167.831 |
| 1 | 471.417 | 4 | 1277.740 | -3.328 | 12.639 | 179.079 |
| 2 | 582.862 | 1 | 29.565 | -7.550 | -7.612 | 65.918 |
| 2 | 582.862 | 2 | 451.235 | -5.996 | -2.537 | 107.185 |
| 2 | 582.862 | 3 | 872.905 | 1.071 | 2.537 | 136.954 |
| 2 | 582.862 | 4 | 1294.580 | 7.153 | 7.612 | 156.925 |
| 3 | 719.985 | 1 | 17.085 | 5.806 | -0.746 | 14.942 |
| 3 | 719.985 | 2 | 447.075 | 8.509 | -0.249 | 45.001 |
| 3 | 719.985 | 3 | 877.066 | 3.390 | 0.249 | 80.369 |
| 3 | 719.985 | 4 | 1307.060 | -4.847 | 0.746 | 114.728 |
| 4 | 857.109 | 1 | 9.872 | -1.307 | -0.072 | -10.699 |
| 4 | 857.109 | 2 | 444.672 | 6.976 | -0.024 | 6.053 |
| 4 | 857.109 | 3 | 879.470 | 8.845 | 0.024 | 30.105 |
| 4 | 857.109 | 4 | 1314.270 | 2.582 | 0.072 | 59.303 |
| 5 | 1048.540 | 1 | 4.591 | 5.045 | 0 | -17.398 |
| 5 | 1048.540 | 2 | 442.911 | 9.772 | 0 | -16.491 |
| 5 | 1048.540 | 3 | 881.235 | 5.515 | 0 | -7.624 |
| 5 | 1048.540 | 4 | 1319.550 | -3.812 | 0 | 8.021 |
| 6 | 1405.600 | 1 | 1.101 | 8.360 | 0 | -0.172 |
| 6 | 1405.600 | 2 | 441.747 | 0.036 | 0 | -6.322 |
| 6 | 1405.600 | 3 | 882.393 | -8.321 | 0 | -13.860 |
| 6 | 1405.600 | 4 | 1323.040 | -9.027 | 0 | 0 |

Production interpolation adds a pre-flip all-back sample at `rotateY(180deg)`
and a final all-front sample at `rotateY(0deg)`. The supplied negative angles
remain the overshoot before the final settle.

Normalized sample timing is:

```text
0.00 pre-flip fan
0.10 measured sample 1
0.22 measured sample 2
0.39 measured sample 3
0.57 measured sample 4
0.77 measured sample 5
1.00 stable front row
```

Each interval uses eased interpolation. This creates overlapping first-to-fourth
flips without a generic per-card delay formula.

## Six-tier composition

| Tier | Heading/copy | Cards | Trail |
|---|---|---|---|
| XS 360–639 | autoplay heading only | one centered card at a time | hidden |
| SM 640–767 | autoplay heading only | one centered card at a time | hidden |
| MD 768–1023 | autoplay heading plus copy | stable centered 2x2 pair flips | hidden |
| LG 1024–1279 | autoplay heading plus copy | stable centered 2x2 pair flips | hidden |
| XL 1280–1535 | autoplay heading, PC shift/exit | measured lead/deck/fan/flip/hold | visible |
| 2XL >=1536 | largest bounded PC composition | wider measured choreography | visible |

One semantic DOM and one controller serve all tiers. CSS mode variables select
chronology; there is no device, locale, browser, or controller fork.

## Locale and direction

- ID and EN share LTR chronology and Inter.
- AR shares the same neutral vertical reveal and Y-axis card flip in Cairo/RTL.
- The PC second-line horizontal shift mirrors through the existing logical
  direction token.
- Card item order remains semantic order in every locale.
- Vertical scroll, time, and neutral 3D rotation are not reversed for RTL.

## Ownership

| Concern | Owner |
|---|---|
| semantic content | `resources/views/home/sections/school-values.blade.php` |
| heading treatment | `resources/css/surfaces/home/values/story-heading.css` |
| card layout/treatment | `resources/css/surfaces/home/values/story-cards.css` |
| entry state | `resources/js/surfaces/home/values/heading-state.js` |
| controller/RAF lifecycle | `resources/js/surfaces/home/values/controller.js` |
| mode story timing | `resources/js/surfaces/home/values/layout.js` |
| PC lead/deck choreography | `resources/js/surfaces/home/values/desktop-layout.js` |
| measured PC samples | `resources/js/surfaces/home/values/desktop-keyframes.js` |
| scroll inertia | `resources/js/surfaces/home/values/motion.js` |
| durable proof ledger | `docs/architecture/UI_UX_CURRENT_STATE.md` |

## Browser, performance, and accessibility

- Frequent movement remains transform/opacity based.
- The existing single Values RAF is reused; no second scheduler, observer,
  listener family, asset, dependency, canvas, or WebGL context is introduced.
- RAF stops only after both scroll inertia and time-based heading reveal settle.
- Reduced motion retains the readable static grid.
- Runtime proof is still required for Chromium/WebKit sticky, clipping,
  `preserve-3d`, backface visibility, reverse scroll, resize, and BFCache.

## Proof state

Source syntax and local transform sampling passed before publication. Commit
publication proves source state only.

Required repository/runtime gates remain:

```text
git diff --check
npm run check:structure
npm run build
php artisan test
```

Runtime proof must cover the six tiers, ID/EN/AR, LTR/RTL, Chromium/WebKit,
reduced motion, resize/orientation, reverse entry, accessibility, and PageSpeed.
