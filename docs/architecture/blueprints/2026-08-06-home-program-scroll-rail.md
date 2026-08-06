# Homepage Program Fixed Chrome and White Curtain Blueprint

State: `OWNER_ACCEPTED / IMPLEMENTING`
Date: 2026-08-06
Surface: homepage Program `#program`
Source baseline: `076efb5e2e3770d90688cd4c9267633b523ac485`
Batch: `HOME-PROGRAM-018-FIXED-CHROME-CURTAIN-RAIL`

## OWNER GOAL

Correct only the observed Program defects. The section title becomes each
program title through a text transition. The title moves once toward the
logical top corner; the description changes text without changing position.
Title, description, and rail remain independent and stationary while Program
media moves. The white opening canvas moves plainly upward to reveal the first
image underneath. The rail must be clickable. No unrelated visual invention is
authorized.

## FACT AND ROOT CAUSE

- `main` already uses a sticky full-screen Program viewport and Gallery-style
  native-target `0.08` lerp.
- The current handoff changes state at separate root thresholds and starts media
  travel immediately, so a small scroll can trigger title/copy changes before a
  dedicated opening transition exists.
- The description owner adds a `clamp()` height and clipping even though the
  owner only requested stable coordinates and changing text.
- There is no single opaque white layer separating the Visi/Misi handoff from
  the Program image, so the surfaces can appear to overlap.
- The previous owner correction intentionally changed the rail into passive
  `<li>` indicators. The latest owner correction explicitly restores clicking.

## SCOPE AND OWNER MAP

Editable:

- `resources/views/home/sections/featured-programs.blade.php`
- `resources/css/pages/welcome/program-journey/{base,hud,rail}.css`
- `resources/js/surfaces/home/program-journey/{controller,geometry}.js`
- focused Program test
- this blueprint and `UI_UX_CURRENT_STATE.md`

Read-only / forbidden:

- Visi/Misi source and controller
- Values, Gallery, Articles, About, Testimonial, header, and other sections
- localized program data and the six temporary Unsplash images

## REQUIRED RESULT

1. Native document scroll remains the only target.
2. Visual current continues to use `lerp(current, target, 0.08)`.
3. One entry viewport is added before media travel.
4. During that entry, one opaque white curtain translates upward. It does not
   fade, crossfade, or mix with the image.
5. Frame one remains stationary below the curtain.
6. The title node moves only during the original handoff to the Program corner.
7. Later title and description text swaps use opacity/light blur only.
8. Description top/left/width come from the measured Visi/Misi coordinate; no
   invented fixed height or clipping is applied.
9. Title, description, metadata, link, and rail remain in the fixed HUD while
   curtain/media move behind them.
10. Six rail items are native fragment links to six document-position anchors.
    Clicking may use normal browser scrolling, but no automatic snap system,
    wheel interception, projected landing, or Program `window.scrollTo` returns.
11. Active rail state remains derived from visual current.
12. The blue and white-line Values handoff remains unchanged.

## RESPONSIVE, LOCALE, AND FALLBACK

- Existing title and rail placement rules remain; no new composition is added.
- ID/EN remain LTR and AR mirrors logical title/rail placement.
- Existing compact tiers may continue hiding the rail where already defined.
- Without JS, all six semantic Program articles and links remain readable.
- Reduced motion maps visual current directly to native target and removes
  animated text travel.

## PROOF

Available source checks:

- JS syntax
- focused PHP syntax
- CSS brace balance
- source files at or below 200 lines
- absence of snap/scroll-writing owners
- presence of white curtain, entry geometry, stable copy, and six native links
- atomic fast-forward publication and changed-path verification

Runtime/browser/build gates remain `BLOCKED_BY_MISSING_EVIDENCE` unless actually
run and recorded.
