# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE
Updated: 2026-08-01
Repository: `Asyraf2003/schoolai`
Baseline audited source: `a580a509fecaaf912d718e56a38695212d41fcc3`
Hero blueprint: `HOME-HERO-STABILIZATION-001`
Concurrent navbar source reconciled through: `dda36abbc1f693294cfbc16b266f7e01d72975ec`

Commit publication is source evidence only. Browser, accessibility, lifecycle,
and performance claims below are limited to the proof explicitly recorded.

## Active product goal

Build a distinctive Al Mustaqbal experience while retaining:

- semantic content and usable static/reduced-motion fallbacks;
- fluid XS, SM, MD, LG, XL, and 2XL behavior from 360px upward;
- ID/EN in LTR and AR in RTL;
- measured Chromium and real Safari proof;
- keyboard, touch, pointer, lifecycle, zoom, and short-height access;
- bounded performance cost and maintainable Blade/CSS/JS ownership;
- no production WebGL dependency without an isolated accepted lab.

## B00 baseline

Status: `COMPLETE_WITH_KNOWN_GAPS`

B00 established these facts and defects:

- Laravel 13 + Blade + Vite 8;
- About intentionally disabled and protected;
- Testimonial source exists but is not rendered on the homepage;
- `npx vite build` passed;
- the structure gate was blocked by oversized Vision/Mission files and stale
  Hero equivalence records;
- the full PHP suite had one stale About test failure;
- locale output proved ID/EN LTR and AR RTL;
- Chromium found a 390px Hero ornament/copy collision;
- hamburger was correct at 1180px, but desktop navigation was absent at 1181px;
- real Safari and complete performance/accessibility proof were missing.

## Completed production batch — Hero

Blueprint: `docs/architecture/blueprints/2026-08-01-home-hero-stabilization.md`

Status: `COMPLETE_WITH_EXTERNAL_SAFARI_DEFERRED`
Progress: `98%` of the Hero DoD.

The remaining 2% is external evidence, not unfinished source work: real macOS
Safari and Lighthouse/PageSpeed were unavailable. No Safari or PageSpeed PASS is
claimed.

### Hero ownership

- Controller concern owns normalized image/video/poster/fallback data.
- Hero Blade owns one semantic DOM, heading hierarchy, copy, CTA, controls, and
  the static no-JS state.
- Hero CSS is split by layout, media, motion, controls, responsive, locale/RTL,
  and reduced-motion ownership.
- Hero JS is split by controller/state, media lifecycle, and event lifecycle.
- Navigation assets are no longer loaded through Hero or the language flag.
- Effective equivalence records protect the migrated Hero entry and directly
  affected `welcome.css` module order/checksum.
- Every new Hero source file remains at or below 200 lines.

### Hero media and lifecycle

- Mixed image and direct native-video slides preserve the existing data flow.
- Unsupported or failed video degrades to poster/local fallback without schema
  changes or legacy iframe/YouTube revival.
- Video is muted and inline; only active media is hydrated/prioritized.
- Poster remains visible until video reaches `playing`.
- Inactive, hidden, offscreen, navigating, and disposed media pauses/resets.
- Timers, listeners, observers, transition state, and BFCache behavior are
  deliberately cleaned or resumed.

### Hero transition and access

- Exactly one slide remains active after repeated changes.
- Leaving media scales into bounded blur; entering media resolves from wider
  blur to normal scale without blank or stacked active frames.
- Arrows, dots, pause/play, keyboard, pointer, and touch-safe swipe work.
- Automatic changes never move focus.
- Reduced motion disables autoplay/choreography without hiding content.
- The first semantic slide remains available when JavaScript is disabled.

### Hero responsive, locale, and navigation proof

Chromium runtime proof covered 33 locale-width combinations:

- ID/LTR, EN/LTR, and AR/RTL;
- 360, 390, 640, 768, 1024, 1180, 1181, 1279, 1280, 1536, 1920;
- XS, SM, MD, LG, XL, and 2XL;
- overflow, copy/control collision, title clipping, navbar collision, active and
  transient slide invariants, media/failure state, and navigation mode.

