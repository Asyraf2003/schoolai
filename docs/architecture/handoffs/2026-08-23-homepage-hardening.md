# Homepage Hardening Handoff — 2026-08-23

Status: `ACTIVE / HARDENING`

## 1. Identity

```text
HANDOFF ID: HOMEPAGE-HARDENING-2026-08-23
DATE: 2026-08-23
REPOSITORY: Asyraf2003/schoolai
SOURCE MAIN SHA: 04e3aa68dc06b0bb2777678538d4e65b01c656ee
RESULTING SHA, IF PUBLISHED: resolve the docs-containing current main commit
ACTIVE BLUEPRINT / STATE: UI_UX_CURRENT_STATE.md / HARDENING_ACTIVE
FROM CHANNEL: Web AI + owner visual/runtime proof
TO EXACTLY ONE CHANNEL: next SchoolAI implementation session
CONTEXT STATUS: durable; chat history not required
```

## 2. Goal and scope packet

```text
OWNER GOAL:
Stabilize the homepage engine before continuing visual polishing. Work slowly,
one bounded capability at a time, and prove each fix before the next one.

ACTIVE SURFACE OR CAPABILITY:
H1 Gallery false-fallback hardening only.

REFERENCE:
Codrops StickySections Demo 3 is a behavior reference for the Gallery -> Article
handoff, not copied implementation ownership. Owner screenshots from 2026-08-23
show the slow-scroll failure.

READ-ONLY FILES FOR H1:
- docs/architecture mandatory chain
- resources/views/home/sections/articles.blade.php
- resources/css/surfaces/home/article-story/*
- resources/css/pages/welcome-hero/* shell/background owners
- vite.config.js
- package.json
- config/filesystems.php

EDITABLE FILES FOR H1:
- resources/js/surfaces/home/gallery-depth/engine-frame.js
- resources/js/surfaces/home/gallery-depth/engine.js only if the proven fallback
  boundary requires it
- resources/js/surfaces/home/gallery-depth/controller.js only if the proven
  fallback boundary requires it
- existing focused Gallery tests needed to prove the fix

FORBIDDEN FILES FOR H1:
- Article composition/layout files unless a direct engine dependency is proven
- Program/Values/Vision/Mission owners
- Footer owners
- Cloudflare/filesystem migration files
- Blade cleanup unrelated to the Gallery runtime defect
- dependency/version changes

PROTECTED SURFACES:
Current visible Gallery composition, Article in-progress composition, temporary
viewport ruler, and tes markers.

EXPECTED OUTPUT:
A minimal Gallery engine fix that prevents a healthy transitional frame from
being classified as an engine failure and prevents static fallback from spawning
under slow forward/reverse scrolling.
```

## 3. Durable state

### FACT

- Slow scrolling through the closing Gallery -> Article region can expose a dark
  green/black shell, spawn the static Gallery fallback, and leave downstream UI
  broken. Faster scrolling can sometimes pass.
- `isDepthFrameHealthy()` currently requires either a visible plane opacity over
  `.01` or a visible end CTA in addition to valid drawing-buffer/context checks.
- `renderDepthFrame()` returning false causes `DepthGalleryEngine.update()` to
  stop and call the failure path.
- `applyFallbackState()` removes Gallery active/ready classes and exposes the
  static fallback.
- The exposed dark surface corresponds to `.nav-shell` background `#071b18`.
- The >500 kB Vite warning is observed during a successful build but is not
  proven to trigger the slow-scroll fallback.
- Gallery currently runtime-loads `three@0.183.0` from jsDelivr while the package
  graph declares `three ^0.185.1`; this is a later hardening concern.

### OWNER-ACCEPTED DECISIONS

- 2026-08-23: freeze visual polishing and enter hardening.
- 2026-08-23: proceed slowly; another session should read rules/docs and resolve
  one capability at a time.
- 2026-08-23: future Blade target is presentation-only, without inline `@php`
  data preparation/business logic.
- 2026-08-23: future media target is Cloudflare-backed object delivery for media
  CRUD/index/display, with performance, responsive, locale, and browser support
  preserved.
- 2026-08-23: final support remains six responsive tiers, ID/EN/AR, Chromium and
  Safari/WebKit, and Lighthouse/PageSpeed `100/100/100/100` on declared profiles.

### OPEN GAPS

- `GAP-H1-01`: prove whether the visible-plane/end-CTA condition is the actual
  false-positive trigger in runtime. Smallest proof: instrument/inspect the
  frame state at the exact slow-scroll transition and reproduce before/after.
- `GAP-H2-01`: explicit Gallery lifecycle state contract is not yet implemented.
- `GAP-H3-01`: authoritative scroll/lifecycle clock still needs a bounded audit
  after H1 if slow/reverse behavior remains nondeterministic.
- `GAP-H4-01`: graphics runtime unification and measured chunking are pending.
- `GAP-H5-01`: Blade presentation-purity migration scope is not yet enumerated.
- `GAP-H6-01`: Cloudflare R2 object-key, URL/CDN, variant, cache, upload/delete,
  and migration contracts are not yet designed.
- `GAP-H7-01`: 36-combination release matrix and PageSpeed/WebKit certification
  remain unrun.

### FILES CHANGED IN THIS HANDOFF

- `docs/architecture/UI_UX_CURRENT_STATE.md`: replace stale continuation state
  with active hardening phase and exactly one NEXT.
- `docs/architecture/handoffs/2026-08-23-homepage-hardening.md`: durable transfer
  packet for future sessions.

No runtime source is changed by this handoff batch.

## 4. Proof

