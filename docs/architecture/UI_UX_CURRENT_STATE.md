# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE
Audited: 2026-07-31
Repository: `Asyraf2003/schoolai`
Source `main`: `e4ecdd6eaf70847635cd479462ef007ba6c945c9`

## Active product goal

Build a distinctive Al Mustaqbal school experience with Lusion-class visual
polish, motion, spatial storytelling, and meaningful 3D while retaining:

- semantic school content and usable static/reduced fallbacks;
- fluid behavior across six viewport tiers from 360px upward;
- ID/EN in LTR and AR in RTL;
- current Chromium and Safari/WebKit;
- accessibility, input, orientation, and short-height behavior;
- Lighthouse/PageSpeed `100/100/100/100`;
- field CWV `3/3` only when p75 evidence proves it;
- isolated experiments and maintainable Blade/CSS/JS/WebGL ownership.

## FACT

- The application is Laravel 13 + Blade + Vite 8.
- Locale switching is server-rendered:
  `POST /bahasa/{locale}` -> session/cookie -> redirect -> new Blade response.
- Public `<html>` emits `lang` and `dir`; ID/EN use Inter/LTR and AR uses
  Cairo/RTL.
- `package.json` currently declares no 3D engine.
- `verify-source-structure.mjs` enforces 200 lines for configured PHP, Blade,
  JS, and CSS roots and checks source imports.
- `resources/css/app.css` already composes `foundation`, `shared`,
  `components`, and `pages` roots.
- `welcome.blade.php` composes named section partials.
- `vite.config.js` exposes many page and leaf CSS/JS entries.
- `resources/css/pages/welcome.css` preserves 47 ordered imports.
- `source-module-equivalence.json` protects the current mechanical split and
  import order; it does not prove component ownership.
- Homepage JS has a page entry with smaller imported modules.
- Existing source uses some `requestAnimationFrame`, `IntersectionObserver`,
  logical properties, lazy imports, and reduced-motion handling.
- Some typography is measured in JS, creating an ownership tension that must be
  audited per surface.
- About is disabled in rendered homepage while its source remains.
- Testimonial source/assets exist without proven active homepage rendering.
- The owner intentionally removed old milestone/error-log documents. Their
  percentages and `PASS` claims are not reusable.
- The owner accepted 360px as certified minimum and 390px as primary XS
  baseline.
- The owner clarified that “six frames” means XS, SM, MD, LG, XL, and 2XL. It
  does not mean six cinematic scenes, models, canvases, or renderers.
- The owner requires WebGL for approved cinematic scenes with strict
  performance governance and functional fallback.

## Resolved foundation decisions

The active package now defines:

- strict FACT/GAP/decision/blueprint/one-step/proof/progress workflow;
- six fluid viewport tiers plus exact representative and boundary proof;
- ID/EN/AR and LTR/RTL transition lifecycle;
- Chromium/WebKit and accessibility contracts;
- PageSpeed/CWV evidence boundaries;
- WebGL renderer, asset, quality, failure, and disposal lifecycle;
- target source ownership and atomic surface migration;
- isolated non-production experiments and promotion/removal;
- audit, edit, build, experiment, continuation, and handoff templates.

Canonical terms are in `UI_UX_EXECUTION_FOUNDATION.md`. The former scene-count
misunderstanding is removed.

## Open GAP

### `BASELINE-GAP-001`

No current built-asset, Lighthouse/PageSpeed, WebKit, screenshot, long-task,
render-frame time, GPU-memory, or field CWV baseline is committed.

Impact: numeric feature delta budgets, pilot selection, and quality `PASS`
remain unproven.

Smallest proof: a full checkout running build/asset inventory plus repeatable
Chromium/WebKit runtime samples before source migration.

### `TEXT-GAP-001`

Current typography source exists, but no replacement audit proves full surface
coverage after the old milestone documents were removed.

Impact: each active surface must inspect semantic roles, lang/DB content,
computed type, Arabic joining/clipping, and ID/EN/AR wrapping.

### `MIGRATION-GAP-001`

No surface has a current ownership map proving the safest first pilot.

