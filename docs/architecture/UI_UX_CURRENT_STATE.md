# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE
Updated: 2026-08-01
Repository: `Asyraf2003/schoolai`
Baseline audited source: `a580a509fecaaf912d718e56a38695212d41fcc3`
Hero stabilization blueprint: `HOME-HERO-STABILIZATION-001`
Hero WebGL blueprint: `HOME-HERO-WEBGL-DEMO1-001`
Hero stabilization main SHA: `f77545362801059ef801d480d660ca1ce638f464`

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
- production WebGL only when isolated, owner-accepted, deferred, and disposable.

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

## Completed production batch — Hero stabilization

Blueprint: `docs/architecture/blueprints/2026-08-01-home-hero-stabilization.md`

Status: `COMPLETE_WITH_EXTERNAL_SAFARI_DEFERRED`
Progress: `98%` of the stabilization DoD.

The remaining 2% is external evidence, not unfinished source work: real macOS
Safari and Lighthouse/PageSpeed were unavailable. No Safari or PageSpeed PASS is
claimed.

### Stabilized Hero ownership

- Controller concern owns normalized image/video/poster/fallback data.
- Hero Blade owns one semantic DOM, heading hierarchy, copy, CTA, controls, and
  the static no-JS state.
- Hero CSS is split by layout, media, motion, controls, responsive, locale/RTL,
  reduced motion, and the accepted WebGL layer.
- Hero JS is split by controller/state, media lifecycle, input events,
  direction policy, and deferred WebGL lifecycle.
- Navigation assets are not loaded through Hero or the language flag.
- Effective equivalence records protect the migrated Hero entry and directly
  affected `welcome.css` module order/checksum.
- Every Hero CSS/JS source file remains at or below 200 lines.

### Stabilized media and lifecycle

- Mixed image and direct native-video slides preserve the existing data flow.
- Unsupported or failed video degrades to poster/local fallback without schema
  changes or legacy iframe/YouTube revival.
- Video is muted and inline; only active media is hydrated/prioritized.
- Poster remains visible until video reaches `playing`.
- Inactive, hidden, offscreen, navigating, and disposed media pauses/resets.
- Timers, listeners, observers, transition state, and BFCache behavior are
  deliberately cleaned or resumed.
- The first semantic slide remains available when JavaScript is disabled.

### Stabilized responsive, locale, and navigation proof

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

## Completed production batch — Hero Demo 1 WebGL transition

Blueprint: `docs/architecture/blueprints/2026-08-01-home-hero-webgl-demo1.md`

Status: `COMPLETE_WITH_EXTERNAL_BROWSER_PERFORMANCE_DEFERRED`
Reference: `akella/webGLImageTransitions`, Demo 1.

### Product behavior

- Physical right arrow/key/swipe-left wipes right to left.
- Physical left arrow/key/swipe-right wipes left to right.
- Automatic image timer and video-ended transitions follow locale:
  ID/EN right to left, AR left to right.
- Dot navigation resolves direction from logical target and locale.
- The incoming native video starts when its slide becomes active, before the
  WebGL transition begins.
- Automatic changes never move focus.

### WebGL architecture

- No Three.js, Babylon.js, GSAP, dat.GUI, or new npm dependency was added.
- One project-owned WebGL1 fullscreen quad runs the Demo 1-inspired noisy wipe.
- The renderer is dynamically imported and warmed during idle time.
- DPR is capped at 1.5.
- RAF runs only while a transition is active.
- The outgoing frame remains visible while the incoming texture becomes
  drawable, bounded by a 1.2-second staging window.
- The wipe duration is 1.35 seconds.
- Image, poster, and available native-video frames can become textures.
- Remote image texture loading is isolated from native media rendering.
- Cancel, replacement, visibility, offscreen state, pagehide, BFCache,
  reduced motion, context loss, and disposal remove stale canvas/RAF/resources.
- When WebGL or texture creation fails, the established CSS transition remains
  the complete fallback.

### WebGL proof

Proven branch head before documentation closeout:
`321aeeca9cc07cc293651e6a93542ae5650e7778`.

- clean diff/checkout hygiene: PASS;
- Vite 8.1.3 production build: PASS, 90 modules transformed;
- focused Hero/navigation: 9 passed, 205 assertions;
- Chromium responsive/locale matrix: 33 cases PASS;
- physical directions: PASS;
- video-ended automatic direction for ID and AR: PASS;
- image timer automatic direction for ID and AR: PASS;
- repeated transitions and one-active-slide invariant: PASS;
- no stale canvas/transient classes after settlement: PASS;
- hidden media pause, video/image failure, and BFCache restoration: PASS;
- reduced motion and no-JavaScript fallback: PASS;
- Hero source ownership and equivalence: PASS.

### WebGL bundle result

- Hero CSS: 12,953 raw / 3,062 gzip bytes;
- Hero entry JS: 9,751 raw / 3,345 gzip bytes;
- deferred WebGL chunk: 7,126 raw / 2,890 gzip bytes;
- navigation CSS: 13,915 raw / 3,275 gzip bytes, separately owned.

