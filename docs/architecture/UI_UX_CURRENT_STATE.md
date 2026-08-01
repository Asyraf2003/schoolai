# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE
Updated: 2026-08-01
Repository: `Asyraf2003/schoolai`
Proofed candidate: PR `#26`, `HOME-HERO-STABILIZATION-001`

This ledger preserves the B00 baseline and records the first completed production
surface migration. The final squash commit that contains this file is the durable
`main` boundary for the Hero implementation.

## Active product goal

Build a distinctive Al Mustaqbal school experience while retaining:

- semantic content and usable static/reduced-motion fallbacks;
- fluid XS, SM, MD, LG, XL, and 2XL behavior from 360px upward;
- ID/EN in LTR and AR in RTL;
- measured Chromium and real Safari proof;
- accessible input, lifecycle cleanup, and bounded performance cost;
- isolated source ownership with no production WebGL dependency.

## B00 baseline

Status: `COMPLETE_WITH_KNOWN_GAPS`

B00 was read-only and established these Hero defects:

- yellow ornaments collided with Hero copy at `390x844`;
- hamburger was correct at `1180px`;
- both hamburger and desktop navigation were absent at `1181px`;
- desktop navigation returned by `1279px` and remained correct at `1280px`;
- Hero CSS/JS ownership was mixed with navigation and language-flag rendering;
- real Safari, failure fallback, reduced motion, keyboard, BFCache, and the full
  locale-width matrix were not proven.

Baseline bundle evidence:

- `welcome-hero.css`: about 22.65 kB raw / 4.99 kB gzip;
- `welcome-hero.js`: about 9.29 kB raw / 2.93 kB gzip;
- `npx vite build`: PASS;
- full structure gate: blocked by Vision/Mission source debt;
- full Laravel suite: one stale test for the intentionally disabled About surface.

## P00 owner decision

The owner selected **Hero** as the first pilot, superseding the B00 recommendation
of School Values. The accepted blueprint is `HOME-HERO-STABILIZATION-001`.

This changed priority only. About, Testimonial, Vision/Mission, School Values,
Gallery, Articles, footer, admin, editor, typography redesign, schema changes,
and WebGL remained out of scope.

## Hero final state

Status: `COMPLETE_WITH_EXTERNAL_SAFARI_DEFERRED`
Progress: `98%` of the Hero DoD.

The remaining 2% is external browser evidence, not unfinished source work:
real macOS Safari and Lighthouse/PageSpeed were unavailable in the execution
environment. No Safari or PageSpeed PASS is claimed.

### Ownership

- Controller concern owns the normalized image/video/poster/fallback contract.
- Hero Blade owns one semantic DOM, headings, copy, CTA, controls, and no-JS state.
- Hero CSS is split by layout, media, motion, controls, responsive, locale/RTL,
  and reduced-motion responsibility.
- Hero JS is split by controller/state, media lifecycle, and event lifecycle.
- Navigation CSS/JS is no longer loaded through the Hero or language flag.
- Effective equivalence records protect both the migrated Hero entry and the
  directly affected `welcome.css` navigation module order/checksum.
- Every new Hero source file remains at or below 200 lines.

### Media and lifecycle

- Mixed `image` and direct native `video` slides use existing data flow.
- Unsupported or failed video degrades to poster/local fallback without schema
  changes or legacy iframe/YouTube revival.
- Video is muted and plays inline; only the active slide is hydrated/prioritized.
- Inactive, hidden, offscreen, navigating, and disposed media is paused/reset.
- Timers, listeners, observers, transition classes, and BFCache state are cleaned
  or resumed deliberately.
- Poster remains visible until video reaches `playing`.

### Transition and interaction

- Exactly one slide remains active after repeated changes.
- Leaving media scales up into bounded blur; entering media resolves from wider
  blur to normal scale without blank frame or stacked active state.
- Pointer, touch-safe swipe, arrows, dots, keyboard, and pause/play work.
- Automatic changes never move focus.
- Reduced motion disables autoplay and transition choreography without hiding
  content.
- First slide content remains available when JavaScript is disabled.

### Responsive, locale, and navigation

Chromium runtime proof covered 33 locale-width combinations:

- locales: ID/LTR, EN/LTR, AR/RTL;
- widths: 360, 390, 640, 768, 1024, 1180, 1181, 1279, 1280, 1536, 1920;
- tiers: XS, SM, MD, LG, XL, and 2XL;
- checks: overflow, copy/control collision, title clipping, navbar collision,
  active/transient slide invariants, media/failure behavior, and navigation mode.

Results:

- 390px ornament/copy collision: PASS, removed by the owned control layout;
- hamburger through and including 1180px: PASS;
- desktop navigation from 1181px: PASS;
- one-pixel dead zone: removed;
- ID, EN, AR wrapping/direction: PASS in Chromium;
- LTR/RTL spatial controls: PASS in Chromium.

