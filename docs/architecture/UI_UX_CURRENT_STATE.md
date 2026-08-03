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
- Surface: homepage Values section `#nilai` and its visual handoff from the final
  Mission scene
- Current atomic scope: Values heading and supporting description only
- Protected in this step: cards, flip chronology, trail, transition layer, Hero,
  Vision/Mission content and motion, Programs, Gallery, Articles, navigation,
  footer, DB/admin/routes, About, and Testimonial

## Owner-accepted storyboard

The Values journey must run in this order:

1. Final Mission color/content dissolves through blur into the Values blue field.
2. Localized heading appears from one shared center seam.
3. Its two lines separate vertically into the final two-line composition.
4. On wide layouts only, line two then shifts inward toward the visual center.
5. Supporting copy appears after the heading settles where the tier permits it.
6. One front-facing card rises below the heading.
7. The remaining cards become visible as a four-card deck.
8. The deck spreads, cards flip with temporal overlap, and the line advances.
9. Cards rise and leave before the next-section handoff.

One DOM, physical choreography, and controller serve ID, EN, and AR. Locale and
RTL alter content, font, direction, and natural text alignment only.

## Current heading/description tier contract

| Tier | Heading | Supporting copy | Line-two inward shift |
|---|---|---|---|
| XS 360–639 | two-line heading only | hidden | none |
| SM 640–767 | two-line heading only | hidden | none |
| MD 768–1023 | heading + description/eyebrow | visible | none |
| LG 1024–1279 | heading + description/eyebrow | visible | none |
| XL 1280–1535 | heading + description/eyebrow | visible | `104px` logical inward |
| 2XL >=1536 | heading + description/eyebrow | visible | `144px` logical inward |

The two lines start invisible with `0.41em` opposing offsets. Because the title
line-height is `.82`, both line centers initially coincide. They separate as
opacity rises, and converge back toward the same seam while fading on exit.

## Runtime feedback FACT

### Runtime 1: sticky failure

Owner screenshots on local 1920x1080 Brave/Chromium proved the stage travelled
under the navbar, cards were small, the heading did not enter, and long empty
blue travel remained. The sticky containing-block defect was corrected.

### Runtime 2: storyboard and face failure

Fresh screenshots proved sticky containment worked, but chronology, card faces,
static line treatment, and transition still failed the accepted storyboard. The
bounded card/trail/transition correction was published.

### Runtime 3: heading and copy failure

The latest owner screenshots prove the heading/copy result still fails:

- line one and line two travel from approximately `±28svh`, far beyond the
  intended shared center seam;
- inertia during title exit creates crossed/ghosted letter compositions;
- supporting copy remains visible on phone widths despite the owner requiring a
  heading-only phone result;
- line two does not have a tier-aware inward desktop finish.

Cards, flip progression, trail, and transition are not evaluated in this atomic
feedback step.

## Implemented source correction

Current correction source head before this ledger update:
`ec5b82371f9c8e5c50bb0efba6c221345db295f3`.

- Header travel is font-relative, not viewport-relative: `0.41em` and `-0.41em`.
- Header lines no longer receive scroll-momentum displacement.
- Line two shifts only after the two lines have nearly opened.
- CSS owns tier targets; JS only reads the resolved target and paints progress.
- XS and SM hide description and eyebrow, including the static fallback.
- MD and LG retain copy without horizontal title shift.
- XL and 2XL use bounded logical inward targets; RTL receives the opposite
  physical sign toward the same visual center.
- Heading/copy anchors now use logical inset properties.
- Card, flip, trail, transition, content, routes, and unrelated surfaces are
  unchanged by this correction.

## Source ownership

- Blade/content: `resources/views/home/sections/school-values.blade.php`
- Heading/copy CSS: `resources/css/surfaces/home/values/story-shell.css`
- Tier adapters: `resources/css/surfaces/home/values/story-responsive.css`
- Geometry measurement: `resources/js/surfaces/home/values/controller.js`
- Timeline: `resources/js/surfaces/home/values/layout.js`
- Painting/cleanup: `resources/js/surfaces/home/values/paint.js`

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Mandatory docs/current main | `PASS_SOURCE` | current chain and owners inspected |
| Latest owner heading/copy decision | `PASS` | exact prompt plus screenshots |
| Runtime 3 heading/copy | `FAIL` | excessive travel, ghosting, phone copy |
| Center-seam source correction | `IMPLEMENTED_SOURCE` | bounded timeline/painter patch |
| Six-tier copy/shift contract | `IMPLEMENTED_SOURCE` | CSS tier targets and visibility |
| ID/EN/AR logical architecture | `IMPLEMENTED_SOURCE` | one DOM plus logical insets |
| JavaScript syntax | `PASS_LOCAL_PATCH` | controller/layout/paint `node --check` |
| CSS balance | `PASS_LOCAL_PATCH` | changed CSS brace balance checked |
| Source line limit | `PASS_LOCAL_PATCH` | all changed source <=200 lines |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repo command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| Corrected Chromium tier runtime | `BLOCKED_BY_MISSING_EVIDENCE` | fresh screenshots absent |
| WebKit/RTL/accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | matrix absent |

## STATUS

The latest runtime heading/copy result failed. A bounded center-seam title,
wide-only inward second-line shift, phone heading-only rule, and logical
ID/EN/AR positioning are implemented and published to `main`. Runtime and
release status remain unproven.

## NEXT VALID STEP

Pull current `main` and capture the Values heading/copy at 390, 768, 1280, and
1920 widths in Brave/Chromium before returning to cards or trail behavior.
