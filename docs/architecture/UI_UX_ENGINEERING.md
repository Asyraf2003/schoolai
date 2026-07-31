# UI/UX Engineering Contract

Status: ACTIVE
Scope: SchoolAI public UI, motion, responsive behavior, and future 3D

## 1. Product goal

SchoolAI may feel cinematic and memorable without turning the site into an
opaque demo. Education content, navigation, locale switching, and primary
actions remain the product. Motion and 3D support that story.

```text
content
-> semantic Blade DOM
-> shared design/text tokens
-> section/component layout
-> locale + direction adapter
-> capability-gated motion
-> optional 3D renderer
```

Every later layer must have a usable result when it is unavailable.

## 2. Ownership model

### Blade and content

- Render meaningful landmarks, headings, links, buttons, labels, and media.
- Keep important copy and actions outside canvas/WebGL.
- Render one content source; do not duplicate ID/EN/AR or mobile/desktop trees
  merely for styling.
- DB and locale files store content, not animation timings, breakpoints, CSS
  classes, font sizes, or renderer configuration.

### CSS

- Tokens own repeated values; component modules own named surfaces.
- The Unified Text System owns migrated font family, size, weight, line-height,
  and tracking.
- Component CSS owns layout, color, decoration, positioning, and intentional
  motion states.
- Prefer logical properties: `margin-inline`, `inset-inline`, `text-align:
  start`, and direction-safe transforms.
- Responsive layout is CSS-first. Use media/container rules and fluid values.
- Do not append a global override file when the actual owner can be corrected.
- Do not add `!important` except for a documented accessibility or utility
  invariant with proven cascade need.
- Do not introduce a new global cascade-layer migration without a blueprint;
  migrate one owned surface at a time.

### JavaScript

- One controller owns one surface's state.
- JS may toggle semantic state classes/attributes and orchestrate timelines.
- JS must not duplicate visible content, choose typography by viewport, or act
  as the normal cascade repair layer.
- Use event delegation where it reduces duplicate listeners.
- Use `requestAnimationFrame` for frame work and keep one scheduler per motion
  system rather than independent loops per node.
- Use `IntersectionObserver` for proximity activation where appropriate.
- Cancel frames, timers, observers, media, and listeners on deactivation.
- State transitions must be explicit: idle, loading, ready, active, suspended,
  failed, and disposed where relevant.

## 3. Responsive architecture

- Canonical public proof widths: 390, 768, and 1440px.
- Test exact component boundaries in addition to canonical widths.
- Navigation changes mode at 1180/1181px.
- Phone/tablet/desktop share DOM and controller by default.
- A separate implementation is allowed only when the interaction model is
  truly different, such as an off-canvas mobile navigation layer.
- A separate implementation still shares content, semantic roles, state
  contract, accessibility behavior, and design tokens.
- Avoid viewport-height assumptions. Provide `vh` fallback before `svh`/`dvh`
  and verify mobile browser chrome behavior.
- Do not rely on hover for required information or actions.

## 4. Locale and direction

- ID and EN use the shared Latin system.
- AR uses Cairo and RTL under the current Arabic contract.
- Locale switching changes content/direction, not the component architecture.
- Use logical CSS and `dir`/`lang` before writing locale selectors.
- Locale-specific CSS/JS is an adapter, not a full page or component copy.
- Different line composition is allowed when language measurement proves it.
- Different animation choreography by locale is not the default.
- Mirror directional entrances, exits, arrows, and progress where meaning
  follows reading direction.
- Keep non-directional motion identical unless an approved art-direction
  storyboard explicitly defines a locale variant.
- Every variant must preserve semantic hierarchy and reduced-motion behavior.

## 5. Browser strategy

- Required engines: current stable Chromium family and current stable
  Safari/WebKit on desktop and mobile.
- Record exact browser/OS versions used for proof; do not claim an untested
  support range.
- Use feature detection and `@supports`, not user-agent forks.
- A prefixed property may supplement, not replace, a usable fallback.
- Verify pointer, touch, keyboard, focus, overflow, fixed positioning,
  backdrop/filter, autoplay, `playsinline`, and stacking/containing blocks.
- Browser-specific code is allowed only for a reproduced engine defect with a
  minimal test case, documented scope, and removal condition.

## 6. Motion system

- Write a storyboard before implementation: trigger, initial state, active
  state, exit, interruption, resize, locale direction, and reduced motion.
- Prefer transform and opacity for frequent animation.
- Filters, masks, blur, clip paths, and large fixed backdrops require profiling.
- Do not animate layout properties continuously without measured justification.
- Scroll-linked motion must not hijack normal navigation or make content
  unreachable.
- Interactions must survive fast scroll, reverse scroll, resize, tab hiding,
  repeated open/close, and route/page restoration.
- Reduced motion removes spatial travel and looping effects while preserving
  information and state changes.

## 7. 3D and graphics

- Start with a semantic HTML/media fallback.
- Load the renderer only after capability, preference, and relevance checks.
- Do not put a renderer in the critical path unless an accepted measurement
  proves it is the best LCP strategy.
- Lazy-load models, textures, shaders, and engine code by proximity or intent.
- Cap device pixel ratio from measured budgets; do not blindly use native DPR.
- Compress geometry/textures and record transfer size plus decoded/GPU cost.
- Pause when offscreen or hidden. Dispose buffers, textures, contexts, and
  listeners when no longer needed.
- Handle renderer failure and context loss without losing content or actions.
- One page should not create multiple continuous render loops or WebGL contexts
  without a measured architecture decision.
- Provide still image/poster and non-motion fallback for unsupported or reduced
  experiences.

## 8. Performance ownership

- The initial server response must expose primary content and navigation.
- Reserve media dimensions to protect CLS.
- Preload only proven critical resources.
- Do not preload all slides, videos, models, fonts, or hidden section assets.
- Inactive videos do not autoplay or decode continuously.
- Dynamic import is required for non-critical cinematic and renderer systems.
- A feature owns its loading, runtime, memory, and disposal measurements.
- A visual improvement that breaks the declared performance gate is incomplete.

## 9. Accessibility ownership

- Native semantics first; ARIA supplements rather than replaces them.
- Preserve skip links, focus order, visible focus, Escape/close behavior, and
  focus restoration.
- Every pointer interaction must have keyboard/touch-equivalent behavior.
- Decorative canvas and motion are hidden from assistive technology.
- Meaningful media has appropriate text alternatives/captions.
- Color, blur, parallax, and animation cannot be the only information carrier.
- Test zoom, text expansion, RTL, and reduced motion.

## 10. Anti-spaghetti rules

Forbidden without an accepted migration decision:

- a new generic `cascade-*`, `fix-*`, or `override-*` module;
- a duplicate controller listening to the same control/state;
- inline CSS/JS fallback that silently differs from the primary module;
- complete files forked by browser, locale, or device;
- unexplained z-index escalation;
- arbitrary timeouts used as state synchronization;
- JS-measured typography when a CSS layout contract can own it;
- renderer/media initialization for hidden or disabled sections;
- mass deletion of legacy rules before computed winners and behavior are proven.

New files should be named by ownership and purpose, for example:

```text
home/vision-mission-layout.css
home/vision-mission-motion.css
home/vision-mission-controller.js
graphics/campus-orbit-renderer.js
```

Split by responsibility, not by line count alone. The 200-line limit is a guard,
not permission to create anonymous numbered fragments.
