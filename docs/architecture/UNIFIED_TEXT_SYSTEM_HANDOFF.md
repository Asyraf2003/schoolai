# Unified Text System — Session Handoff

Status: ACTIVE
Date: 2026-07-29
Branch: `main`
Repository: `Asyraf2003/schoolai`

This is the primary continuation point. Do not repeat work already proven unless later code changes invalidate the evidence.

## 1. Goal

```text
DB / lang / Blade / JS content
            ↓
render location decides semantic role
            ↓
shared text-system tokens own typography
            ↓
locale adapter adapts family/tracking/direction only as needed
            ↓
stable ID / EN / AR UI
```

`Seragam` means equivalent semantic hierarchy, not identical numeric size for every node.

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

Existing component classes keep layout/behavior ownership. `data-text-role` owns typography.

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

- evidence first;
- one atomic surface/batch;
- one safe local CLI action at a time when runtime proof is required;
- no unrelated cleanup;
- no hidden layout/animation redesign;
- no DB typography fields;
- no viewport typography in JS;
- no mass-delete legacy CSS;
- admin remains desktop-only;
- use `rg`/`fd`, not `grep`/`find`;
- GitHub writes may go directly to `main` after evidence;
- Brave + CDP + native Node WebSocket are already proven;
- `getComputedStyle()` is authoritative for resolved typography.

## 3. Locked migration order

```text
M00 baseline inventory
M01 shared text-system foundation
M02 shared navigation + Hero
M03 homepage core
M04 homepage gallery/article/footer
M05 public gallery
M06 public article list + reader
M07 PPDB
M08 admin desktop
M09 article canvas/editor
M10 legacy cleanup + final audit
```

Do not start M03 before M02 PASS.

## 4. Progress

### M00 — PASS

All M02-M09 surfaces have baseline role inventories.

M00 is historical evidence. Old Arabic measurements/family observations are not automatically target values.

### M01 — PASS

`resources/css/text-system.css` exists and loads in this order:

```text
legacy/component CSS
→ text-system.css
→ arabic-typography.css
```

Stored proof:

- structure PASS, 446 source files, max 200 lines;
- build PASS;
- focused regression 11 passed / 118 assertions;
- full suite 139 passed / 1337 assertions;
- browser confirmed generated text-system CSS loaded;
- pre-M02 homepage had zero role markers, proving M01 foundation was inert.

M01 evidence:

`docs/architecture/UNIFIED_TEXT_SYSTEM_M01_FOUNDATION.md`

### M02 — ACTIVE

Semantic mapping is implemented for shared Navbar/Mega Menu/Language Modal and Hero.

Representative roles:

Navbar:

- normal nav link → `action`;
- language trigger → `action`;
- mega eyebrow → `label`;
- mega title → `component-title`;
- mega description → `description`;
- mega link label → `action`;
- mega link description → `description`;
- navbar CTA → `action`.

Hero:

- eyebrow → `label`;
- title → `display`;
- description → `description`;
- CTA → `action` when present.

Language modal:

- title → `component-title`;
- option label → `action`.

## 5. M02 source work already done

Relevant M02 commits include:

- `f1153695` — Hero semantic roles;
- `f633f56c` — Navbar semantic roles;
- `452cf6b0` — Language modal roles;
- `9e905eb9` — remove Hero typography ownership from local inline layer;
- `98a9a229` — desktop mega typography handoff;
- `ed9cecd4` — responsive nav typography handoff;
- `00831af4` — language-modal typography handoff;
- `e17ade53` — responsive modal typography cleanup;
- `ce46f56f` — Arabic semantic adapter;
- `c0999826` — remove winning legacy Navbar typography from `welcome-hero/002`;
- `9f83de93` — retire legacy Arabic optical scale.

Manifest source-equivalence was refreshed with exact local hashes after module edits.

## 6. M02 automated proof already returned

Before the latest Cairo-only adjustment:

- structure PASS;
- `git diff --check` PASS;
- Vite build PASS;
- full PHP test suite PASS.

After Navbar cascade fix:

- structure PASS;
- rebuild PASS.

Because `arabic-type-scale.css` changed again under the Cairo-only decision, automated gates must be rerun after pulling current `main` before M02 closure.

## 7. M02 runtime proof already returned

### EN / 1440 — PASS

Browser computed-style evidence after Navbar fix:

Navbar action:

```text
role: action
size: 15px
weight: 600
line-height: 19.5px
letter-spacing: normal
family: system-ui...
```

