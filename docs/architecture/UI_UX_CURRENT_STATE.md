# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE
Updated: 2026-08-01
Repository: `Asyraf2003/schoolai`
Baseline audited source: `a580a509fecaaf912d718e56a38695212d41fcc3`
Hero stabilization blueprint: `HOME-HERO-STABILIZATION-001`
Hero WebGL blueprint: `HOME-HERO-WEBGL-DEMO1-001`
Hero correction blueprint: `HOME-HERO-SCOPE-CORRECTION-001`
Hero stabilization main SHA: `f77545362801059ef801d480d660ca1ce638f464`
Hero Demo 1 main SHA: `ca99e0be7d339f52d8509381da7cc6ca1162b22b`
Hero correction proven branch SHA: `e6f15793a538dea6392abd7ebacece1cb9f9a250`

Commit publication is source evidence only. Browser, accessibility, lifecycle,
and performance claims below are limited to proof explicitly recorded here.

## Active product goal

Build a distinctive Al Mustaqbal experience while retaining:

- semantic content and usable static/reduced-motion fallbacks;
- fluid XS, SM, MD, LG, XL, and 2XL behavior from 360px upward;
- ID/EN in LTR and AR in RTL;
- measured Chromium and real Safari proof;
- keyboard, touch, pointer, lifecycle, zoom, and short-height access;
- bounded performance cost and maintainable Blade/CSS/JS ownership;
- production WebGL only when isolated, owner-accepted, deferred, disposable,
  visually faithful, and backed by a complete non-WebGL fallback.

## B00 baseline

Status: `COMPLETE_WITH_KNOWN_GAPS`

B00 established these durable facts:

- Laravel 13 + Blade + Vite 8;
- About intentionally disabled and protected;
- Testimonial source exists but is not rendered on the homepage;
- locale output supports ID/EN LTR and AR RTL;
- hamburger is owned through 1180px and desktop navigation from 1181px;
- three pre-existing Vision/Mission files exceed the 200-line gate;
- one stale About test targets the intentionally disabled section;
- real Safari and complete performance evidence remain external.

## Completed production batch — Hero stabilization

Blueprint: `docs/architecture/blueprints/2026-08-01-home-hero-stabilization.md`

Status: `COMPLETE_WITH_EXTERNAL_SAFARI_DEFERRED`

The stabilization established:

- one semantic mixed image/native-video Hero DOM;
- controller-owned normalized media data;
- named Blade/CSS/JS owners rather than anonymous cascade ownership;
- active-media hydration, poster/failure fallback, pause/reset lifecycle, BFCache,
  visibility, offscreen, navigation, and disposal cleanup;
- native arrows, keyboard, pointer, swipe, autoplay, and live-region behavior;
- six responsive tiers, exact 1180/1181 navigation boundary, ID/EN/AR, LTR/RTL;
- no legacy YouTube iframe/path;
- source modules at or below 200 lines.

Chromium stabilization proof covered 33 locale-width combinations and removed
known Hero overflow, collision, and navigation-boundary defects. Real Safari and
measured Lighthouse/PageSpeed remained deferred.

## Completed production batch — Hero Demo 1 WebGL transition

Blueprint: `docs/architecture/blueprints/2026-08-01-home-hero-webgl-demo1.md`
Reference: `akella/webGLImageTransitions`, Demo 1.
Main SHA: `ca99e0be7d339f52d8509381da7cc6ca1162b22b`

Status before owner correction: `TECHNICALLY_PROVEN_BUT_VISUALLY_REJECTED`

Accepted behavior retained from this batch:

- physical right arrow/key/swipe-left wipes right to left;
- physical left arrow/key/swipe-right wipes left to right;
- automatic timer/video-ended changes follow locale: ID/EN right to left,
  AR left to right;
- one project-owned deferred WebGL1 fullscreen quad;
- DPR capped at 1.5 and RAF active only during a transition;
- reduced-motion, no-JavaScript, failure, lifecycle, and CSS fallback;
- no Three.js, Babylon.js, GSAP, dat.GUI, package, or schema change.

Rejected scope expansion recorded in the mandatory architecture index:

- the stabilization had replaced the accepted floating yellow chevrons with an
  unrequested bottom rail, counter, dots, and playback control;
- the visible Hero became darker than the owner-approved design;
- the first video adaptation could show a static poster over an advancing native
  video, creating a jump/restart impression when the canvas disappeared.

The owner rejected those visual and playback changes. They are not accepted art
direction and cannot be copied by future sessions.

## Hero scope correction

Blueprint: `docs/architecture/blueprints/2026-08-01-home-hero-scope-correction.md`
Blueprint ID: `HOME-HERO-SCOPE-CORRECTION-001`
Status: `PROVEN_PENDING_SQUASH`
Proven branch SHA: `e6f15793a538dea6392abd7ebacece1cb9f9a250`