```text
COMMAND OR RUNTIME GATE:
- Owner screenshots + manual slow-scroll reproduction
- Result: FAIL, Gallery can enter static fallback and expose dark shell

COMMAND OR RUNTIME GATE:
- npm run build, owner terminal
- Result: PASS with >500 kB chunk warning

NOT PROVEN:
- H1 fix
- repeated slow/reverse stability
- Safari/WebKit
- six responsive tiers
- ID/EN/AR matrix
- Lighthouse/PageSpeed
- field CWV
- Cloudflare media architecture
- Blade purity migration

PUBLICATION:
- docs only, main, fast-forward required
```

Publication success is not runtime proof.

## 5. Progress and continuation

```text
COMPLETED:
- visual symptom captured
- candidate failure path inspected
- hardening phase accepted
- durable staged backlog documented

ACTIVE OR PAUSED:
- H1 Gallery false-fallback hardening: ACTIVE
- Article visual refinement: PAUSED
- Footer transition: PAUSED

PENDING:
- H2 lifecycle state
- H3 scroll-clock reconciliation
- H4 Three/runtime/chunk hardening
- H5 Blade presentation-purity migration
- H6 Cloudflare media migration
- H7 release certification

STATUS:
HARDENING_ACTIVE

ROLLBACK POINT:
04e3aa68dc06b0bb2777678538d4e65b01c656ee is the inspected runtime-source
checkpoint before docs-only hardening publication.

NEXT EXECUTION CHANNEL:
next SchoolAI implementation session

ONE NEXT VALID STEP:
H1 only: inspect and fix the Gallery frame-health/fallback boundary so a healthy
transition frame cannot trigger static fallback.

EXPECTED NEXT OUTPUT:
Minimal runtime/test patch. No redesign, dependency migration, Blade cleanup,
Cloudflare work, or PageSpeed tuning in the same step.

NEXT PROOF:
Repeated Chromium slow forward and reverse scrolling, including pauses inside
the Gallery ending/handoff, with zero static fallback spawn, zero dark-shell
exposure, and no Gallery engine failure. Then run focused tests and build.
```

The receiving agent must resolve current `main`, read `AGENTS.md` and the
mandatory architecture chain, reconcile this handoff source SHA, and stop if
current source materially conflicts with this packet.

## 6. Codex runtime checkpoint — 2026-08-23

Current-main runtime proof at `e4df67327eaf0a58713afb94168ecccf096758a0`
used HeadlessChrome 151 on Linux x86_64 at 1440 x 913. Two slow
forward/pause/reverse cycles and five rapid forward/reverse cycles produced 302
healthy samples: no fallback after ready, invalid-frame failure, lost context,
or invalid drawing buffer.

H1 nevertheless remains `FAIL`: the scaled Gallery viewport exposes the
body-owned `#071b18` background before Article covers it. The Gallery remains
active and its canvas/context remain healthy during the visible gap, so this is
not the false-fallback path fixed by the published H1 source patch.

H2 is still blocked. The next step requires an owner decision to expand the H1
editable packet narrowly to the Gallery handoff CSS/authoritative scroll-clock
owner, then rerun this same runtime gate.

## 7. H1 proven checkpoint — 2026-08-23

Owner authorized the narrow coverage expansion. Source commit
`19a86d1600943ce858da248171d99a1afc7972c1` moves the transitioning background
owner into `article-handoff.css` and uses the existing Article surface color;
Gallery scale/timing, Article composition, and Gallery JS are unchanged.

The same HeadlessChrome 151, Linux x86_64, 1440 x 913 gate is PASS: two slow
forward/pause/reverse cycles, five rapid forward/reverse cycles, 302 healthy
samples, no fallback/context/frame failure, and no dark-shell exposure at the
early, middle, paused, Article, or reverse checkpoints.

H1 is complete. Exactly one NEXT is H2 Gallery lifecycle/state hardening from
the accepted execution packet.

## 8. H2 proven checkpoint — 2026-08-23

Source/test commit `b10bcb7e044fe4bf3d345aef6120dff5f5b9c7d6`
replaces the Gallery controller's parallel lifecycle booleans with one explicit
state owner. Route exit, visibility, observer, reduced-motion, failure, BFCache
and disposal paths now resolve through that contract; choreography and H1 CSS
are unchanged.

HeadlessChrome 151 on Linux x86_64 at 1440 x 913 passed four repeated
offscreen/resume cycles, hidden/visible tab, persisted BFCache restore,
context-loss fallback, idempotent non-persisted disposal and reduced motion.
There were no state contradictions, runtime errors, duplicate canvases or Three
requests on the reduced path. The H1 gate remained PASS across 302 slow/pause/
reverse/rapid samples.

H2 is complete. Exactly one NEXT is H3 scroll-clock reconciliation from the
accepted execution packet.

## 9. H3 proven checkpoint — 2026-08-23

Source/test commit `9b0eda2aa3651348ff66f77cdbf8dd9acf003818`
separates Gallery authoritative target/end/handoff progress from the existing
smoothed camera and visual-motion clock. End-CTA semantic classes and link state
now consume the authoritative owner without changing visual choreography,
Article or H1/H2 behavior.

HeadlessChrome 151 on Linux x86_64 at 1440 x 913 passed 33 same-position samples
across four boundaries using slow/fast forward/reverse approaches and pauses.
Each position had one semantic signature, with no mismatch, pause drift, runtime
failure or resize contradiction. Visual opacity smoothing remained active. H1
passed 302 samples and the full H2 lifecycle runtime remained PASS.

H3 is complete. Exactly one NEXT is H4 graphics runtime, loading graph and
bundle hardening from the accepted execution packet.
