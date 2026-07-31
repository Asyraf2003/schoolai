# UI/UX Engineering Contract

Status: ACTIVE
Scope: SchoolAI public UI, motion, responsive behavior, and graphics

## 1. Product architecture

SchoolAI may be cinematic, spatial, and memorable without becoming an opaque demo.
School identity, content, navigation, locale switching, and actions remain the product.

```text
content and DB/lang source
-> semantic Blade DOM
-> shared text/design tokens
-> section/component layout
-> responsive + locale/direction adapters
-> capability-gated motion
-> approved WebGL cinematic scene
```

Every later layer must preserve a usable earlier result.

## 2. Ownership

### Blade and content

- Render meaningful landmarks, headings, links, buttons, labels, and media.
- Keep important text/actions outside canvas/WebGL.
- Use one semantic content tree across ID, EN, AR and width tiers by default.
- Lang files/DB own content, not timings, breakpoints, CSS classes, camera
  positions, shader values, or renderer configuration.
- Duplication requires a proven different interaction model and must still
  share content, semantics, accessibility, and state contracts.

### CSS

- Tokens own repeated values; named components own layout/treatment.
- Current typography owners are live source, not deleted milestone documents.
- Prefer logical properties and direction-aware tokens.
- Responsive layout is CSS/container-rule first.
- Correct the proven owner; do not append global fixes.
- `!important` requires a documented invariant and proven cascade need.
- Do not introduce a site-wide cascade/layer migration without a separate
  accepted blueprint and computed-winner audit.

### JavaScript

- One controller owns one surface's interaction state.
- JS toggles semantic state and orchestrates timelines/renderers.
- JS must not duplicate visible content, choose normal typography by viewport,
  or repair the cascade as its default role.
- Use explicit states where relevant:
  `idle -> loading -> ready -> active -> suspended -> failed -> disposed`.
- Use event delegation where appropriate and one scheduler per motion system.
- Cancel animation frames, observers, timers, listeners, media, and renderer
  work when
  inactive.
- Re-entry, resize, locale reload, BFCache, and repeated open/close must not
  accumulate state owners.

### Graphics

- `UI_UX_WEBGL_3D_PIPELINE.md` owns renderer/model/shader rules.
- One page-level renderer/context/scene director is the default.
- A scene owns purpose and state, not its own unbounded engine copy.
- Assets have a surface/scene owner, activation, fallback, budget, cache, and
  disposal path.
- WebGL integration must be removable without breaking semantic layout.

## 3. Responsive architecture

`UI_UX_RESPONSIVE_LOCALE_MATRIX.md` is normative.
`UI_UX_EXECUTION_FOUNDATION.md` owns fluid migration and dependency direction.

- Certified minimum is 360px.
- Six global tiers start at 360, 640, 768, 1024, 1280, and 1536px.
- 390px remains the primary XS baseline.
- Navigation remains hamburger through 1180px and desktop from 1181px.
- Tiers define available-space behavior, not guessed hardware.
- Every surface blueprint states composition, content flow, media crop,
  controls, motion, and graphics behavior in all six tiers.
- Component/container boundaries may supplement global tiers when measured
  content requires them; record and test both sides.
- Avoid fragile `100vh`; provide fallbacks and test `svh`/`dvh`, orientation,
  safe areas, browser chrome, text expansion, and short viewports.
- Required information/actions cannot rely on hover.

## 4. Locale and direction

- ID and EN share Inter and LTR.
- AR uses Cairo and RTL.
- Locale changes content/direction, not component architecture.
- Use `lang`, `dir`, logical CSS, and direction tokens before locale selectors.
- Locale-specific code is a narrow adapter, not a page/component fork.
- Locale-specific line composition is allowed from measured text need.
- Directional motion mirrors when meaning follows reading direction.
- Non-directional motion stays shared unless an owner-accepted storyboard says
  otherwise.
- Arabic does not reverse time, scroll progress, media playback, numerals, or
  non-directional 3D rotation merely because layout is RTL.
- Verify ID->AR, EN->AR, AR->ID, and AR->EN transitions.

