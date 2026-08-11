# G1 Blueprint — Program Formation → Character Color Handoff

BLUEPRINT ID: `G1-PROGRAM-FORMATION-HANDOFF-001`
STATUS: `IMPLEMENTED / RUNTIME-PROOF-OPEN`
OWNER ACCEPTED: 2026-08-11
UPDATED: 2026-08-12
PARENT: `2026-08-11-release-readiness-program-values-hardening.md`
TARGET: `Asyraf2003/schoolai` `main`
IMPLEMENTATION HEAD BEFORE DOCS SYNC: `5830576134ab69904800f6956dbe4be79f03c86e`

## Goal

Turn the Program → Character/Values passage into a longer, deliberate scroll
choreography without redesigning the accepted Program card geometry, detail UI,
open/back interaction, Values card system, Values worm/line renderer, Gallery,
or Article.

Accepted visual narrative:

`WHITE EMPTY FIELD → CARD 1 → CARD 2 → CARD 3 → CARD 4 → CARD 5 → CARD 6 → FULL PROGRAM HOLD → WHITE-TO-BLUE HANDOFF → PONDASI KARAKTER → VALUES`

## Protected scope

The following remain protected unless the owner explicitly opens a new bounded
scope:

- Program card layout/geometry at every responsive tier;
- Program copy, media, detail composition, Back control, and hit targets;
- Program open/detail/close GSAP behavior;
- Program → Values sibling hit-layer protection;
- Pondasi Karakter copy and directional/replay behavior;
- Values fan/flip/card choreography except bounded entry-position tuning;
- Values worm/line/spatial renderer;
- Gallery and Article;
- Hero, Vision/Mission, About, Testimonial, and unrelated navigation.

## Accepted choreography

### Phase 0 — White Program field

Program begins on a white visual field. Cards may remain in the semantic DOM but
start visually absent with a small below-position. No large off-screen throw,
layout jump, or forced document scroll is allowed.

### Phase 1 — Sequential formation

Cards reveal one by one in semantic order while native scroll advances:

`0 → 1 → 2 → 3 → 4 → 5 → 6`

Each card resolves into the existing layout. The grid itself is not animated or
restructured merely to create the reveal.

### Phase 2 — Deterministic reverse

Reverse scroll unwinds the same state:

`6 → 5 → 4 → 3 → 2 → 1 → 0`

No random replay, stale opacity, timer-driven animation against reverse scroll,
or duplicate card-motion owner is allowed.

### Phase 3 — Full Program hold

After card 6 is complete, all six cards remain readable on white for a deliberate
breathing interval. Blue must not begin at the same instant card 6 finishes.

### Phase 4 — Continuous white → blue handoff

After the hold, the shared Program/Values visual world transitions continuously
toward Character/Values blue `#2038ff`.

No stripe staircase, sudden wipe, body-background flash, stale blue overlay, or
pointer-stealing transition layer is allowed.

### Phase 5 — Pondasi Karakter takeover

Pondasi Karakter receives the stage after Character/Values visual ownership is
established. Its existing directional/replay controller remains the sole heading
owner.

## Scroll-engine contract

Native document scrolling remains the source/target.

Forbidden without a new owner decision:

- wheel hijacking;
- `window.scrollTo` used to force landing;
- automatic snap;
- projected landing;
- anchor settling;
- one-wheel-notch-per-card behavior;
- click-to-frame behavior;
- RAF writes to document scroll;
- long independent reveal animations that keep running against reverse scroll.

Preferred ownership remains:

`Program formation owner → shared color-world owner → existing Values story owner`

## Implemented architecture

The source audit selected these existing/smallest owners:

- Program formation/visibility:
  `resources/js/surfaces/home/program-journey/formation.js`;
- Program open/detail/back: existing Program journey controller/integration;
- Program responsive geometry: existing Program journey CSS;
- shared white → blue field:
  `resources/js/surfaces/home/program-values-world.js` and
  `resources/css/surfaces/home/values/story-kinetic.css`;
