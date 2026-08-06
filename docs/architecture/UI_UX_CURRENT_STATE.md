# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-06
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-06-home-program-scroll-rail.md`
Source baseline: `18c80280afd585c45fc2910d7f4f881c47285564`

## Active production batch

- ID: `HOME-PROGRAM-014-NATIVE-SCROLL-RAIL`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Program `#program`
- Goal: let parents experience all six programs through ordinary vertical scroll,
  synchronized title/description/media/link, a centered side rail, soft frame
  settling, and a blue handoff into Values.

## Owner decision

- Vision/Mission keeps the single Program heading and introductory description.
- Users are not required to press previous/next controls; ordinary scroll is the
  primary journey in both directions.
- Six images remain physical vertical HTML frames with visible boundaries.
- When input stops inside the frame range, the page may settle gently to the
  nearest frame, but new input must cancel that motion.
- Desktop LTR places the title at inline-start and the rail at inline-end; RTL
  swaps only those logical positions.
- Tablets center title/description, hide the rail when narrow, and show it when
  width permits. Phones retain the same story with smaller type/media.
- Temporary Unsplash images are accepted for this review cycle.
- The sixth frame transitions into blue with white lines before Values.

## Implemented source

- Replaced the previous two-column Program prototype with six semantic vertical
  frame articles and one sticky synchronized HUD.
- The HUD updates active label, count, title, summary, description, link, accent,
  and rail state from the nearest visible frame.
- Entry begins from the current Vision/Mission Program copy and settles the title
  toward its responsive active position.
- Copy and rail use the established mobile-menu blur/vertical/easing character
  through cancellable WAAPI animations.
- Soft settling uses a cancellable RAF animation after scroll input pauses. It
  does not intercept wheel/touch events or require arrow controls.
- The rail exposes six line items, keeps its focus line centered, reveals labels
  on hover/focus, and can optionally scroll directly to a frame.
- A final blue full-viewport panel draws eight white lines from alternating sides
  and naturally releases into Values.
- No-JS fallback renders all six images, titles, descriptions, and PPDB links.
- Reduced motion disables automatic settle and collapses transition durations.
- Six temporary static Unsplash CDN images are used; the existing CSP already
  permits that host.

## Files changed

- `resources/views/home/sections/featured-programs.blade.php`
- `resources/css/pages/welcome/program-showcase-desktop.css`
- `resources/css/pages/welcome/program-journey/base.css`
- `resources/css/pages/welcome/program-journey/hud.css`
- `resources/css/pages/welcome/program-journey/rail.css`
- `resources/css/pages/welcome/program-journey/compact.css`
- `resources/css/pages/welcome/program-journey/wide.css`
- `resources/js/pages/welcome/program-cards.js`
- `resources/js/surfaces/home/program-journey/controller.js`
- `resources/js/surfaces/home/program-journey/motion.js`
- `tests/Feature/HomeProgramJourneyTest.php`
- active blueprint and this ledger

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | baseline fetched before implementation |
| one Program heading | `PASS_SOURCE` | Program still references `vision-program-title` |
| six semantic frames | `PASS_LOCAL_STATIC` | Blade contract and focused test require six |
| ordinary scroll ownership | `PASS_SOURCE` | no wheel/touch prevention or scroll lock |
| WAAPI copy/rail motion | `PASS_SOURCE` | cancellable blur/vertical animations |
| soft-settle interruption | `PASS_SOURCE` | wheel/touch/pointer/key cancel RAF settle |
| RTL logical placement | `PASS_SOURCE` | title/rail use logical inset rules |
| no-JS content/action | `PASS_SOURCE` | six visible fallback articles and PPDB links |
| reduced motion | `PASS_SOURCE` | settle disabled and transition durations collapsed |
| source line limit | `PASS_LOCAL_STATIC` | every changed source file is <=200 lines |
| JS syntax | `PASS_LOCAL_STATIC` | `node --check` passed for all changed JS files |
| CSS structural sanity | `PASS_LOCAL_STATIC` | braces balanced in every changed stylesheet |
| external image availability | `BLOCKED_BY_MISSING_EVIDENCE` | runtime network verification unavailable |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | rendered runtime review required |
| PageSpeed/CWV delta | `BLOCKED_BY_MISSING_EVIDENCE` | measured runtime evidence required |

## STATUS

The owner-accepted Program scroll rail is implemented in source. Publication,
build output, actual media crops, settle feel, browser parity, responsive quality,
and performance are not yet proven.

## NEXT VALID STEP

Owner pulls current `main`, runs the required build/test commands, and reviews the
Program journey forward and backward at 390, 768, 1024, 1181, 1440, and 1920px in
ID and AR before permanent Al-Mustaqbal media replaces the temporary images.