Results:

- 390px ornament/copy collision: PASS;
- hamburger through and including 1180px: PASS;
- desktop navigation from 1181px: PASS;
- one-pixel dead zone: removed;
- ID, EN, AR wrapping/direction: PASS in Chromium;
- LTR/RTL spatial controls: PASS in Chromium.

### Hero automated proof

- `git diff --check`: PASS;
- clean proof checkout: PASS;
- `npx vite build`: PASS;
- focused Hero/navigation: 7 passed, 163 assertions;
- Chromium runtime matrix: 33 cases PASS;
- standard interactions, reduced motion, and no-JS fallback: PASS;
- full Laravel: 144 passed, one stale About test failed, 1375 assertions;
- structure: only three pre-existing Vision/Mission line-limit failures;
- Hero and affected `welcome.css` equivalence: PASS.

### Hero bundle result

- Hero CSS: 22.65 kB → 12.136 kB raw, about 46.4% lower;
- Hero CSS gzip: 4.99 kB → 2.980 kB, about 40.3% lower;
- Hero JS: 9.29 kB → 8.027 kB raw, about 13.6% lower;
- Hero JS gzip: 2.93 kB → 2.667 kB, about 9.0% lower;
- navigation CSS is separately owned at 13.915 kB raw / 3.275 kB gzip.

The Hero entry reduction partly reflects corrected ownership separation. It does
not claim total page CSS fell by the same percentage.

## Active production batch — Unified navbar 3D roll

Blueprint: `docs/architecture/blueprints/2026-08-01-navbar-mega-3d-roll.md`

Blueprint ID: `NAV-MEGA-ROLL-001`
Status: `IMPLEMENTING`
Active surface: unified public navigation

### Goal

Apply the Codrops-inspired 3D text roll to desktop and hamburger top-level,
nested, locale, and CTA labels across all responsive tiers while preserving
static media and existing navigation ownership.

### Source facts

- Server-rendered labels remain the no-JS fallback.
- `data-nav-roll="main"` marks top-level and CTA labels.
- `data-nav-roll="sub"` marks nested links.
- One navbar partial creates two visual layers per enhanced label.
- CSS owns perspective/clipping; the Web Animations API owns bounded timelines.
- ID/EN use grapheme-level stagger; AR rolls the whole word to preserve joining.
- Pointer, focus, click/tap, hamburger entrance, and nested open are supported.
- Reduced motion keeps labels static.
- No Three.js, GSAP, Splitting, WebGL context, RAF loop, or dependency was added.
- Hero reconciliation preserves the latest navbar hooks, orchestration, mobile
  coverage, and source-limit refactor from `main`.

### Navbar roll proof status

Source is published and preserved, but its own complete browser matrix remains
`BLOCKED_BY_MISSING_EVIDENCE`. Hero Chromium proof validates the shared page and
1180/1181 mode boundary; it is not a substitute for the navbar roll's full
pointer/touch/timeline acceptance.

## Deferred external Hero proof

Status: `DEFERRED_TO_REAL_MAC_SAFARI`.

Run on real Safari for macOS, not Epiphany/WebKitGTK:

1. Load ID at 360, 390, 640, 768, 1024, 1180, 1181, 1279, 1280, 1536, and 1920.
2. Resize across every boundary and verify no overflow, collision, blank frame,
   duplicate active slide, or navbar dead zone.
3. Switch ID → EN → AR → ID through POST/session/redirect; verify `lang`, `dir`,
   wrapping, Arabic joining, line-height, controls, and spatial direction.
4. Verify native video is muted, `playsinline`, poster-backed, and pauses when
   inactive, hidden, offscreen, or navigating away.
5. Force video and image failures; verify poster, local image, then stable
   gradient fallback while semantic content stays readable.
6. Repeat arrows, dots, keyboard, pointer, and touch changes; verify transitions
   settle and focus never moves automatically or becomes trapped.
7. Enable Reduce Motion; verify autoplay/motion stop while content and controls
   remain usable.
