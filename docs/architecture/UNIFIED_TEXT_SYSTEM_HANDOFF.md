# Unified Text System — Session Handoff

Status: ACTIVE
Date: 2026-07-29
Branch: `main`
Repository: `Asyraf2003/schoolai`
Local checkout: `/home/asyraf/Code/laravel/school/schoolai`

This file is the primary continuation point. Do not repeat work already marked PASS unless later code changes invalidate stored evidence.

## 1. Goal

Create one predictable semantic text presentation system for SchoolAI.

```text
DB / lang / Blade / JS content
            ↓
render location decides semantic role
            ↓
shared text-system tokens own typography
            ↓
locale adapter may adjust family / optical scale
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

Existing component classes continue to own layout/behavior. `data-text-role` expresses typography ownership.

## 2. Hard workflow rules

Use:

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

Allowed statuses only:

- `PASS`
- `FAIL`
- `BLOCKED_BY_MISSING_EVIDENCE`

Rules:

- zero assumption;
- repository/runtime evidence first;
- one atomic surface/batch;
- one valid next CLI action at a time when local runtime proof is required;
- no unrelated cleanup;
- no visual redesign hidden inside typography work;
- no mass-delete legacy typography;
- no typography fields/classes in DB;
- no viewport-based typography logic in JS;
- admin remains desktop-only unless separately approved;
- use `rg` and `fd`, not `grep` / `find`, for CLI guidance;
- GitHub writes may go directly to `main` after evidence;
- do not install Playwright/Puppeteer;
- Brave/CDP/Node native WebSocket browser proof is already established;
- `getComputedStyle()` is authoritative when source cascade ownership is ambiguous.

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

## 4. Progress

### M00 — PASS

All implementation surface groups M02-M09 are baselined.

Authoritative evidence exists for:

- homepage `/`;
- `/galeri`;
- `/artikel`;
- native article reader;
- `/ppdb`;
- admin desktop;
- article canvas/editor.

M00 progress = **100%**.

### M01 — PASS

`resources/css/text-system.css` now exists as a focused standalone Vite CSS entry.

Runtime order is:

```text
legacy/component CSS
→ text-system.css
→ arabic-typography.css
```

Loaded roots:

- homepage;
- shared public layout;
- admin layout;
- article-canvas layout.

M01 proof returned locally:

- `npm run check:structure` PASS;
- source checker: 446 files, max 200 lines;
- `npm run build` PASS;
- focused regression: 11 passed / 118 assertions;
- full suite: 139 passed / 1337 assertions;
- browser DOM loaded generated `text-system-COSzPnPQ.css`;
- rendered homepage contained zero `data-text-role` markers, proving the foundation remained inert before M02.

M01 evidence:

- `docs/architecture/UNIFIED_TEXT_SYSTEM_M01_FOUNDATION.md`.

M01 closure commit:

- `f3697425600fe0122d59b6f42fe0e76302b14cee` — `docs: close M01 shared text foundation`.

### Whole project

Using the conservative 11-milestone M00-M10 method:

**overall project progress ≈ 18%.**

M02 is the active milestone.

## 5. Important M01 regression evidence

During M01 validation, unrelated/concurrent homepage work exposed two non-text-system issues:

1. source-module equivalence manifest had become stale after valid About/footer evolution;
2. Indonesian homepage translation structure had diverged from the active controller/view contract.

Resolved without reverting valid About/footer work:

- `f5dfc5883c0c98cde7527bb7b707a6d3cb0c75e3` — refresh source-module equivalence;
- `591381b7c1a1b53cffbe88a8b9c5246bf65f8514` — restore homepage translation contract;
- `ff6be61319477d155844a8d3066ee53643540ca1` — align stale About reel test with intentionally simplified current UI.

Do not reintroduce the removed About CTA/media label merely to satisfy old expectations.

The valid About warp module `006-warp-layer.css` must remain.

## 6. Browser/runtime proof infrastructure

Already proven:

- Brave: `/usr/local/bin/brave`;
- local app: `http://127.0.0.1:8000`;
- CDP fixed port 9222 works;
- Node native `WebSocket` works;
- no Playwright/Puppeteer needed.

