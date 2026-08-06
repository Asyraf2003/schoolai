# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-06
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-015-FULL-FRAME-CONTINUITY`
Source baseline: `e13f505d8bc739a413da747bd43530647ee5a6e0`

## Owner corrections

- The Program title and description at the end of Vision/Mission are now the same DOM nodes used by the Program HUD.
- Those nodes move into responsive Program slots with a FLIP transition and return when scrolling upward.
- The visible Program copy contains one description, not a summary plus a second paragraph.
- Six Program photographs are edge-to-edge `100vw × 100dvh` frames without card borders, radius, margins, or shadows.
- Normal word wrapping replaces arbitrary character breaks.
- Scroll settling now samples velocity, projects a landing frame, and uses a cancellable damped spring.
- New wheel, touch, pointer, or keyboard input interrupts settling.
- The rail, temporary Unsplash images, portal link, RTL placement, and blue Values handoff remain.

## Changed owners

- `resources/views/home/sections/vision-mission.blade.php`
- `resources/views/home/sections/featured-programs.blade.php`
- `resources/css/pages/welcome/program-journey/{base,hud,rail,compact,wide}.css`
- `resources/js/surfaces/home/program-journey/{controller,motion}.js`
- `tests/Feature/HomeProgramJourneyTest.php`
- this ledger

## Proof status

- Current main checked before writing.
- Changed JS passed `node --check` on staged source.
- Changed PHP test passed `php -l` on staged source.
- CSS braces are balanced.
- Changed source files remain at or below 200 lines.
- Repository build, full PHP tests, browser matrix, media availability, and performance remain `BLOCKED_BY_MISSING_EVIDENCE` until run from a checkout.

## Next valid step

Pull `main`, run the build and full tests, then review forward and reverse Program scrolling at 390, 768, 1024, 1181, 1440, and 1920 pixels in ID and AR.
