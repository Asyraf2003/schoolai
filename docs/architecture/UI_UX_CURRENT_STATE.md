# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-06
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-018-FIXED-CHROME-CURTAIN-RAIL`
Source baseline: `076efb5e2e3770d90688cd4c9267633b523ac485`
Active blueprint: `blueprints/2026-08-06-home-program-scroll-rail.md`

## Latest owner correction implemented in staged source

- Program keeps the Gallery-style native-target model: document scroll supplies
  `target`, visual `current` follows with `0.08` lerp, and RAF never writes
  document scroll.
- One added entry distance keeps frame one stationary while a single opaque
  white curtain moves upward. Images remain underneath, removing the former
  overlapping handoff appearance.
- The original title still moves once to the logical Program corner. Later title
  text swaps use opacity/light blur only, so the title node no longer travels
  vertically when the active program changes.
- The original description keeps its measured Visi/Misi viewport coordinate.
  The invented clamp height and clipping were removed; its text swaps use only
  opacity/light blur.
- Title, description, metadata, link, and rail remain in the fixed Program HUD
  while only the curtain and media track move.
- The rail is clickable again through six native fragment links. It does not
  restore snap timers, projected landing, wheel interception, Program
  `window.scrollTo`, or a second rail timeline.
- The rail active state still derives from the same visual current as media.
- The final blue/white-line Values handoff, six localized programs, six
  temporary Unsplash images, fallback HTML, reduced motion, LTR/RTL, and the
  existing responsive visibility rules remain.

## Changed owners

- `resources/views/home/sections/featured-programs.blade.php`
- `resources/css/pages/welcome/program-journey/{base,hud,rail}.css`
- `resources/js/surfaces/home/program-journey/{controller,geometry}.js`
- `tests/Feature/HomeProgramJourneyTest.php`
- active blueprint and this ledger

Protected surfaces remain unchanged: Vision/Mission source, Values, Gallery,
Articles, About, Testimonial, header, and every unrelated section.

## Source proof

- Current `main` resolved as `076efb5e2e3770d90688cd4c9267633b523ac485`
  before audit and must be revalidated immediately before publication.
- Controller, geometry, and motion pass `node --check` on staged source.
- Focused PHP test passes `php -l` on staged source.
- Changed CSS braces are balanced.
- Every changed source file remains at or below 200 lines.
- Staged Program source contains no `snapTimer`, projected landing,
  `window.scrollTo`, `snapNow`, description FLIP, or rail click handler.
- Focused source contracts cover the entry curtain, fixed title/description
  text swaps, six native rail links, sticky viewport, and `0.08` visual lerp.

## Blocked proof

The GitHub connector cannot run repository commands or browser/runtime tests.
The following remain `BLOCKED_BY_MISSING_EVIDENCE`:

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- full `php artisan test`
- Chromium/WebKit forward, reverse, interrupted, rail-click, responsive,
  locale, RTL, reduced-motion, resize, short-height, zoom, BFCache,
  external-image failure, PageSpeed, and CWV matrix
- owner review that the white curtain is plain, Program chrome stays fixed, and
  scroll feel matches Gallery in the rendered homepage

## Next valid step

Owner pulls the resulting `main`, then reviews the exact handoff and rail clicks
before any further visual change.