Impact: target folders and migration rules are accepted, but production files
must not be moved merely to populate the structure.

Smallest proof: complete `BASELINE-GAP-001`, then rank bounded pilot candidates
by isolation, dependency count, visual value, and proof cost.

### `ENGINE-GAP-001`

WebGL is accepted as a capability, but the engine is intentionally unselected.
This does not block DOM/CSS ownership migration or the baseline.

Question purpose later: choose the smallest runtime that can deliver the first
accepted cinematic scene without undermining PageSpeed, lifecycle, or WebKit.

| Option | Benefit | Cost/risk |
|---|---|---|
| A — targeted Three.js | mature loaders and custom cinematic control | import/API discipline required |
| B — Babylon.js | integrated engine/tooling | larger abstraction surface |
| C — raw WebGL2 | maximum low-level control | highest build/test/maintenance cost |
| Hybrid — recommended for evaluation | minimal Three.js core + owned lifecycle + measured custom shaders | must prove the boundary stays small |

Smallest future decision input: B00 baseline plus one accepted cinematic scene
blueprint and asset needs. Do not select/install an engine before that.

### `AUDIT-GAP-001`

Mandatory docs are manually linked, but no repository script rejects a broken
UI/UX documentation chain.

Impact: manual governance proof is valid; automated enforcement remains a
separate source capability requiring a full checkout and tests.

## Accepted decisions

- Use one semantic DOM and content source by default.
- Use fluid XS, SM, MD, LG, XL, and 2XL contracts; tiers are not device
  detection or separate implementations.
- Preserve minimum 360px, baseline 390px, and navigation 1180/1181px.
- Use Inter/LTR for ID/EN and Cairo/RTL for AR until source proof changes it.
- Keep current server locale flow; suspend outgoing enhancements and initialize
  new logical geometry after the redirected document is ready.
- Create target folders only while migrating a proven owner.
- Migrate one atomic surface/capability under an accepted blueprint.
- Keep production and lab dependency graphs separate; production never imports
  or bundles lab code.
- WebGL attaches to semantic cinematic scenes, not viewport tiers.
- Use one page-level WebGL context/renderer/scheduler by default.
- Keep renderer/model/texture/decoder bytes outside the initial critical path.
- Downgrade fidelity before content or interaction meaning.
- Do not big-bang rewrite the homepage or append anonymous cascade patches.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G00 governance hardening | `PASS` | decision, session, matrix, DOD, and proof contracts |
| G01 execution foundation | `PASS` | terminology, migration, lab, prompt, and handoff contracts |
| B00 current baseline | `BLOCKED_BY_MISSING_EVIDENCE` | `BASELINE-GAP-001` |
| P00 first pilot selection | `BLOCKED_BY_MISSING_EVIDENCE` | `MIGRATION-GAP-001` |
| S00 first surface migration | `BLOCKED_BY_MISSING_EVIDENCE` | baseline + pilot blueprint absent |
| E00 WebGL engine ADR | `BLOCKED_BY_MISSING_EVIDENCE` | baseline + first scene blueprint absent |
| I00 cinematic implementation | `BLOCKED_BY_MISSING_EVIDENCE` | engine/assets/runtime proof absent |
| R00 PageSpeed/CWV acceptance | `BLOCKED_BY_MISSING_EVIDENCE` | no lab or field evidence |

Foundation accounting:

- agent/rulebook coverage for currently known owner requirements: `100%`;
- production source ownership migration: `0%`;
- new WebGL cinematic implementation: `0%`.

Documentation progress never implies visual/runtime progress.

## STATUS

- Agent execution foundation: `PASS`.
- Production source/runtime quality: `BLOCKED_BY_MISSING_EVIDENCE`.

## NEXT VALID STEP

Execution channel: Terminal Codex with a full current checkout.

Run only B00 as a read-only baseline using the audit and build/proof templates.
Return current source/runtime facts, exact missing proof, and ranked A/B/C pilot
surface options with purpose, relation to the product goal, benefits, risks, and
a recommended hybrid only if it is concrete. Do not move source or install a
WebGL engine in B00.
