# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE
Updated: 2026-08-01
Repository: `Asyraf2003/schoolai`
Current source main at batch start: `3761b7f7bbd32e09ff18df60b638517eba73c931`
Baseline audited source: `a580a509fecaaf912d718e56a38695212d41fcc3`
Hero stabilization main SHA: `f77545362801059ef801d480d660ca1ce638f464`
Hero Demo 1 main SHA: `ca99e0be7d339f52d8509381da7cc6ca1162b22b`
Hero correction main SHA: `8c3c84a09833b6ce093cd6a154bc03cb998fc0a3`
Hero Akella removal main SHA: `e579880580a5dcc54aff3fc4bd984cbf6d69ecc5`
Active blueprint: `HOME-HERO-REMOVE-AKELLA-WEBGL-001`
Proven implementation SHA: `62df105ecfa169590de7c8f9d038b981bfe65fea`
Proven documentation head: `f62e9d25151993c6a9dd4027b976e301fb56b1ab`

Commit publication is source evidence only. Browser, accessibility, lifecycle,
and performance claims below are limited to proof explicitly recorded here.

## Durable product contracts

- Laravel 13 + Blade + Vite 8.
- One semantic public DOM across ID/EN/AR and responsive tiers.
- XS, SM, MD, LG, XL, and 2XL remain certified from 360px upward.
- Hamburger remains active through 1180px and desktop navigation from 1181px.
- ID/EN use Inter/LTR; AR uses Cairo/RTL.
- About remains intentionally disabled/protected.
- Testimonial source remains present but unrendered/protected.
- Accepted Hero presentation is bright, uses compact lower-left copy, and
  exposes exactly two floating yellow triple-chevron buttons.
- The rejected bottom rail, counter, dots, and playback control must not return.
- WebGL and graphics are removable enhancement layers, never semantic owners.

## Historical Hero batches retained as evidence

### Hero stabilization

Blueprint: `HOME-HERO-STABILIZATION-001`
Main SHA: `f77545362801059ef801d480d660ca1ce638f464`
Status: `COMPLETE_WITH_EXTERNAL_SAFARI_DEFERRED`

This batch established semantic mixed media, lifecycle ownership, responsive and
locale contracts, no-JS/reduced-motion behavior, and the original CSS transition
using `motion.css`, `is-entering`, `is-leaving`, and 980ms cleanup.

### Demo 1 WebGL

Blueprint: `HOME-HERO-WEBGL-DEMO1-001`
Reference: `akella/webGLImageTransitions`, Demo 1
Main SHA: `ca99e0be7d339f52d8509381da7cc6ca1162b22b`
Status: `HISTORICAL_OWNER_RETIRED`

Its renderer, shader, texture, direction, transition, proof, and technical record
remain documented. The implementation is no longer an accepted production
contract.

### Scope violation and correction

The mandatory architecture index retains these owner-rejected facts:

- visible controls changed beyond the requested transition scope;
- the Hero became darker than the approved presentation;
- poster masking could make native video appear to jump or restart.

Blueprint `HOME-HERO-SCOPE-CORRECTION-001` restored the bright presentation,
compact copy, and two floating chevrons. Those presentation results remain. Its
transparent live-video WebGL reveal was retired by the final owner decision.

## Completed batch — remove Akella WebGL implementation

Blueprint:
`docs/architecture/blueprints/2026-08-01-home-hero-remove-akella-webgl.md`
Blueprint ID: `HOME-HERO-REMOVE-AKELLA-WEBGL-001`
Status: `COMPLETE_ON_MAIN_PENDING_OWNER_VISUAL`
Main SHA: `e579880580a5dcc54aff3fc4bd984cbf6d69ecc5`
Pull request: `#29`

### Owner decision

Remove the Akella/Demo 1 effect and every production/testing dependency used
solely by it. Restore the pre-Akella CSS transition. Preserve the accepted bright
Hero and preserve all architecture documents describing the integration,
mistake, correction, and retirement.

### Completed source change

- Restored controller-owned CSS transition classes and 980ms settlement.
- Restored pre-Akella locale-aware keyboard/swipe event behavior.
- Removed the Hero WebGL stylesheet import.
- Deleted WebGL stylesheet, direction adapter, transition controller, program,
  renderer, shader, and texture modules.
- Removed the dynamic renderer path and WebGL lifecycle state.
- Restored normal bundle reporting without a deferred renderer chunk.
- Replaced WebGL-positive tests with CSS-transition and explicit-absence proof.
- Added active post-WebGL Hero CSS equivalence without rewriting the retained
  historical WebGL equivalence record.
- Preserved Hero Blade, bright presentation CSS, native media, navbar, About,
  Testimonial, content, schema, package, and lock files.

### Automated proof

