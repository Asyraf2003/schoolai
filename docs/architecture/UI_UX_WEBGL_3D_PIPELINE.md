# WebGL, 3D Asset, and Cinematic Scene Pipeline

Status: ACTIVE
Updated: 2026-07-31

## 1. Product position

WebGL is required for owner-approved cinematic scenes when spatial storytelling
adds product value beyond DOM motion.

WebGL is not required to read content, navigate, switch locale, submit a CTA, or
understand the school. It is progressive fidelity, never progressive access.

## 2. Vocabulary

Use `UI_UX_EXECUTION_FOUNDATION.md`:

- six viewport tiers are XS through 2XL;
- a cinematic scene is a bounded spatial story owned by a semantic surface;
- a render frame is one timing sample.

There is no required count of scenes or models. Six tiers never mean six
canvases, engines, bundles, contexts, scene copies, or render loops.

## 3. Architecture

```text
semantic Blade + poster/DOM fallback
-> capability and preference gate
-> deferred engine import
-> shared asset registry
-> one page-level renderer/context
-> scene director
-> active surface scene
-> suspend/dispose/fallback
```

Defaults:

- one canvas, WebGL context, renderer, and RAF scheduler per page;
- one active high-fidelity scene;
- optional adjacent-scene prewarm only within accepted memory/network budget;
- shared world, materials, lighting, loaders, and asset cache where useful;
- scene modules own configuration/state, not engine copies.

Another simultaneous renderer/context requires measured necessity, an accepted
blueprint exception, and full lifecycle proof.

## 4. Cinematic scene contract

Each accepted scene defines:

- school purpose and semantic surface/content/CTA;
- reference/storyboard and what is not copied;
- idle, enter, active, exit/reverse, suspended, failed, and disposed states;
- DOM/camera/object alignment and interruption behavior;
- XS/SM/MD/LG/XL/2XL camera, crop, input, and fallback;
- ID/EN/AR plus LTR/RTL composition;
- normal, reduced-motion, and static results;
- assets/LODs, activation/prewarm/unload, budgets, failure, and proof.

Scene count and narrative order are surface blueprint decisions, not global
architecture.

## 5. Engine decision

No engine exists in current `package.json`. Resolve `ENGINE-GAP-001` only after
the baseline and an accepted first scene blueprint.

| Path | Strength | Risk |
|---|---|---|
| targeted Three.js | custom visuals, ecosystem, mature loaders | accidental bundle/API growth |
| Babylon.js | integrated engine/tooling | larger framework surface |
| raw WebGL2 | low-level control | highest implementation/browser/test cost |
| evaluation hybrid | minimal Three.js core + project-owned lifecycle + measured shaders | strict import/ownership discipline |

Do not install multiple engines in production. Comparisons use the isolated lab
contract and are removed after the ADR.

## 6. Critical-path gate

The initial route requests zero renderer, model, texture, shader, or decoder
bytes as render-blocking resources unless comparative proof accepts an
exception.

Activation requires:

- semantic DOM and fallback already usable;
- reduced-motion/user preference evaluated;
- WebGL capability/context creation tested safely;
- surface near viewport or explicit user intent;
- critical content/LCP not competing for the same load window;
- asset budget available;
- abort path for navigation/locale change.

Dynamic import is mandatory for engine and scene code.

## 7. Asset pipeline

Every asset group records surface/scene owner, purpose, provenance/license,
runtime format, compressed transfer, decoded CPU/GPU cost, LOD variants,
critical/deferred state, activation/prewarm, poster fallback, cache/version, and
unload/disposal behavior.

Preferred measured techniques:

- glTF/GLB runtime delivery;
- Meshopt or Draco only when decode tradeoff passes;
- KTX2/Basis textures with supported fallback;
- instancing for repeated geometry;
- atlas/merge only when measured;
- baked lighting or selective real-time effects;
- right-sized model/texture LODs;
- workers only when support and transfer costs pass.

Lusion's reported 3.5 MB case is evidence of a custom pipeline, not a SchoolAI
budget.

## 8. Quality ladder

Fidelity uses combined measured capability, never a single user-agent, width,
DPR, core, memory, connection, or battery heuristic.

| Tier | Rendering contract |
|---|---|
| 0 static | semantic HTML + poster; no continuous renderer |
| 1 motion | bounded DOM/CSS motion; no WebGL requirement |
| 2 efficient WebGL | reduced LOD/effects, bounded DPR/update |
| 3 high fidelity | richer assets/effects after target-profile proof |

- Tier 2 defaults to `min(devicePixelRatio, 1.5)`.
- Tier 3 may use up to `2` only after profile proof.
- Native DPR is never unbounded.

Downgrade post-processing, shadows, particles, texture/geometry LOD, DPR, and
update rate before changing story meaning.

## 9. Runtime targets

Numeric transfer/memory budgets follow `BASELINE-GAP-001` per scene/profile.
They cannot be quietly relaxed.

- No WebGL-attributable long task over 50 ms during critical input without a
  breakdown and fix.
- Tier 3 targets p95 render-frame work within 16.7 ms on its declared profile.
- Tier 2 targets p95 within 33.3 ms.
- Input handlers never synchronously parse/compile heavy assets.
- Layout reads are batched before writes.
- Inactive/offscreen/hidden scenes perform no continuous rendering.
- Repeated entry/exit cannot grow contexts, listeners, observers, buffers,
  textures, media, or memory without bound.

FPS alone is not proof; record long tasks, input latency, GPU/CPU time where
available, memory/resource counts, and visual correctness.

## 10. Lifecycle

```text
idle -> eligible -> loading -> ready -> active
active -> suspended -> active
loading/active -> failed -> fallback
ready/active/failed -> disposed
```

Suspend on offscreen, hidden tab, blocking overlay when appropriate, locale
unload, and page transition. Dispose on permanent removal/navigation.

Disposal covers RAF, observers, listeners, timers, abort controllers,
geometries, materials, textures, render targets, video/audio, workers, owned
caches, and context when page ownership ends.

Handle context loss/restoration without an infinite loader, scroll lock, lost
focus, layout shift, or missing content.

## 11. Locale, responsive, browser, and failure

- Text stays in DOM unless a decorative 3D label remains accessible.
- Inline-start/end camera anchors mirror for RTL; neutral orbit/time/physics and
  real-world orientation do not.
- Locale reload suspends before unload and rebuilds anchors after fonts,
  `lang`, `dir`, and layout are ready.
- One scene defines fluid camera/canvas/crop/touch/fallback across all six
  viewport tiers; resize/orientation preserves logical state.
- Feature-detect WebGL2/extensions. Use WebGL1 only from an accepted maintenance
  budget; otherwise use Tier 0/1.
- Verify Chromium and Safari/WebKit context, sizing, color, shader precision,
  texture limits, viewport/touch/BFCache, video texture, and cleanup.
- Failure restores poster/DOM, content, CTA, scroll, and focus; retry is bounded
  and user-driven when appropriate.

## 12. Acceptance

No cinematic scene is `PASS` until semantic fallback, six tiers, ID/EN/AR,
LTR/RTL, reduced motion, Chromium/WebKit, asset ledger, activation,
render-frame/long-task evidence, context loss, suspend, dispose, and PageSpeed
delta all have proof.