Hero eyebrow:

```text
role: label
size: 13px
weight: 600
line-height: 16.9px
```

Hero title:

```text
role: display
size: 71.2px
weight: 700
line-height: 69.776px
family: ui-rounded...
```

Hero description:

```text
role: description
size: 16.8px
weight: 400
line-height: 27.72px
```

This proves the shared semantic system now wins for representative EN Navbar + Hero nodes at 1440px.

Hero CTA was absent on the active rendered slide and was therefore not a failure.

Still missing for M02 closure:

- EN 768/390;
- ID 1440/768/390;
- AR 1440/768/390 under the new Cairo-only contract;
- representative Mega Menu/Language Modal runtime proof where needed;
- final automated gates after current Cairo-only runtime adjustment.

## 8. Arabic contract changed on 2026-07-29

This is a major intentional contract update.

Old target, now retired:

```text
Cairo → display/UI
Lateef → prose/description/longform
Arabic-only enlarged optical scale → compensate for Lateef
```

Current target:

```text
ALL Arabic semantic roles → Cairo
shared text-system → size/weight/line-height
Arabic adapter → family + normal tracking + RTL/directional behavior
```

Facts:

- `--font-ar-display` resolves to Cairo;
- `--font-ar-body` resolves to Cairo;
- `arabic-type-scale.css` no longer contains the old enlarged Arabic/Naskhi scale;
- role adapter continues to use shared semantic size tokens;
- Lateef is not an M02 acceptance target.

The old scale included values up to 2.25rem / 2.75rem / 3.25rem to compensate for Lateef. Those values must not return.

Historical M00 docs may still mention Lateef. Treat those as historical baseline only.

Canonical Arabic doc:

`docs/architecture/ARABIC_TYPOGRAPHY_REFACTOR.md`

Canonical DOD has also been rewritten to the Cairo-only contract.

`@fontsource/lateef` may remain temporarily as an unused package dependency. It is not imported at runtime. Dead dependency cleanup belongs to M10 unless package validation requires earlier removal.

## 9. Repository hygiene incident resolved

Commit `82ac0cd` accidentally created 207 empty root files named after translation keys such as `articles.cta,`, `hero.slides.0,`, and `Indonesian`.

They were proven unrelated and removed in:

- `c9c3738` — `chore: remove accidental translation-key files`.

Do not recreate them.

The intentional changes from `82ac0cd` that remain are:

- Arabic body/display aliases both resolve to Cairo;
- Arabic typography entry no longer imports Lateef faces;
- source-module-equivalence update.

## 10. Concurrent About work

Do not revert unrelated valid About evolution.

Known valid state includes:

- simplified About reel without stale CTA/media label;
- continuous reel/line work;
- `006-warp-layer.css` is intentional;
- About motion/layout work is separate from Unified Text M02.

Typography migration may touch About only starting M03 and only for semantic text ownership.

## 11. Browser proof infrastructure

Already established:

- Brave: `/usr/local/bin/brave`;
- app: `http://127.0.0.1:8000`;
- CDP works;
- Node native `WebSocket` works;
- no Playwright/Puppeteer needed.

Required public widths:

- 390px;
- 768px;
- 1440px.

Locales:

- ID → LTR;
- EN → LTR;
- AR → RTL.

## 12. Authoritative documents

Priority:

1. `UNIFIED_TEXT_SYSTEM_HANDOFF.md` — current continuation state;
2. `UNIFIED_TEXT_SYSTEM_DOD.md` — workflow and DoD;
3. `ARABIC_TYPOGRAPHY_REFACTOR.md` — Cairo-only Arabic contract;
4. `UNIFIED_TEXT_SYSTEM_M01_FOUNDATION.md` — M01 evidence;
5. M00 ledgers — historical baseline/evidence for each surface.

If an older M00 Arabic statement says Lateef should be the target, ignore the target interpretation. M00 only records the old runtime state.

## 13. Exact next valid step

Current M02 status:

`BLOCKED_BY_MISSING_EVIDENCE`

Reason:

- EN/1440 is proven;
- Cairo-only runtime contract has just changed;
- latest source/build/test state after `9f83de93` is not yet proven locally;
- responsive and AR locale proof is incomplete.

Next action after pulling current `main`:

```text
structure + build + full tests
```

Only after those gates PASS, continue runtime proof at 768/390 and Arabic Cairo/RTL.
