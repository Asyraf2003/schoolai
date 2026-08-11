# G1 Blueprint — Program Formation → Character Color Handoff

BLUEPRINT ID: `G1-PROGRAM-FORMATION-HANDOFF-001`
STATUS: `OWNER-ACCEPTED / READY-FOR-SOURCE-AUDIT`
OWNER ACCEPTED: 2026-08-11
PARENT: `2026-08-11-release-readiness-program-values-hardening.md`
TARGET: `Asyraf2003/schoolai` `main`

## Goal

Turn the Program → Character/Values passage into a longer, deliberate scroll
choreography without changing the accepted Program card geometry, detail UI,
open/back interaction, Values card system, Values worm/line renderer, Gallery,
or Article.

The accepted visual narrative is:

`WHITE EMPTY FIELD → CARD 1 → CARD 2 → CARD 3 → CARD 4 → CARD 5 → CARD 6 → FULL PROGRAM HOLD → WHITE-TO-BLUE HANDOFF → PONDASI KARAKTER → VALUES`

The user should feel that the Program composition is formed progressively from
empty space, receives a short complete resting moment, and only then yields the
visual world to Character/Values.

## Protected scope

The following are protected and must not be redesigned merely to implement G1:

- existing Program card layout/geometry at each responsive tier;
- Program titles, descriptions, media, detail composition, Back control, and
  physical hit targets;
- Program open/detail/close GSAP behavior unless runtime evidence proves a
  bounded compatibility change is required;
- existing Program → Values sibling hit-layer fix;
- Pondasi Karakter copy and accepted directional/replay behavior;
- Values cards and Values worm/line renderer;
- Gallery and Article sections;
- Hero, Vision/Mission, About, Testimonial, and unrelated navigation.

## Accepted choreography

### Phase 0 — White empty field

After the Program heading establishes the section, Program owns a visually white
field. Before card formation begins, the card composition is visually absent.

Cards may remain in the semantic DOM and layout ownership. The implementation
must not remove them from accessibility/semantic ownership merely to create the
visual reveal.

The hidden visual state is conceptually:

- opacity at or near zero;
- a small positive block-axis offset so the card begins slightly below its final
  resting position;
- no large off-screen throw;
- no layout jump;
- no forced scroll position.

The exact offset is intentionally **not** hard-coded by this blueprint. It must
be chosen from source geometry and runtime proof for phone/tablet/desktop rather
than invented as one global pixel value.

### Phase 1 — Sequential card formation

Cards reveal one by one in semantic order as downward native scroll progress
advances.

Required behavior:

- card 1 becomes visible first;
- card 2 follows only after card 1 has meaningfully begun/established;
- continue sequentially through card 6;
- each card rises a short distance into its existing final layout position while
  opacity resolves to the accepted visible state;
- no row appears as one synchronized batch unless later runtime evidence proves
  one-by-one sequencing is harmful on a specific tier;
- the existing responsive grid remains unchanged: the reveal animates cards,
  not layout topology.

The implementation must use scroll progress/state ownership, not six long
independent time-based animations that continue after the user has reversed
scroll direction.

### Phase 2 — Accumulation and deterministic reverse

On downward progress the visible set accumulates:

`0 → 1 → 2 → 3 → 4 → 5 → 6`

A card that has formed remains formed while the user continues downward through
the remaining Program-formation range.

On reverse scroll the state unwinds deterministically:

`6 → 5 → 4 → 3 → 2 → 1 → 0`

No random replay, bounce, flicker, stale opacity, or multiple competing timelines
may own the same card state.

### Phase 3 — Full Program hold / breathing room

After card 6 is fully formed, all six cards remain complete on the white Program
field for a dedicated scroll interval.

This interval exists so the complete Program composition can actually be read
before the color handoff begins.

The background must **not** begin the blue transition immediately at the same
moment card 6 finishes.

The exact hold distance is not fixed by this blueprint. It must be tuned from
runtime evidence and remain proportional/comfortable across phone, tablet, and
desktop rather than being a brittle desktop-only number.

### Phase 4 — Continuous white → blue handoff

Only after the full Program hold does the shared visual world begin changing
from Program white to the accepted Character/Values blue, currently `#2038ff`.

The transition is continuous and scroll-progress driven.

Required behavior:

- white begins as the actual Program resting field;
- the visual world interpolates smoothly toward `#2038ff`;
- no stripe staircase;
- no sudden wipe;
- no flash to the body/default background;
- no uncontrolled overlay that can steal pointer events;
- no stale blue layer when reversing back into Program;
- rapid forward/reverse movement resolves to the current scroll target rather
  than finishing an obsolete animation.