Required public proof widths:

- 390px;
- 768px;
- 1440px.

Locales:

- ID → LTR;
- EN → LTR;
- AR → RTL.

## 7. Baseline facts relevant to M02

M00 semantic mapping for shared navigation:

- nav links → `action`;
- navbar CTA → `action`;
- mega eyebrow → `label`;
- mega title → `component-title`;
- mega description → `description`;
- mega link labels → `action`;
- mega link descriptions → `description`;
- language modal title → `component-title`;
- language options → `action`.

M00 semantic mapping for Hero:

- eyebrow → `label`;
- title → `display`;
- description → `description`;
- CTA → `action`.

Representative EN baseline before M02:

- nav action at 1440: 12.48px / weight 760;
- nav action at 768: 17.92px / weight 780;
- nav action at 390: 16px / weight 780;
- Hero title at 1440: 43.2px / weight 760 / ui-rounded;
- Hero title at 768: 36.096px;
- Hero title at 390: 30.42px;
- Hero description at 1440/768: 13.44px / weight 820;
- Hero description at 390: 11.84px / weight 820.

The shared M01 targets are intentionally normalized rather than copying these per-component values.

## 8. Current M02 source facts

Hero markup is in:

`resources/views/home/sections/hero.blade.php`

Shared navbar markup is in:

- `resources/views/partials/site-navbar/header.blade.php`;
- `resources/views/partials/site-navbar/language-modal.blade.php`.

Hero has high-specificity legacy typography in `resources/views/partials/home-hero-copy-layout.blade.php`, including `.home-page .hero-cinema__*` rules.

Responsive Hero/nav cascades also contain typography declarations, including:

- `resources/css/pages/welcome-hero/008-welcome-hero-cascade-008.css`;
- `resources/css/pages/welcome-hero/009-welcome-hero-cascade-009.css`;
- `resources/css/pages/welcome-mega-menu/001-homepage-mega-navigation-polish-desktop-full-width-u.css`;
- `resources/css/pages/welcome-mega-menu/003-welcome-mega-menu-cascade-003.css`;
- navbar inline style partials, including language-modal typography.

Therefore adding `data-text-role` markers alone is not sufficient. M02 must add semantic markers and then remove only the legacy typography declarations that still win over the shared role layer.

Layout, spacing, colors, shadows, dimensions, positioning, transforms, animation, truncation, and interaction behavior must remain component-owned.

## 9. Arabic constraint

Arabic stays a locale adapter.

Target ownership:

- Cairo for display/heading/UI roles;
- Lateef for prose/description/longform roles.

M02 may add semantic-role-aware Arabic adapter rules in the existing Arabic typography layer if required by runtime proof. Do not create a second competing Arabic text system.

## 10. Authoritative docs

Read only as needed:

1. `UNIFIED_TEXT_SYSTEM_HANDOFF.md` — this file;
2. `UNIFIED_TEXT_SYSTEM_DOD.md`;
3. `UNIFIED_TEXT_SYSTEM_M01_FOUNDATION.md`;
4. `UNIFIED_TEXT_SYSTEM_RATIONALE.md`;
5. `UNIFIED_TEXT_SYSTEM_M00_HOME.md`;
6. `UNIFIED_TEXT_SYSTEM_M00_HOME_HANDOFF.md`;
7. `ARABIC_TYPOGRAPHY_REFACTOR.md`;
8. remaining M00 surface ledgers for later milestones.

Do not repeat already-proven M00 browser/tooling discovery.

## 11. Exact next scope

Continue **M02 — shared navigation + Hero**.

Atomic process:

```text
existing rendered node
→ assign canonical data-text-role
→ determine actual winning typography
→ remove only conflicting legacy typography declarations
→ runtime proof ID/EN/AR at 390/768/1440
→ preserve layout/behavior
→ automated gates
→ M02 PASS
```

Do not start M03 before M02 PASS.
