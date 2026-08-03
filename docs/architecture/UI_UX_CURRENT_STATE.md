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
- Current atomic result: clipped one-way heading plus tier-specific card story
- Protected: Hero, Vision/Mission content/controller, Programs, Gallery,
  Articles, navigation, footer, DB/admin/routes, About, and Testimonial

## Latest owner-accepted direction

### Heading and copy

- Heading must emerge edge-first from one shared center seam, like a ruler
  sliding out of a pencil case; a fully visible word block must not translate.
- The reveal runs only when entering from the preceding section.
- Reverse scroll from later Values states keeps the resolved heading static.
- If forward reveal is interrupted and reversed, the heading resolves fully
  instead of remaining partially clipped.
- The second line shifts inward through a time-based CSS transition, not scrub.
- PC deck onset triggers a time-based heading lift.
- XS and SM hide description and eyebrow; MD through 2XL show them.

### Cards and trail

- Enhanced cards begin with their decorative backs visible and flip to their
  information fronts.
- XS and SM show one centered card at a time and use flip-only choreography.
- MD and LG keep a stable 2x2 layout and flip two cards as a pair.
- XL and 2XL use lead rise, four-card deck, spread, overlapping flips, and exit.
- The white scroll-drawn line is active only on XL and 2XL.
- ID/EN/AR share one DOM/controller and physical chronology.

## Runtime feedback FACT

### Runtime 1: sticky failure

Owner screenshots at 1920x1080 Brave/Chromium proved the stage travelled under
the navbar and left long empty blue travel. The sticky containing-block defect
was corrected.

### Runtime 2: chronology and card-face failure

Fresh screenshots proved sticky containment worked, but chronology, mirrored
front faces, static line treatment, and Mission-to-Values transition still
failed. The bounded face/timeline/trail/transition correction was published.

### Runtime 3: heading travel failure

Screenshots proved the whole heading bodies remained visible while translating,
creating crossed and ghosted text. The first heading correction reduced travel
but still used whole-line transforms and therefore failed the requested reveal
material.

### Runtime 4: latest owner screenshot

The latest 1920x1080 screenshot proves:

- both complete word bodies are visible during entry;
- the result does not resemble edge-first extraction through a seam;
- reverse behavior still needs a latched resolved heading contract;
- the responsive card story must diverge between phone, tablet, and PC;
- cards must start on their backs;
- stack/spread and the moving line must be PC-only.

This runtime is `FAIL` for the corrected owner direction. It is the evidence for
the current source rewrite.

## Implemented source correction

Source head before this ledger update:
`07d757371ce8f214257cd52fd493f63cdaf50550`.

### Heading implementation

- Each title line now contains a nested `.values-story__title-text` span.
- The line boxes clip their children.
- Line one begins `108%` below its box and line two begins `108%` above its box,
  so only the leading edge becomes visible before the rest of the glyph body.
- `heading-state.js` owns one-way reveal state separately from scroll inertia.
- Reveal advances only on forward entry and latches at completion.
- Reverse during partial reveal resolves the full static heading.
- Leaving above Values resets the state for a future forward entry.
- Wide second-line shift and heading lift are class-triggered CSS transitions,
  not continuous scroll interpolation.
- RTL uses the opposite X sign toward the same visual center.

### Responsive card implementation

| Tier | Mode | Story travel |
|---|---|---:|
| XS 360–639 | one card at a time, back-to-front flip only | `520svh` |
| SM 640–767 | one card at a time, back-to-front flip only | `500svh` |
| MD 768–1023 | stable 2x2, paired flips | `400svh` |
| LG 1024–1279 | stable 2x2, paired flips | `400svh` |
| XL 1280–1535 | lead/deck/spread/overlap/exit | `560svh` |
| 2XL >=1536 | wider lead/deck/spread/overlap/exit | `580svh` |

- All enhanced cards begin at `rotateY(180deg)`, exposing the decorative back.
- Phone slots reveal and flip one card before yielding to the next.
- Tablet right pair flips first, followed by the left pair; geometry stays fixed.
- PC lead card rises during heading entry and settles about `20vh` below the
  title composition.
- PC deck reveal triggers the time-based heading lift.
- PC spread precedes right-to-left overlapping flips.
- Flip duration remains `0.15` progress with `0.045` offsets.
- PC trail remains scroll-driven; CSS and the painter disable it below 1280px.

## Source ownership

- Blade: `resources/views/home/sections/school-values.blade.php`
- CSS entry: `resources/css/pages/welcome-values-story.css`
- Sticky shell: `resources/css/surfaces/home/values/story-shell.css`
- Heading/copy: `resources/css/surfaces/home/values/story-heading.css`
- Tier adapters: `resources/css/surfaces/home/values/story-responsive.css`
- Cards/backs/trail: remaining Values surface CSS modules
- Controller: `resources/js/surfaces/home/values/controller.js`
- Heading state: `resources/js/surfaces/home/values/heading-state.js`
- Geometry/timeline: `resources/js/surfaces/home/values/layout.js`
- Inertia: `resources/js/surfaces/home/values/motion.js`
- Painting/cleanup: `resources/js/surfaces/home/values/paint.js`
- Focused test: `tests/Feature/HomeValuesStoryTest.php`

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Mandatory docs/current main audit | `PASS_SOURCE` | current owners inspected before write |
| Latest owner storyboard | `PASS` | exact prompt plus runtime screenshot |
| Runtime 4 | `FAIL` | whole-body heading reveal and wrong tier model |
| Nested clipped heading DOM | `IMPLEMENTED_SOURCE` | two semantic nested text spans |
| One-way heading state | `IMPLEMENTED_SOURCE` | dedicated state owner and lifecycle classes |
| Back-first cards | `IMPLEMENTED_SOURCE` | Y rotation starts at 180 degrees |
| XS/SM phone mode | `IMPLEMENTED_SOURCE` | one-card flip slots |
| MD/LG tablet mode | `IMPLEMENTED_SOURCE` | stable 2x2 paired flips |
| XL/2XL PC mode | `IMPLEMENTED_SOURCE` | lead/deck/spread/overlap/exit |
| PC-only moving trail | `IMPLEMENTED_SOURCE` | CSS media gate plus painter gate |
| Focused DOM test | `IMPLEMENTED_SOURCE` | nested heading contract added |
| JavaScript syntax | `PASS_LOCAL_PATCH` | all changed JS passed `node --check` |
| CSS brace balance | `PASS_LOCAL_PATCH` | changed CSS balances verified |
| Source line limit | `PASS_LOCAL_PATCH` | every changed source file <=200 lines |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repo command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| Corrected Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | fresh sequence absent |
| WebKit/RTL/accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | required matrix absent |

## STATUS

The latest rendered heading and responsive card model failed the owner direction.
A clipped edge-first heading, forward-only latched state, back-first cards, three
responsive choreography families, and PC-only trail are implemented and
published to `main`. Runtime and release status remain unproven.

## NEXT VALID STEP

Pull current `main` and capture normal plus reverse scroll at 390px, 1024px, and
1920px in Brave/Chromium, covering heading entry, static reverse heading,
back-to-front cards, tier-specific layout, and PC-only trail.
