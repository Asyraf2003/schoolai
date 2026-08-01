# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE
Updated: 2026-08-01
Repository: `Asyraf2003/schoolai`
Current source main at batch start: `3761b7f7bbd32e09ff18df60b638517eba73c931`
Baseline audited source: `a580a509fecaaf912d718e56a38695212d41fcc3`
Hero stabilization main SHA: `f77545362801059ef801d480d660ca1ce638f464`
Hero Demo 1 main SHA: `ca99e0be7d339f52d8509381da7cc6ca1162b22b`
Hero correction main SHA: `8c3c84a09833b6ce093cd6a154bc03cb998fc0a3`
Active blueprint: `HOME-HERO-REMOVE-AKELLA-WEBGL-001`
Active branch: `agent/remove-hero-akella-webgl-001`

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
- Current accepted Hero presentation is bright, uses compact lower-left copy,
  and exposes exactly two floating yellow triple-chevron buttons.
- The rejected bottom rail, counter, dots, and playback control must not return.
- WebGL and graphics are removable enhancement layers, never semantic owners.

## Historical batch — Hero stabilization

Blueprint: `HOME-HERO-STABILIZATION-001`
Main SHA: `f77545362801059ef801d480d660ca1ce638f464`
Status: `COMPLETE_WITH_EXTERNAL_SAFARI_DEFERRED`

The stabilization established the semantic mixed image/native-video Hero,
controller-owned media lifecycle, CSS transition classes, responsive/locale
contracts, no-JS/reduced-motion behavior, and source ownership. Its pre-Akella
transition uses `motion.css`, `is-entering`, `is-leaving`, and bounded controller
cleanup.

## Historical batch — Demo 1 WebGL

Blueprint: `HOME-HERO-WEBGL-DEMO1-001`
Reference: `akella/webGLImageTransitions`, Demo 1
Main SHA: `ca99e0be7d339f52d8509381da7cc6ca1162b22b`
Status: `HISTORICAL_OWNER_RETIRED`

The batch introduced project-owned WebGL1 renderer, shader, program, texture,
direction, transition, deferred bundle, and runtime proof code. Its technical
proof is retained in architecture documents.

The mandatory architecture index also retains the owner-rejected scope facts:

- controls and visible composition changed beyond the transition request;
- the Hero became darker than the approved presentation;
- poster masking could make native video appear to jump or restart.

These records remain historical evidence and must not be deleted or rewritten as
accepted art direction.

## Historical batch — Hero scope correction

Blueprint: `HOME-HERO-SCOPE-CORRECTION-001`
Main SHA: `8c3c84a09833b6ce093cd6a154bc03cb998fc0a3`
Status: `HISTORICAL_PRESENTATION_RETAINED`

The correction restored the accepted bright Hero, compact copy, and two floating
chevrons while removing the rejected rail/dots/playback UI. Those presentation
results remain current. Its transparent live-video WebGL reveal is retired by
the new owner decision.

## Active batch — remove Akella WebGL implementation

Blueprint:
`docs/architecture/blueprints/2026-08-01-home-hero-remove-akella-webgl.md`
Blueprint ID: `HOME-HERO-REMOVE-AKELLA-WEBGL-001`
Status: `IMPLEMENTING`

### Owner decision

Remove the Akella/Demo 1 effect and every production/testing dependency that
exists solely to support it. Restore the transition path that existed before the
repository was adopted. Preserve the current bright Hero UI and preserve all
architecture documents describing the integration, mistake, correction, and
retirement.

### FACT

At batch start, production contained:

- `webgl.css`;
- `direction.js` and `transition.js`;
- WebGL program, renderer, shader, and texture modules;
- dynamic renderer wiring in the Hero controller;
- WebGL-specific bundle, structure, test, and Chromium proof expectations.

The pre-Akella CSS owner remains present in `motion.css` and provides scale,
blur, opacity, and copy entrance/exit animations.

### DECISION

