# WebGL, 3D Asset, and Six-Frame Pipeline

Status: ACTIVE
Updated: 2026-07-31

## 1. Product position

WebGL is required for owner-approved cinematic frames because the target
experience needs spatial storytelling beyond normal DOM motion.

WebGL is not required to read content, navigate, switch locale, submit a CTA,
or understand the school. It is progressive fidelity, never progressive access.

## 2. Architecture

```text
semantic Blade + poster/DOM fallback
-> capability and preference gate
-> deferred engine import
-> shared asset registry
-> one page-level renderer/context
-> one frame director
-> six frame states
-> suspend/dispose/fallback
```

Defaults:

- one canvas, WebGL context, renderer, and RAF scheduler per page;
- one active high-fidelity frame;
- optional adjacent-frame prewarm within accepted memory/network budget;
- shared scene/world, materials, lighting, loaders, and asset cache where useful;
- frame modules own state/configuration, not engine copies.

Another simultaneous renderer/context requires measured necessity, an accepted
blueprint exception, and full lifecycle proof.

## 3. Six-frame contract

The exact content of “six model frames” is `FRAME-GAP-001`. Until resolved, an
agent may design the reusable lifecycle but cannot invent frame subjects,
models, order, or school narrative.

Each accepted frame defines its ID/school purpose, semantic content/CTA,
reference/storyboard, enter/active/exit/reverse, camera/object/DOM alignment,
ID/EN/AR and LTR/RTL composition, six-tier camera/crop, normal/reduced/static
results, assets/LODs, activation/prewarm/unload, budget, failure, and proof.

Six frames must not mean six critical bundles, canvases, contexts, or loops.

## 4. Engine decision

No engine exists in current `package.json`. Resolve `ENGINE-GAP-001` only after
the baseline and frame definition.

Candidate ADR:

| Path | Strength | Risk |
|---|---|---|
| targeted Three.js imports | custom visuals, ecosystem, mature loaders | accidental bundle growth and global abstractions |
| Babylon.js | integrated engine/tooling | larger framework surface |
| raw WebGL2 | low-level control | highest implementation/browser/test cost |
| recommended hybrid | minimal Three.js core + project-owned lifecycle + measured custom shaders | import and ownership discipline required |

Do not install multiple engines for comparison in production. Prototype
comparisons must be isolated and removed after the ADR.

## 5. Critical-path gate

The initial route must request zero renderer, model, texture, shader, or decoder
bytes as render-blocking critical resources unless comparative proof accepts an
exception.

Activation requires:

- semantic DOM and fallback already usable;
- `prefers-reduced-motion` and user preference evaluated;
- WebGL capability/context creation tested safely;
- surface near viewport or explicit user intent;
- critical content/LCP not competing for the same load window;
- asset budget available;
- abort path for navigation/locale change.

Dynamic import is mandatory for engine and frame code.

## 6. Asset pipeline

Every asset group records purpose/owner frame, provenance/license, runtime
format, compressed transfer, decoded CPU/GPU cost, LOD variants,
critical/deferred status, activation/prewarm, poster fallback, cache/version
policy, and unload/disposal behavior.

Preferred measured techniques:

- glTF/GLB runtime delivery;
- geometry compression such as Meshopt or Draco where decode tradeoff passes;
- KTX2/Basis texture compression with supported fallback;
- instancing for repeated geometry;
- atlas/merge only when it improves measured draw/load cost;
- baked lighting or selective real-time effects;
- right-sized texture/model LODs;
- worker/off-main-thread decode only when browser support and transfer cost pass.

Lusion's reported 3.5 MB case study is evidence of a custom optimization
pipeline, not an approved SchoolAI asset budget.

## 7. Quality ladder

Fidelity is selected by combined measured capability, not a single user-agent,
width, DPR, core, memory, connection, or battery heuristic.

| Tier | Rendering contract |
|---|---|
| 0 static | semantic HTML + poster; no continuous renderer |
| 1 motion | DOM/CSS transform/opacity; no WebGL requirement |
| 2 efficient WebGL | reduced LOD/effects, bounded DPR/update rate |
| 3 high fidelity | richer LOD/shaders/effects after target-profile proof |

Provisional DPR:

- Tier 2 defaults to `min(devicePixelRatio, 1.5)`;
- Tier 3 may use up to `2` only after profile proof;
- native DPR is never used unbounded.

Downgrade order normally reduces post-processing, shadows, particles, texture
LOD, geometry LOD, DPR, and update frequency before changing story meaning.

## 8. Runtime targets

Numeric transfer/memory budgets are assigned after `BASELINE-GAP-001`, per
surface and profile. They cannot be quietly relaxed.

Runtime gates:

- no WebGL-attributable long task over 50 ms during critical input without an
  accepted breakdown/fix;
- Tier 3 targets p95 active-frame work within 16.7 ms on its declared profile;
- Tier 2 targets p95 within 33.3 ms;
- scroll/input handlers do not synchronously parse/compile heavy assets;
- layout reads are batched before writes;
- inactive/offscreen/hidden frames do no continuous rendering;
- repeated entry/exit does not grow contexts, listeners, observers, buffers,
  textures, media, or memory without bound.

Frame rate alone is not proof; record long tasks, interaction latency, GPU/CPU
time where available, memory/resource counts, and visual correctness.

## 9. Lifecycle

Required states:

```text
idle -> eligible -> loading -> ready -> active
active -> suspended -> active
loading/active -> failed -> fallback
ready/active/failed -> disposed
```

Suspend on offscreen, hidden tab, open blocking overlay where appropriate,
locale unload, and page transition. Dispose on permanent removal/navigation.

Disposal covers RAF, observers, listeners, timers, loaders/abort controllers,
geometries, materials, textures, render targets, video textures, audio,
workers, caches owned only by the frame, and context when page ownership ends.

Handle `webglcontextlost` and restoration without an infinite loader, scroll
lock, lost focus, or missing content.

## 10. Locale, direction, and responsive

- Text remains DOM unless a proven 3D label is decorative and accessible.
- Camera anchors tied to inline-start/inline-end mirror for RTL.
- Neutral orbit, time, physics, and real-world orientation do not auto-mirror.
- On locale reload, suspend before unload and rebuild measurements/anchors after
  new fonts, `lang`, `dir`, and layout are ready.
- Every frame defines six-tier camera, canvas, crop, touch, and fallback behavior.
- Resize/orientation preserves logical state and avoids context recreation when
  a resize is sufficient.

## 11. Browser and failure

Feature-detect WebGL2 and required extensions. A WebGL1 path is allowed only when
the accepted visual/maintenance budget justifies it; otherwise use Tier 0/1.

Verify Chromium and Safari/WebKit for:

- context creation/loss, canvas sizing, color, shader precision, texture limits;
- resize, safe areas, viewport units, touch/pointer, page hide/show, BFCache;
- video texture autoplay/`playsinline`;
- memory/resource cleanup.

Failure always restores poster/DOM, content, CTA, scroll, and focus without
layout shift. Retry is bounded and user-driven when appropriate.

## 12. Acceptance

No frame is `PASS` until semantic fallback, all six tiers, ID/EN/AR, LTR/RTL,
reduced motion, Chromium/WebKit, asset ledger, activation, frame-time/long-task,
context-loss, suspend, dispose, and PageSpeed impact have proof.
