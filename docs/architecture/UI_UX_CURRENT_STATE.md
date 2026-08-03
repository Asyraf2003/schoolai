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
- Current atomic result: shared enhanced-card origin, coherent PC motion plane,
  first-to-fourth flip order, stable final row, and rebalanced Latin heading
- Protected: Hero, Vision/Mission, Programs, Gallery, Articles, navigation,
  footer, DB/admin/routes, About, and Testimonial

## Latest owner-accepted direction

### Heading

- Preserve the slow clipped reveal, second-line shift, and scroll-driven PC exit.
- Weight `300` was too heavy, but the later weight `100` became visibly hairline.
- Use a genuinely light editorial weight between those extremes.
- Arabic remains governed by Cairo, natural tracking, and its current adapter.

### Cards

- All enhanced card geometry must be calculated from one shared stage center.
- The temporary deck and fan may overlap, but cards must straighten before flip.
- Scroll momentum may move the group slightly; it must not create a vertical
  staircase by multiplying movement per card.
- Flip order follows the visual cards from first to fourth with overlapping
  anticipation, drive, overshoot, and settle.
- After all information fronts appear, the row stays coherent until the sticky
  section releases naturally.
- Phone remains one card at a time; tablet remains a stable 2x2 pair sequence.
- The white trail remains PC-only.

## Runtime feedback FACT

### Runtime 1–6

Earlier owner screenshots proved and drove corrections for sticky containment,
transition, clipped heading entry, responsive chronology, card scale, face
orientation, depth crossing, and missing upright staging.

### Runtime 7: latest owner comparison sequence

Ten new 1920x1080 Brave/Chromium screenshots compare SchoolAI against the
reference chronology and prove:

- the Latin heading at weight `100` is much thinner than the intended reference;
- the enhanced card container still contributes four grid-column origins while
  JavaScript also applies center-relative X offsets;
- cards therefore do not behave as one spatial group;
- the current flip order is fourth-to-first, while the supplied reference
  sequence and owner chronology proceed first-to-fourth;
- per-card momentum produces different vertical displacement for every card;
- the exit phase creates a large diagonal staircase after fronts are visible;
- the final information row is not held as one stable composition.

Runtime 7 is `FAIL` and is the evidence for this bounded correction.

## Root-cause FACT

### Double horizontal origin

Desktop CSS retained:

```text
display: grid
four desktop columns
```

At the same time, `desktop-layout.js` calculated every X pose as if all cards
started from one center point. Natural grid placement and animated translation
were therefore added together.

### Per-card vertical momentum

The previous desktop frame used:

```text
current.y += momentum * (index + 1) * 5
```

The fourth card could receive four times the vertical displacement of the first.
That directly produced the diagonal staircase visible during fast scroll.

### Reversed flip order

The previous start formula was:

```text
0.56 + (3 - index) * 0.045
```

That starts card four first. The owner sequence requires card one first.

### Artificial exit fan

The previous `exitPose` assigned different vertical destinations and staggered
starts to each card. No accepted owner direction required that breakup.

## Implemented source correction

Source head before this ledger update:
`60436413ec36bbd7216bd37e0c4b58f5f0b0fe05`.

### Shared coordinate plane

- In enhanced mode, `.values-story__cards` is now one absolute stage layer.
- Every enhanced card is absolutely anchored at physical center `left: 50%`.
- Card translation uses `calc(-50% + var(--values-x))`.
- Lead, deck, fan, upright row, phone, and tablet poses now resolve from the
  coordinate origin their JavaScript geometry already assumed.
- Static/no-JS/reduced-motion layout remains the original semantic CSS grid.

### Coherent PC card motion

- The temporary fan still resolves into a common upright row by progress `0.50`.
- Momentum is now one shared `momentum * 6` offset for all four cards.
- Independent CSS float remains limited to `-4px..4px` and does not alter the
  scroll geometry.
- Flip start is now `0.56 + index * 0.045`, producing card one through card four.
- Drive remains overlapping, with shared anticipation and front overshoot.
- The separate indexed exit pose was removed.
- Final fronts hold their upright slots until normal sticky release.

### Heading balance

- ID/EN Values heading now uses Inter variable weight `200`.
- `font-synthesis: none` remains active.
- Tracking is `-.04em`, between the former dense heavy state and the hairline
  state.
- Arabic keeps weight `300`, normal variation settings, and natural tracking.

## Source ownership

- Semantic cards: `resources/views/home/sections/school-values.blade.php`
- Card layout/treatment: `resources/css/surfaces/home/values/story-cards.css`
- Heading treatment: `resources/css/surfaces/home/values/story-heading.css`
- PC geometry/flip: `resources/js/surfaces/home/values/desktop-layout.js`
- Shared painting/controller/inertia: remaining Values JS modules
- Durable plan: active Values blueprint

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Current main/source audit | `PASS_SOURCE` | active owners and mandatory docs inspected |
| Runtime 7 | `FAIL` | hairline heading and fragmented card plane |
| Shared enhanced-card origin | `IMPLEMENTED_SOURCE` | absolute center anchor replaces dual origins |
| Group momentum | `IMPLEMENTED_SOURCE` | one common Y offset replaces indexed multiplier |
| First-to-fourth flip | `IMPLEMENTED_SOURCE` | start formula uses `index` |
| Stable final row | `IMPLEMENTED_SOURCE` | indexed exit pose removed |
| Latin heading balance | `IMPLEMENTED_SOURCE` | Inter axis set to 200 |
| Arabic typography boundary | `PASS_SOURCE` | Cairo owner retained |
| Phone/tablet chronology | `PASS_SOURCE` | mode algorithms unchanged |
| JavaScript syntax | `PASS_LOCAL_PATCH` | changed desktop module passed `node --check` |
| CSS brace balance | `PASS_LOCAL_PATCH` | changed card CSS balance is zero |
| Source line limit | `PASS_LOCAL_PATCH` | desktop JS 102 lines; card CSS 187 lines |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repo command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| Corrected Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | fresh sequence absent |
| WebKit/RTL/accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | matrix absent |

## STATUS

Runtime 7 failed. Source now gives every enhanced card one center-relative motion
plane, uses shared momentum, flips first-to-fourth, holds the final row, and
raises the Latin heading from hairline weight `100` to editorial weight `200`.
Runtime and release status remain unproven.

## NEXT VALID STEP

Pull current `main` and capture one 1920px Brave/Chromium sequence covering:
heading, centered lead/deck, fan, upright backs, card-one/middle/card-four flip,
and the stable four-front row during continued scroll.
