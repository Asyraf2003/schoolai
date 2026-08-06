# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-06
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-017-NATIVE-TARGET-STICKY-TRACK`
Source baseline: `c72c7eb05818b61bbd253391a229053c092270e2`
Active blueprint: `blueprints/2026-08-06-home-program-scroll-rail.md`

## Owner correction implemented in source

- Removed Program automatic snap, projected landing, anchor settling,
  `snapTimer`, Program `window.scrollTo`, and rail click-to-frame behavior.
- Native document scroll is now only the target. One visual current follows it
  with Gallery-style `0.08` lerp and never writes document scroll from RAF.
- Program now owns explicit scroll distance plus one sticky viewport. Six
  full-screen HTML frames live in an enhanced vertical visual track whose only
  scroll-linked movement is `translateY(-visualCurrent)`.
- The original description keeps its measured Visi/Misi viewport coordinate. It
  is reparented without FLIP, uses a stable fixed area, and changes text through
  opacity/light blur only.
- The original title remains the same node and keeps its directional FLIP to the
  logical Program title slot.
- The rail is a six-line, non-clickable indicator. Labels reveal only on hover or
  focus-within; active state derives directly from the same visual current as
  media and exit.
- The final blue/white-line Values handoff, six localized programs, six temporary
  Unsplash images, HTML fallback, reduced motion, LTR/RTL, and six-tier contract
  remain.

## Changed owners

- `resources/views/home/sections/featured-programs.blade.php`
- `resources/css/pages/welcome/program-journey/{base,hud,rail,wide}.css`
- `resources/js/surfaces/home/program-journey/{controller,geometry,motion}.js`
- `tests/Feature/HomeProgramJourneyTest.php`
- active blueprint and this ledger

Protected surfaces remain unchanged: Vision/Mission source, Values, Gallery,
Articles, About, Testimonial, header, and all unrelated sections.

## Source proof

- Current `main` was resolved as `c72c7eb05818b61bbd253391a229053c092270e2`
  before audit and will be revalidated immediately before publication.
- Controller, geometry, and motion pass `node --check` on staged source.
- Focused PHP test passes `php -l` on staged source.
- Changed CSS braces are balanced.
- Every changed source file remains at or below 200 lines.
- Staged source contains no Program `snapTimer`, projected landing,
  `window.scrollTo`, `snapNow`, description FLIP call, rail click listener, or
  rail button.
- Focused test records sticky viewport, six frames, native-target lerp, fixed
  description ownership, and non-clickable rail contracts.

## Blocked proof

The GitHub connector cannot run repository commands or browser/runtime tests.
The following remain `BLOCKED_BY_MISSING_EVIDENCE`:

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- full `php artisan test`
- Chromium/WebKit responsive, locale, RTL, reduced-motion, input, resize,
  short-height, zoom, BFCache, external-image failure, PageSpeed, and CWV matrix
- owner review of actual slow/fast/reverse/interrupted scroll feel

## Next valid step

Owner pulls the resulting `main`, runs the four command gates, then reviews
Program at 390, 768, 1024, 1181, 1440, and 1920 pixels in ID and AR using slow,
fast, reverse, and interrupted wheel/touchpad input.