### Automated proof

- `git diff --check`: PASS;
- clean proof checkout: PASS;
- `npx vite build`: PASS;
- focused Hero/navigation tests: 7 passed, 163 assertions;
- Chromium runtime matrix: PASS, 33 cases;
- standard interactions: PASS;
- reduced motion: PASS;
- JavaScript-disabled fallback: PASS;
- full Laravel suite: 144 passed, one stale About test failed, 1375 assertions;
- structure check: only three pre-existing Vision/Mission line-limit failures;
- Hero and affected `welcome.css` equivalence checks: PASS.

### Bundle result

- Hero CSS: 22.65 kB → 12.136 kB raw, about 46.4% lower;
- Hero CSS gzip: 4.99 kB → 2.980 kB, about 40.3% lower;
- Hero JS: 9.29 kB → 8.027 kB raw, about 13.6% lower;
- Hero JS gzip: 2.93 kB → 2.667 kB, about 9.0% lower;
- navigation CSS is now a separately owned 13.915 kB raw / 3.275 kB gzip entry.

The Hero entry reduction partly reflects correct ownership separation. It is not
a claim that total page CSS fell by the same percentage.

## Deferred external proof

### Real Safari checklist

Status: `DEFERRED_TO_REAL_MAC_SAFARI`.

Run on real Safari for macOS, not Epiphany/WebKitGTK:

1. Load ID at 360, 390, 640, 768, 1024, 1180, 1181, 1279, 1280, 1536, and 1920.
2. Resize across every adjacent breakpoint and verify no overflow, collision,
   blank frame, duplicate active slide, or navbar dead zone.
3. Switch ID → EN → AR → ID through POST/session/redirect and verify `lang`,
   `dir`, wrapping, Arabic joining, line-height, controls, and ornament direction.
4. Verify native video is muted, `playsinline`, poster-backed, and pauses when
   inactive, hidden, offscreen, or navigating away.
5. Force video and image failures and verify poster, local image, then stable
   gradient fallback with readable semantic content.
6. Repeat arrows, dots, keyboard, pointer, and touch changes; verify transitions
   settle and focus never moves automatically or becomes trapped.
7. Enable Reduce Motion and verify autoplay/motion stop while all content and
   controls remain usable.
8. Disable JavaScript and verify the first slide, heading, description, CTA, and
   media fallback remain present without enhanced controls.
9. Navigate away/back and verify BFCache restoration, timers, video state, and
   controls resume without duplicate listeners.
10. Inspect Safari Web Inspector console/network/media for errors, repeated media
    downloads, autoplay rejection loops, layout shifts, and failed cleanup.

Lighthouse/PageSpeed/CWV remain `UNMEASURED`, not failed and not passed.

## Open repository gaps

### `STRUCTURE-GAP-001`

Only the three pre-existing Vision/Mission files remain above the 200-line gate.
Do not fix them inside Hero scope.

### `TEST-GAP-001`

`HomeAboutReelTest` remains stale against the intentionally disabled protected
About surface. Do not reactivate About merely to turn the suite green.

### `BROWSER-PERF-GAP-001`

Real Safari and measured Lighthouse/PageSpeed/CWV evidence remain external.

### `ENGINE-LAB-GAP-001`

No WebGL engine or production/lab scene was introduced. This does not block the
completed DOM/CSS Hero migration.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G00 governance hardening | `PASS` | decision, session, matrix, DoD, proof contracts |
| G01 execution foundation | `PASS` | migration, lab, prompt, handoff contracts |
| B00 current baseline | `COMPLETE_WITH_KNOWN_GAPS` | durable baseline evidence |
| P00 Hero pilot selection | `OWNER_ACCEPTED` | owner decision recorded |
| H00–H06 Hero implementation | `PASS` | ownership, media, lifecycle, tiers, locale, nav, accessibility |
| H07 automated/runtime proof | `PASS_CHROMIUM` | focused tests and 33-case runtime matrix |
| H08 docs/atomic main delivery | `PASS_ON_THIS_SQUASH` | blueprint, ledger, equivalence, CI, rollback |
| Safari acceptance | `DEFERRED_TO_REAL_MAC_SAFARI` | real Mac owner checklist required |
| Lighthouse/PageSpeed | `UNMEASURED` | explicit future measurement |

## STATUS

- Hero source implementation: `COMPLETE`.
- Hero available proof: `PASS`.
- Hero overall status: `COMPLETE_WITH_EXTERNAL_SAFARI_DEFERRED`.
- Protected surfaces changed: `NONE`.
- Heavy animation/WebGL dependencies added: `NONE`.
- Database schema changes: `NONE`.

## NEXT VALID STEP

Run the exact real Mac Safari checklist above and attach screenshots/console/media
evidence. After that external proof is recorded, select the next homepage pilot
from fresh evidence. Do not automatically begin School Values or any other section.
