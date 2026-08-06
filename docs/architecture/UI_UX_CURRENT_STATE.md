# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-06
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-016-VISUAL-LERP-RAIL-REVEAL`
Source baseline: `c0d8ff61acd3d5321bdb4c74c4d4b0bbed5befd8`
Active blueprint: `blueprints/2026-08-06-home-program-scroll-rail.md`

## Owner corrections

- Only the Program title changes visual position during handoff.
- The existing description remains anchored to its measured viewport coordinate;
  only its text changes between the introduction and six programs.
- Program no longer uses a custom damped spring that rewrites document scroll on
  every RAF frame.
- Native document scroll is the target; the six-frame media stack visually lerps
  toward it at the same `0.08` smoothing factor used by Gallery.
- Active Program state derives from rendered scroll, keeping media, title,
  description, count, accent, and rail synchronized.
- A single native smooth-scroll request may align the nearest frame after input
  quiets; new input cancels it. Reduced motion disables automatic snapping.
- The rail normally displays only lines. All labels appear only while the rail is
  hovered or contains keyboard focus; the active line remains visibly accented.
- Full-frame media, temporary Unsplash images, portal links, RTL placement, and
  the blue Values handoff remain unchanged.

## Changed owners

- `resources/css/pages/welcome/program-journey/{base,hud,rail,compact,wide}.css`
- `resources/js/surfaces/home/program-journey/{controller,geometry,motion}.js`
- `tests/Feature/HomeProgramJourneyTest.php`
- active blueprint and this ledger

## Source proof

- Current `main` was fetched before implementation.
- Controller, geometry, and motion modules pass `node --check` on staged source.
- Focused PHP test passes `php -l` on staged source.
- Changed CSS braces are balanced.
- Every changed source file remains at or below 200 lines.
- Source contract test records visual lerp, fixed description anchoring, and
  rail hover/focus reveal ownership.

## Blocked proof

The GitHub connector cannot run repository commands or browser/runtime tests.
The following remain `BLOCKED_BY_MISSING_EVIDENCE`:

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- full `php artisan test`
- Chromium/WebKit responsive, RTL, reduced-motion, and input matrix
- actual scroll feel, image availability/crops, PageSpeed, and CWV delta

## Next valid step

Owner pulls `main`, runs the required command gates, then reviews Program with
slow/fast/reverse/interrupted wheel and touchpad input at 390, 768, 1024, 1181,
1440, and 1920 pixels in ID and AR.
