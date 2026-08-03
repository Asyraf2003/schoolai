# UI/UX Engineering — Current State and Progress Ledger

Status: `BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-03-home-values-card-story.md`
Raw evidence: `measurements/2026-08-03-home-values-reference-motion-raw.md`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VALUES-001`
- State: `IMPLEMENTING`
- Surface: homepage Values section `#nilai`
- Raw evidence commit: `75138335c381535e7f942a77210a2f34988cccc0`
- Source implementation head: `f48d9a9a3b1e93b72944a33616b3328d3c575f64`
- Blueprint ledger head: `afd91c5ebd7321a736d02f222587c65266e90935`
- Current atomic result: earlier heading entry, rebalanced heading weight,
  raised PC staging, and continuous measured card interpolation
- Protected: Hero, Vision/Mission, Programs, Gallery, Articles, navigation,
  footer, DB/admin/routes, About, and Testimonial

## Latest owner evidence and direction

### Evidence packet

The owner supplied:

- normal downward reference recording, 1280x720, 4.156 seconds;
- normal upward reference recording, 1280x720, 1.648 seconds;
- fast downward reference recording, 1280x720, 0.720 seconds;
- computed heading word transforms at 90%, 50%, and 0% travel;
- reference card hierarchy, perspective, transform origins, and base geometry;
- five `matrix3d()` card snapshots;
- projected front-face bounds across stack, edge-on, front, and overshoot;
- six four-card X/Y/`rotateZ`/`rotateY` samples.

The full unnormalized dataset is preserved in the raw evidence document.

### Heading direction

- Trigger the clipped heading reveal when the section first enters from above.
- Do not require continued scroll for the entry animation.
- Start earlier than the prior 72% viewport threshold.
- Make the current Latin weight `200` slightly thicker while remaining much
  lighter than the rejected heavy state.
- Keep a narrow seam between the two clipped lines.
- Make the reveal somewhat faster without becoming abrupt.
- Keep PC-only line-two shift and scroll-driven heading exit.
- Reverse entry from the following section remains statically revealed.

### Card direction

- The lead stack may begin low, but it must visibly rise into the center plane.
- Heading exit begins with deck formation.
- Fan tilt stays active while flips begin.
- Cards move from fan tilt toward upright during the flip itself.
- First-to-fourth flips overlap.
- Sample points guide one continuous path; they must not become mechanical stop
  points.
- Negative `rotateY` values remain bounded front-side overshoot.
- Final cards hold one stable upright information row.
- Phone/tablet behavior and PC-only trail contracts remain unchanged.

## Runtime feedback FACT

### Runtime 9: owner recordings plus computed styles

The new recordings prove that the reference motion remains continuous under
normal downward, reverse, and fast downward input. The reference compresses or
reverses one spatial path rather than switching layouts.

Owner screenshots of the previous SchoolAI source still showed:

- title entry beginning too late and appearing too hairline;
- insufficient visible title seam;
- lead/deck motion staying visually submerged too long;
- card movement feeling mechanical despite matching captured sample values.

Runtime 9 is `FAIL` for the prior SchoolAI implementation and is the evidence for
this correction. Corrected runtime remains unproven.

## Root-cause FACT

### Mechanical sample interpolation

The previous `desktop-keyframes.js` applied `smooth()` independently inside
every sample interval. Smoothstep reaches zero velocity at both ends of each
interval. Every captured reference sample therefore became a tiny artificial
rest point:

```text
sample 1 -> stop -> sample 2 -> stop -> sample 3 -> stop
```

The recordings instead show continuous travel through those samples.

### Late heading entry

The prior heading threshold was `72%` of viewport height and duration was
`1600ms`. That delayed the start after the blue section was already visible and
made the owner wait before the full title resolved.

### Compressed vertical staging

Prior card centers were:

```text
lead 78%
deck 60%
active 56%
```

The distance was too subtle at the current card scale. The lead looked low but
the active deck did not rise enough to read as a deliberate center lock.

## Implemented source correction

### Heading