- `motion.css` becomes the sole production Hero transition renderer again.
- Controller/event source returns to the pre-Akella state path.
- Current Hero Blade and bright presentation CSS remain unchanged.
- No new transition library or graphics engine replaces Demo 1.
- Old WebGL blueprints, correction docs, README violation record, and historical
  equivalence data remain in `docs/architecture`.
- Production structure validation no longer consumes the WebGL override.

### Current execution state

Published on the active branch, pending CI proof:

- controller restored to bounded CSS transition cleanup;
- events restored to pre-Akella logical locale behavior;
- WebGL CSS import removed;
- WebGL stylesheet, direction/transition adapter, renderer, shader, program, and
  texture modules deleted from production;
- bundle report restored to normal route entries only;
- runtime proof rewritten for CSS transition and explicit WebGL absence;
- structure gate restored to the normal equivalence override;
- focused tests rewritten to protect CSS rollback, current UI, and retained docs.

## Active production batch outside Hero — unified navbar 3D roll

Blueprint: `NAV-MEGA-ROLL-001`
Status: `IMPLEMENTING_BUT_NOT_ACTIVE_IN_THIS_STEP`

The navbar remains a separate scope. This Hero rollback does not alter navbar
source and does not complete its browser/input proof.

## Open GAP

### `HERO-REMOVE-PROOF-GAP-001`

The removal branch still requires Vite, focused Laravel, structure, full Laravel,
and Chromium runtime proof before merge.

### `OWNER-VISUAL-ACCEPTANCE-GAP-001`

The owner must pull the final main commit and verify the bright UI and CSS
transition visually.

### `STRUCTURE-GAP-001`

Three pre-existing Vision/Mission files exceed the 200-line source gate. Do not
fix them inside Hero scope.

### `TEST-GAP-001`

`HomeAboutReelTest` remains stale against the intentionally disabled About
surface. Do not reactivate About to make that test green.

### `NAV-ROLL-PROOF-GAP-001`

Navbar roll still requires its own six-tier ID/EN/AR pointer, keyboard, touch,
repeated-open, reduced-motion, Chromium, and real Safari proof.

### `BROWSER-PERF-GAP-001`

Real Safari and measured Lighthouse/PageSpeed/CWV evidence remain external.

### `DEPENDENCY-AUDIT-GAP-001`

One pre-existing high-severity npm audit issue remains separate. This Hero batch
must not alter package or lock files.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| H00–H07 Hero stabilization | `PASS_CHROMIUM` | semantic/media/CSS baseline |
| W00 Demo 1 blueprint | `HISTORICAL` | docs retained |
| W01 Demo 1 implementation | `OWNER_RETIRED` | removal requested 2026-08-01 |
| W02 Demo 1 scope violation | `OWNER_REJECTED` | binding README record retained |
| C00–C04 correction | `HISTORICAL_PRESENTATION_RETAINED` | bright UI and chevrons remain |
| R00 removal blueprint | `OWNER_ACCEPTED` | exact owner instruction |
| R01 production graph removal | `IMPLEMENTING` | branch source published |
| R02 automated/runtime proof | `PENDING` | CI required |
| R03 delivery to main | `PENDING` | only after proof |
| Owner visual acceptance | `PENDING_PULL` | final screenshot comparison |
| Safari acceptance | `DEFERRED_TO_REAL_MAC_SAFARI` | external proof |
| Navbar roll proof | `BLOCKED_BY_MISSING_EVIDENCE` | separate scope |
| PageSpeed/CWV | `UNMEASURED` | external proof |

## STATUS

- Active surface: homepage Hero transition implementation.
- Current branch state: Akella/WebGL production graph removed.
- Current presentation: intentionally preserved.
- Historical error/correction documents: preserved.
- About/Testimonial: preserved.
- Package/schema/content changes: none.
- Runtime completion: `BLOCKED_BY_MISSING_EVIDENCE` until CI runs.

## NEXT VALID STEP

Run the focused Hero PR proof on the exact branch head. If source, build,
focused tests, and Chromium matrix pass, mark the removal blueprint `PROVEN`,
update this ledger with exact evidence, and squash the branch to `main`. Do not
begin another Hero design or homepage surface.
