# Unified Text System — M01 Shared Foundation Evidence

Status: PASS
Date: 2026-07-29
Branch: `main`
Scope: M01 shared text-system foundation only

## 1. FACT

M00 is complete and PASS. M01 establishes one shared semantic typography layer without migrating component markup yet.

Locked architecture:

```text
DB / lang / Blade / JS content
            ↓
render location decides semantic role
            ↓
resources/css/text-system.css
            ↓
locale adapter
            ↓
stable ID / EN / AR UI
```

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

## 2. GAP BEFORE M01

Before M01:

- `resources/css/text-system.css` did not exist;
- there was no shared semantic family/size/weight/leading/tracking token set;
- Vite did not register a shared text-system entry;
- homepage/public/admin/article-canvas roots did not share one typography layer;
- no rendered application markup used `data-text-role`.

## 3. GOAL

Create a buildable shared semantic typography foundation loaded on every already-baselined root while remaining visually inert until later milestones assign semantic roles.

M01 must not:

- migrate M02-M09 component markup;
- redesign layout;
- add DB/schema typography fields;
- add viewport typography logic in JS;
- replace Arabic with a competing typography system;
- mass-delete legacy component typography.

## 4. DECISION

Shared file:

`resources/css/text-system.css`

Runtime order:

```text
legacy/component CSS
→ resources/css/text-system.css
→ resources/css/arabic-typography.css
```

Arabic therefore remains the final locale adapter.

## 5. EXECUTION

M01 implementation on `main` includes:

- `resources/css/text-system.css`, 149 source lines;
- dedicated Vite registration in `vite.config.js`;
- homepage load point;
- public layout load point;
- admin layout load point;
- article-canvas layout load point.

The shared file owns typography only. Component layout, spacing, color, animation, positioning, truncation, and behavior remain component-owned.

No application `data-text-role` rollout was performed during M01.

## 6. INITIAL SHARED SCALE

The foundation normalizes M00 evidence into these initial role targets:

- `display`: 40px → 76px;
- `page-title`: 36px → 60px;
- `section-title`: 32px → 48px;
- `component-title`: 18px → 22.4px;
- `subtitle`: 16.8px → 20px;
- `body`: 16px;
- `description`: 15.2px → 16.8px;
- `label`: 13px;
- `meta`: 13px;
- `action`: 15px;
- `longform`: 18px → 20.8px.

Weights are normalized to 400/500/600/700 instead of preserving legacy requests such as 750-950.

These are system targets, not claims that legacy components already match them.

## 7. CONCURRENT-MAIN / REGRESSION EVENTS

M01 overlapped with unrelated homepage/footer work on `main`. No force push was used.

The source-module equivalence manifest had become stale because current About and footer modules had evolved. It was refreshed without reverting the valid About warp layer or footer work.

Commit:

`f5dfc5883c0c98cde7527bb7b707a6d3cb0c75e3` — `docs: refresh current source module equivalence`

During validation, homepage tests exposed a separate translation-contract regression: Indonesian `home.php` no longer matched the active `nilai_sekolah` / `program_unggulan` render contract used by the controller/views. The active three-locale contract was restored without changing M01 CSS.

Commit:

`591381b7c1a1b53cffbe88a8b9c5246bf65f8514` — `fix: restore homepage translation contract`

A remaining About test expected CTA/media-label elements that had intentionally been removed by earlier UI simplification. The stale test was aligned with the current UI instead of reintroducing removed elements.

Commit:

`ff6be61319477d155844a8d3066ee53643540ca1` — `test: align about reel contract with simplified UI`

## 8. AUTOMATED PROOF

Local proof returned by the user:

- `npm run check:structure` → PASS;
- source structure checker → `446 files`, maximum `200` lines each;
- `npm run build` → PASS;
- focused regression gate → `11 passed`, `118 assertions`;
- full Laravel suite → `139 passed`, `1337 assertions`;
- `git diff --check` → PASS during the automated gate;
- M01 foundation did not require DB/schema or JS behavior changes.

## 9. RUNTIME PROOF

Homepage runtime URL:

`http://127.0.0.1:8000`

Headless Brave DOM proof returned:

- generated stylesheet `text-system-COSzPnPQ.css` is preloaded and loaded;
- `data-text-role` does not appear in the rendered homepage DOM.

Therefore the foundation is present in the browser but inert, exactly as required for M01.

This proves M01 did not accidentally rewrite existing component typography before semantic-role migration begins.

## 10. STATUS

`PASS`

Acceptance basis:

- shared foundation exists;
- all required roots load it;
- Arabic remains after it in the cascade;
- source structure passes;
- build passes;
- full tests pass;
- runtime confirms the shared CSS is loaded;
- zero semantic-role markers means zero intentional role application before M02.

## 11. NEXT VALID STEP

Start **M02 — shared navigation + Hero**.

Required M02 mapping:

- nav links → `action`;
- navbar CTA → `action`;
- mega eyebrow → `label`;
- mega title → `component-title`;
- mega description → `description`;
- mega link label → `action`;
- mega link description → `description`;
- language modal title → `component-title`;
- language options → `action`;
- Hero eyebrow → `label`;
- Hero title → `display`;
- Hero description → `description`;
- Hero CTA → `action`.

Migration rule: add semantic ownership, prove the winning cascade, then remove only conflicting/redundant legacy typography declarations. Do not remove layout/behavior styling or perform unrelated cleanup.
