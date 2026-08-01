# Homepage Hero Akella WebGL Removal

BLUEPRINT ID: `HOME-HERO-REMOVE-AKELLA-WEBGL-001`
STATUS: `PROVEN_PENDING_SQUASH`
OWNER: Asyraf Mubarak
DATE: 2026-08-01
SOURCE MAIN SHA: `3761b7f7bbd32e09ff18df60b638517eba73c931`
PROVEN IMPLEMENTATION SHA: `62df105ecfa169590de7c8f9d038b981bfe65fea`
ACTIVE ROUTE/SURFACE: homepage Hero media transition
TARGET BRANCH: `agent/remove-hero-akella-webgl-001`
RELATED HISTORY: `HOME-HERO-WEBGL-DEMO1-001`, `HOME-HERO-SCOPE-CORRECTION-001`

## FACT

- Main previously contained a project-owned raw WebGL1 transition adapted from
  Demo 1 of `akella/webGLImageTransitions`.
- The production graph included `webgl.css`, `transition.js`, `direction.js`,
  renderer, shader, program, texture modules, deferred bundle reporting, and
  WebGL-specific source/runtime proof.
- Before that integration, Hero used the CSS transition in `motion.css`, driven
  by `is-entering` and `is-leaving`, with controller cleanup after 980ms.
- The owner-approved bright Hero, compact copy, and floating yellow chevrons were
  restored after the WebGL batch and had to remain.
- The owner explicitly required all architecture documents describing the
  integration, scope violation, correction, and retirement to remain.

## GOAL

Remove the Akella/Demo 1 WebGL effect and every production/testing dependency
that exists solely to support it, restore the pre-Akella CSS transition path,
and preserve the accepted Hero presentation and all historical documentation.

## IMPLEMENTED DECISION

1. `motion.css` is again the sole production Hero transition renderer.
2. Controller changes slide state, applies `is-entering` and `is-leaving`, and
   removes transient classes after 980ms.
3. Event direction returned to the pre-Akella logical locale behavior without a
   separate Demo 1 direction module.
4. Current bright presentation and floating chevrons remain unchanged.
5. Historical WebGL blueprints and violation/correction records remain in
   `docs/architecture`.
6. Production structure validation no longer consumes the historical WebGL
   equivalence override.
7. Active Hero CSS equivalence is recorded separately in
   `source-module-equivalence-overrides.hero-css.json`.
8. No replacement graphics engine, transition library, package, schema, content,
   or media URL was introduced.

## REMOVED PRODUCTION GRAPH

- `resources/css/surfaces/home/hero/webgl.css`
- `resources/js/surfaces/home/hero/direction.js`
- `resources/js/surfaces/home/hero/transition.js`
- `resources/js/surfaces/home/hero/webgl/program.js`
- `resources/js/surfaces/home/hero/webgl/renderer.js`
- `resources/js/surfaces/home/hero/webgl/shaders.js`
- `resources/js/surfaces/home/hero/webgl/textures.js`
- the Hero WebGL stylesheet import;
- controller/event imports and lifecycle hooks used only by Demo 1;
- deferred renderer bundle reporting;
- WebGL-positive test/runtime expectations.

## PRESERVED

- current Hero Blade and accepted presentation CSS;
- native image/video media and existing failure/poster behavior;
- autoplay, keyboard, pointer, swipe, locale, responsive, BFCache, visibility,
  offscreen, pagehide, no-JS, and reduced-motion behavior;
- exact 1180/1181 navigation boundary;
- ID/EN LTR and AR RTL;
- `docs/architecture/README.md` scope-violation record;
- `2026-08-01-home-hero-webgl-demo1.md`;
- `2026-08-01-home-hero-scope-correction.md`;
- `source-module-equivalence-overrides.webgl.json` as historical evidence;
- navbar, About, Testimonial, other sections, content, DB, package, and lock files.

## SEMANTIC / FALLBACK CONTRACT

- One semantic Blade DOM remains.
- The first slide remains meaningful without JavaScript.
- CSS motion is the normal enhanced path, not a WebGL fallback.
- Reduced motion changes slide state without transient motion classes.
- Media failure remains owned by existing poster/local fallback behavior.
- No canvas, WebGL context, shader, texture upload, or WebGL state may appear.

## PROOF

Proven on implementation SHA `62df105ecfa169590de7c8f9d038b981bfe65fea`:

- diff hygiene: PASS;
- Vite 8.1.3 production build: PASS, 84 modules transformed;
- focused Hero/navigation: 9 passed, 214 assertions;
- Chromium responsive/locale matrix: 33 cases PASS;
- locales: ID, EN, AR;
- widths: 360, 390, 640, 768, 1024, 1180, 1181, 1279, 1280,
  1536, and 1920;
- exactly two visible chevrons: PASS;
- rejected rail, dots, counter, and playback control absent: PASS;
- CSS `is-entering` and `is-leaving` transition appears and settles: PASS;
- one-active-slide invariant after repeated changes: PASS;
- Hero canvas count is zero in all matrix cases and interactions: PASS;
- Hero WebGL data state absent in all matrix cases: PASS;
- standard interaction, reduced motion, and no-JavaScript: PASS;
- image/video failure, visibility pause, and BFCache return: PASS;
- active Hero CSS equivalence checksum: PASS;
- no WebGL stylesheet, adapter, program, renderer, shader, or texture production
  file remains: PASS.

Bundle after removal:

- Hero CSS: 10,657 raw / 2,648 gzip bytes;
- Hero entry JS: 8,027 raw / 2,667 gzip bytes;
- deferred WebGL chunk: absent;
- navigation CSS remains separately owned at 13,915 raw / 3,275 gzip bytes.

Known unrelated results recorded honestly:

- structure gate still reports only three pre-existing oversized Vision/Mission
  files: 271, 326, and 610 lines;
- the full Laravel suite still reports the stale protected About test;
- npm audit still reports one pre-existing high-severity issue;
- package and lockfiles were not changed by this batch.

## EXTERNAL GAP

- Owner visual comparison after pulling final `main`.
- Real Safari/WebKit acceptance.
- Measured Lighthouse/PageSpeed/CWV.

## ROLLBACK

Revert the removal squash commit to restore the retired implementation. The
historical blueprints remain in either direction.

## NEXT VALID STEP

Run proof once more on the documentation-closeout head, mark PR #29 ready, and
squash to `main` only if the exact-head proof passes. Do not begin another Hero
design or homepage surface.
