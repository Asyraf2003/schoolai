# Homepage Atmospheric Depth Gallery

BLUEPRINT ID: `HOME-GALLERY-003`
STATUS: `IMPLEMENTING`
OWNER: Asyraf
DATE: 2026-08-03
SOURCE MAIN SHA: `76f542d610fe28fc71e63781d114784b0f4915eb`
ACTIVE ROUTE/SURFACE: homepage Gallery only
TARGET EXECUTION CHANNEL: Web AI with GitHub connector
REFERENCE: `houmahani/codrops-depth-gallery`
LICENSE: MIT notice under `docs/third-party/`

## Owner goal and acceptance

Port the reference Gallery faithfully. Do not invent another composition. The
only intended product substitutions are:

- SchoolAI database media replaces the reference flower textures;
- SchoolAI title and optional description replace color-spec labels;
- the standalone demo scroll is mapped to a sticky homepage section so the
  surrounding page remains navigable;
- debug pane, FPS display, Codrops frame/branding, and demo-only controls are
  omitted.

The owner explicitly accepted this engine and art direction on 2026-08-03.

## FACT

The rejected implementation used HTML cards, CSS depth transforms, a custom raw
WebGL background, and an SVG trail. That was not a port of the reference.

Reference source inspection proves the actual system uses:

- Three.js `PerspectiveCamera(45, 1, 0.1, 100)` and WebGLRenderer;
- textured `PlaneGeometry(3, 3)` meshes placed on the Z axis;
- plane gap `5`, camera offsets `5`, desktop scale `1`, mobile scale `0.65`,
  and mobile X spread `0.25`;
- opacity blending between current and next planes;
- pointer parallax, velocity breath, tilt, scale pulse, and gesture drift;
- an orthographic GLSL background with two animated blobs, grain, palette
  blending, depth radius response, and velocity luminance response;
- a tapered Three.js Catmull-Rom tube trail plus trail-head particles;
- small fixed label overlays independent from plane dimensions.

## Scope

SCOPE IN:
- homepage Gallery Blade/CSS/JS owners;
- deferred pinned Three.js runtime;
- CSP permission for that pinned runtime;
- existing lightbox integration;
- focused Gallery and CSP tests;
- MIT notice, blueprint, and current-state ledger.

SCOPE OUT:
- `/galeri` page;
- Hero, Vision/Mission, Values, Programs, Articles, navigation, and footer;
- DB schema/admin/data normalization;
- About and Testimonial activation;
- unrelated historical CSS migration;
- pre-existing Hero checksum mismatch.

## Semantic and fallback contract

- Blade renders one normal link per media item with image, title, and caption.
- The fallback list remains visible with no JS, reduced motion, CDN failure,
  unsupported WebGL, texture failure, or context loss.
- When Three is ready, the fallback list is hidden and made inert.
- The canvas becomes the active-media button for pointer and keyboard users.
- Title and description remain DOM overlay text, not canvas text.
- Existing lightbox semantics, Escape close, and focus restoration remain.

## Faithful scene contract

- One Three scene owns all media planes and the 3D trail.
- One orthographic background scene renders before the depth scene.
- Renderer order is background, clear depth, then gallery/trail scene.
- Camera and plane configuration retain reference numeric values.
- Media aspect ratio changes only plane X scale; images are not cropped.
- X positions and palettes cycle through the five reference presets when
  SchoolAI provides more than five items.
- Labels show title on one side and optional description on the other.
- Labels are small, black, and never change plane geometry.
- Clicking or pressing Enter/Space on the canvas opens the active SchoolAI item.

## Homepage scroll integration

The reference intercepts wheel/touch because it is a standalone full-screen
demo. SchoolAI cannot hijack the whole homepage.

The accepted adapter:

- keeps reference camera bounds and smoothing;
- maps sticky-section document progress to camera Z;
- retains velocity calculation for breath, trail, and background response;
- preserves normal page scrolling before and after Gallery;
- does not create a second visual choreography.

## Runtime and dependency decision

- Three.js is pinned to `0.183.0` and dynamically imported only near Gallery.
- The module is currently served from `cdn.jsdelivr.net` because this connector
  cannot truthfully regenerate `package-lock.json` with `npm install`.
- CSP allows only that HTTPS origin for script/connect loading.
- CDN or network failure immediately retains the semantic fallback.
- A future self-hosting/package migration requires its own lockfile/build proof;
  it must not change scene behavior.
- DPR remains capped at `1.5` under the SchoolAI quality contract.

## Six-tier contract

One scene/controller/canvas/content source serves every tier.

| Tier | Scene and labels |
|---|---|
| XS 360–639 | reference mobile scale/spread; labels at bottom in two columns |
| SM 640–767 | mobile scale/spread; wider safe-area label spacing |
| MD 768–1023 | desktop scale/spread; labels return to viewport sides |
| LG 1024–1279 | same camera/scene; increased side label inset |
| XL 1280–1535 | same camera/scene; bounded editorial side offsets |
| 2XL >=1536 | same camera/scene; wider atmosphere, bounded labels |

Short-height profiles retain the same scene and shift label anchors only.

## Locale and direction

- ID and EN remain LTR; AR remains RTL.
- DOM labels use logical inset and text alignment.
- Arabic does not reverse camera time, plane order, palette chronology, trail
  growth, media orientation, or vertical scroll.
- No locale-specific scene, renderer, or content fork is introduced.

## Lifecycle

- Page entry dynamically imports Gallery controller near the section.
- One RAF runs only while the section is relevant and the document is visible.
- Resize updates camera projection, renderer size, plane scale, and layout.
- Hidden tab, offscreen state, BFCache, permanent page exit, initialization
  failure, and context loss stop or dispose the engine.
- Disposal covers RAF, listeners, renderer, textures, materials, geometry,
  background, trail, particles, and scene references.

## Proof gates

Source:
- no custom `renderer.js`, `scene.js`, CSS card depth, or SVG trail remains;
- Three camera/planes/background/trail parameters match the reference;
- title/description and SchoolAI media are the only visible data substitution;
- all active source files are at most 200 lines;
- six-tier and RTL adapters remain one architecture;
- MIT notice and CSP source are present.

Automated:
- `git diff --check`;
- focused Gallery and Security Headers tests;
- `npm run check:structure`;
- `npm run build`;
- `php artisan test`.

Runtime:
- representatives and boundaries for all six tiers;
- ID, EN, AR and LTR/RTL transitions;
- Chromium and WebKit;
- pointer, keyboard, touch, fast/reverse scroll, resize, short height;
- normal/reduced motion, CDN failure, texture failure, and context loss;
- visual comparison against the reference for camera, plane position, fade,
  background, trail, particles, and label placement.

## Known blockers

- Required local commands cannot run through the GitHub connector.
- `npm run check:structure` previously failed on the unrelated
  `resources/css/pages/welcome-hero.css` checksum.
- Publication proves source state only, not browser fidelity or performance.
