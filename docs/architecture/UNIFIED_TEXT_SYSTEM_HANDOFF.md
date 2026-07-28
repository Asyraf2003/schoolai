# Unified Text System — Session Handoff

Status: ACTIVE
Date: 2026-07-29
Branch: `main`
Repository: `Asyraf2003/schoolai`
Local checkout: `/home/asyraf/Code/laravel/school/schoolai`

This file is the primary continuation point for the next AI/session. Read it before doing new discovery. Do not repeat work already marked PASS unless a later code change invalidates the stored evidence.

## 1. Goal

Create one predictable semantic text presentation system for SchoolAI.

Target flow:

```text
DB / lang / Blade / JS content
            ↓
render location decides semantic role
            ↓
shared text-system tokens own typography
            ↓
locale adapter may adjust family/optical scale
            ↓
stable ID / EN / AR UI
```

`Seragam` means equivalent semantic hierarchy, not identical numeric font sizes.

Canonical roles:

- `display`
- `page-title`
- `section-title`
- `component-title`
- `subtitle`
- `body`
- `description`
- `label`
- `meta`
- `action`
- `longform`

Target marker during implementation:

```html
<h2 class="section-title" data-text-role="section-title">...</h2>
```

Existing component classes stay for layout/behavior. `data-text-role` will express typography ownership.

## 2. Hard workflow rules

Use this chain:

```text
FACT
→ GAP
→ GOAL
→ IMPACT
→ DECISION
→ EXECUTION
→ PROOF
→ STATUS
→ NEXT VALID STEP
```

Allowed STATUS values only:

- `PASS`
- `FAIL`
- `BLOCKED_BY_MISSING_EVIDENCE`

Rules:

- zero assumption;
- repository/runtime evidence first;
- one atomic surface/batch;
- one valid next CLI action at a time only when user runtime proof is genuinely required;
- no unrelated cleanup;
- no visual redesign as part of typography work;
- no mass-delete legacy typography;
- no typography fields/classes in DB;
- no viewport-based typography logic in JS;
- admin remains intentionally desktop-only unless separately approved;
- use `rg` and `fd`, not `grep` / `find`, for CLI guidance;
- GitHub writes may go directly to `main` after proof;
- do not merge/audit old branches unless a concrete missing behavior requires it;
- do not install Playwright/Puppeteer for this workflow;
- existing Brave + CDP + Node native WebSocket capability is already proven and must not be rediscovered;
- `getComputedStyle()` is authoritative when source order/specificity cannot establish the runtime winner.

## 3. Locked migration order

```text
M00 baseline text-role inventory
M01 shared text-system foundation
M02 shared navigation + hero
M03 homepage core
M04 homepage gallery/article/footer
M05 public gallery
M06 public article list + reader
M07 PPDB
M08 admin desktop
M09 article canvas/editor
M10 legacy cleanup + final audit
```

M00 is complete. Typography implementation has not started.

## 4. Progress

### M00 public surfaces

PASS:

- homepage `/`, including navbar, Hero, About, core sections, homepage gallery/articles/footer;
- `/galeri`;
- `/artikel`;
- `/artikel/{article:slug}` native reader;
- `/ppdb`.

Public M00 coverage corresponding to M02-M07 is **100%**.

### M08 admin desktop

PASS:

- protected admin route/provider tree mapped;
- Dashboard, PPDB, non-canvas Articles, Gallery + nested section/media management, Statistics, Testimonials, and Hero admin entry views mapped;
- shared `layouts.admin` render tree mapped;
- major visible/accessibility text groups classified;
- Blade/lang/DB/controller/runtime/JS sources separated;
- CSS ownership recorded;
- admin shell locale is forced to Indonesian by `ForceAdminLocale`;
- desktop-only product contract preserved;
- no M08 implementation performed.

Authoritative evidence:

- `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_ADMIN.md`.

### M09 article canvas/editor

PASS:

