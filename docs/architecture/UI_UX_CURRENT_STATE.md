# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-06
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: owner-accepted dense desktop Program exploration from 2026-08-06
Source baseline: `d35311a8d09ddc2398ad7128076372b7af0d9322`
Source implementation head before ledger: `79a34befb6cf879f1b2434ac9015740156efc7e4`

## Active production batch

- ID: `HOME-PROGRAM-013-CINEMATIC-SCROLL-CHAPTERS`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Program `#program`
- Goal: push the desktop Program prototype toward an award-level school story
  by combining normal scroll chapters, a sticky media stage, functional actions,
  and dense education content without scroll hijacking.

## Owner decision

- Homepage order remains Vision/Mission -> Program -> Values -> Gallery -> Articles.
- The final Program heading inside Vision/Mission remains the single Program heading.
- Creative evaluation is currently desktop-first at `>=1181px`.
- The section may become intentionally content-dense for owner review.
- Phone, tablet, and full RTL/LTR certification remain deferred.
- Program remains functional: visible semantic chapter content, PPDB action, Gallery
  action, keyboard selection, and reduced-motion behavior are retained.

## Implemented source

- Six programs now render as six long-form scroll chapters rather than a compact
  card index.
- Information architecture remains split into:
  - school journey: KB, TK, and SD;
  - learning pillars: Tahfidz, Mitra Bahasa, and Literasi.
- A sticky desktop media stage now synchronizes:
  - active chapter number, accent, code, label, title, summary, and description;
  - one large local media layer and one secondary floating local media layer;
  - vertical progress, section ambient drift, and pointer depth;
  - PPDB and Gallery actions.
- IntersectionObserver activates chapters from ordinary page scroll. There is no
  wheel interception, forced horizontal travel, or full-page scroll hijack.
- Click, hover, focus, Arrow keys, Home, and End remain supported.
- Image changes preload before replacement. WAAPI transitions cancel prior runs.
- RAF work is shared for pointer and scroll updates, and pagehide cleans observers,
  animation frames, and active WAAPI animations.
- A three-part manifesto closes the section: Berakar, Bereksplorasi, and
  Berkontribusi.
- Existing compact shell ownership is retained for the deferred non-desktop path.

## Files changed

- `resources/views/home/sections/featured-programs.blade.php`
- `resources/css/pages/welcome/program-showcase-desktop.css`
- `resources/js/pages/welcome/program-cards.js`
- `docs/architecture/UI_UX_CURRENT_STATE.md`

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | revalidated immediately before writes |
| single Program heading | `PASS_SOURCE` | section still references `vision-program-title` |
| six chapter data contract | `PASS_SOURCE` | every program carries content, accent, and two local media paths |
| normal-scroll activation | `PASS_SOURCE` | IntersectionObserver only; no wheel/touch scroll interception |
| functional CTA path | `PASS_SOURCE` | `/ppdb` and `#galeri` remain native links |
| keyboard state path | `PASS_SOURCE` | focus, arrows, Home, and End are wired |
| reduced motion | `PASS_SOURCE` | WAAPI and pointer motion are suppressed |
| lifecycle cleanup | `PASS_SOURCE` | observer, RAF, and animations cleaned on pagehide |
| JS syntax | `PASS_LOCAL_STATIC` | `node --check` passed on equivalent staged source |
| source line limit | `PASS_LOCAL_STATIC` | Blade 165, JS 162, CSS 24 physical lines |
| CSS structural sanity | `PASS_LOCAL_STATIC` | balanced rule braces; unsupported calc multiplication removed |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| desktop Chromium/WebKit review | `BLOCKED_BY_MISSING_EVIDENCE` | rendered owner review required |
| compact/RTL matrix | `DEFERRED_OWNER_SCOPE` | not part of this creative evaluation |

## STATUS

The dense cinematic Program direction is published to source. Publication and
source wiring are proven. Rendering quality, crop suitability of the selected
local media, build output, browser behavior, and performance remain unproven.

## NEXT VALID STEP

Owner pulls current `main`, builds the project, and reviews the Program section
at desktop width `>=1181px`. The next correction should be based on rendered
feedback, then compared against the supplied video and shortlisted award-winning
school references.
