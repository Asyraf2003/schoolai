# UI/UX Engineering — Current State and Progress Ledger

Status: `BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-03-home-values-card-story.md`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VALUES-001`
- State: `IMPLEMENTING`
- Surface: homepage Values section `#nilai`
- Source head after bounded implementation:
  `0db23a9a450b94bcabdd5261bade505be3098767`
- Current atomic result: time-triggered heading entry plus measured PC
  lead/deck/fan/flip/settle choreography
- Protected: Hero, Vision/Mission, Programs, Gallery, Articles, navigation,
  footer, DB/admin/routes, About, and Testimonial

## Latest owner-accepted direction

### Heading

- The heading reveal is an animation triggered by entering the section from
  above, not a direct scroll-progress effect.
- Both clipped lines travel from opposite vertical directions toward their
  complete forms over a deliberately slow, smooth entry.
- After reveal, line two shifts toward visual center on PC only.
- Entering backward from the following section keeps the heading statically
  revealed; it does not replay the forward entry.
- When the four-card deck begins to form, the PC heading starts its
  scroll-driven upward exit.
- Description/eyebrow remain available on tablet and PC and hidden on phone.

### Cards

- The first card begins below the heading rather than intersecting it.
- The deck rises toward a shared stage center while the heading exits.
- Fan geometry preserves outward left/right `rotateZ`.
- Cards straighten during their overlapping flips; there is no separate
  generic upright row before the first flip.
- Per-card X, Y, `rotateZ`, and `rotateY` follow the owner-supplied measured
  samples.
- The negative `rotateY` values act as overshoot before the final all-front
  `0deg` settle.
- Phone remains one card at a time; tablet remains a stable paired 2x2
  sequence; the white trail remains PC-only.

## Runtime feedback FACT

### Runtime 8: latest owner screenshots and measurements

The owner supplied SchoolAI/reference screenshots plus six transform samples.
They prove the intended chronology and expose these mismatches in the prior
source:

- heading reveal was still derived from scroll progress rather than running as
  its own section-entry animation;
- the initial card stack occupied an arbitrary Y offset and did not preserve the
  intended distance below the heading;
- the generic `fan -> upright -> delayed flip` sequence removed fan tilt before
  the flip, while the reference straightens during the flip;
- the generic flip formula could not reproduce the measured overlap, overshoot,
  or per-card orientation;
- the card group needed to remain centered throughout the active choreography.

Runtime 8 is `FAIL` for the old implementation and is the evidence for this
bounded correction.

## Root-cause FACT

### Scroll-scrubbed heading entry

`heading-state.js` previously calculated reveal from:

```text
phase(targetProgress, revealStart, revealEnd)
```

Stopping scroll therefore stopped the heading, even though the owner defined it
as an automatic entrance animation.

### Arbitrary card-top geometry

The previous desktop layout used fixed viewport fractions directly as card-top
positions:

```text
lead/deck: viewportHeight * 0.20
fan/upright: viewportHeight * 0.10
```

Those values ignored card height and could not express “lead below heading, then
lock the active deck near stage center.”

### Generic flip model

The previous choreography forced every card through the same formula:

```text
fan rotateZ
-> separate upright rotateZ(0)
-> shared anticipation
-> per-index delayed rotateY
```

The supplied data instead shows `rotateZ` converging toward zero while each
card's `rotateY` follows a distinct overlapping trajectory.

## Implemented source correction

Source head before this batch:
`e23d1f220ca76aa8782bb6c5cfd8f3b2aa4ba298`.

### Independent heading entry

- `heading-state.js` now owns `idle -> revealing -> revealed`.
- Forward entry begins when the section crosses a 72% viewport trigger.
- Reveal runs for `1600ms` with eased frame progression.
- The controller keeps the existing RAF active until both scroll motion and the
  heading reveal settle.
- Reverse entry resolves instantly to the static revealed state.
- Scrolling completely above the trigger resets the next forward entry.
- PC line-two shift is applied only after reveal completion.

### Center-derived desktop staging

