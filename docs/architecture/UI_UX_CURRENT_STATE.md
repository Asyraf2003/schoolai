# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-07
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-019-SEPARATE-OWNERS`
Source baseline: `719a3b5114660767dc7268a325b9465622754132`
Active blueprint: `blueprints/2026-08-06-home-program-scroll-rail.md`

## Implemented owner correction

- Visi/Misi now owns only its vision, mission, and image story.
- Program title and description were removed from the Visi/Misi Blade and moved
  into the Program Blade.
- Program no longer queries, captures, reparents, or restores DOM nodes from
  Visi/Misi.
- The Program opening white canvas and six image panels remain one vertical
  seven-frame track with no gaps, margins, or overlay curtain.
- Program copy is a separate absolute layer inside the Program sticky viewport.
  It is not fixed to the browser viewport.
- The title starts at the Program intro layout and moves to its corner during
  the first frame travel. The description keeps the intro coordinates while
  only its text changes.
- Rail clicks use one exact frame transaction: document scroll and visual
  current are both set to `(index + 1) * viewport height`.
- Native wheel/touch scroll retains the Gallery-style `0.08` visual lerp.
- Visi/Misi keeps its horizontal WAAPI animation. Its scroll height now derives
  from real horizontal travel rather than the removed Program panel and the
  former fixed `430svh` value.
- Values and all unrelated homepage sections remain unchanged.

## Changed owners

- `resources/views/home/sections/{vision-mission,featured-programs}.blade.php`
- `resources/css/pages/welcome/program-journey/{base,hud,rail,compact,wide}.css`
- `resources/css/pages/welcome-vision-waapi/{base,compact,enhanced,responsive}.css`
- `resources/js/surfaces/home/program-journey/{controller,geometry}.js`
- `resources/js/surfaces/home/vision-story/controller.js`
- focused Program and Visi/Misi tests
- active blueprint and this ledger

## Source proof

- Changed JavaScript passes `node --check` on staged source.
- Focused PHP tests pass `php -l` on staged source.
- Changed CSS braces are balanced.
- Every changed source file remains at or below 200 lines.
- Program source contains no `data-program-origin`, Visi/Misi reparenting,
  `handoffIn`, `handoffOut`, fixed HUD, white curtain, metadata counter, or
  percentage rail anchors.
- Program source contains one intro frame, six media frames, exact rail frame
  positions, local Program title/description ownership, and `0.08` lerp.

## Blocked proof

The GitHub connector cannot render the local browser. The following remain
`BLOCKED_BY_MISSING_EVIDENCE` until actually run:

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- full `php artisan test`
- Chromium/WebKit forward, reverse, interrupted, rail-click, responsive,
  locale, RTL, reduced-motion, resize, short-height, zoom, BFCache,
  external-image failure, PageSpeed, and CWV matrix
- owner review of the rendered Visi/Misi → Program → Values continuity