- Entry trigger moved from `72%` to `94%` viewport height.
- Reveal duration changed from `1600ms` to `1200ms`.
- Entry uses cubic ease-out instead of symmetric smoothstep.
- ID/EN Inter variable weight changed from `200` to `260`.
- A small `.018em` row seam separates the two title masks.
- Line-two PC shift transition changed to `1050ms` with a soft ease-out curve.
- Initial clipped travel is approximately one line height (`103%`).
- Cairo/RTL typography ownership remains unchanged.

### Card staging

- PC lead center changed to `86%` stage height.
- Deck center changed to `63%`.
- Active/final center changed to `53.5%`.
- Lead reveal now runs at progress `0.03–0.12`.
- Deck formation and heading exit begin together at progress `0.12`.
- Fan formation runs at `0.22–0.34`.
- Measured travel runs at `0.34–0.86` and then holds.

### Continuous measured curves

- Piecewise smoothstep interpolation was replaced by cubic Hermite
  interpolation.
- Finite-difference tangents provide a continuous first derivative across
  irregular sample times.
- Tangents are bounded with scale `0.72` to avoid uncontrolled overshoot.
- X, Y, `rotateZ`, and `rotateY` share the same continuous sampling model.
- `rotateY` remains clamped to `-24deg..180deg`.
- Existing scroll inertia, shared momentum, fixed z-order, and CSS float remain.

## Source ownership

- Semantic cards: `resources/views/home/sections/school-values.blade.php`
- Heading CSS: `resources/css/surfaces/home/values/story-heading.css`
- Card CSS: `resources/css/surfaces/home/values/story-cards.css`
- Entry state: `resources/js/surfaces/home/values/heading-state.js`
- Controller/RAF: `resources/js/surfaces/home/values/controller.js`
- Shared mode timing: `resources/js/surfaces/home/values/layout.js`
- Desktop staging: `resources/js/surfaces/home/values/desktop-layout.js`
- Measured curves: `resources/js/surfaces/home/values/desktop-keyframes.js`
- Scroll inertia: `resources/js/surfaces/home/values/motion.js`

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Current main/source audit | `PASS_SOURCE` | mandatory docs and active owners inspected |
| Raw reference evidence | `PASS_SOURCE` | recordings and computed data preserved before code |
| Runtime 9 prior implementation | `FAIL` | owner screenshots/recordings |
| Earlier automatic heading entry | `IMPLEMENTED_SOURCE` | 94% trigger and independent 1200ms state |
| Heading weight/seam | `IMPLEMENTED_SOURCE` | Inter 260 and clipped row seam |
| Raised PC stage travel | `IMPLEMENTED_SOURCE` | 86% -> 63% -> 53.5% centers |
| Continuous sample velocity | `IMPLEMENTED_SOURCE` | cubic Hermite finite-difference curves |
| Fan-to-upright during flip | `IMPLEMENTED_SOURCE` | rotateZ and rotateY remain coupled |
| Stable final front row | `IMPLEMENTED_SOURCE` | final sample and hold remain all-front |
| Phone/tablet contracts | `PASS_SOURCE` | their algorithms were not changed |
| Arabic typography boundary | `PASS_SOURCE` | Cairo owner and locale DOM unchanged |
| Changed JavaScript syntax | `PASS_LOCAL_PATCH` | four changed JS files passed `node --check` |
| Source line limit | `PASS_LOCAL_PATCH` | changed source files remain below 200 lines |
| Scoped compare | `PASS_SOURCE` | only five Values source owners changed |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repository command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| Corrected Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | fresh capture absent |
| WebKit/RTL/accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | matrix absent |

## STATUS

Raw reference evidence is durable. Source now starts the heading earlier, makes
it slightly heavier and faster, raises the desktop card plane more visibly, and
removes the zero-velocity pause at every measured sample. Runtime and release
status remain unproven.

## NEXT VALID STEP

Pull current `main` and capture one 1920px Brave/Chromium forward sequence plus
one reverse sequence covering: immediate title entry, center seam, PC line-two
shift, low lead stack, visible rise to center, fan, edge-on overlap, overshoot,
stable four-front row, and fast scroll continuity.