- Lead, deck, and final row use card-centered stage anchors.
- Lead center is 78% of stage height, deck center is 60%, and active/final center
  is 56%.
- Card half-height is subtracted so transforms describe actual card top.
- Deck formation begins with the PC heading exit at progress `0.18`.
- The active group receives one shared momentum offset only.

### Measured keyframe choreography

- Added `desktop-keyframes.js` as the sole owner of measured PC transform data.
- Raw X is normalized into one shared center-relative row that adapts to card
  width and available viewport width.
- Y samples are scaled narrowly from the measured 400px card basis.
- `rotateZ` follows the measured fan-to-zero path.
- `rotateY` adds an all-back start, follows the six supplied per-card samples,
  preserves negative overshoot, and finishes at a stable all-front row.
- Piecewise eased interpolation replaces the generic flip-delay formula.
- Fixed z-order and the shared coordinate plane remain unchanged.

### Story timing

- PC heading exit now runs from progress `0.18–0.36`.
- Deck forms during `0.18–0.29`.
- Fan forms during `0.28–0.38`.
- Measured transform travel runs from progress `0.38–0.88`.
- Trail start was aligned with card-story activation instead of appearing near
  the beginning of the section.

## Source ownership

- Semantic cards: `resources/views/home/sections/school-values.blade.php`
- Heading CSS: `resources/css/surfaces/home/values/story-heading.css`
- Card CSS: `resources/css/surfaces/home/values/story-cards.css`
- Entry state: `resources/js/surfaces/home/values/heading-state.js`
- Controller/RAF: `resources/js/surfaces/home/values/controller.js`
- Shared mode timing: `resources/js/surfaces/home/values/layout.js`
- Desktop staging: `resources/js/surfaces/home/values/desktop-layout.js`
- Measured transforms: `resources/js/surfaces/home/values/desktop-keyframes.js`
- Inertia: `resources/js/surfaces/home/values/motion.js`

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Current main/source audit | `PASS_SOURCE` | mandatory docs and active Values owners inspected |
| Runtime 8 prior implementation | `FAIL` | owner screenshots and measured transforms |
| Time-triggered heading entry | `IMPLEMENTED_SOURCE` | explicit heading state and 1600ms timeline |
| Reverse static heading | `IMPLEMENTED_SOURCE` | non-forward entry resolves revealed/instant |
| Center-derived card Y | `IMPLEMENTED_SOURCE` | card height included in lead/deck/final anchors |
| Measured per-card transforms | `IMPLEMENTED_SOURCE` | dedicated keyframe owner added |
| Fan-to-upright during flip | `IMPLEMENTED_SOURCE` | rotateZ and rotateY interpolate together |
| Stable final front row | `IMPLEMENTED_SOURCE` | final sample is all-front and centered |
| Phone/tablet contracts | `PASS_SOURCE` | their algorithms remain unchanged |
| Arabic typography boundary | `PASS_SOURCE` | Cairo owner and locale DOM unchanged |
| Changed JavaScript syntax | `PASS_LOCAL_PATCH` | five changed/added modules passed `node --check` |
| Transform sample inspection | `PASS_LOCAL_PATCH` | representative progress samples produced ordered centered poses |
| Source line limit | `PASS_LOCAL_PATCH` | controller 178; heading 93; desktop 93; keyframes 92; layout 106 |
| Scoped compare | `PASS_SOURCE` | source diff contains only five Values JS files |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repository command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| Corrected Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | fresh forward/reverse sequence absent |
| WebKit/RTL/accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | matrix absent |

## STATUS

The source now separates heading entry time from scroll, aligns heading exit with
deck formation, derives card height from the stage, and drives PC fan/flip/settle
from the supplied measured transforms. Runtime and release status remain
unproven.

## NEXT VALID STEP

Pull current `main` and capture one 1920px Brave/Chromium forward-and-reverse
sequence covering: automatic heading reveal while scroll is stopped, PC line-two
shift, lower lead card, centered deck/fan, all four measured flip stages, stable
front row, and backward entry from the following section.
