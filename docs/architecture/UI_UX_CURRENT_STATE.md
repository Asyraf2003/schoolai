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
- Current atomic result: slower heading plus enlarged cinematic PC card motion
- Protected: Hero, Vision/Mission content/controller, Programs, Gallery,
  Articles, navigation, footer, DB/admin/routes, About, and Testimonial

## Latest owner-accepted direction

### Heading and copy

- Preserve the edge-first clipped reveal but make it much slower and smoother.
- Make the title larger and visibly thinner.
- Keep reverse-scroll latching.
- Begin moving the PC title upward with scroll as soon as the card deck forms.
- Let supporting copy leave as the PC deck takes visual priority.
- XS and SM remain heading-only.

### Cards and trail

- Increase PC card scale by roughly one visual step.
- During spread, all backs anticipate together by about `15deg`.
- Cards then flip right-to-left with overlap.
- Each card overshoots the front by about `15deg`, then settles to neutral.
- Cards float vertically by a few pixels throughout the PC choreography.
- Phone/tablet chronology and PC-only trail boundaries remain unchanged.

## Runtime feedback FACT

### Runtime 1: sticky failure

Owner screenshots at 1920x1080 Brave/Chromium proved the stage travelled under
the navbar and left long empty blue travel. Sticky containment was corrected.

### Runtime 2: chronology and card-face failure

Screenshots proved sticky containment worked, but chronology, mirrored fronts,
line treatment, and transition still failed. Face/timeline/trail corrections
were published.

### Runtime 3: heading travel failure

Screenshots proved whole word bodies moved visibly, producing crossed and
ghosted text. Nested clipped title spans were introduced.

### Runtime 4: tier and reverse contract failure

Screenshots proved the result still needed one-way heading state, back-first
cards, divergent phone/tablet/PC choreography, and a PC-only line. Those source
owners were split and published.

### Runtime 5: latest owner screenshots

The supplied 1920x1080 comparison sequence proves:

- the clipped heading now resolves, but its reveal feels abrupt;
- the heading weight is much heavier and its scale smaller than the intended
  reference rhythm;
- PC cards are materially smaller than the target visual mass;
- title remains in the card field instead of beginning a scroll-scrubbed exit
  when the deck appears;
- the current direct `180deg -> 0deg` flip lacks shared anticipation, endpoint
  overshoot, and settle;
- cards lack the subtle continuous vertical drift visible in the intended
  experience.

This runtime is `FAIL` for the latest owner direction and is the evidence for
the current correction.

## Root-cause FACT

The heading reveal speed defect was not merely a short numeric range.

`heading-state.js` previously used:

```text
not moving forward
+ any partial reveal
-> force reveal to 1
```

The scroll controller continues RAF frames while its inertial position settles.
On the first RAF frame after a raw scroll event, `target` is unchanged. The old
state therefore misclassified an idle frame as rollback and completed the entire
heading immediately.

The corrected owner distinguishes three states:

```text
forward delta
backward delta
idle/no delta
```

Idle now preserves partial reveal. Only actual backward delta resolves a partial
heading to the static reverse state.

## Implemented source correction

Source head before this ledger update:
`abd13fbdc8b582fac7acbcbab4f29fc4f58dbd49`.

### Heading implementation

- Reveal range expanded from `0.018–0.13` to `0.012–0.20`.
- Reveal uses an additional smooth pass for gentler acceleration and release.
- Idle RAF frames no longer force completion.
- Page load inside Values initializes a resolved static heading.
- Title weight changed from `500` to `300`.
- Desktop title scales up to `15rem` on XL and `16rem` on 2XL.
- Line-two independent transition increased to `1350ms`.
- PC title Y is now painted from scroll progress:
  - start `0.22`;
  - finish `0.46`;
  - destination `-56%` of viewport height.
- Supporting copy fades between progress `0.24` and `0.36`.
- The old time-only heading-lift class and target variable were removed.

