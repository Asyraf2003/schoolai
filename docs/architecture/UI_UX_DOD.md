# UI/UX Workflow and Definition of Done

Status: ACTIVE
Target branch: `main`

## 1. Required workflow

Every batch follows:

```text
FACT -> GAP -> GOAL -> IMPACT -> DECISION -> BLUEPRINT
-> ACTIVE STEP -> EXECUTION -> PROOF -> PROGRESS -> STATUS -> NEXT
```

Only one surface or capability is active. Apply `UI_UX_SESSION_PROTOCOL.md` and `UI_UX_EXECUTION_FOUNDATION.md`.

## 2. Discovery gate

Before editing the active scope, inspect:

- current `main`, working diff, and mandatory document chain;
- rendered Blade and relevant parent/partial chain;
- lang/DB source for visible content;
- CSS selectors, import order, specificity, inherited values, computed winners,
  containing/stacking contexts;
- JS controllers, listeners, timers, observers, state classes, fallback, BFCache
  and lifecycle;
- Vite entry and dynamic-import graph;
- production/lab dependency direction when experiments exist;
- media/model loading, dimensions, crop, poster, cache, and fallback;
- six tiers, component boundaries, orientation, and short heights;
- ID/EN/AR, LTR/RTL, locale switch, and text metrics;
- Chromium/WebKit capabilities;
- accessibility tree, keyboard, touch/pointer, focus, zoom, reduced motion;
- existing tests and accepted blueprint.

Translate screenshots/videos into trigger, geometry, progression, easing, final, reverse/interruption, adapters, reduced-motion, and cleanup.

Do not edit before root ownership and the smallest safe change are stated.

## 3. Blueprint gate

Use `UI_UX_BLUEPRINT_TEMPLATE.md`. Implementation requires:

- explicit scope in/out and owner-visible result;
- semantic/no-JS/no-WebGL/reduced/failure results;
- owner map for DOM, CSS, JS, Vite, locale, media, and graphics;
- all six tier contracts;
- ID/EN/AR and LTR/RTL transition contracts;
- Chromium/WebKit and capability downgrade;
- asset/performance/accessibility budgets;
- state lifecycle and proof plan;
- `OWNER_ACCEPTED` for material art-direction/engine/scene decisions.

An unresolved owner-level scene, model, engine, asset, or art-direction decision blocks only its dependent implementation.

## 4. Execution rules

- Fetch source/blob state again immediately before writing.
- Change only proven owners in editable scope.
- Preserve unrelated user work and protected sections.
- Do not activate/deactivate About or Testimonial unless named.
- Do not change DB/controller/content source for a presentation-only issue.
- Keep source files at or below 200 lines.
- Add/update tests for durable DOM/state/accessibility contracts.
- Update `UI_UX_CURRENT_STATE.md` when FACT/GAP/decision/progress/NEXT changes.
- Do not mix baseline, engine selection, site-wide refactor, and scene
  implementation in one active step.
- Do not create empty target folders or move source before ownership proof.
- Production must never import, route to, preload, or bundle lab code.

## 5. Automated proof

Run and report:

```bash
git diff --check
git status --short
npm run check:structure
npm run build
php artisan test
```

If the channel cannot run a required command, record `BLOCKED_BY_MISSING_EVIDENCE`; never translate it into `PASS`.

## 6. Responsive/locale runtime matrix

Use `UI_UX_RESPONSIVE_LOCALE_MATRIX.md`.

Core release proof:

| Dimension | Required |
|---|---|
| Width tiers | XS, SM, MD, LG, XL, 2XL |
| Representatives | 360, 390, 640, 768, 1024, 1280, 1440, 1536, 1920 |
| Boundaries | both sides of every affected global/component boundary |
| Navigation | 1180/1181 when affected |
| Locale | ID, EN, AR in every tier |
| Direction | LTR and RTL semantics/motion |
| Engine | declared current Chromium and Safari/WebKit |
| Motion | normal and reduced |
| Input | keyboard, pointer, touch as applicable |
| Text | 200% zoom/expansion |
| Viewport | relevant orientation and short-height case |

Verify no unintended overflow/clipping, hidden content, stale direction, broken
crop, inaccessible controls, state loss, or mixed-locale surface state.

## 7. Interaction/lifecycle proof

For stateful motion/media/graphics, prove:

- fast and reverse scroll;
- resize and orientation during each meaningful state;
- repeated open/close/enter/exit;
- hidden/visible tab and BFCache restoration;
- locale switch from/to AR while idle/loading/active/failed;
- focus, scroll lock, Escape, and restoration;
- load abort, failure, retry, suspend, and dispose;
- no listener/observer/timer/media/context accumulation.

## 8. Performance proof

Target:

- Performance 100;
- Accessibility 100;
- Best Practices 100;
- SEO 100.

Record URL, commit, date, tool/version, browser/OS/hardware, viewport, CPU/network,
cold/warm state, run count, scores, metrics, transfer by asset type, LCP
resource/element, CLS sources, long tasks, and renderer evidence.

For performance-sensitive work, run at least three comparable lab samples and
report median plus worst. A lucky single 100 is insufficient.

Field `3/3` requires p75 RUM/CrUX:

- LCP <= 2.5s;
- INP <= 200ms;
- CLS <= 0.1.

Lighthouse cannot prove field INP or field `3/3`.

## 9. WebGL/3D proof

In addition to the full matrix, record:

- semantic poster/DOM fallback;
- capability/preference/activation condition;
- engine and initial/deferred transfer;
- model/texture/decoder asset ledger and LOD;
- canvas bounds, resolution, DPR cap, camera per tier/direction;
- long tasks and p95 render-frame time on declared profiles;
- offscreen/hidden/locale/page lifecycle;
- context loss/failure;
- buffers/textures/targets/workers/listeners disposed;
- PageSpeed delta against baseline.

No cinematic scene is `PASS` from high-end desktop observation alone.

## 10. Accessibility proof

Verify semantic headings/landmarks, accessible names, reading/focus order,
visible focus, keyboard/touch parity, screen-reader canvas treatment, text
alternatives/captions, contrast, reduced motion, zoom, RTL, and error/fallback
announcements where relevant.

## 11. Definition of Done

A surface/capability is `PASS` only when:

1. source/state ownership and accepted blueprint are explicit;
2. no anonymous cascade, duplicate controller/renderer, or old/new owner overlap;
3. semantic content and primary actions work without enhancement;
4. all six tiers and affected boundaries have proof;
5. ID/EN/AR and LTR/RTL switch behavior have proof;
6. Chromium/WebKit and applicable input/orientation have proof;
7. keyboard/focus/screen-reader/zoom/reduced motion pass;
8. loading, media, motion, WebGL, failure, and disposal are bounded;
9. PageSpeed/CWV claims match actual evidence;
10. diff, structure, build, focused tests, and PHP tests pass;
11. source files obey 200-line and ownership rules;
12. no unrelated area or production-to-lab dependency changed;
13. owner feedback for active scope is resolved;
14. current state/progress/NEXT are accurate.

## 12. Completion report

```text
BATCH / MAIN SHA / BLUEPRINT:
FACT / GAP / DECISION:
EXECUTION / PROOF:
PROGRESS:
STATUS: PASS / FAIL / BLOCKED_BY_MISSING_EVIDENCE
NEXT EXECUTION CHANNEL:
NEXT VALID STEP:
```
Commit success proves publication only, never runtime completion.