### Corrected product behavior

- Restored the owner-approved bright media treatment.
- Restored compact lower-left copy geometry and bounded text shadows.
- Restored exactly two floating yellow triple-chevron controls.
- Removed the unrequested rail, counter, dots, and playback control.
- Preserved native previous/next buttons, keyboard, pointer, swipe, autoplay,
  locale direction, live region, and one-active-slide ownership.
- Preserved Demo 1 image-to-image shader behavior and physical/automatic
  direction policy.
- Incoming native video starts through the existing controller before the
  transition begins.
- The transition no longer replaces video with a poster or sampled copy.

### Corrected native-video architecture

Incoming video now uses transparent live reveal:

```text
native video activates and play() is attempted
-> transparent WebGL canvas holds the outgoing frame
-> Demo 1 noisy directional mask reduces canvas alpha
-> the same native video playing underneath becomes visible through the mask
-> canvas is removed
-> the same video, stream, and playback position remain
```

Consequences:

- no external video pixels are uploaded into WebGL;
- no CORS-clean third-party video assumption is required;
- native poster/error fallback remains owned by the video layer;
- transition settlement does not call `play()`, `load()`, or change
  `currentTime`;
- image-to-image transitions still mix two normal textures;
- failure to initialize WebGL retains the established CSS fallback.

### Corrected ownership

- Hero Blade owns the two visible chevrons and semantic media/content.
- `layout.css` owns accepted copy geometry and text treatment.
- `media.css` owns the light overlay and native poster/video visibility.
- `controls.css` owns floating chevron geometry and interaction treatment.
- `responsive.css` owns six-tier and short-height adaptation.
- `locale.css` owns RTL mirroring and Arabic typography, not a darker variant.
- `textures.js` owns normal image textures and native-video reveal metadata.
- `shaders.js` owns Demo 1 mask plus transparent video-reveal mode.
- `renderer.js` owns transparent canvas composition and resource cleanup.
- Controller/events/direction remain the single interaction/state path.

### Corrected proof

Proven on branch SHA `e6f15793a538dea6392abd7ebacece1cb9f9a250`:

- diff hygiene: PASS;
- Vite 8.1.3 production build: PASS, 90 modules transformed;
- focused Hero/navigation: 11 passed, 236 assertions;
- Chromium responsive/locale matrix: 33 cases PASS;
- exactly two visible Hero chevrons: PASS;
- rejected rail/dots/playback absent: PASS;
- copy/chevron collision and horizontal overflow: PASS;
- physical next/previous directions: PASS;
- automatic video-ended and image-timer directions for ID and AR: PASS;
- standard interactions, reduced motion, no-JavaScript, media failure, and BFCache:
  PASS;
- native live-video continuity while the Demo 1 canvas is active: PASS;
- same native stream remains after canvas settlement: PASS;
- no stale canvas or transient slide class after settlement: PASS;
- source ownership, source limits, and Hero equivalence: PASS.

The video continuity proof uses a test-only `canvas.captureStream()` MediaStream
attached to the existing native video in Chromium. It verifies advancing frames
and stream identity without changing production content, media URLs, database,
or bundle code.

### Corrected bundle result

- Hero CSS: 11,474 raw / 2,730 gzip bytes;
- Hero entry JS: 9,751 raw / 3,346 gzip bytes;
- deferred WebGL chunk: 7,827 raw / 3,105 gzip bytes;
- navigation CSS remains separately owned at 13,915 raw / 3,275 gzip bytes.

These are bundle sizes, not measured CWV or runtime GPU performance claims.

## Active production batch — Unified navbar 3D roll

Blueprint: `docs/architecture/blueprints/2026-08-01-navbar-mega-3d-roll.md`
Blueprint ID: `NAV-MEGA-ROLL-001`
Status: `IMPLEMENTING`
Active surface: unified public navigation

The navbar roll remains a separate scope. Hero correction did not authorize
navbar redesign or count as complete navbar runtime proof.

Current facts:

- server-rendered labels remain the no-JS fallback;
- CSS owns perspective/clipping and Web Animations API owns bounded timelines;
- ID/EN use grapheme-level stagger;
- AR rolls whole words to preserve joining;
- pointer, focus, click/tap, hamburger entrance, and nested open are represented;
- reduced motion keeps labels static;
- no Three.js, GSAP, Splitting, WebGL context, RAF loop, or dependency was added
  to the navbar roll;
- its own full pointer/touch/timeline/Safari acceptance remains missing.

## Deferred external Hero proof

Status: `DEFERRED_TO_OWNER_AND_REAL_SAFARI`.

After the correction squash reaches `main`:

1. Owner pulls and compares the actual Hero against the accepted bright
   screenshot.
2. Verify the two floating chevrons, copy geometry, brightness, and absence of
   rail/dots/playback at desktop, tablet, and mobile.