- Pondasi heading replay: `values/heading-state.js`;
- Values story smoothing/progress: existing Values motion/controller;
- Values card choreography: `values/desktop-layout.js` +
  `values/desktop-keyframes.js`;
- Values worm/line/spatial renderer: existing Values spatial ownership.

G1 did not add a third transition section or duplicate Values controller.

## Current implementation facts

At source head `5830576134ab...`:

- Program formation is implemented as a dedicated concern;
- shared world starts white and transitions toward `#2038ff`;
- Program formation does not force document scroll;
- the owner has reported the color transition visually OK;
- Values card motion was restored after a spacing regression and the owner
  reported the original desired animation behavior returned;
- current desktop entry tuning changes only the center-relative Y pose ratios:
  - `hiddenPose = center.y + geometry.cardHeight * 0.012`;
  - `deckPose = center.y + geometry.cardHeight * 0.008 + index * 2`.

These ratios are visual tuning values, not a literal physical-pixel guarantee.

## Values entry-spacing regression record

The initial request was only to bring the Values entry deck closer to the Pondasi
heading. Two early ratio-only changes preserved the accepted animation.

Regression began when spacing was implemented by changing the coordinate system
rather than the local pose offset:

1. heading DOM geometry was measured into `headingTitleBottomOffset`;
2. `titleDeckOffset()` replaced center-relative card positions with
   heading/document-relative positions;
3. a later `centerRelease()` read raw `window.scrollY` while the existing Values
   engine still used smoothed story progress.

This created competing coordinate/progress ownership and visibly degraded the
card path.

The recovery restored `desktop-layout.js` and `geometry.js` to the
last-known-good choreography baseline from
`649b5d8d00ca0ed27df1cc3ca41ee43d60a903f2`, then resumed only bounded ratio
tuning.

### Permanent guardrail

A request to change only Values entry spacing must not:

- introduce heading measurements into card choreography;
- replace center-relative card poses with document-relative poses;
- add raw-scroll motion ownership alongside smoothed story progress;
- add release/capture logic;
- change fan/flip phases, spring/momentum, or center destination.

Use the smallest local pose adjustment unless the owner explicitly asks to
redesign motion.

## Responsive and locale contract

Existing Program topology remains protected:

- phone: 2 + 2 + 2;
- tablet: 3 + 3;
- desktop: existing approved composition.

The same semantic DOM supports Indonesian LTR, English LTR, and Arabic RTL.
Neutral formation motion is block-axis based and is not mechanically mirrored
for RTL.

## Accessibility and reduced motion

Reduced-motion mode must preserve content readability and white → blue state
ownership without requiring pronounced sequential translation. JavaScript or
motion failure must not permanently hide Program content.

## Proof still required

G1 is implemented but not PASS. Before closing it, record one frozen source head
and prove:

1. fresh `git diff --check`;
2. structure check and build;
3. focused Program/Values tests;
4. full PHP suite with `0 failed`;
5. initial white field;
6. Program card formation 1 → 6;
7. complete six-card white hold;
8. white → blue handoff and blue → white reverse;
9. Pondasi takeover;
10. rapid down/up/down recovery;
11. physical Program open/detail/back around the handoff;
12. no horizontal overflow/layout jump;
13. reduced-motion readability;
14. final owner confirmation that the current tight Values entry position keeps
    the restored animation intact.

## Values worm/line boundary

Values worm/line/spatial is intentionally protected by G1. It is not an implicit
remaining item required to call Program formation complete.

If the owner wants line/worm visual polish, open a separate bounded Values
spatial-polish scope after G1 is frozen. That work must begin with its own
FACT/GAP/GOAL and must not reopen Program formation or Values card choreography
without evidence.

## Completion definition

G1 becomes PASS only when the bounded implementation is frozen and the automated
plus representative runtime proof above is recorded. Full responsive/browser
and locale coverage remains G2/G3 and is not implied by G1 PASS.

## NEXT VALID STEP

Freeze visual implementation changes and run the current-head G1 proof bundle.
Only after that proof should the owner either:

1. open a bounded Values line/worm polish scope, if still desired; or
2. proceed directly to G2/G3 responsive/browser/locale proof.