Compared with the stabilized pre-WebGL Hero, the shader cost is isolated in the
deferred chunk rather than being paid by the initial Hero entry. No claim is
made that total page cost or CWV improved without measured Lighthouse evidence.

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
- No Three.js, GSAP, Splitting, WebGL context, RAF loop, or dependency was added
  to the navbar roll.
- Hero work preserves the latest navbar hooks, orchestration, mobile coverage,
  and source-limit refactor from `main`.

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
   duplicate active slide, stale canvas, or navbar dead zone.
3. Switch ID → EN → AR → ID through POST/session/redirect; verify `lang`, `dir`,
   wrapping, Arabic joining, line-height, controls, and automatic wipe direction.
4. Verify physical arrow/key/swipe direction remains physical in ID, EN, and AR.
5. Verify native video begins immediately, is muted/inline/poster-backed, and
   pauses when inactive, hidden, offscreen, or navigating away.
6. Force video/image/WebGL failures and context loss; verify semantic content,
   native media, poster/local image, gradient, and CSS transition fallback.
7. Repeat arrows, dots, keyboard, pointer, and touch changes; verify transitions
   settle, resources disappear, and focus never moves automatically or traps.
8. Enable Reduce Motion; verify autoplay/WebGL stop while content and controls
   remain usable.
9. Disable JavaScript; verify the first slide, heading, description, CTA, and
   fallback media remain present without enhanced controls.
10. Navigate away/back and inspect Web Inspector console/network/media for
    duplicate listeners, inactive downloads, autoplay loops, context errors,
    layout shifts, stale canvas, and cleanup.

Lighthouse/PageSpeed/CWV and representative mobile GPU frame timing remain
`UNMEASURED`, not failed and not passed.

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
Hero WebGL also requires measured frame timing on representative lower-end and
mobile hardware before performance claims are allowed.

### `DEPENDENCY-AUDIT-GAP-001`

The repository security workflow currently reports one high-severity npm audit
issue. Hero WebGL changed neither `package.json` nor lockfiles, so dependency
remediation remains a separate repository task.

## Accepted decisions

- Preserve one semantic DOM/content source.
- Preserve XS 360–639, SM 640–767, MD 768–1023, LG 1024–1279,
  XL 1280–1535, and 2XL 1536+.
- Preserve hamburger through 1180px and desktop navigation from 1181px.
- Preserve Inter/LTR for ID/EN and Cairo/RTL for AR.
- Touch behavior must not depend on hover.
- Arabic joining must not be broken into independently transformed letters.
- Reduced motion keeps content usable and labels static.
- Production WebGL must be deferred, bounded, capability-checked, disposable,
  and backed by semantic/CSS fallback.
- About and Testimonial remain protected.
- Commit success is not runtime proof.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G00 governance hardening | `PASS` | architecture contracts |
| G01 execution foundation | `PASS` | migration, lab, and handoff rules |
| B00 current baseline | `COMPLETE_WITH_KNOWN_GAPS` | durable evidence |
| P00 Hero pilot selection | `OWNER_ACCEPTED` | owner decision |
| H00–H06 Hero stabilization | `PASS` | media, lifecycle, tiers, locale, nav, access |
| H07 stabilization runtime proof | `PASS_CHROMIUM` | 33-case matrix |
| W00 Demo 1 WebGL blueprint | `OWNER_ACCEPTED` | exact direction/video policy |
| W01 WebGL implementation | `PASS` | bounded raw WebGL1, fallback, lifecycle |
| W02 WebGL automated/runtime proof | `PASS_CHROMIUM` | 9 tests, 205 assertions, 33 cases |
| W03 WebGL docs/atomic delivery | `PASS_ON_SQUASH` | blueprint, ledger, equivalence, rollback |
| Safari Hero acceptance | `DEFERRED_TO_REAL_MAC_SAFARI` | owner checklist required |
| N00 navbar media mapping | `PUBLISHED_NOT_RUNTIME_PROVEN` | local media committed |
| N01 navbar text-overlay removal | `PUBLISHED_NOT_RUNTIME_PROVEN` | source committed |
| N02 unified navbar 3D roll | `IMPLEMENTING` | source preserved; full proof missing |
| R00 PageSpeed/CWV/GPU timing | `UNMEASURED` | measured evidence absent |

## STATUS

- Hero stabilization source: `COMPLETE`.
- Hero Demo 1 WebGL source: `COMPLETE`.
- Available Hero proof: `PASS_CHROMIUM`.
- Hero overall: `COMPLETE_WITH_EXTERNAL_SAFARI_PERFORMANCE_DEFERRED`.
- Active repository surface after Hero: unified public navigation roll proof.
- About/Testimonial protection: preserved.
- Third-party animation/WebGL dependencies added by Hero: none.
- Database schema changes: none.

## NEXT VALID STEP

For Hero, run the exact real Mac Safari checklist above and attach screenshots,
console, network, media, context-loss, and frame-timing evidence. Separately,
finish the already-active navbar-roll proof matrix before selecting another
homepage surface. Do not automatically begin School Values or another section.
