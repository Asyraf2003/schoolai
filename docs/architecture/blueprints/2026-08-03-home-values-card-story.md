# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTING`
Owner: Asyraf Mubarak
Date: 2026-08-03
Source main before this correction: `d67edbd1a5e23866164ddd2b011bfd0d42854fdb`
Surface: homepage `#nilai`
Execution channel: Web AI with explicit direct-`main` authorization

## Owner goal and reference

Replace the Values grid with a full-viewport scroll story informed by the
owner-provided Lusion About screenshots without copying Lusion code, assets,
branding, card art, or exact composition.

The latest accepted owner direction is:

- preserve the clipped edge-first heading reveal and make it substantially
  slower and smoother;
- enlarge the heading while reducing visual weight;
- when the PC card deck starts forming, move the heading upward as a
  scroll-scrubbed object rather than through a detached time-only lift;
- enlarge PC cards by roughly one visual scale step;
- make all PC cards anticipate together by rotating about `15deg` away from
  their destination;
- flip cards one by one with overlap, overshoot the front by about `15deg`, then
  settle to a neutral front-facing pose;
- keep a subtle, continuous vertical float during the card choreography;
- preserve the existing phone and tablet choreography families.

## Scope

In scope:

- Values heading reveal state, weight, scale, and PC exit motion;
- PC Values card scale, deck/spread geometry, flip timing, and float layer;
- focused DOM test and durable architecture state.

Protected and out of scope:

- Hero, Vision/Mission content/controller, Programs, Gallery, Articles;
- navigation, footer, About, Testimonial, DB/admin/routes;
- translation copy, WebGL, third-party dependencies, and unrelated cleanup;
- phone and tablet card chronology except shared semantic markup.

## Semantic and fallback contract

- One semantic `section`, localized `h2`, description, and four `article` cards.
- Heading line wrappers exist only for clipping; accessible reading order stays
  unchanged.
- A decorative `.values-card__float` wrapper separates time-based floating from
  scroll-driven position and Y-axis card rotation.
- Card fronts retain all meaningful content; backs remain decorative and
  `aria-hidden`.
- No JavaScript, unsupported 3D, and reduced motion render the readable static
  front-card grid.
- No controls, focus traps, hidden actions, canvas, or WebGL are introduced.

## Heading state contract

```text
before Values
-> reveal 0
-> forward entry scrubs reveal from progress 0.012 through 0.20
-> an idle RAF frame preserves the current partial reveal
-> completed reveal latches
-> line two shifts through a 1350ms CSS transition
-> PC deck progress 0.22 begins scroll-driven vertical heading travel
-> progress 0.46 places the heading above the sticky viewport
-> reverse scroll resolves a partial reveal and keeps the heading static
-> leaving above Values resets the state
```

The former state treated any frame without new raw scroll delta as reverse
behavior. Since inertial rendering continues after the scroll event, the next
RAF frame forced a partial heading directly to `100%`. The corrected state
distinguishes forward, backward, and idle frames.

- Reveal uses double-smoothed progress for gentler acceleration/deceleration.
- Line one begins `108%` below its clipping box.
- Line two begins `108%` above its clipping box.
- Latin and Arabic headings use weight `300`; Arabic tracking remains natural.
- Loading inside the section initializes a resolved static heading.

## Six-tier composition

| Tier | Heading/copy | Cards | Trail |
|---|---|---|---|
| XS 360–639 | larger thin heading only | one card at a time; back-to-front flip | hidden |
| SM 640–767 | larger thin heading only | one card at a time; back-to-front flip | hidden |
| MD 768–1023 | heading plus copy | fixed 2x2; paired flips | hidden |
| LG 1024–1279 | heading plus copy | fixed 2x2; paired flips | hidden |
| XL 1280–1535 | thin large heading, inward second-line shift, scrubbed exit | enlarged lead/deck/spread/anticipate/flip/settle/exit | visible |
| 2XL >=1536 | largest bounded heading and cards | wider enlarged PC chronology | visible |

