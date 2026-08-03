# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Source main before batch: `76f542d610fe28fc71e63781d114784b0f4915eb`
Active branch: `ai/faithful-codrops-depth-gallery-20260803`

Commit publication proves source state only. It does not prove build, browser
fidelity, responsive parity, accessibility, performance, or lifecycle.

## Active production batch

Blueprint: `blueprints/2026-08-03-home-depth-gallery.md`

- ID: `HOME-GALLERY-003`
- State: `IMPLEMENTING`
- Surface: homepage Gallery only
- Owner decision: faithful port of Houmahani Kane / Codrops Atmospheric Depth
  Gallery; only SchoolAI media/text and required homepage integration differ.
- Protected: Hero, Vision/Mission, Values, Programs, Articles, navigation,
  footer, `/galeri`, DB/admin, About, Testimonial, and unrelated typography.

## Runtime FACT

Owner screenshots proved two earlier implementations were not faithful:

- the first placed oversized white content inside HTML cards;
- the second remained a custom HTML/CSS/raw-WebGL scene with an SVG trail;
- plane geometry, camera movement, shader behavior, 3D trail, particles, and
  fixed label overlay did not match the reference source.

## Current source contract

The active branch now ports the reference mechanics:

- deferred Three.js `0.183.0` runtime;
- `PerspectiveCamera(45, 1, 0.1, 100)`;
- textured `PlaneGeometry(3, 3)` meshes;
- plane gap `5`, desktop scale `1`, mobile scale `0.65`, mobile X spread `0.25`;
- current/next plane opacity blending;
- pointer parallax, velocity breath/tilt/scale pulse, and gesture drift;
- orthographic GLSL background with two moving blobs, grain, palette blend,
  depth radius response, and velocity luminance response;
- tapered Catmull-Rom Three.js tube trail and trail-head particles;
- small DOM title/description overlays independent of media geometry;
- SchoolAI media and existing lightbox integration;
- semantic fallback for no JS, reduced motion, CDN/WebGL/texture/context failure.

The rejected custom `renderer.js`, `scene.js`, CSS depth cards, and SVG trail
are removed from the active owner graph.

## Necessary integration differences

- The reference is a standalone demo that intercepts wheel and touch.
- SchoolAI maps the same camera range/smoothing to a sticky homepage section so
  normal page scrolling remains available before and after Gallery.
- Debug pane, FPS meter, Codrops frame/branding, and demo-only controls are not
  shipped.
- Labels use SchoolAI title and optional description in small black text.
- Clicking or pressing Enter/Space on the canvas opens the active SchoolAI media.
- DPR is capped at `1.5` under the SchoolAI quality contract.

## Dependency and CSP decision

- Three.js is dynamically imported from the exact pinned jsDelivr module URL.
- This avoids pretending `package-lock.json` was regenerated when the GitHub
  connector cannot run `npm install`.
- CSP now permits only `https://cdn.jsdelivr.net` for this script/connect path.
- Failure to load the module retains the semantic fallback.
- A future self-hosted/package migration is separate and must preserve behavior.

## Six-tier source contract

| Tier | Contract |
|---|---|
| XS 360–639 | reference mobile scale/spread; labels at bottom |
| SM 640–767 | mobile scale/spread; wider safe-area spacing |
| MD 768–1023 | desktop scale/spread; labels at viewport sides |
| LG 1024–1279 | same scene; larger side insets |
| XL 1280–1535 | same scene; bounded editorial offsets |
| 2XL >=1536 | same scene; wider atmosphere, bounded labels |

One Blade source, controller, renderer, camera, scene, and sequence serve all
six tiers. ID/EN remain LTR. AR uses logical RTL label alignment without
reversing time, plane order, trail growth, or camera depth.

## Source proof status

| Gate | Status | Evidence |
|---|---|---|
| Owner art direction | `PASS` | faithful port explicitly accepted |
| Reference source audit | `PASS` | engine, planes, scroll, background, label, trail, particles inspected |
| Custom owner removal | `PASS_SOURCE` | old raw renderer and DOM scene files deleted |
| Three plane/camera contract | `PASS_SOURCE` | reference numeric values recorded in source/test |
| Background shader | `PASS_SOURCE` | adapted shader and mood/motion uniforms present |
| 3D trail/particles | `PASS_SOURCE` | Catmull-Rom tube and particle pool present |
| SchoolAI data adapter | `PASS_SOURCE` | DB-provided media/title/caption mapped from Blade |
| Semantic fallback | `PASS_SOURCE` | real links/images/title/caption remain |
| Lightbox/keyboard | `PASS_SOURCE` | canvas click/Enter/Space opens active existing lightbox |
| Six-tier architecture | `PASS_SOURCE` | one scene plus declared tier label adapters |
| RTL architecture | `PASS_SOURCE` | logical labels; no locale/scene fork |
| Lifecycle | `PASS_SOURCE` | proximity load, offscreen/hidden stop, BFCache/context failure/disposal |
| License | `PASS_SOURCE` | Codrops MIT notice included |
| File limit | `PASS_SOURCE` | focused test checks all active Gallery JS <=200 lines |
| Focused PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | source updated but not executed locally |
| `npm run check:structure` | `FAIL_PRE_EXISTING` | prior Hero checksum mismatch remains unrelated |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | unavailable in connector |
| Full PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | unavailable in connector |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | faithful branch not rendered yet |
| Lighthouse/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | no comparable runs |

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G01 mandatory docs/current main | `PASS` | read from `76f542d...` |
| G02 reference source audit | `PASS` | actual Three architecture inspected |
| G03 owner acceptance | `PASS` | faithful port approved |
| G04 semantic scene shell | `IMPLEMENTED_SOURCE` | canvas, labels, fallback list |
| G05 Three camera/planes | `IMPLEMENTED_SOURCE` | reference geometry/depth/motion |
| G06 background shader | `IMPLEMENTED_SOURCE` | reference mood and velocity system |
| G07 3D trail/particles | `IMPLEMENTED_SOURCE` | reference-style spatial trail |
| G08 homepage/lightbox lifecycle | `IMPLEMENTED_SOURCE` | sticky progress and bounded activation |
| G09 six-tier/RTL adapters | `IMPLEMENTED_SOURCE` | one architecture with CSS label adapters |
| G10 focused contracts/license/CSP | `IMPLEMENTED_SOURCE` | tests and notice updated |
| G11 automated proof | `BLOCKED_BY_MISSING_EVIDENCE` | local execution required |
| G12 runtime matrix | `BLOCKED_BY_MISSING_EVIDENCE` | browser comparison required |

## STATUS

The faithful Three.js port is implemented in source on the staging branch. It
must not be called complete or visually matched until local tests/build and a
reference comparison screenshot are provided.

## NEXT VALID STEP

After publication, pull `main` and run only the focused Gallery and Security
Headers tests. Report the exact output before visual tuning or six-tier proof.
