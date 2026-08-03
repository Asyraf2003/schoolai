# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTING`
Owner: Asyraf Mubarak
Updated: 2026-08-04
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active route/surface: homepage `#nilai`
Source implementation head: `f0dd63a11b33078058f7c2f81344f0c2b66fbd09`
Raw evidence: `../measurements/2026-08-03-home-values-reference-motion-raw.md`
Execution channel: Web AI with explicit direct-`main` authorization

## Owner goal

Create an Al Mustaqbal Values sequence informed by the supplied Lusion About
recordings, screenshots, computed styles, and transform samples without copying
Lusion code, assets, card art, branding, shaders, or type files.

The PC sequence must read as one continuous spatial story:

```text
automatic clipped heading reveal
-> low but fully visible card stack
-> stack rises into the center plane
-> fan opens while heading exits
-> overlapping perspective flips straighten each card
-> bounded overshoot
-> stable four-card information row
```

## Latest owner-accepted correction

- The lead stack and the flip action must run around the visual center, not from
  the bottom edge of the sticky stage.
- A card near edge-on must project as a perspective trapezoid: the side closer
  to the viewer appears larger and the far side appears smaller.
- The flip must not look like a rectangle whose width is merely compressed.
- The existing fan `rotateZ` remains while `rotateY` progresses toward upright.
- This surface remains DOM/CSS 3D. Three.js and WebGL are out of scope.

## FACT

- The prior PC lead center was `86%` of stage height, which placed most of the
  initial card below the useful center plane.
- The stage perspective was `1600px`, much flatter than the measured reference
  perspective near `964px`.
- `.values-story__cards` did not preserve the 3D chain between the stage and the
  nested rotating card inner.
- Card rotation is owned by CSS transforms and JS-authored pose variables.
- One semantic `h2` and four semantic `article` cards remain the content source.
- Phone and tablet use their existing simpler flip chronologies.
- The white trail remains PC-only.

## GAP

Corrected runtime is not yet captured. Browser, responsive, RTL, accessibility,
build, test, and PageSpeed status remain `BLOCKED_BY_MISSING_EVIDENCE`.

## Scope

### In scope

- PC lead/deck/active vertical anchors.
- Sticky stage perspective and perspective origin.
- Continuous `preserve-3d` ancestry for the rotating card plane.
- Durable blueprint and current-state records.

### Protected and out of scope

- Hero, Vision/Mission, Programs, Gallery, Articles, navigation, footer.
- About, Testimonial, DB/admin/routes, translations, card content.
- Heading chronology and typography from the preceding accepted correction.
- Phone/tablet chronology.
- Dependencies, Three.js, canvas, WebGL, models, shaders, and assets.

## Semantic and fallback contract

- The localized heading and four card articles remain in Blade.
- Card fronts contain all meaningful information; card backs are decorative.
- No-JS, reduced-motion, or unsupported-3D paths retain the readable static
  front-card layout.
- No control, content source, focus order, or accessible name changes.

## Heading contract

The accepted heading behavior remains unchanged:

```text
section enters from above
-> time-based clipped reveal starts immediately
-> two lines meet across the narrow center seam
-> PC line two shifts toward visual center
-> heading exits upward when deck formation starts
```

Reverse entry from the following section resolves to the static revealed state.
XS/SM hide the description; MD/LG retain heading and copy without the PC shift;
XL/2XL use the full PC choreography.

## PC card stage geometry

All Y transforms describe the card top after subtracting half the measured card
height from the desired visual center:

```text
lead center:   64% stage height
deck center:   57% stage height
active center: 53.5% stage height
hidden lead:   lead + 10% stage height
```

This keeps the lead stack low relative to the title while still fully visible,
then raises it into the centered action plane before fan and flip motion.

## Perspective contract

The sticky stage owns one shared perspective camera:

```text
perspective:        960px
perspective-origin: 50% 52%
```

The chain remains three-dimensional through:

```text
stage
-> cards container
-> card
-> float wrapper
-> inner flip plane
-> front/back faces
```

Each intermediate owner uses `transform-style: preserve-3d`. The outer card
continues to own translation and fan `rotateZ`; the inner plane owns `rotateY`.
The shared camera must project edge-on cards with natural near/far side
foreshortening instead of uniform width compression.

## Story timing

The previously accepted timing remains:

```text
lead reveal:       progress 0.03–0.12
deck formation:    progress 0.12–0.24
heading exit:      progress 0.12–0.30
fan formation:     progress 0.22–0.34
measured travel:   progress 0.34–0.86
stable front hold: progress 0.86 onward
```

Measured X, Y, `rotateZ`, and `rotateY` continue through the shared cubic Hermite
interpolation. Fixed z-order remains unchanged.

## Six-tier contract

| Tier | Heading/copy | Cards | Trail |
|---|---|---|---|
| XS 360–639 | heading only | one centered flip at a time | hidden |
| SM 640–767 | heading only | one centered flip at a time | hidden |
| MD 768–1023 | heading plus copy | centered 2x2 pair flips | hidden |
| LG 1024–1279 | heading plus copy | centered 2x2 pair flips | hidden |
| XL 1280–1535 | full PC heading | lead/deck/fan/perspective flip | visible |
| 2XL >=1536 | bounded large composition | shared PC choreography | visible |

One semantic DOM and one controller serve every tier.

## Locale and direction

- ID and EN use the shared Inter/LTR composition.
- AR keeps Cairo/RTL typography and the same neutral vertical/3D chronology.
- The logical PC line-two shift mirrors for RTL.
- Vertical scroll, time, and neutral card rotation do not reverse for RTL.

## Ownership

| Concern | Owner |
|---|---|
| semantic content | `resources/views/home/sections/school-values.blade.php` |
| stage/perspective | `resources/css/surfaces/home/values/story-shell.css` |
| card 3D chain | `resources/css/surfaces/home/values/story-cards.css` |
| PC stage anchors | `resources/js/surfaces/home/values/desktop-layout.js` |
| measured curves | `resources/js/surfaces/home/values/desktop-keyframes.js` |
| controller/RAF | `resources/js/surfaces/home/values/controller.js` |
| scroll inertia | `resources/js/surfaces/home/values/motion.js` |
| proof ledger | `docs/architecture/UI_UX_CURRENT_STATE.md` |

## Performance and accessibility

- No dependency, renderer, canvas, asset, listener family, or RAF was added.
- Frequent motion remains transform/opacity based.
- Semantic content remains outside graphics.
- Reduced motion retains the static readable grid.
- Runtime proof must include Chromium/WebKit perspective behavior, reverse and
  fast scroll, resize/orientation, all six tiers, ID/EN/AR, RTL, zoom, and
  accessibility.

## Proof state

Source publication proves only that the bounded owners changed. Required gates
remain:

```text
git diff --check
npm run check:structure
npm run build
php artisan test
```

A fresh owner capture is required before the visual correction can be marked
`PASS`.

## Next valid step

Pull current `main` and capture the XL/2XL forward sequence, focusing on the
lead stack center, fan position, edge-on trapezoid, near/far side projection,
overlapping flips, and stable final row.