8. Disable JavaScript; verify the first slide, heading, description, CTA, and
   fallback media remain present without enhanced controls.
9. Navigate away/back; verify BFCache restoration without duplicate listeners,
   timers, or media playback.
10. Inspect Safari Web Inspector console/network/media for errors, repeated
    inactive downloads, autoplay rejection loops, layout shifts, and cleanup.

Lighthouse/PageSpeed/CWV remain `UNMEASURED`, not failed and not passed.

## Open GAP

### `STRUCTURE-GAP-001`

Only three pre-existing Vision/Mission files remain above the 200-line source
gate. Do not fix them inside Hero or navbar scope.

### `TEST-GAP-001`

`HomeAboutReelTest` remains stale against the intentionally disabled protected
About surface. Do not reactivate About merely to turn the suite green.

### `NAV-ROLL-PROOF-GAP-001`

The navbar roll still requires its own full six-tier ID/EN/AR pointer, keyboard,
touch, repeated-open, reduced-motion, Chromium, and real Safari proof.

### `BROWSER-PERF-GAP-001`

Real Safari and measured Lighthouse/PageSpeed/CWV evidence remain external.

### `DEPENDENCY-AUDIT-GAP-001`

The repository security workflow currently fails at `npm audit`. The Hero batch
changed neither `package.json` nor lockfiles, so dependency remediation remains a
separate repository task rather than an unreviewed Hero change.

### `ENGINE-LAB-GAP-001`

No WebGL engine or production/lab scene was introduced. This does not block the
DOM/CSS Hero migration or navbar text effect.

## Accepted decisions

- Preserve one semantic DOM/content source.
- Preserve XS 360–639, SM 640–767, MD 768–1023, LG 1024–1279,
  XL 1280–1535, and 2XL 1536+.
- Preserve hamburger through 1180px and desktop navigation from 1181px.
- Preserve Inter/LTR for ID/EN and Cairo/RTL for AR.
- Touch behavior must not depend on hover.
- Arabic joining must not be broken into independently transformed letters.
- Reduced motion keeps content usable and labels static.
- About and Testimonial remain protected.
- Commit success is not runtime proof.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G00 governance hardening | `PASS` | architecture contracts |
| G01 execution foundation | `PASS` | migration, lab, and handoff rules |
| B00 current baseline | `COMPLETE_WITH_KNOWN_GAPS` | durable evidence |
| P00 Hero pilot selection | `OWNER_ACCEPTED` | owner decision |
| H00–H06 Hero implementation | `PASS` | media, lifecycle, tiers, locale, nav, access |
| H07 Hero automated/runtime proof | `PASS_CHROMIUM` | focused tests and 33-case matrix |
| H08 Hero docs/atomic delivery | `PASS_ON_SQUASH` | blueprint, ledger, equivalence, rollback |
| Safari Hero acceptance | `DEFERRED_TO_REAL_MAC_SAFARI` | owner checklist required |
| N00 navbar media mapping | `PUBLISHED_NOT_RUNTIME_PROVEN` | local media committed |
| N01 navbar text-overlay removal | `PUBLISHED_NOT_RUNTIME_PROVEN` | source committed |
| N02 unified navbar 3D roll | `IMPLEMENTING` | source preserved; full proof missing |
| R00 PageSpeed/CWV | `UNMEASURED` | measured evidence absent |

## STATUS

- Hero source implementation: `COMPLETE`.
- Hero available proof: `PASS`.
- Hero overall: `COMPLETE_WITH_EXTERNAL_SAFARI_DEFERRED`.
- Active repository surface after Hero: unified public navigation roll proof.
- About/Testimonial protection: preserved.
- Heavy animation/WebGL dependencies added by Hero: none.
- Database schema changes: none.

## NEXT VALID STEP

For Hero, run the exact real Mac Safari checklist above and attach screenshots,
console, network, and media evidence. Separately, finish the already-active
navbar-roll proof matrix before selecting another homepage surface. Do not
automatically begin School Values or any other section.