Story travel:

```text
XS 520svh
SM 500svh
MD 400svh
LG 400svh
XL 620svh
2XL 640svh
```

PC card targets:

```text
XL   min(26vw, 25rem)
2XL  min(24vw, 27rem)
```

## PC chronology

1. Heading reveals gradually through its center seam.
2. The first back-facing card rises and settles about `20vh` below the heading.
3. Remaining backs form a deck from progress `0.22`.
4. At the same progress, the heading begins moving upward with scroll and exits
   by progress `0.46`.
5. Deck spreads from progress `0.31` through `0.48` into enlarged cards with
   closer overlap than the previous row.
6. All card backs anticipate together from `180deg` to `195deg` during progress
   `0.40` through `0.48`.
7. Physical right-to-left cards drive from `195deg` to `-15deg`; starts are
   offset by `0.052`, each drive lasts `0.18`, preserving strong overlap.
8. Each card settles from `-15deg` to `0deg` over the following `0.07`.
9. A separate float wrapper moves each card between `-4px` and `4px` on a
   `4.8s` ease-in-out cycle with staggered phases.
10. Cards begin exit after progress `0.90`; the PC trail remains scroll-driven.

This is the requested motion grammar:

```text
anticipation -> launch -> overshoot -> settle
```

The cards therefore imply mass and intention instead of mechanically rotating
between two flat endpoints.

## Tablet chronology

- Four decorative backs occupy a stable 2x2 arrangement.
- No lead card, deck, spread, white line, or continuous float layer is activated.
- The physical right pair flips together, followed by the left pair.
- Geometry stays stable while the inner faces rotate.

## Phone chronology

- One centered card is visible at a time.
- Each card appears on its back, flips to the information front, and yields to
  the next card.
- No deck, spread, pair grid, horizontal heading shift, supporting copy, white
  line, or continuous float is used.

## Locale and direction

- One DOM, controller, progress model, and physical chronology serve ID, EN,
  and AR.
- Locale changes copy, family, direction, and natural alignment only.
- On PC, line two moves toward the visual center: positive X in LTR and negative
  X in RTL.
- Arabic does not reverse time, card order, vertical scroll, anticipation, or
  neutral Y-axis rotation.

## Ownership

| Concern | Owner |
|---|---|
| DOM/content/float wrapper | `resources/views/home/sections/school-values.blade.php` |
| shell/sticky/root variables | `story-shell.css` |
| heading/copy treatment | `story-heading.css` |
| tier scale and travel | `story-responsive.css` |
| card treatment/float cycle | `story-cards.css` |
| backs | `story-card-back.css` |
| PC trail | `story-trail.css` |
| controller/lifecycle | `controller.js` |
| one-way heading direction state | `heading-state.js` |
| phone/tablet/story frame | `layout.js` |
| PC card frame | `desktop-layout.js` |
| scroll inertia | `motion.js` |
| style painting/cleanup | `paint.js` |
| focused DOM contract | `tests/Feature/HomeValuesStoryTest.php` |

## Browser, performance, and accessibility

- Capability tier 0 remains the semantic static front-card grid.
- Capability tier 1 uses CSS 3D plus one bounded scroll RAF scheduler.
- The float uses CSS animation on four decorative wrappers only at `>=1280px`.
- Reduced motion disables the sticky enhanced story and float.
- Frequent updates remain transform/opacity based.
- Work pauses offscreen/hidden and disposes listeners/classes/properties.
- Chromium and WebKit still require runtime proof for clipping, 3D backfaces,
  variable updates inside transforms, sticky behavior, resize, and BFCache.

## Proof state

Implemented source is not runtime proof. Required gates remain:

```text
git diff --check
npm run check:structure
npm run build
php artisan test
```

Runtime proof must cover 390, 640, 768, 1024, 1280, 1536, and 1920 widths,
ID/EN/AR, LTR/RTL, Chromium/WebKit, normal/reduced motion, reverse scroll,
resize/orientation, zoom, accessibility, and PageSpeed.