### PC card implementation

- Desktop card frame moved to `desktop-layout.js` to keep each source file below
  200 lines.
- XL card target changed to `min(26vw, 25rem)`.
- 2XL card target changed to `min(24vw, 27rem)`.
- PC story travel increased to `620svh` and `640svh`.
- Lead/deck scale is `0.92`; spread scale reaches `1`.
- Row spacing reduced to `0.88 * cardWidth`, producing larger controlled overlap.
- Spread runs from progress `0.31` through `0.48`.
- All backs anticipate from `180deg` to `195deg` during `0.40–0.48`.
- Right-to-left flip starts use `0.052` offsets.
- Each drive lasts `0.18`, ending at `-15deg`.
- Each settle lasts `0.07`, ending at `0deg`.
- Exit begins after progress `0.90`.
- A dedicated `.values-card__float` wrapper animates from `-4px` to `4px` over
  `4.8s` with staggered negative delays.
- Float is active only at `>=1280px` and only when reduced motion is not set.

## Source ownership

- Blade: `resources/views/home/sections/school-values.blade.php`
- CSS entry: `resources/css/pages/welcome-values-story.css`
- Sticky shell/root variables: `story-shell.css`
- Heading/copy: `story-heading.css`
- Tier scale/travel: `story-responsive.css`
- Card treatment/float: `story-cards.css`
- Card backs: `story-card-back.css`
- PC trail: `story-trail.css`
- Controller: `controller.js`
- Heading direction state: `heading-state.js`
- Phone/tablet/story frame: `layout.js`
- PC card frame: `desktop-layout.js`
- Inertia: `motion.js`
- Painting/cleanup: `paint.js`
- Focused test: `tests/Feature/HomeValuesStoryTest.php`

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Current main/source audit | `PASS_SOURCE` | source head and active owners inspected |
| Latest owner sequence | `PASS` | exact prompt plus eleven 1920x1080 screenshots |
| Runtime 5 | `FAIL` | abrupt heading and mechanically small/direct card motion |
| Idle-vs-reverse heading state | `IMPLEMENTED_SOURCE` | explicit directional delta branches |
| Slower/lighter/larger heading | `IMPLEMENTED_SOURCE` | range, weight, scale, and CSS timing |
| Scroll-driven PC heading exit | `IMPLEMENTED_SOURCE` | painted Y from deck start |
| Enlarged PC cards | `IMPLEMENTED_SOURCE` | XL/2XL targets and row scale |
| Anticipation/overshoot/settle | `IMPLEMENTED_SOURCE` | dedicated desktop frame |
| PC card float | `IMPLEMENTED_SOURCE` | independent wrapper and reduced-motion gate |
| Phone/tablet boundary | `IMPLEMENTED_SOURCE` | existing mode branches preserved |
| Focused DOM test | `IMPLEMENTED_SOURCE` | four float wrappers asserted |
| JavaScript syntax | `PASS_LOCAL_PATCH` | changed JS passed `node --check` |
| PHP test syntax | `PASS_LOCAL_PATCH` | focused test passed `php -l` |
| CSS brace balance | `PASS_LOCAL_PATCH` | changed CSS balances verified |
| Source line limit | `PASS_LOCAL_PATCH` | every changed source file <=200 lines |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repo command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| Corrected Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | fresh sequence absent |
| WebKit/RTL/accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | matrix absent |

## STATUS

Runtime 5 failed the latest motion target. Source now contains a corrected
direction-aware heading reveal, lighter/larger title, scroll-scrubbed PC title
exit, enlarged PC cards, shared anticipation, overlapping drive, front
overshoot, settle, and subtle float. Runtime and release status remain unproven.

## NEXT VALID STEP

Pull current `main` and capture the PC sequence at 1920px in Brave/Chromium:
heading entry, deck onset/title exit, shared anticipation, first/middle/final
flip, neutral settle, and card exit.
