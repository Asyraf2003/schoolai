# UI/UX Engineering — Current State and Progress Ledger

Status: `BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-04
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
- Source implementation head:
  `f0dd63a11b33078058f7c2f81344f0c2b66fbd09`
- Current atomic result: centered PC card staging plus a continuous CSS 3D
  perspective chain for natural flip projection
- Protected: Hero, Vision/Mission, Programs, Gallery, Articles, navigation,
  footer, DB/admin/routes, About, and Testimonial

## Latest owner runtime feedback

The owner supplied SchoolAI and Lusion screenshots showing two remaining
mismatches in the prior implementation:

1. The lead stack and flip action remained visually submerged near the bottom of
   the sticky stage.
2. Edge-on SchoolAI cards looked like rectangles whose width was compressed,
   while the reference showed a perspective trapezoid with a larger near side
   and smaller far side.

The owner's road analogy is accurate: the prior result kept both road edges
nearly parallel, while a natural perspective projection converges toward a
vanishing point.

This runtime feedback is `FAIL` for source head
`3f2c8596ef37a61627098e87d4140e33ec9a5e9f`. Corrected runtime remains unproven.

## Root-cause FACT

### Low stage anchor

The prior PC centers were:

```text
lead:   86%
deck:   63%
active: 53.5%
```

At the current card height, the `86%` lead center placed a large portion of the
stack below the useful viewport plane. The subsequent movement therefore began
from the bottom instead of reading as a centered stage action.

### Flattened perspective chain

The sticky stage used:

```text
perspective: 1600px
```

That distance produced a relatively flat projection compared with the measured
reference perspective near `964px`.

The intermediate `.values-story__cards` owner did not explicitly preserve 3D.
The nested card inner owned `rotateY`, while translation and fan `rotateZ` lived
on outer wrappers. Without one continuous preserve-3d ancestry, the perceived
flip could collapse into uniform width compression instead of near/far side
foreshortening.

## Implemented source correction

### Vertical staging

`resources/js/surfaces/home/values/desktop-layout.js` now uses:

```text
lead center:   64%
deck center:   57%
active center: 53.5%
hidden offset: +10%
```

The lead remains lower than the final action plane but is fully visible. Deck
formation then rises through the center instead of emerging from the bottom.

Commit:

- `8d0f4ca25000dfafd4d13464e19b7abb948d72a3`
  `fix(values): raise desktop card staging`

### Shared perspective camera

`resources/css/surfaces/home/values/story-shell.css` now uses:

```text
perspective: 960px
perspective-origin: 50% 52%
transform-style: preserve-3d
```

Commit:

- `c4e9b3c88863cd6e1bfdfa119079e3c32ac516b2`
  `fix(values): tighten desktop card perspective`

### Continuous 3D ancestry

`resources/css/surfaces/home/values/story-cards.css` now preserves 3D through the
cards container, card, float wrapper, and inner flip plane. The inner transform
origin is explicitly centered.

Commit:

- `f0dd63a11b33078058f7c2f81344f0c2b66fbd09`
  `fix(values): preserve card depth through flip`

No Three.js, WebGL, canvas, dependency, model, shader, or additional renderer was
introduced. This remains semantic DOM plus CSS 3D transforms driven by the
existing Values controller.

## Unchanged contracts

- Heading entry, line-two PC shift, heading exit, and typography remain from the
  preceding accepted correction.
- Measured X/Y/`rotateZ`/`rotateY` curves and overlap timing remain unchanged.
- Fan tilt remains on the outer card while `rotateY` progresses on the inner
  plane.
- Phone remains one-card flip only.
- Tablet remains paired 2x2 flip only.
- White trail remains PC-only.
- Card content, Blade semantics, translations, Cairo ownership, navigation,
  other homepage sections, DB, admin, and routes were not changed.

## Source ownership

- Semantic cards: `resources/views/home/sections/school-values.blade.php`
- Heading CSS: `resources/css/surfaces/home/values/story-heading.css`
- Stage perspective: `resources/css/surfaces/home/values/story-shell.css`
- Card 3D chain: `resources/css/surfaces/home/values/story-cards.css`
- Controller/RAF: `resources/js/surfaces/home/values/controller.js`
- Shared timing: `resources/js/surfaces/home/values/layout.js`
- PC staging: `resources/js/surfaces/home/values/desktop-layout.js`
- Measured curves: `resources/js/surfaces/home/values/desktop-keyframes.js`
- Scroll inertia: `resources/js/surfaces/home/values/motion.js`

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Current main/source audit | `PASS_SOURCE` | mandatory docs and active owners inspected |
| Prior owner runtime | `FAIL` | low action plane and flat flip projection |
| Raised PC lead/deck plane | `IMPLEMENTED_SOURCE` | 64% -> 57% -> 53.5% centers |
| Reference-scale perspective | `IMPLEMENTED_SOURCE` | 960px shared stage camera |
| Continuous preserve-3d chain | `IMPLEMENTED_SOURCE` | stage through inner flip plane |
| No Three.js/WebGL expansion | `PASS_SOURCE` | no dependency or renderer changed |
| Phone/tablet chronology | `PASS_SOURCE` | algorithms unchanged |
| Arabic typography boundary | `PASS_SOURCE` | Cairo owner and locale DOM unchanged |
| Source line limit | `PASS_SOURCE` | changed source files remain under 200 lines |
| Scoped source mutation | `PASS_SOURCE` | only three Values owners changed |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repository command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| Corrected Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | fresh capture absent |
| WebKit/RTL/accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | matrix absent |

## STATUS

The bounded source correction is published. The PC card group now enters and
acts around the visual center, and the CSS 3D camera/ancestry is configured to
produce near/far side foreshortening rather than a uniformly squeezed rectangle.

Runtime and release status remain `BLOCKED_BY_MISSING_EVIDENCE`.

## NEXT VALID STEP

Pull current `main` and capture one XL/2XL forward sequence plus one reverse
sequence. Verify:

- the lead stack is fully visible around the middle plane;
- the deck rises rather than emerging from the bottom;
- edge-on cards form a perspective trapezoid;
- the near side appears larger than the far side;
- fan tilt survives into the overlapping flip;
- the final information row settles upright without a position jump.
