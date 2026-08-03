# Homepage Atmospheric Depth Gallery

BLUEPRINT ID: `HOME-GALLERY-003`
STATUS: `IMPLEMENTING`
OWNER: Asyraf
DATE: 2026-08-03
SOURCE MAIN SHA: `03624783eebe794fbbc383326b25a31806b0dc8a`
ACTIVE ROUTE/SURFACE: homepage Gallery plus bounded transition into `/galeri`
TARGET EXECUTION CHANNEL: Web AI with GitHub connector
REFERENCE: `houmahani/codrops-depth-gallery`
LICENSE: MIT notice under `docs/third-party/`

## Owner goal and acceptance

Port the reference Gallery faithfully without inventing another composition.
SchoolAI media and text replace the reference content, while the standalone demo
scroll is mapped to a sticky homepage section.

The owner accepted these refinements on 2026-08-03 after the corrected desktop
runtime became visible:

- reduce media plane width and height to roughly two thirds of the accepted
  result;
- make every homepage media item a passive preview, not a link or lightbox
  trigger;
- exclude video/embed items from homepage data entirely;
- keep video/embed playback available on the dedicated `/galeri` page;
- place the localized Gallery CTA as the final depth step after all media;
- on plain primary click, enlarge the CTA/viewport, blur, rotate the CTA to
  approximately `45deg`, then navigate to `/galeri`;
- settle the destination page from blur/scale into its normal layout;
- use ordinary navigation for reduced motion, modified clicks, no WebGL, or
  animation API failure;
- replace seeded and runtime dummy captions with meaningful localized school
  documentation copy.

## FACT

The active Three.js implementation uses:

- `PerspectiveCamera(45, 1, 0.1, 100)`;
- textured `PlaneGeometry(3, 3)` meshes along the Z axis;
- plane gap `5` and mobile X spread `0.25`;
- plane opacity blending, pointer parallax, velocity breath/tilt/scale, and
  gesture drift;
- an orthographic GLSL background with animated blobs, grain, palette blend,
  depth response, and velocity response;
- a tapered Catmull-Rom tube trail plus trail-head particles;
- DOM title/description labels independent of media geometry;
- a semantic fallback that remains usable without WebGL.

The prior `1x1` canvas bootstrap failure and superseded public Gallery
stylesheet were already corrected. The latest owner screenshot proves the
corrected desktop runtime now displays the first media plane, background, title,
and caption. It does not prove the six-tier or browser matrix.

Source inspection also proves:

- homepage data previously accepted both photo and video records;
- homepage media previously opened a dedicated lightbox from canvas and fallback
  links;
- `/galeri` has its own controller, cards, and lightbox that create trusted video
  iframes;
- dummy captions originated in the Gallery seeder and could persist in an
  existing database;
- the old CTA existed outside the depth journey.

## Scope

SCOPE IN:
- homepage Gallery query and normalization;
- homepage Gallery Blade/CSS/JS scene owners;
- final depth CTA and route-transition lifecycle;
- destination arrival enhancement on `/galeri`;
- Gallery seed copy;
- focused Gallery tests, blueprint, and current-state ledger.

SCOPE OUT:
- `/galeri` grid, card composition, media trust policy, and lightbox behavior;
- Gallery DB schema and admin CRUD;
- Hero, Vision/Mission, Values, Programs, Articles, navigation, footer, About,
  and Testimonial;
- unrelated historical CSS migration;
- pre-existing Hero checksum mismatch.

## Semantic and media contract

- Homepage displays published photo items only.
- Video/embed records remain stored and remain available on `/galeri`.
- Homepage canvas is decorative and permanently hidden from assistive
  technology; it has no button role, tab stop, accessible action, or click
  handler.
- Homepage fallback items are passive semantic articles containing image, title,
  and optional caption. They are not anchors or buttons.
- The only homepage Gallery action is the localized CTA to `/galeri`.
- No-JS, reduced-motion, unsupported WebGL, texture failure, invalid first frame,
  or context loss exposes the passive list followed by a normal CTA link.
- Meaningful title/caption text remains DOM content.
- Existing `/galeri` cards retain keyboard/pointer lightbox behavior and trusted
  video iframe playback.

## Scene and scale contract

- One Three scene owns all photo planes and the 3D trail.
- One orthographic background scene renders before the depth scene.
- Render order remains background, clear depth, then gallery/trail scene.
- Homepage desktop plane scale is `0.67`; mobile plane scale is `0.44`.
- These values are approximately two thirds of the previous `1` and `0.65`
  scales while preserving intrinsic image aspect ratio.
- Media aspect affects only plane X scale; images remain uncropped.
- X positions and palettes continue cycling through the five reference presets.
- Labels remain small, black, and independent of plane geometry.
- The final media fades during the extra depth step as the CTA enters.

## Final CTA storyboard

