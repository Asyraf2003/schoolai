# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTING`
Owner: Asyraf Mubarak
Date: 2026-08-03
Source main before this correction: `e8ae9c0945c1dae0a9f703da67f64001cd55e608`
Raw evidence commit: `75138335c381535e7f942a77210a2f34988cccc0`
Source head after bounded implementation: `f48d9a9a3b1e93b72944a33616b3328d3c575f64`
Surface: homepage `#nilai`
Execution channel: Web AI with explicit direct-`main` authorization

## Owner goal and reference

Build a full-viewport Values story informed by owner-provided Lusion About
recordings, screenshots, computed styles, and measured transform samples without
copying Lusion code, assets, branding, card art, type files, or exact
composition.

Raw evidence is preserved at:

`../measurements/2026-08-03-home-values-reference-motion-raw.md`

Latest owner-accepted correction:

- heading entry is a time-based section-entry animation, not scroll scrubbing;
- entry starts as soon as the section enters the viewport;
- two clipped title lines emerge from opposite directions with a narrow center
  seam, then line two shifts toward visual center on PC only;
- Latin heading remains editorial-light but is slightly thicker than weight
  `200`;
- reverse entry from the following section shows the static revealed heading;
- the lead card begins low below the heading, then the deck rises into one
  centered stage plane while the heading exits;
- the fan keeps outward `rotateZ` while the first-to-fourth flips overlap;
- X, Y, `rotateZ`, and `rotateY` evolve together;
- captured samples guide the path but must not become stop points;
- interpolation must preserve continuous velocity through samples, followed by
  bounded front-side overshoot and a stable upright information row;
- phone remains one-card flip only, tablet remains paired 2x2 flip only, and the
  white trail remains PC-only.

## Scope

In scope:

- Values heading trigger, duration, easing, weight, and center seam;
- PC heading exit timing relative to deck formation;
- PC lead/deck/fan vertical staging;
- continuous measured PC interpolation for X, Y, `rotateZ`, and `rotateY`;
- durable raw evidence, blueprint, and current-state records.

Protected and out of scope:

- Hero, Vision/Mission, Programs, Gallery, Articles, navigation, footer;
- About, Testimonial, DB/admin/routes, translations, WebGL, dependencies;
- card semantic content, back illustration, card size tokens, Cairo ownership,
  phone chronology, tablet chronology, and locale content.

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
-> section top crosses 94% viewport trigger
-> 1200ms cubic ease-out reveal runs independently of scroll
-> PC line two shifts through its CSS transition
-> revealed/static
-> PC heading exits upward when card deck formation begins
```

Reverse/re-entry rules:

- entering from the following section resolves instantly to static revealed;
- scrolling fully above the entry threshold resets the next forward entry;
- RAF continues until both scroll inertia and heading reveal settle;
- CSS owns the line-two shift; JavaScript owns entry state.

Typography/treatment:

- ID/EN use Inter variable weight `260` and existing optical sizing;
- AR keeps Cairo weight `300`, natural tracking, and current adapter;
- title lines use a small final row gap and clipped travel of approximately one
  line height;
- the PC line-two shift remains logical-direction aware.

Responsive heading/copy:

- XS/SM: heading only, no description/eyebrow, no line-two shift;
- MD/LG: heading plus copy, no line-two shift or scroll exit;
- XL/2XL: heading plus copy, PC line-two shift and scroll-driven exit.

## PC spatial chronology

```text
heading entry
-> one low lead back
-> four-card centered deck rises
-> heading begins upward exit
-> deck opens into measured fan
-> overlapping first-to-fourth flips
-> rotateZ converges toward zero while rotateY crosses edge-on
-> small negative rotateY overshoot
-> stable four-front centered row
-> normal sticky release
```

### Stage anchors

All Y values are card-top transforms derived from stage and card geometry:

```text
lead center:   86% stage height
deck center:   63% stage height
active center: 53.5% stage height
```

Card half-height is subtracted from every center. The increased lead-to-active
travel makes the deck visibly rise rather than remaining submerged near the
bottom of the stage.

### Story timing

```text
lead reveal:       progress 0.03–0.12
deck formation:    progress 0.12–0.24
heading exit:      progress 0.12–0.30
fan formation:     progress 0.22–0.34
measured travel:   progress 0.34–0.86
stable front hold: progress 0.86 onward
```

The fan and measured stages overlap briefly so there is no generic intermediate
upright row.

### Horizontal normalization

Raw reference X positions are normalized into one center-relative row:

- final slot spacing is bounded by card width and available viewport width;
- early fan spread remains approximately 93% of the final row;
- every card stays anchored to the same physical stage center;
- fixed card z-order prevents a one-frame left/right ownership jump.

## Continuous measured interpolation

Raw samples and matrix snapshots are stored in the linked evidence packet.
Production keeps the accepted normalized times:

```text
0.00 pre-flip fan
0.10 measured sample 1
0.22 measured sample 2
0.39 measured sample 3
0.57 measured sample 4
0.77 measured sample 5
1.00 stable front row
```

Previous piecewise `smooth()` interpolation reached zero velocity at every
sample and produced mechanical micro-pauses. Production now uses cubic Hermite
interpolation with finite-difference tangents and bounded tangent scale. This
keeps a continuous first derivative across X, Y, `rotateZ`, and `rotateY` while
still passing through every captured sample.

The pre-flip all-back state remains `rotateY(180deg)`. Negative measured values
remain front-side overshoot, clamped to a safe bounded range before final
`0deg` settle.

## Six-tier composition

| Tier | Heading/copy | Cards | Trail |
|---|---|---|---|
| XS 360–639 | autoplay heading only | one centered card at a time | hidden |
| SM 640–767 | autoplay heading only | one centered card at a time | hidden |
| MD 768–1023 | autoplay heading plus copy | stable centered 2x2 pair flips | hidden |
| LG 1024–1279 | autoplay heading plus copy | stable centered 2x2 pair flips | hidden |
| XL 1280–1535 | autoplay heading, PC shift/exit | lead/deck/fan/flip/hold | visible |
| 2XL >=1536 | largest bounded PC composition | wider shared choreography | visible |

One semantic DOM and one controller serve all tiers. CSS mode variables select
chronology; there is no device, locale, browser, or controller fork.

## Locale and direction

- ID and EN share LTR chronology and Inter.
- AR shares the neutral vertical reveal and Y-axis card flip in Cairo/RTL.
- PC second-line horizontal shift mirrors through the existing logical token.
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
| PC staging | `resources/js/surfaces/home/values/desktop-layout.js` |
| measured curves | `resources/js/surfaces/home/values/desktop-keyframes.js` |
| scroll inertia | `resources/js/surfaces/home/values/motion.js` |
| raw evidence | `docs/architecture/measurements/2026-08-03-home-values-reference-motion-raw.md` |
| durable proof ledger | `docs/architecture/UI_UX_CURRENT_STATE.md` |

## Browser, performance, and accessibility

- Frequent movement remains transform/opacity based.
- The existing single Values RAF is reused; no new scheduler, observer,
  listener family, asset, dependency, canvas, or WebGL context is introduced.
- RAF stops only after both scroll inertia and time-based heading reveal settle.
- Reduced motion retains the readable static grid.
- Runtime proof is still required for Chromium/WebKit sticky, clipping,
  `preserve-3d`, backface visibility, fast/reverse scroll, resize, and BFCache.

## Proof state

Commit publication proves source state only. Required repository/runtime gates
remain:

```text
git diff --check
npm run check:structure
npm run build
php artisan test
```

Runtime proof must cover the six tiers, ID/EN/AR, LTR/RTL, Chromium/WebKit,
reduced motion, resize/orientation, reverse entry, accessibility, and PageSpeed.