Current locale switching is server-rendered. Enhancements may animate exit/load, but must:

1. keep the form/navigation path functional without JS;
2. block duplicate submits accessibly;
3. suspend outgoing media/motion/renderer before unload;
4. emit new `lang`/`dir` before layout;
5. recompute logical layout, camera anchors, and text geometry;
6. restore focus/scroll intentionally;
7. never show mixed-language duplicate DOM as the fallback.

## 5. Browser strategy

- Required engines are current stable Chromium and Safari/WebKit on desktop and
  mobile/tablet profiles.
- Record browser, OS, hardware, viewport, input, and capability tier.
- Use `@supports` and feature detection; do not fork by user agent.
- Prefixes supplement a usable fallback.
- Engine-specific code requires a reproduced minimal defect, narrow scope,
  proof, and removal condition.
- Explicitly test fixed/sticky/overflow, filters/backdrops, viewport units, autoplay/`playsinline`, touch/pointer/keyboard, focus/inert, font metrics, canvas/context loss, and lifecycle.

## 6. Motion grammar

Every motion pattern has a storyboard:

- purpose and trigger;
- initial, enter, active, exit/reverse;
- interruption, fast/reverse scroll, resize, orientation, and BFCache;
- LTR/RTL meaning;
- reduced-motion/static result;
- duration, easing, distance, scale, blur/filter, and layer ownership;
- activation and cleanup.

Prefer transform and opacity for frequent animation. Profile masks, filters, blur,
clip paths, large backdrops, and layout animation. Scroll storytelling cannot hijack navigation, trap input, or make content unreachable.

## 7. WebGL and cinematic scenes

- WebGL is required for owner-approved cinematic scenes.
- WebGL is progressive fidelity, not progressive access.
- Cinematic scenes share one director/renderer by default; only an accepted
  measured exception permits another simultaneous context.
- No scene count is implied by the six viewport tiers.
- Engine selection resolves `ENGINE-GAP-001` after baseline and an accepted
  first scene blueprint.
- Canvas is decorative/interactive enhancement around semantic content.
- Reduced motion, unsupported graphics, context loss, or constrained runtime
  uses an equivalent poster/DOM path.
- Quality tiers may reduce DPR, texture/model LOD, effects, shadows, particles,
  or update rate while preserving the scene's story and controls.

## 8. Performance

- Primary HTML exposes content/navigation/CTA immediately.
- The real LCP resource is discoverable and not wrongly lazy-loaded.
- Reserve image/video/canvas dimensions.
- Do not preload all scenes, slides, videos, models, fonts, or decoders.
- WebGL engine/assets stay outside the initial critical path.
- Dynamic import is required for non-critical cinematic systems.
- One feature owns initial/deferred bytes, long tasks, render-frame time, memory,
  activation, suspension, failure, and disposal.
- A visual improvement that misses its accepted budget or PageSpeed gate is not
  complete; reduce fidelity or revisit architecture.

## 9. Accessibility

- Native semantics first; ARIA supplements them.
- Preserve skip links, focus order/visibility/restoration, Escape/close, and
  keyboard/touch/pointer equivalence.
- Decorative canvas is hidden from assistive technology.
- Meaningful media has text alternative/caption as appropriate.
- Motion, depth, blur, parallax, color, or audio cannot be the sole information
  carrier.
- Test 200% zoom/text expansion, RTL, reduced motion, contrast, and screen
  readers on the active surface.

## 10. Anti-spaghetti rules

Forbidden without a separate accepted migration:

- new generic `cascade-*`, `fix-*`, `override-*`, or numbered patch modules;
- duplicate controllers for the same state/control;
- complete browser/locale/tier forks;
- arbitrary timeouts or unexplained z-index escalation;
- inline fallback that silently differs from the owned module;
- renderer/media initialization for hidden/disabled surfaces;
- one render loop/context per scene;
- mass deletion before computed winners and behavior are proven.

Name files by owner/purpose, for example:

```text
surfaces/home/hero/layout.css
surfaces/home/hero/controller.js
graphics/scenes/hero.js
```
Split by responsibility, not line count alone.
