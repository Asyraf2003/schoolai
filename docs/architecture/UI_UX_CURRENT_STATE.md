# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-06
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-018-SINGLE-SEVEN-FRAME-TRACK`
Source baseline: `3865b248093da2d379d4e6b666e1a6caf13daec6`
Active blueprint: `blueprints/2026-08-06-home-program-scroll-rail.md`

## Owner correction implemented in source

- Removed the unrequested Program eyebrow, category label, and `00 / 00` counter
  above the title.
- Removed the separate fixed white curtain. White is now frame zero in the same
  vertical track as the six Program images.
- The track contains exactly seven full-viewport frames: one plain white frame
  followed by six Program media frames. One Gallery-style visual current moves
  the complete track.
- The original title and description remain the same nodes. Their final visible
  Visi/Misi viewport coordinates are captured before Program starts.
- Only the title moves from its original coordinate to the Program corner during
  the white-to-first-image travel. The description remains at its captured
  coordinate and only its text changes.
- Program rail links remain clickable. Each hash anchor is positioned at the
  exact integer viewport step for its related image, so Program 2 resolves to
  frame 2 instead of a percentage-derived intermediate position.
- HUD title, description, link, and rail remain outside the moving media track.
- Program keeps native document scrolling as target and Gallery-style `0.08`
  visual lerp. No snap timer, projected landing, or Program `window.scrollTo`
  was introduced.

## Changed owners

- `resources/views/home/sections/featured-programs.blade.php`
- `resources/css/pages/welcome/program-journey/{base,hud}.css`
- `resources/js/surfaces/home/program-journey/{controller,geometry}.js`
- `tests/Feature/HomeProgramJourneyTest.php`
- active blueprint and this ledger

Protected surfaces remain unchanged: Visi/Misi source and motion, Values,
Gallery, Articles, About, Testimonial, header, and unrelated homepage sections.

## Source proof

- Controller and geometry pass `node --check` on staged source.
- Focused PHP test passes `php -l` on staged source.
- Changed CSS braces are balanced.
- Every changed source file remains at or below 200 lines.
- Staged Program source contains no curtain, visible category/counter metadata,
  `entryProgress`, Program `window.scrollTo`, snap timer, or projected landing.
- Focused contracts require one white intro frame, six Program media frames, six
  exact-step rail anchors, one seven-frame track, and no Program metadata row.

## Blocked proof

The GitHub connector cannot run the local browser/runtime matrix. These remain
`BLOCKED_BY_MISSING_EVIDENCE` until checked from a working checkout:

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- `php artisan test`
- Chromium/WebKit visual review at all six responsive tiers and ID/EN/AR
- slow, fast, reverse, interrupted, rail-click, resize, BFCache, reduced-motion,
  short-height, zoom, failed-image, PageSpeed, and CWV proof
