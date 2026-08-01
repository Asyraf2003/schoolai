# Homepage Hero Akella WebGL Removal

BLUEPRINT ID: `HOME-HERO-REMOVE-AKELLA-WEBGL-001`
STATUS: `OWNER_ACCEPTED`
OWNER: Asyraf Mubarak
DATE: 2026-08-01
SOURCE MAIN SHA: `3761b7f7bbd32e09ff18df60b638517eba73c931`
ACTIVE ROUTE/SURFACE: homepage Hero media transition
TARGET EXECUTION CHANNEL: Web AI with GitHub branch and PR
RELATED HISTORY: `HOME-HERO-WEBGL-DEMO1-001`, `HOME-HERO-SCOPE-CORRECTION-001`

## FACT

- Main contains a project-owned raw WebGL1 transition adapted from Demo 1 of
  `akella/webGLImageTransitions`.
- The production graph includes `webgl.css`, `transition.js`, `direction.js`,
  renderer, shader, program, texture modules, deferred bundle reporting, and
  WebGL-specific source/runtime proof.
- Before that integration, Hero used the existing CSS transition in `motion.css`
  driven by `is-entering` and `is-leaving`, with controller cleanup after 980ms.
- The current owner-approved bright Hero, compact copy, and floating yellow
  chevrons were restored after the WebGL batch and must remain.
- The owner explicitly requires all architecture documents describing the
  integration, scope violation, and correction to remain available.

## GAP

- Real Safari and owner visual proof after the removal remain external.
- Existing unrelated structure, About-test, dependency-audit, navbar-proof, and
  performance gaps remain outside this batch.

## GOAL

Remove the Akella/Demo 1 WebGL effect and every production/testing dependency
that exists solely to support it, restore the pre-Akella CSS transition path,
and preserve the current accepted Hero presentation and all historical docs.

## IMPACT

- No Hero canvas, WebGL context, shader, texture upload, dynamic renderer import,
  WebGL data attributes, or deferred WebGL chunk remains in production.
- Hero continues to change slides using the existing CSS scale/blur/fade motion.
- Current Blade, media source, brightness, copy geometry, chevrons, locale,
  responsive behavior, accessibility, autoplay, and lifecycle remain.
- Historical docs remain and are reclassified as retained history rather than an
  active implementation contract.

## SCOPE IN

- Restore pre-WebGL Hero controller and event wiring.
- Remove production WebGL CSS/JS modules and imports.
- Restore bundle, structure, and runtime proof to the CSS transition contract.
- Replace WebGL-focused tests with absence and CSS-transition assertions.
- Update `UI_UX_CURRENT_STATE.md` with the owner retirement decision.

## SCOPE OUT

- Hero presentation redesign, copy, media URLs, crop, duration, autoplay policy,
  navbar, About, Testimonial, other homepage sections, DB/schema, translations,
  packages, and old architecture documents.

## EDITABLE FILES

- `resources/css/pages/welcome-hero.css`
- `resources/js/surfaces/home/hero/controller.js`
- `resources/js/surfaces/home/hero/events.js`
- WebGL-only Hero CSS/JS files for deletion
- `scripts/hero-bundle-report.mjs`
- `scripts/hero-runtime-proof.mjs`
- `scripts/verify-source-structure.mjs`
- `tests/Feature/HomeHeroStabilizationTest.php`
- this blueprint and `UI_UX_CURRENT_STATE.md`

## READ-ONLY / PRESERVED

- current Hero Blade and accepted presentation CSS modules;
- `docs/architecture/README.md` binding scope-violation record;
- `2026-08-01-home-hero-webgl-demo1.md`;
- `2026-08-01-home-hero-scope-correction.md`;
- `source-module-equivalence-overrides.webgl.json` as retained historical data;
- navbar, About, Testimonial, content, DB, package and lock files.

## DECISION

1. `motion.css` is again the sole production Hero transition renderer.
2. Controller changes slide state immediately, applies `is-entering` and
   `is-leaving`, and removes transient classes after 980ms.
3. Event direction returns to the pre-Akella logical locale behavior without a
   separate Demo 1 direction module.
4. Current bright presentation and floating chevrons stay unchanged.
5. Historical WebGL documents stay in `docs/architecture`; production structure
   validation no longer loads the WebGL equivalence override.
6. No replacement graphics engine or new transition library is introduced.

## SEMANTIC / FALLBACK CONTRACT

- One semantic Blade DOM remains.
- First slide remains meaningful without JavaScript.
- Without WebGL there is no graphics downgrade branch; CSS motion is the normal
  enhanced path.
- Reduced motion changes slide state without transition classes.
- Media failure remains owned by existing poster/local fallback behavior.

## RESPONSIVE / LOCALE CONTRACT

- Preserve XS, SM, MD, LG, XL, and 2XL from 360px upward.
- Preserve exact navigation boundary at 1180/1181.
- Preserve ID/EN LTR and AR RTL.
- Keyboard and swipe remain logical to locale as before the Akella integration.
- No canvas or WebGL state may appear at any tier or locale.

## PROOF

Required:

- diff hygiene;
- structure result recorded honestly;
- Vite production build;
- focused Hero/navigation tests;
- full Laravel result recorded honestly;
- Chromium 33-case ID/EN/AR width matrix;
- exactly two visible chevrons and no rejected rail/dots/playback;
- CSS `is-entering`/`is-leaving` transition appears and settles;
- no Hero canvas, WebGL data state, dynamic renderer import, shader, texture,
  program, or WebGL stylesheet exists in production;
- reduced-motion and no-JS fallback;
- current presentation CSS and protected sections unchanged.

External:

- owner screenshot comparison after pull;
- real Safari/WebKit acceptance;
- measured Lighthouse/PageSpeed/CWV.

## ROLLBACK

Revert the removal squash commit to restore the retired implementation. The
historical blueprints remain in either direction.

## ACTIVE STEP

Implement the bounded removal on `agent/remove-hero-akella-webgl-001`, run the
focused PR proof, update this blueprint/current state with exact evidence, then
squash to `main` only after proof passes.
