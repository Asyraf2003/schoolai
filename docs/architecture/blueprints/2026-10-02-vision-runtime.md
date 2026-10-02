# MAP-HOME-01 — Vision runtime blueprint

State: IMPLEMENTING. Date:2026-10-02. Owner: homepage owner.
Source main:8c399a280b8cc5df1ae70c39aee8be72803f371c.
Issue:#62. Channel:Terminal Codex. Route:/; section:#visi-misi.

## Goal and scope

Preserve existing About→Vision→Mission text traversal, centered sticky media,
story-driven clips/video, premium visual transitions. Optimize measured runtime
cost without redesign. One active map only. Program and both seam owners remain
read-only. No asset replacement, new engine, typography/content or dependency work.

## Ownership audit

- Blade:home/sections/vision-mission; three articles/figures, two modal openers.
- Content:HomeVisionMissionComposer + home/home_vision locale strings, unchanged.
- CSS import order:base(layout/RTL), background, enhanced, compact, about-video,
  responsive. Typography remains with current global owners.
- Layout:root relative/overflow clip; grid z1 above background z0; >=1024 visual
  container sticky top0,100svh/100dvh; absolute media centered50%, z3/2/1.
  Stories min100svh/100dvh. Media clip wipes + +/-8% transform scale1.08.
- Geometry:measure rectangle/height on prepare and debounced resize; scroll only
  reads cached progress. Controller passive scroll, finite smoothed RAF,
  near IO and visibility/pagehide/pageshow/reduced listeners.
- Timeline:active class/media range event only on change; clip/transform and
  background properties written every RAF even if values do not change.
- Background:two full-height layers inset-2rem; blur0–12px,opacity,drift/scale;
  pattern pseudo blend multiply. Palette rules at1280. Seam ::after untouched.
- Media:preview controller in welcome entry; readiness WeakMap; visible range
  permits two transition videos. Proximity IO starts remaining sources after
  gate unlock. loadeddata settles readiness before future buffer is checked.
- Vite:Vision stylesheet deferred existing entry; story/controller imported in
  preparation after Hero. Modal/previews in welcome entry. No new entry planned.

## Candidate decisions (runtime comparison pending)

| Current | Benefit | Cost | Alternative | Risk | Decision |
|---|---|---|---|---|---|
| Repeat all properties each RAF | Simple state | Redundant writes | Cache emitted values/state | Stale reset/resize | Measure, retain visuals |
| Two filtered drifting background layers | Palette storytelling | Large raster/compositor surface | Static single background or opacity-only palette | Loses story identity | Browser compare first |
| Source1/2 prepare after unlock | Lower early transfer | First-entry decoder/source work | Prepare all Vision previews before ready | Longer gate / bandwidth | Measure baseline, bounded media readiness |
| Geometry cached | No layout reads in scroll | Resize lifecycle | Preserve | Stale mode | Keep; test boundaries |

No CSS deletion. Layout/clip selectors USED_EFFECTIVE. Background effects are
USED_EFFECTIVE pending low-value comparison; UNKNOWN selectors remain.

## Experience, tiers and locale

One semantic DOM and existing copy. No-JS/static/reduced paths expose every story
and poster; two labeled modal actions remain keyboard usable. No WebGL added.
XS360/390, SM640, MD768: existing sequential text/media crop and touch controls.
LG1024: existing sticky cinematic layout;1280/1536+ retain existing columns/crop.
Test1023/1024 and1279/1280;1180/1181 navigation only regression.
ID/EN use Inter/LTR; AR Cairo/RTL with existing logical grid/shadow adaptation.
Vertical wipe meaning is shared; no locale-specific animation or DOM fork.
Test active/idle/failed locale navigation, normal/reduced,200%zoom,short height.

## Lifecycle and media budget

Prepared geometry/controller before activation; finite RAF only while near,
visible and moving. Reverse and fast scroll must not remount or hydrate sources.
Resize/motion changes must reset mode correctly. Hidden/pagehide pause videos;
BFCache resumes without duplicate controllers/listeners. Abort releases pending
work. Failed media retains decoded poster and semantic content, no spinner.
No full download requirement: reserve geometry, poster/frame, source/controller
and enough buffered playback for entry. Continuing byte ranges allowed.
Hero and navigation must remain usable; no initial heavy import added to Hero.

## Active step and proof

Baseline confirms source1/2 absent at unlock in all3 cold runs; Mission sometimes
never starts because viewport pause can reject its pending play and permanently
cache poster fallback. First-pass maxima216.6/99.9/66.7ms. These are diagnostic,
not causal attribution of every hitch. Corrected sampling cancels pending RAF
between passes (original reverse samples could include duplicate callbacks).
No-filter experiment retains late-media defect and max100.1ms; insufficient to
justify removing background effects. Keep all CSS and background story visuals.

DECISION under owner authorization: prepare all Vision previews before Vision
ready; require successful muted playback and future buffer; observers must not
cancel preparation; cache unchanged timeline/background writes; recompute desktop
palette on width-mode change. Gate dependency evidence: all3 cold corrected-media runs are aborted by the
existing8s deadline, removing sources and disabling cinematic layout. This is
not media failure or real readiness. Include the minimum coupled gate correction:
elapsed time may mark a slow preparation, but cannot abort/unlock automatically.
Existing explicit navigation/escape/error fallback stays. No other section or
homepage-wide preparation ordering change. This map does not certify the full
homepage readiness goal; the latter remains NEXT MAP.

ACTIVE: implement these Vision-owned readiness and redundant-write corrections.
PENDING: select smallest supported implementation; update state; targeted runtime
contracts + regression + fullPHP/build/structure/diff; scoped browser matrix;
PR/CI/merge main; close handoff; only then next seam.

Record >=3 cold comparable samples, median/worst, RAF hitches, main/paint/GPU
trace, video buffered/decoded/dropped frames, new sources/requests, screenshots.
Chromium native GPU and available WebKit named accurately; no physical Safari
or field CWV claim without evidence. Browser no-init proof separate from frame
smoothness. Automated green alone does not establish PASS.

Full gate: six tiers + affected boundaries,ID/EN/AR,normal/reduced,keyboard/
pointer/touch,modal focus/Escape,hidden/BFCache,resize/orientation,media errors,
abort and no-JS. Preserve visual composition and seam. No known scoped blocker
before PR/CI/merge. Current status:BLOCKED_BY_MISSING_EVIDENCE (audit ongoing).

Handoff:../handoffs/2026-10-02-home-sequential-optimization.md.