- active canvas route/controller/trait tree mapped;
- dedicated `layouts.article-canvas` render tree mapped;
- topbar, workspace, block menu, inline/image/code toolbars, dialogs, Unsplash, publish drawer, category editor, and browser-native feedback inventoried;
- ID/EN document content is Blade-rendered from Article DB values;
- AR document/button is created by `article-canvas-arabic.js` from persisted DB values before main canvas mount;
- Arabic autosave persistence through `PersistArabicArticleCanvas` is mapped;
- all active canvas JS modules were inspected for human-visible/accessibility text creation or mutation;
- document title/subtitle/body and nested editorial content are mapped to page-title/subtitle/longform hierarchy;
- CSS pseudo-content and browser-native alert/prompt copy are documented;
- canvas CSS ownership and Arabic adapter ownership are deterministic from source load order, so no redundant M00 computed-style run was required;
- no M09 implementation performed.

Authoritative evidence:

- `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_ARTICLE_CANVAS.md`.

### M00 overall

M00 is measured against the 8 implementation surface groups M02-M09.

All 8 groups are baselined:

**M00 progress = 100%.**

### Whole Unified Text System project

M01-M10 implementation has not started.

Using the same conservative milestone-level method as earlier handoffs:

**overall project progress ≈ 9%.**

This intentionally does not pretend baseline documentation equals implementation.

## 5. Authoritative docs

Read these in this order when needed:

