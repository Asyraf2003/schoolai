# Homepage Atmospheric Depth Gallery

BLUEPRINT ID: `HOME-GALLERY-003`
STATUS: `IMPLEMENTING`
OWNER: Asyraf
DATE: 2026-08-03
SOURCE MAIN SHA: `dfef1cef25f3de744c3abaf03d1825781c4f9db6`
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

The rejected first implementation used large HTML cards. The rejected second
implementation used CSS depth transforms, a custom raw WebGL background, and an
SVG trail. Neither was a faithful port.

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

The first Three.js integration also failed at runtime. The owner screenshot
proved that only a flat background and HTML labels appeared. Source audit proved
that the canvas was `display: none` while `engine.init()` measured it, producing
a `1x1` drawing buffer. The fallback was then hidden before any visible plane or
healthy post-layout frame was proven. A superseded public Gallery stylesheet was
also still linked from the homepage.

## Scope

SCOPE IN:
- homepage Gallery Blade/CSS/JS owners;
- deferred pinned Three.js runtime;
- WebGL bootstrap, first-frame, resize, failure, and fallback lifecycle;
- removal of the superseded public Gallery stylesheet owner;
- CSP permission for the pinned runtime;
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
  unsupported WebGL, primary texture failure, invalid first frame, or context
  loss.
- The canvas remains in layout but visually hidden, inert, `aria-hidden`, and
  removed from the tab order while Three initializes.
- The controller applies sticky geometry first, waits one animation frame, then
  reruns renderer sizing and a complete frame.
- The fallback is hidden and made inert only when the post-layout frame proves a
  drawing buffer larger than `1x1`, a live context, no GL error, and at least one
  visible media plane.
- When activation succeeds, the canvas becomes the active-media button for
  pointer and keyboard users.
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
- Clicking or pressing Enter/Space on the active canvas opens the active
  SchoolAI item.

## Homepage scroll integration

The reference intercepts wheel/touch because it is a standalone full-screen
demo. SchoolAI cannot hijack the whole homepage.

The accepted adapter:

- keeps reference camera bounds and smoothing;
- maps sticky-section document progress to camera Z;
- retains velocity calculation for breath, trail, and background response;
- preserves normal page scrolling before and after Gallery;
- does not create a second visual choreography.

## Bootstrap and resize contract

- The canvas must never use `display: none` while renderer dimensions are read.
- Pre-activation hiding uses visibility, opacity, and pointer state only.
- Before sticky geometry is active, the drawing-buffer height is bounded to the
  viewport, not the full semantic fallback-list document height.
- After `is-depth-ready` applies sticky geometry, one RAF must elapse before
  `engine.activate()` reruns `resize()` and renders the acceptance frame.
- `ResizeObserver` watches the Gallery viewport. Window resize remains as a
  secondary signal.
- Camera projection, renderer dimensions, plane scale, and plane layout update
  together.
- The primary texture must be present before cinematic activation. Partial
  secondary texture failure may use the plane fallback color without removing
  semantic access.
- A failed frame stops and disposes the engine before returning to the fallback.
- `is-depth-ready` means geometry is prepared. `is-depth-active` means the
  acceptance frame passed. These states must not be conflated again.

## Owner cleanup contract

- `public/css/welcome-gallery-desktop.css` is a losing owner from the former
  `.galeri-story*` composition.
- Its direct homepage `<link>` and file are removed in this batch.
- Historical CSS that also owns other surfaces is not deleted merely because it
  contains Gallery-era names. It requires separate ownership proof.
- No later selector, inline fallback, or z-index escalation may conceal a
  conflicting owner.

## Runtime and dependency decision

- Three.js is pinned to `0.183.0` and dynamically imported only near Gallery.
- The module is currently served from `cdn.jsdelivr.net` because this connector
  cannot truthfully regenerate `package-lock.json` with `npm install`.
- CSP allows only that HTTPS origin for script/connect loading on the homepage.
- CDN or network failure retains the semantic fallback.
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
Declared tier source is not runtime proof; each representative and boundary must
still be rendered.

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
- Reduced-motion preference changes can dispose the cinematic engine and restore
  the semantic fallback without reloading the page.
- Hidden tab, offscreen state, BFCache, permanent page exit, initialization
  failure, invalid frame, and context loss stop or dispose the engine.
- Disposal covers RAF, activation RAF, observers, listeners, renderer, textures,
  materials, geometry, background, trail, particles, and scene references.

## Proof gates

Source:
- no custom `renderer.js`, `scene.js`, CSS card depth, or SVG trail remains;
- Three camera/planes/background/trail parameters match the reference;
- title/description and SchoolAI media are the only visible data substitution;
- canvas remains measurable before initialization;
- post-layout activation requires a healthy rendered frame;
- invalid graphics states preserve the semantic fallback;
- the legacy public Gallery stylesheet is absent and unlinked;
- all active source files are at most 200 lines;
- six-tier and RTL adapters remain one architecture;
- MIT notice and CSP source are present.

Automated:
- `git diff --check`;
- focused Gallery, Gallery bootstrap, and Security Headers tests;
- `npm run check:structure`;
- `npm run build`;
- `php artisan test`.

Runtime:
- drawing buffer matches CSS geometry after activation and after resize;
- representatives and boundaries for all six tiers;
- ID, EN, AR and LTR/RTL transitions;
- Chromium and WebKit;
- pointer, keyboard, touch, fast/reverse scroll, resize, and short height;
- normal/reduced motion, CDN failure, texture failure, invalid frame, and context
  loss;
- visual comparison against the reference for camera, plane position, fade,
  background, trail, particles, and label placement.

## Known blockers

- Required local commands cannot run through the GitHub connector.
- `npm run check:structure` previously failed on the unrelated
  `resources/css/pages/welcome-hero.css` checksum.
- The corrected branch has not yet been rendered by the owner.
- Publication proves source state only, not browser fidelity or performance.
