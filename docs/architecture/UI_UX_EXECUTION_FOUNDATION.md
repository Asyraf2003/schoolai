# UI/UX Execution Foundation

Status: ACTIVE
Owner accepted: 2026-07-31

## 1. Purpose

This contract turns the UI/UX rulebook into an executable, isolated migration
system. It defines vocabulary, target ownership, fluid behavior, production
entry rules, experiments, promotion, and rollback without moving source yet.

The accepted blueprint is
`blueprints/2026-07-31-execution-foundation.md`.

## 2. Canonical vocabulary

- **Six viewport tiers**: XS, SM, MD, LG, XL, and 2XL. The owner's earlier
  phrase “six frames” means these six responsive width contracts.
- **Cinematic scene**: one bounded WebGL/DOM story state owned by a surface.
  There is no required scene count.
- **Render frame**: one browser rendering sample used only for frame-time
  measurement.
- **Surface**: a semantic product area such as navigation, Hero, Gallery, or
  Article teaser.

Do not call cinematic scenes “the six frames.” Six tiers never authorize six
DOM trees, stylesheets, controllers, canvases, renderers, or bundles.

## 3. Inspected starting point

- `resources/css/app.css` already has `foundation`, `shared`, `components`, and
  `pages` ownership roots.
- `resources/views/welcome.blade.php` composes named section partials.
- `vite.config.js` currently exposes many page and leaf CSS/JS entries.
- `resources/css/pages/welcome.css` preserves 47 ordered imports.
- `source-module-equivalence.json` and `verify-source-structure.mjs` protect the
  current mechanical split and import order.
- Homepage JS has a page entry plus smaller imported modules.
- There is no accepted lab build boundary or WebGL dependency.

These are migration constraints. They are not permission for a big-bang rename.

## 4. Target ownership map

Create paths only when the first proven owner is migrated:

```text
resources/views/home/sections/<surface>.blade.php

resources/css/
  foundation/                 shared tokens and primitives
  components/<component>/     reusable component treatment
  surfaces/home/<surface>/    homepage surface layout and state

resources/js/
  core/                       runtime primitives without page assumptions
  components/<component>/     reusable component controllers
  surfaces/home/<surface>/    one surface controller and local motion
  graphics/runtime/           renderer, scheduler, loaders, registry
  graphics/scenes/<surface>/  scene state owned by one surface

resources/labs/<experiment>/  isolated, non-production experiments
```

Shared motion primitives may live under `css/motion/` or `js/motion/` only after
two proven consumers share the same meaning and lifecycle. Surface-specific
motion stays with its surface.

## 5. Entry and dependency direction

```text
foundation -> component -> surface -> route entry
graphics runtime -> surface scene -> surface controller
lab may import stable production primitives
production must never import lab
```

- A route entry owns route composition.
- Migrated leaf modules are imported by their owner entry; they are not all
  permanent Vite inputs.
- Existing entries remain until equivalent loading and runtime proof passes.
- Lang/DB data never stores CSS, breakpoints, camera values, or timelines.
- Cross-surface imports require a promoted component/shared primitive.
- Circular ownership and side-effect-only global imports are forbidden.

## 6. Fluid-by-default contract

Tiers are composition thresholds, not fixed canvases. Between thresholds:

- use intrinsic grid/flex, logical properties, `min()`, `max()`, `clamp()`,
  aspect ratios, and container queries;
- size copy from semantic type roles and readable measures;
- derive media/canvas bounds from their container;
- clamp motion distance and camera framing to visible geometry;
- react to inline size, block size, content length, safe area, input, and
  capability—not width alone;
- preserve logical state across resize/orientation instead of reinitializing.

Fixed values are allowed only for proven invariants such as minimum touch
targets, borders, icon geometry, readable max measures, or reserved media ratio.

Every surface proves:

- boundary pairs and at least one interior width per affected tier;
- short and tall viewport behavior;
- ID, EN, and AR content expansion;
- 200% zoom, orientation, touch/keyboard, and reduced motion;
- Chromium and WebKit.

## 7. Atomic surface migration

One migration batch owns one surface or one tightly coupled capability:

1. Capture current DOM, CSS winners/import order, JS/Vite graph, locale source,
   media, runtime behavior, and proof baseline.
2. Accept a surface blueprint and editable/read-only/forbidden file packet.
3. Identify rules/state that belong to the surface and conflicts that do not.
4. Create only the target folders/files needed by that owner.
5. Move or rewrite the proven owner; remove the superseded owner when safe.
6. Update route imports, Vite entries, equivalence records, and focused tests.
7. Prove semantic equivalence plus the full changed behavior matrix.
8. Update current state and name one NEXT.

Do not split solely to satisfy line count, copy all legacy rules into a new
folder, or leave old and new controllers active together.

## 8. Isolated experiment boundary

Labs are for uncertain motion, shader, camera, renderer, or composition work.
Their implementation blueprint must provide:

- a local/testing-only route such as `/__ui-lab/<slug>`;
- a lab-only Vite config and command, separate from production `npm run build`;
- no production Blade/Vite/import reference;
- synthetic or licensed local data/assets with no production DB mutation;
- one hypothesis, owner, expiry/removal rule, and measurable acceptance gate;
- static/reduced fallback when the experiment evaluates product behavior;
- cleanup of RAF, contexts, media, listeners, observers, and assets.

The normal production build must not emit or preload lab modules. Structure
checks must validate both dependency graphs and reject any production-to-lab
edge.

Lab states:

```text
DRAFT -> ACTIVE -> ACCEPTED or REJECTED
ACCEPTED -> PROMOTED
REJECTED -> REMOVED
```

Promotion is a fresh production patch under an accepted surface blueprint. Do
not make production import the lab or merely rename the experiment folder.

## 9. WebGL integration

- A scene is attached to a semantic surface, not to a viewport tier.
- One page-level renderer/context/scheduler remains the default.
- Scene code and assets are dynamically imported after semantic/LCP readiness.
- Six tiers define camera/crop/quality behavior for the same scene.
- Quality tiers may downgrade rendering independently of layout tiers.
- No engine is selected until baseline proof and a first scene blueprint exist.

## 10. Prompt, handoff, and rollback

- Start work from `UI_UX_PROMPT_TEMPLATES.md`.
- Cross-session transfer uses `UI_UX_HANDOFF_TEMPLATE.md`.
- Preserve the last proven owner until the replacement passes focused proof.
- Rollback removes the new owner/import and restores the prior proven graph;
  it must not require reconstructing deleted source from chat.

## 11. Foundation acceptance

This documentation foundation is complete when terminology is consistent,
mandatory documents link it, templates are usable, the accepted blueprint is
recorded, and governance lint passes.

It does not prove source structure, rendering, build, browser parity, WebGL,
PageSpeed, or CWV. The first source step is the read-only baseline in
`UI_UX_CURRENT_STATE.md`.
