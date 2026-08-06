# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-06
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: owner-accepted desktop Program prototype from 2026-08-06
Source baseline: `b24ff5d511dd3d67a15f53140a8a7be080d4cd14`

## Active production batch

- ID: `HOME-PROGRAM-012-DESKTOP-LEARNING-ATLAS`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Program `#program`
- Goal: use the final Program heading inside Vision/Mission as the single
  section introduction, then replace the duplicate Program heading and card grid
  with an exploratory PC-first editorial showcase.

## Owner decision

- Homepage order remains Vision/Mission -> Program -> Values -> Gallery -> Articles.
- The Program heading at the end of Vision/Mission is the only Program heading.
- The standalone Program section must not render a duplicate editorial heading.
- Creative proof is currently evaluated on desktop only.
- Phone, tablet, and RTL/LTR certification is deferred; existing compact content
  remains present and must not be claimed as proven.
- The supplied video and external reference are deferred until owner review of
  this independent creative direction.

## Implemented source

- Program information architecture now separates:
  - school journey: KB, TK, and SD;
  - learning-strengthening programs: Tahfidz, Mitra Bahasa, and Literasi.
- Desktop `>=1181px` uses a dark editorial learning-atlas composition:
  - interactive index on the left;
  - sticky active-program stage on the right;
  - active code, color, label, title, summary, and description synchronization;
  - lightweight rings, grid field, pointer depth, and WAAPI content transition.
- Click, hover, focus, arrow keys, Home, and End update the active program.
- Reduced-motion users do not receive ring or WAAPI motion.
- New CSS is imported by the Program controller, avoiding changes to the legacy
  welcome CSS checksum/import chain.

## Files changed

- `resources/views/home/sections/featured-programs.blade.php`
- `resources/js/pages/welcome/program-cards.js`
- `resources/css/pages/welcome/program-showcase-desktop.css`

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | validated before each write |
| duplicate heading removal | `PASS_SOURCE` | Program section references `vision-program-title` |
| Program data/state wiring | `PASS_SOURCE` | six cards expose synchronized data attributes |
| JS syntax | `PASS_LOCAL_STATIC` | `node --check` passed |
| source line limit | `PASS_LOCAL_STATIC` | Blade 134, JS 126, CSS 140 lines |
| changed-file scope | `PASS_SOURCE` | Program owners plus this ledger only |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| desktop Chromium/WebKit review | `BLOCKED_BY_MISSING_EVIDENCE` | owner runtime review required |
| compact/RTL matrix | `DEFERRED_OWNER_SCOPE` | not part of this creative proof |

## STATUS

The desktop Program creative direction is published to source. Publication is
proven; rendered composition, build output, browser behavior, and performance are
not yet proven.

## NEXT VALID STEP

Owner pulls current `main`, builds the project, and reviews the Program section
at desktop width `>=1181px` before the supplied video/reference is used for the
next correction cycle.