3. Verify image-to-image and image-to-video transitions preserve Demo 1 motion.
4. Verify video begins visibly during the wipe and never appears to restart or
   jump when the canvas disappears.
5. Run the same checks in real Safari for macOS, not Epiphany/WebKitGTK.
6. Inspect Safari console/network/media for context errors, autoplay loops,
   inactive downloads, stale canvas, or layout shift.
7. Measure Lighthouse/PageSpeed/CWV and representative lower-end/mobile GPU frame
   timing before making performance claims.

## Open GAP

### `OWNER-VISUAL-ACCEPTANCE-GAP-001`

Automated Chromium proof passed, but the owner has not yet pulled the corrected
squash commit and visually accepted it against the supplied screenshot.

### `STRUCTURE-GAP-001`

Only three pre-existing Vision/Mission files remain above the 200-line gate. Do
not fix them inside Hero or navbar scope.

### `TEST-GAP-001`

`HomeAboutReelTest` remains stale against the intentionally disabled protected
About surface. Do not reactivate About merely to turn the suite green.

### `NAV-ROLL-PROOF-GAP-001`

The navbar roll still requires its own six-tier ID/EN/AR pointer, keyboard,
touch, repeated-open, reduced-motion, Chromium, and real Safari proof.

### `BROWSER-PERF-GAP-001`

Real Safari, Lighthouse/PageSpeed/CWV, representative GPU timing, and measured
WebGL/native-media color parity remain external.

### `DEPENDENCY-AUDIT-GAP-001`

The security workflow reports one pre-existing high-severity npm audit issue.
The Hero correction changed neither `package.json` nor lockfiles, so dependency
remediation remains a separate repository task.

## Accepted decisions

- Explicit current owner scope outranks prior implementation convenience.
- A transition-only task may change transition rendering and minimum state
  plumbing, not unrelated UI or art direction.
- Preserve one semantic DOM/content source.
- Preserve XS 360–639, SM 640–767, MD 768–1023, LG 1024–1279,
  XL 1280–1535, and 2XL 1536+.
- Preserve hamburger through 1180px and desktop navigation from 1181px.
- Preserve Inter/LTR for ID/EN and Cairo/RTL for AR.
- Touch behavior must not depend on hover.
- Arabic joining must not be broken into independently transformed letters.
- Reduced motion keeps content usable and motion static.
- Production WebGL must be deferred, bounded, capability-checked, disposable,
  visually faithful, and backed by semantic/CSS fallback.
- About and Testimonial remain protected.
- Commit success is not runtime or owner visual proof.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G00 governance hardening | `PASS` | architecture contracts |
| G01 execution foundation | `PASS` | migration, lab, and handoff rules |
| B00 current baseline | `COMPLETE_WITH_KNOWN_GAPS` | durable evidence |
| H00–H07 Hero stabilization | `PASS_CHROMIUM` | media, lifecycle, 33-case matrix |
| W00 Demo 1 direction policy | `OWNER_ACCEPTED` | physical/manual and locale/automatic |
| W01 Demo 1 implementation | `PASS_TECHNICAL` | bounded WebGL1 and fallback |
| W02 Demo 1 visual/playback scope | `OWNER_REJECTED` | dark UI, changed controls, poster masking |
| C00 correction blueprint | `OWNER_ACCEPTED` | exact restore/playback contract |
| C01 UI restoration | `PASS_CHROMIUM` | bright UI, two chevrons, no rejected rail |
| C02 native-video reveal | `PASS_CHROMIUM` | advancing same video through canvas |
| C03 correction proof | `PASS_CHROMIUM` | 11 tests, 236 assertions, 33 cases |
| C04 correction delivery | `PASS_ON_SQUASH` | blueprint, ledger, equivalence, rollback |
| Owner visual acceptance | `PENDING_PULL` | screenshot comparison required |
| Safari Hero acceptance | `DEFERRED_TO_REAL_MAC_SAFARI` | external checklist |
| N02 unified navbar roll | `IMPLEMENTING` | full runtime proof missing |
| R00 PageSpeed/CWV/GPU/color | `UNMEASURED` | measured evidence absent |

## STATUS

- Hero stabilization source: `COMPLETE`.
- Demo 1 transition direction and image behavior: `PRESERVED`.
- Rejected dark/control/poster behavior: `CORRECTED_ON_BRANCH`.
- Correction available proof: `PASS_CHROMIUM`.
- Correction delivery: `PENDING_SQUASH_TO_MAIN`.
- Owner visual acceptance: `PENDING_PULL`.
- About/Testimonial protection: preserved.
- Third-party animation/WebGL dependencies added by correction: none.
- Database/schema/content/package changes: none.

## NEXT VALID STEP

Squash PR #28 into `main` after the exact documentation-head proof passes. Then
the owner pulls and visually compares the Hero against the accepted screenshot.
Do not begin another Hero redesign or homepage surface from this correction.