1. `docs/architecture/UNIFIED_TEXT_SYSTEM_HANDOFF.md` — primary continuation file.
2. `docs/architecture/UNIFIED_TEXT_SYSTEM_DOD.md` — workflow, canonical roles, proof gates, migration order.
3. `docs/architecture/UNIFIED_TEXT_SYSTEM_RATIONALE.md` — architectural rationale.
4. `docs/architecture/UNIFIED_TEXT_SYSTEM_PREFLIGHT.md` — branch archaeology/preflight closure.
5. `docs/architecture/ARABIC_TYPOGRAPHY_REFACTOR.md` — Arabic adapter constraints.
6. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_HOME.md`.
7. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_HOME_HANDOFF.md`.
8. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_GALLERY.md`.
9. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_ARTICLES.md`.
10. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_PPDB.md`.
11. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_ADMIN.md`.
12. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_ARTICLE_CANVAS.md`.

Do not create duplicate M00 docs for already-covered surfaces.

## 6. Important commits

Historical orientation only; current `main` remains truth.

- `e5f19131a481d979193588eaa03dcf97be16c599` — preflight closed, M00 allowed.
- `3a55341601c60a4b9468465797e19b6a93b0f195` — homepage M00 closure.
- `6307dd958934ed518fb19f3f81c5a71a38dc9791` — gallery M00 closure.
- `5aa76f9eb75c5a9e4da0ce9e9dd8c1a0116bda86` — articles/native reader M00 closure.
- `4bfc02e6c145b498e3520dbd53be90cc68b38af2` — PPDB M00 runtime closure.
- `111b8b5349e80e5eb292ef6eb08544ed360009cd` — M08 admin desktop M00 evidence closure.
- `67373618cff149ccd99f9010cf0e7333268919a4` — M09 article canvas/editor M00 evidence closure.

## 7. Browser/runtime proof infrastructure already established

Proven environment:

- Brave available at `/usr/local/bin/brave`;
- headless Chromium/CDP works on fixed port `9222`;
- CDP protocol is reachable through Node native `WebSocket`;
- temporary Brave profiles keep user browser state untouched.

Do not repeat capability detection.

Public proof widths already used:

- 390px;
- 768px;
- 1440px.

Public locales:

- ID -> `ltr`;
- EN -> `ltr`;
- AR -> `rtl`.

Admin remains desktop-only. Article canvas has responsive declarations in its dedicated CSS, but this typography workflow must not silently redefine admin/editor product support.

## 8. Baseline findings already proven

### Homepage

- ID and EN representative computed typography match numerically.
- Arabic adapter uses Cairo for heading/UI-like text and Lateef for prose/description.
- Arabic baseline contains very large prose scales in several components; those are implementation evidence, not baseline blockers.

### Gallery

- ID/EN parity proven at 390/768/1440.
- AR/RTL proven.
- Do not invent dead gallery title/caption UI from legacy selectors that are not rendered.

### Articles / native reader

- ID/EN list and reader typography match numerically.
- Public page title has a non-monotonic responsive baseline: 60.48px at 1440, 33.6px at 768, 50.7px at 390.
- Native reader has a distinct editorial `longform` contract.
- Arabic reader uses Cairo for title/UI and Lateef for prose with large longform values.
- DB category/tag localization issues are content evidence, not typography role changes.

### PPDB

- showcase exists in current runtime data;
- 1440 enables the existing desktop journey enhancement; 768/390 do not;
- registration and guide actions were proven against their current destinations;
- ID/EN computed typography matches numerically at equivalent widths;
- Arabic uses Cairo/Lateef with several documented size/cascade inconsistencies preserved as M07 implementation evidence.

### Admin desktop

- active route tree includes route modules plus provider-backed Testimoni and Hero admin routes;
- admin shell is forced to locale `id`;
- topbar descriptions and some panel/card descriptions are source-present but currently hidden by scoped CSS;
- shared JS creates/replaces limited preview/toast/modal text but contains no typography logic;
- Statistics currently emits `Buka` / `Tutup` through CSS pseudo-content;
- CSS ownership was source-resolvable for M00.

### Article canvas/editor

- dedicated layout loads only canvas CSS, Arabic typography, Arabic injection JS, main canvas JS, and context UI JS;
- shell `<html>` is fixed `lang="id"` while document content supports ID/EN/AR;
- ID/EN documents are Blade/DB; AR document is JS-created/DB-persisted;
- `.canvas-title` -> article `page-title`;
- `.canvas-subtitle` -> `subtitle`;
- `.canvas-body` -> `longform` with nested editorial hierarchy/exceptions preserved;
- save/count/upload/publish/category/Unsplash/error text may be created or mutated by JS/runtime;
- empty body and empty figcaption have CSS-created placeholder text;
- Arabic title resolves through the dedicated canvas Arabic family adapter; Arabic subtitle/body use Lateef Arabic lead/longform rules;
- browser-native alert/prompt copy exists outside app CSS ownership;
- source load order made M09 cascade ownership deterministic.

## 9. What is NOT yet done

Do not claim any of the following has started:

- no `resources/css/text-system.css` foundation;
- no `data-text-role` rollout;
- no semantic token implementation;
- no Arabic selector cleanup for the Unified Text System;
- no legacy typography deletion;
- no M01 implementation;
- no M02-M10 implementation migration;
- no final implementation build/test/runtime acceptance gate.

M00 is complete. Do not rediscover its surfaces unless a later code change invalidates a stored baseline.

## 10. Exact next scope

Continue with **M01 — shared text-system foundation only**.

M01 goal from the DOD:

- create one focused shared typography ownership layer, expected path `resources/css/text-system.css`;
- define shared semantic typography tokens and role rules;
- load the foundation without migrating unrelated surfaces yet;
- do not perform M02-M09 component rollout in the same batch;
- do not remove legacy component typography before a migrated component has runtime proof;
- do not change DB/schema;
- do not add viewport typography logic in JS;
- preserve Arabic as the locale adapter rather than creating a competing Arabic system.

Required M01 workflow:

FACT
→ GAP
→ GOAL
→ IMPACT
→ DECISION
→ EXECUTION
→ PROOF
→ STATUS
→ NEXT VALID STEP

M01 should PASS only after the foundation is loaded and automated/runtime proof shows it does not accidentally rewrite existing UI before component migration begins.

## 11. First action for the next session

Read this handoff and the M01 sections of `UNIFIED_TEXT_SYSTEM_DOD.md`.

Then inspect the current CSS entry/import architecture from `main` to determine the smallest safe load point for the shared foundation.

Do not:

- repeat M00 homepage/gallery/article/PPDB/admin/canvas discovery;
- ask the user to repeat stored baseline evidence;
- start M02 migration before M01 itself passes;
- mass-clean legacy typography;
- add semantic markers across all surfaces before the foundation proof is closed.
