# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-07
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-020-INTEGRATED-DESKTOP-PIN`
Source baseline: `9d5dfbe39d84982734ee45c6956a4de464c8811d`
Active blueprint: `blueprints/2026-08-06-home-program-scroll-rail.md`

## Latest owner correction

- Desktop Visi/Misi and Program no longer hand off across two independent sticky
  section boundaries.
- In enhanced wide mode, the complete Program section is appended as the final
  `100vw` panel of the existing Visi/Misi horizontal track.
- The Visi/Misi pin remains active for both phases. Its first phase consumes the
  actual horizontal overflow until the Program panel fully occupies the
  viewport. Only then does Program local vertical progress begin.
- Program keeps local ownership of its white intro frame, six image frames,
  title, description, link, rail, and exit. No Program copy is moved into or
  out of the Visi/Misi Blade.
- Compact layouts, reduced motion, and no-JS fallback keep Visi/Misi and Program
  as ordinary sequential sections, avoiding nested compact sticky owners.
- Program media remains one continuous seven-frame track. No invented dwell or
  snapping was added.
- Program copy changes at the exact full-frame boundary. Direction is retained
  while the user pauses mid-transition, so forward and reverse scrolling keep
  the last fully occupied frame's copy.
- Rail clicks still synchronize document position and visual current to one
  exact full-frame location.
- Values and unrelated homepage surfaces remain unchanged.

## Changed owners

- `resources/js/surfaces/home/program-journey/{controller,geometry,integration,motion}.js`
- `resources/js/surfaces/home/vision-story/controller.js`
- `resources/css/pages/welcome/program-journey/base.css`
- focused Program and Visi/Misi tests
- active blueprint and this ledger

## Source proof

- All changed JavaScript passes `node --check`.
- Focused PHP tests pass `php -l`.
- Changed CSS braces are balanced.
- Every changed source file remains at or below 200 lines.
- Program integration is wide-only, motion-capable, reversible on resize, and
  restores the original DOM position on destroy.
- Visi/Misi height equals viewport plus complete horizontal travel plus Program
  vertical travel.
- Horizontal timeline distance remains only the horizontal travel; Program
  vertical distance cannot slow or truncate the horizontal motion.
- Program active-copy selection uses completed-frame `floor/ceil` boundaries,
  not midpoint `Math.round`.

## Blocked proof

The GitHub connector cannot render the owner's local browser. These remain
`BLOCKED_BY_MISSING_EVIDENCE` until actually run:

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- full `php artisan test`
- Chromium/WebKit forward, reverse, interrupted, rail-click, responsive,
  locale, RTL, reduced-motion, resize, BFCache, PageSpeed, and CWV matrix
- owner review of the rendered Visi/Misi horizontal completion and Program
  full-frame copy timing
