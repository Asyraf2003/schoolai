# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-06
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-06-home-program-scroll-rail.md`
Source baseline: `18c80280afd585c45fc2910d7f4f881c47285564`
Regression report head: `c22246f6c77793d1a7fa5dac3ac1cce2c741633a`

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
- Program detail links must not expose the closed public PPDB route. Until
  dedicated Program detail routes exist, they use the public portal login route.

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
- No-JS fallback renders all six images, titles, descriptions, and portal links.
- Reduced motion disables automatic settle and collapses transition durations.
- Six temporary static Unsplash CDN images are used; the existing CSP already
  permits that host.

## Regression corrections from owner test run

The full PHP suite at `c22246f6c77793d1a7fa5dac3ac1cce2c741633a`
reported five failures:

- three locale cases found hard-coded `/ppdb` links in Program;
- the Program test counted two existing Hero Unsplash URLs in addition to the six
  Program URLs;
- one Gallery restore test used a `video` fixture while the homepage contract is
  explicitly photo-only.

Corrections published after that report:

- Program Blade uses `route('portal.login')` rather than hard-coded `/ppdb`;
- Program controller preserves the server-rendered link instead of falling back
  to `/ppdb`;
- Program media assertions are scoped to the Program section;
- the Gallery atomic restore/public visibility fixture now uses identical photos,
  while the separate video identity test remains unchanged.

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
- `tests/Feature/Admin/GalleryItemSoftDeleteTest.php`
- active blueprint and this ledger

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | revalidated before corrective writes |
| one Program heading | `PASS_SOURCE` | Program still references `vision-program-title` |
| six semantic frames | `PASS_SOURCE` | Blade and focused test require six |
| ordinary scroll ownership | `PASS_SOURCE` | no wheel/touch prevention or scroll lock |
| WAAPI copy/rail motion | `PASS_SOURCE` | cancellable blur/vertical animations |
| soft-settle interruption | `PASS_SOURCE` | wheel/touch/pointer/key cancel RAF settle |
| RTL logical placement | `PASS_SOURCE` | title/rail use logical inset rules |
| closed PPDB contract | `PASS_SOURCE` | Program has no hard-coded `/ppdb` link/fallback |
| scoped six-image test | `PASS_SOURCE` | count runs against Program section only |
| Gallery homepage contract | `PASS_SOURCE` | restored public fixture is photo; video remains gallery-page only |
| no-JS content/action | `PASS_SOURCE` | six visible fallback articles and portal links |
| reduced motion | `PASS_SOURCE` | settle disabled and transition durations collapsed |
| source line limit | `PASS_SOURCE` | changed Program source files remain <=200 lines |
| external image availability | `BLOCKED_BY_MISSING_EVIDENCE` | runtime network verification unavailable |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| corrected PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | rerun required after corrective commits |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | rendered runtime review required |
| PageSpeed/CWV delta | `BLOCKED_BY_MISSING_EVIDENCE` | measured runtime evidence required |

## STATUS

The Program scroll rail and the source-level regression corrections are published.
The previous five-test failure report is resolved by source changes, but the
corrected suite is not `PASS` until it is rerun.

## NEXT VALID STEP

Owner pulls current `main` and reruns `php artisan test`. After the suite result is
recorded, continue visual review of the Program journey at 390, 768, 1024, 1181,
1440, and 1920px in ID and AR.