Proven on implementation SHA `62df105ecfa169590de7c8f9d038b981bfe65fea`
and repeated successfully on exact documentation head
`f62e9d25151993c6a9dd4027b976e301fb56b1ab` before squash:

- Vite 8.1.3: PASS, 84 modules transformed;
- focused Hero/navigation: 9 passed, 214 assertions;
- Chromium: 33 ID/EN/AR viewport cases PASS;
- widths: 360, 390, 640, 768, 1024, 1180, 1181, 1279, 1280,
  1536, and 1920;
- exactly two visible chevrons and no rejected rail/dots/playback: PASS;
- CSS entrance/exit state appears and settles: PASS;
- one active slide after repeated navigation: PASS;
- canvas count zero and WebGL state absent everywhere: PASS;
- standard interaction, media failure, visibility pause, BFCache, reduced
  motion, and no-JavaScript: PASS;
- active Hero CSS equivalence: PASS.

Bundle after removal:

- Hero CSS: 10,657 raw / 2,648 gzip bytes;
- Hero entry JS: 8,027 raw / 2,667 gzip bytes;
- deferred WebGL chunk: absent;
- navigation CSS: 13,915 raw / 3,275 gzip bytes, separately owned.

### Known unrelated results

- Structure report contains only the three pre-existing oversized
  Vision/Mission files: 271, 326, and 610 lines.
- Full Laravel still contains the stale About test for the intentionally disabled
  protected section.
- npm audit still reports one pre-existing high-severity issue.
- None of these were created or modified by the Hero removal.

## Active production batch outside Hero — unified navbar 3D roll

Blueprint: `NAV-MEGA-ROLL-001`
Status: `IMPLEMENTING_BUT_NOT_ACTIVE_IN_THIS_STEP`

The navbar remains separate. Hero rollback changed no navbar source and does not
complete its browser/input proof.

## Open GAP

### `OWNER-VISUAL-ACCEPTANCE-GAP-001`

The owner must pull final `main` and verify the bright UI and restored CSS
transition visually.

### `STRUCTURE-GAP-001`

Three pre-existing Vision/Mission files exceed the 200-line source gate.

### `TEST-GAP-001`

`HomeAboutReelTest` remains stale against the intentionally disabled About
surface. About must not be reactivated to make that test green.

### `NAV-ROLL-PROOF-GAP-001`

Navbar roll still requires its own six-tier ID/EN/AR pointer, keyboard, touch,
repeated-open, reduced-motion, Chromium, and real Safari proof.

### `BROWSER-PERF-GAP-001`

Real Safari and measured Lighthouse/PageSpeed/CWV remain external.

### `DEPENDENCY-AUDIT-GAP-001`

One pre-existing high-severity npm audit issue remains separate.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| H00–H07 Hero stabilization | `PASS_CHROMIUM` | semantic/media/CSS baseline |
| W00 Demo 1 blueprint | `HISTORICAL` | docs retained |
| W01 Demo 1 implementation | `OWNER_RETIRED` | removal requested 2026-08-01 |
| W02 Demo 1 scope violation | `OWNER_REJECTED` | binding record retained |
| C00–C04 correction | `HISTORICAL_PRESENTATION_RETAINED` | bright UI remains |
| R00 removal blueprint | `OWNER_ACCEPTED` | exact owner instruction |
| R01 production graph removal | `PASS` | WebGL production graph absent |
| R02 source/equivalence proof | `PASS_WITH_KNOWN_EXTERNAL_DEBT` | only old Vision files remain |
| R03 Chromium proof | `PASS_CHROMIUM` | 33 cases, CSS transition, zero canvas |
| R04 delivery to main | `COMPLETE_ON_MAIN` | SHA `e579880580a5dcc54aff3fc4bd984cbf6d69ecc5` |
| Owner visual acceptance | `PENDING_PULL` | final screenshot comparison |
| Safari acceptance | `DEFERRED_TO_REAL_MAC_SAFARI` | external proof |
| Navbar roll proof | `BLOCKED_BY_MISSING_EVIDENCE` | separate scope |
| PageSpeed/CWV | `UNMEASURED` | external proof |

## STATUS

- Akella/Demo 1 production implementation: `REMOVED_ON_MAIN`.
- Pre-Akella CSS Hero transition: `RESTORED_AND_PROVEN`.
- Accepted bright presentation and chevrons: `PRESERVED`.
- Historical error, WebGL, and correction documents: `PRESERVED`.
- About/Testimonial protection: `PRESERVED`.
- Package/schema/content changes: none.
- Delivery: `COMPLETE_ON_MAIN_PENDING_OWNER_VISUAL`.

## NEXT VALID STEP

The owner pulls `main` and compares the real Hero against the accepted bright
reference on representative desktop, tablet, and mobile sizes. Leave the Hero
scope closed until that visual proof is supplied. Do not begin another Hero
design or homepage surface from this batch.