| State | Trigger | Result |
|---|---|---|
| unavailable | media sequence active | CTA invisible, inert, and outside tab order |
| entering | scroll passes the final media step | CTA fades/scales from below; labels and final plane fade |
| ready | end progress reaches the accepted threshold | CTA becomes the only interactive Gallery element |
| leaving | plain primary click | renderer stops; viewport enlarges and blurs; CTA enlarges, then rotates to `45deg` and blurs |
| destination | animations finish | normal navigation to `/galeri`; destination main settles from blur/scale |
| interrupted | modified/middle click or reduced motion | browser-native navigation with no intercepted animation |
| failed/static | WebGL or scene fails | ordinary CTA remains after passive fallback list |

The transition uses Web Animations promises rather than an arbitrary timeout.
Session storage only carries a one-use arrival marker; storage failure does not
block navigation.

## Homepage scroll integration

- Sticky-section document progress remains mapped to camera Z.
- One additional plane-gap-equivalent step is reserved after the last media.
- The extra step is included in journey height and camera bounds.
- `endProgress` is calculated only from that final segment.
- Fast/reverse scrolling can move the CTA and final media in both directions.
- Normal page scrolling remains available before and after Gallery.

## Bootstrap, lifecycle, and failure

- Canvas remains measurable before initialization and never uses `display:none`
  while renderer dimensions are read.
- Activation waits for sticky geometry and a healthy post-layout frame.
- Drawing buffer, context, texture, and visible plane/CTA health are checked
  before the semantic fallback is removed.
- `ResizeObserver` plus window resize update renderer, camera, and plane layout.
- One RAF runs only while the section is relevant and the document visible.
- The route transition stops the renderer before expensive blur/scale work.
- Hidden tab, offscreen state, reduced-motion change, BFCache, permanent exit,
  invalid frame, and context loss suspend or dispose owned resources.
- Disposal includes CTA state, RAF, observers, listeners, renderer, textures,
  materials, geometry, background, trail, particles, and scene references.

## Six-tier contract

One Blade source, scene, controller, CTA, and transition serves all tiers.

| Tier | Media and CTA contract |
|---|---|
| XS 360–639 | mobile plane scale `0.44`; labels at bottom; CTA width bounded to 260px |
| SM 640–767 | same mobile scene; wider safe-area spacing |
| MD 768–1023 | desktop scale `0.67`; labels at viewport sides |
| LG 1024–1279 | same scene; larger side insets |
| XL 1280–1535 | same scene and final CTA; bounded editorial offsets |
| 2XL >=1536 | wider atmosphere; CTA and labels remain bounded |

Short-height profiles keep the same sequence. Declared source behavior is not
runtime proof.

## Locale and direction

- ID and EN remain LTR; AR remains RTL.
- Titles, captions, and CTA labels continue using existing localized sources.
- Logical CSS owns label and CTA alignment.
- Arabic does not reverse camera time, plane order, vertical scroll, neutral
  rotation, route-transition rotation, or trail growth.
- Dummy caption detection covers the known ID, EN, and AR seeded strings.

## Dependency and performance decision

- Three.js remains dynamically imported from pinned `0.183.0` near Gallery.
- CSP permission remains homepage-scoped.
- DPR remains capped at `1.5`.
- Homepage excludes video thumbnails/embeds from its query, reducing media work
  and preventing third-party player initialization.
- The route transition adds no dependency and runs only after explicit intent.
- Blur is bounded to the short exit/arrival transition and never runs in the
  continuous scene loop.

## Proof gates

Source:
- homepage query contains `type = photo`;
- fallback media is passive and canvas has no interaction semantics;
- homepage lightbox owner is deleted;
- `/galeri` still retains video iframe/lightbox behavior;
- scales are `0.67` desktop and `0.44` mobile;
- CTA is inside the final journey step and is the sole action;
- exit contains enlarge, blur, and final `rotate(45deg)`;
- reduced motion and modified click use native navigation;
- dummy seed copy is absent and existing dummy DB captions are normalized;
- all active JS files remain at most 200 lines.

Automated:
- `git diff --check`;
- focused Gallery, bootstrap, passive-preview, and Security Headers tests;
- `npm run check:structure`;
- `npm run build`;
- `php artisan test`.

Runtime:
- desktop comparison at the owner screenshot viewport;
- all six tier representatives and affected boundaries;
- ID, EN, AR and LTR/RTL;
- Chromium and WebKit;
- normal and reduced motion;
- keyboard focus on the CTA, pointer/touch, modified click, reverse/fast scroll,
  resize, short height, BFCache, context loss, and transition interruption;
- `/galeri` photo and video lightbox behavior after arrival.

## Known blockers

- Required commands cannot run through the GitHub connector.
- `npm run check:structure` previously failed on the unrelated
  `resources/css/pages/welcome-hero.css` checksum.
- The new smaller scale, final CTA, and route transition have not yet been
  rendered by the owner.
- Publication proves source state only, not six-tier, browser, accessibility, or
  performance completion.
