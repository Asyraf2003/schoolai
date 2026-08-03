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
- Current atomic result: true-thin Latin heading plus stable upright PC flip row
- Protected: Hero, Vision/Mission content/controller, Programs, Gallery,
  Articles, navigation, footer, DB/admin/routes, About, and Testimonial

## Latest owner-accepted direction

### Heading

- Preserve the clipped, slower, one-way heading reveal.
- The current Latin heading remains visibly too heavy compared with the accepted
  visual rhythm.
- Use the real minimum Inter variable weight rather than a merely lighter normal
  weight.
- Arabic remains governed by its own typography adapter and natural tracking.

### PC cards

- A deck may briefly spread as a fan.
- Before any information flip begins, all four cards must become upright,
  vertically aligned, and placed in their individual horizontal slots.
- Card depth order must not cross while the deck spreads; the visually leading
  card must not jump from one side to another for a frame.
- Flip still uses shared anticipation, overlapping right-to-left drive,
  overshoot, settle, and subtle float.
- Phone/tablet choreography and the PC-only trail boundary remain unchanged.

## Runtime feedback FACT

### Runtime 1–5

Earlier owner screenshots proved sticky containment, transition, heading reveal,
responsive choreography, card scale, and direct-flip defects. Those source
corrections remain published history.

### Runtime 6: latest owner screenshots

Nine new 1920x1080 Brave/Chromium screenshots prove:

- the SchoolAI heading is still materially thicker than the reference heading;
- while the back deck spreads, the visually leading card changes side for a
  fraction of the motion;
- cards retain fan `rotateZ` angles while their faces are flipping;
- front-facing cards remain at different fan angles and heights instead of
  occupying a clean upright row.

Runtime 6 is `FAIL` for the latest owner direction and is the evidence for this
bounded correction.

## Root-cause FACT

### Heading weight

`site-head-meta.blade.php` loads Inter as a Google variable font with the full
`100..900` weight range. The Values heading was set to `300`; this was a real
font weight, but it remained too heavy at the current display size.

### Depth-order jump

The previous PC geometry changed depth from:

```text
deck: -index * 22
row:   index * 2
```

Those values cross while interpolating. Browser 3D compositing therefore changes
which overlapping card is visually nearest during the spread.

### Persistent fan during flip

The previous row pose kept:

```text
rotateZ = [-4, -1.25, 1.25, 4]
y = base + edge offset
```

No upright pose existed between spread and flip. The cards were therefore doing
exactly what the source requested, unfortunately.

## Implemented source correction

Source head before this ledger update:
`5ba09b5c255aa0b89f042a82e25cc04236239b25`.

### Heading implementation

- Latin Values heading now uses `font-weight: 100`.
- `font-variation-settings: "wght" 100` explicitly selects the variable axis.
- `font-synthesis: none` prevents a synthetic replacement weight.
- Tracking relaxes from `-.065em` to `-.045em` so the thin display letters do
  not visually collapse into one dense mass.
- Arabic resets variation settings and keeps weight `300` plus natural tracking.

### PC geometry and flip implementation

The PC sequence is now:

```text
lead -> deck -> fan spread -> upright row -> anticipation -> flip -> settle
```

- `fanPose` preserves the intended temporary fan angles.
- `uprightPose` gives all cards the same Y position and `rotateZ(0deg)`.
- Upright alignment completes by progress `0.50`.
- Anticipation now starts at `0.50` and completes at `0.56`.
- The first drive begins only after the upright phase.
- Depth stays negative and ordered from deck through upright row:
  - deck/fan: `-index * 22`;
  - upright: `-index * 4`.
- Depth values no longer cross signs or reverse nearest-card order.
- Right-to-left starts use `0.045` offsets.
- Each drive lasts `0.17`; each settle completes over the following `0.08`.
- Exit waits until progress `0.955`, after the final front settle.
- Existing desktop float remains separate from scroll geometry and face rotation.

## Source ownership

- Blade/semantic cards: `resources/views/home/sections/school-values.blade.php`
- Heading treatment: `resources/css/surfaces/home/values/story-heading.css`
- PC geometry/flip: `resources/js/surfaces/home/values/desktop-layout.js`
- Shared painting: `resources/js/surfaces/home/values/paint.js`
- Controller/inertia: remaining Values JS modules
- Durable plan: active Values blueprint

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Current main/source audit | `PASS_SOURCE` | main and active owners fetched before write |
| Runtime 6 | `FAIL` | thick heading, depth swap, and fan-shaped flips |
| True Inter thin axis | `IMPLEMENTED_SOURCE` | weight and variation axis set to 100 |
| Arabic typography boundary | `IMPLEMENTED_SOURCE` | variation reset and weight 300 retained |
| Stable depth ordering | `IMPLEMENTED_SOURCE` | negative ordered Z values preserved |
| Fan-to-upright phase | `IMPLEMENTED_SOURCE` | dedicated upright pose before anticipation |
| Upright front settle | `IMPLEMENTED_SOURCE` | common Y and zero Z rotation |
| Phone/tablet boundary | `PASS_SOURCE` | their mode branches were not edited |
| JavaScript syntax | `PASS_LOCAL_PATCH` | desktop module passed `node --check` |
| Source line limit | `PASS_LOCAL_PATCH` | changed source files remain below 200 lines |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repo command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| Corrected Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | fresh sequence absent |
| WebKit/RTL/accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | matrix absent |

## STATUS

Runtime 6 failed. Source now uses the actual Inter thin axis and inserts a stable
upright row between fan spread and card flip. Depth ordering no longer crosses,
and information faces settle vertically aligned at zero fan angle. Runtime and
release status remain unproven.

## NEXT VALID STEP

Pull current `main` and capture 1920px PC frames covering: resolved heading,
fan spread, upright backs, first/middle/final flip, and all four upright fronts.