Intermediate colors are implementation details. This blueprint does not approve
a fixed palette of intermediate stops; the source owner should use one coherent
continuous interpolation or equivalent single-owner mechanism.

### Phase 5 — Pondasi Karakter takeover

Pondasi Karakter receives the stage only after the Character/Values field has
meaningfully taken ownership of the background.

Its existing directional contract remains protected:

- downward first entry may reveal from below into position;
- upward return from below keeps the accepted visible/static behavior;
- returning far enough into Program may hide/re-arm it;
- a new downward entry may replay only according to the already accepted
  re-arm contract.

G1 must not introduce a second heading controller merely to synchronize color.

## Scroll-engine contract

G1 must preserve native scrolling.

Forbidden unless a separate owner decision is opened:

- wheel hijacking;
- `window.scrollTo` used to force Program landing;
- automatic snap;
- projected landing;
- anchor settling;
- one-wheel-notch-per-card behavior;
- click-to-frame behavior;
- long independent animations that keep running against reverse scroll.

Preferred model:

- document/native scroll supplies the target progress;
- one Program/transition owner maps target progress into card-formation and
  color-handoff states;
- visual current may smooth toward target using the existing project motion
  character where appropriate;
- reverse and rapid scroll remain deterministic.

## Responsive contract

The existing Program layout geometry is protected.

Current intent remains:

- phone: existing 2 + 2 + 2 composition;
- tablet: existing 3 + 3 composition;
- desktop: existing approved desktop composition.

Sequential reveal is card-by-card across these layouts. G1 does not redesign the
grid to make the animation easier.

Exact reveal offset, scroll span, and hold span may use tier-aware tokens if
runtime evidence shows one value cannot preserve the same perceived rhythm.

## Locale and direction contract

The same semantic Program DOM must support:

- Indonesian LTR;
- English LTR;
- Arabic RTL.

Card reveal order follows semantic Program order. Neutral formation motion is
block-axis based and must not be mechanically mirrored merely because Arabic is
RTL.

Directional text/alignment behavior remains owned by the existing locale system.

## Accessibility and reduced motion

Reduced motion must preserve the information hierarchy and white → blue state
ownership without requiring six pronounced moving reveals.

Acceptable reduced-motion behavior may shorten or remove translation while
retaining clear sequential/state progression and readability.

No card may become permanently inaccessible because its visual reveal failed,
JavaScript failed, or motion is reduced.

## FACT → GAP → implementation workflow

Before source mutation, perform a bounded source audit to identify:

1. current Program background owner;
2. current card visibility/animation owner;
3. current scroll progress owner;
4. existing responsive Program spacing/geometry owners;
5. Program → Values shared visual-world background owner;
6. Pondasi Karakter trigger/replay owner;
7. reduced-motion fallback owner;
8. tests that currently freeze approved Program behavior.

Do not choose CSS-only, GSAP-only, or a new controller architecture before that
audit proves which owner can implement G1 with the smallest surface area.

## Proof gates

Focused automated proof must demonstrate at minimum:

- six Program cards still exist semantically;
- card detail/open/back ownership is unchanged;
- no forbidden scroll forcing is introduced;
- Program/Values hit-layer protection remains present;
- build and relevant Program/Values tests pass.

Runtime proof must demonstrate:

1. initial white Program field;
2. sequential 1 → 6 downward formation;
3. complete six-card white hold;
4. smooth white → blue handoff;
5. Pondasi Karakter takeover;
6. deterministic reverse blue → white and 6 → 0 card unwind;
7. rapid forward/reverse recovery;
8. physical card open/detail/back click/tap around the transition;
9. no horizontal overflow or layout jump;
10. reduced-motion readable fallback.

Representative desktop proof is required before opening the full G2/G3 matrix.
G1 is not release-complete until G2/G3 later prove the accepted implementation
across phone/tablet/desktop, Chromium/Safari-WebKit evidence, ID/EN LTR, and AR
RTL.

## Completion definition

G1 may be marked PASS only when:

- the Program field begins/rests white;
- cards form one by one from a subtle below-position into the unchanged layout;
- all six receive a deliberate complete hold;
- color transition begins only after that hold;
- the shared field reaches the accepted Character/Values blue;
- Pondasi Karakter takes over without duplicate ownership;
- forward, reverse, rapid scroll, reduced motion, and Program physical controls
  remain correct;
- no protected surface was redesigned as collateral damage.

## NEXT VALID STEP

Read-only source audit of the current Program → Values owners listed above.
Do not edit production source until that audit identifies the smallest existing
owner for sequential formation and the white-to-blue handoff.
