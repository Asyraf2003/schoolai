# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTING`
Owner: Asyraf Mubarak
Date: 2026-08-03
Source main before this correction: `9e14ebd4eb9bab7f28b8c0e030603f895ea30505`
Surface: homepage `#nilai`
Execution channel: Web AI with explicit direct-`main` authorization

## Owner goal and reference

Replace the Values grid with a full-viewport scroll story informed by the
owner-provided Lusion About screenshots without copying Lusion code, assets,
branding, card art, or exact composition.

The corrected owner direction is:

- heading text must emerge through a clipped seam like a ruler sliding from a
  pencil case; the leading edge appears before the complete word body;
- the heading reveal runs only when entering Values from the preceding section;
- reverse scroll from later content keeps the heading in its resolved static
  state instead of replaying the reveal backward;
- the second heading line shifts inward through a time-based animation, not a
  scroll-scrubbed transform;
- cards begin with their decorative backs visible and flip to reveal information;
- phone, tablet, and PC use distinct compositions within one DOM/controller;
- the moving white line and stack/spread sequence exist only on PC widths.

## Scope

In scope:

- Values heading markup, clipping, state, and responsive copy visibility;
- Values card geometry/timeline across all six width tiers;
- PC-only scroll-drawn line visibility;
- focused test and durable architecture state.

Protected and out of scope:

- Hero, Vision/Mission content/controller, Programs, Gallery, Articles;
- navigation, footer, About, Testimonial, DB/admin/routes;
- translation copy, WebGL, third-party dependencies, and unrelated cleanup.

## Semantic and fallback contract

- One semantic `section`, localized `h2`, description, and four `article` cards.
- Heading lines contain nested text spans solely for clipping; reading order and
  accessible heading text remain unchanged.
- DOM value order remains Q, I, G, N in ID, EN, and AR.
- Card fronts contain all meaningful information; backs remain decorative and
  `aria-hidden`.
- No JavaScript, unsupported 3D, and reduced motion render the normal readable
  front-card grid.
- Scroll motion has no controls, focus traps, hidden actions, canvas, or WebGL.

## Heading state contract

```text
before Values
-> reveal 0
-> forward entry opens both clipped lines from their shared seam
-> reveal reaches 1 and latches
-> wide-only second-line shift runs as a CSS transition
-> PC deck threshold triggers a time-based heading lift
-> reverse scroll keeps resolved heading static
-> leaving above Values resets the state for the next forward entry
```

- Line one text begins `108%` below its clipped line box.
- Line two text begins `108%` above its clipped line box.
- Because the boxes meet at the line seam, each line appears edge-first rather
  than moving as a fully visible word block.
- Interrupted forward reveal resolves to the complete static heading on reverse.
- Page load inside/later than the reveal initializes the resolved static state.

## Six-tier composition

| Tier | Heading/copy | Cards | Trail |
|---|---|---|---|
| XS 360–639 | heading only; no description/eyebrow; no shift | one card at a time; back-to-front flip only | hidden |
| SM 640–767 | heading only; no description/eyebrow; no shift | one card at a time; back-to-front flip only | hidden |
| MD 768–1023 | heading plus copy; no horizontal shift | fixed 2x2; two cards flip as a pair, then the other pair | hidden |
| LG 1024–1279 | heading plus copy; no horizontal shift | fixed 2x2; paired flips only | hidden |
| XL 1280–1535 | heading plus copy; line two shifts `104px` inward | lead back rises with heading, four-card deck, spread, overlapping flips, exit | visible |
| 2XL >=1536 | heading plus copy; line two shifts `144px` inward | wider bounded lead/deck/spread/flip/exit | visible |

Story travel:

```text
XS 520svh
SM 500svh
MD 400svh
LG 400svh
XL 560svh
2XL 580svh
```

## PC chronology

1. Heading begins clipped reveal.
2. The first back-facing card rises during the same entry window and settles
   approximately `20vh` below the heading composition.
3. The remaining backs reveal as a four-card deck.
4. Deck threshold triggers a time-based heading lift.
5. Deck spreads into four independent cards.
6. Cards flip right-to-left with `0.15` progress duration and `0.045` offsets,
   preserving roughly 30% temporal overlap.
7. The white SVG line grows and advances throughout the PC journey.
8. Cards rise and leave after flip completion.

## Tablet chronology

- Four decorative backs occupy a stable 2x2 arrangement.
- No lead card, deck, spread, or white line is used.
- The physical right pair flips together, followed by the left pair.
- Spatial geometry remains stable while only the card inner faces rotate.

## Phone chronology

- One centered card is visible at a time.
- Each card appears on its back, flips to its information front, then yields to
  the next card.
- No deck, spread, pair grid, horizontal heading shift, description, eyebrow, or
  white line is used.

## Locale and direction

- One DOM, controller, progress model, and physical card chronology serve ID,
  EN, and AR.
- Locale changes copy, font, `dir`, and natural alignment only.
- On PC, the second heading line moves toward the visual center: positive X in
  LTR and negative X in RTL.
- Arabic does not reverse time, card order, vertical scroll, or neutral Y-axis
  rotation.

## Ownership

| Concern | Owner |
|---|---|
| DOM/content | `resources/views/home/sections/school-values.blade.php` and current lang data |
| shell/sticky | `resources/css/surfaces/home/values/story-shell.css` |
| heading/copy | `resources/css/surfaces/home/values/story-heading.css` |
| tier adapters | `resources/css/surfaces/home/values/story-responsive.css` |
| cards/backs | `story-cards.css` and `story-card-back.css` |
| PC trail | `story-trail.css` |
| controller/lifecycle | `resources/js/surfaces/home/values/controller.js` |
| one-way heading state | `heading-state.js` |
| geometry/timeline | `layout.js` |
| scroll inertia | `motion.js` |
| style painting/cleanup | `paint.js` |
| focused DOM contract | `tests/Feature/HomeValuesStoryTest.php` |

## Browser, performance, and accessibility

- Capability tier 0 remains the semantic static front-card grid.
- Capability tier 1 uses CSS 3D plus one bounded RAF scheduler.
- Narrow widths avoid the SVG trail and desktop deck/spread writes.
- Frequent animation uses transform/opacity; the line uses SVG dash progress.
- Work pauses offscreen/hidden and removes listeners/classes/properties on dispose.
- Chromium and WebKit still require runtime proof for clipping, sticky,
  preserve-3d, backface visibility, viewport units, resize, reverse scroll, and
  BFCache.
- Reduced motion keeps all heading/copy/card content accessible without the
  sticky story.

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
