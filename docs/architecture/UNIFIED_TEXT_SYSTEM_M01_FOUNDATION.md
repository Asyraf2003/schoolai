# Unified Text System — M01 Shared Foundation Evidence

Status: BLOCKED_BY_MISSING_EVIDENCE
Date: 2026-07-29
Branch: `main`
Scope: M01 shared text-system foundation only

## 1. FACT

M00 is already complete and PASS. M01 starts implementation without migrating M02-M09 component markup yet.

The locked contract from `UNIFIED_TEXT_SYSTEM_DOD.md` requires:

- one shared semantic typography layer;
- canonical `data-text-role` ownership;
- responsive type-scale tokens;
- no component layout/media/animation ownership in the shared layer;
- Arabic remains a locale adapter;
- no DB/schema typography fields;
- no viewport typography logic in JS;
- no legacy cleanup before migrated-component proof.

Current public/admin/editor CSS loading is not shared through one existing entry:

- homepage uses dedicated welcome entries;
- public content uses `layouts.public`;
- admin uses `resources/css/app.css`;
- article canvas uses `resources/css/pages/article-canvas.css`.

Therefore `resources/css/app.css` alone cannot serve as the universal M01 load point.

## 2. GAP BEFORE EXECUTION

Before M01:

- `resources/css/text-system.css` did not exist;
- no shared semantic size/family/weight/leading/tracking token set existed;
- no shared `[data-text-role]` rules existed;
- no current rendered source markup used `data-text-role`;
- Vite did not register a text-system entry;
- homepage/public/admin/canvas did not load a shared role layer between legacy CSS and the Arabic adapter.

## 3. GOAL

Create a buildable semantic typography foundation that is loaded on all already-baselined root surfaces while remaining visually inert until later migrations add role markers.

M01 must not migrate navbar, Hero, homepage sections, gallery, article, PPDB, admin components, or canvas content yet.

## 4. IMPACT BOUNDARY

Intended changed files for M01 implementation:

- `resources/css/text-system.css`;
- `vite.config.js`;
- `resources/views/welcome.blade.php`;
- `resources/views/layouts/public.blade.php`;
- `resources/views/layouts/admin.blade.php`;
- `resources/views/layouts/article-canvas.blade.php`.

No intended changes to:

- DB/schema/models;
- controllers/routes;
- JS behavior;
- content values;
- component-specific CSS;
- Arabic typography files;
- responsive layout behavior.

## 5. DECISION

### Shared file

Use `resources/css/text-system.css` as a standalone Vite CSS entry.

Reason:

- importing only from `app.css` would miss homepage/public/canvas;
- importing from Arabic CSS would incorrectly make the shared system architecturally dependent on the locale adapter;
- a dedicated entry allows a consistent runtime order on every root surface.

### Runtime order

Load:

```text
legacy/component CSS
→ resources/css/text-system.css
→ resources/css/arabic-typography.css
```

This preserves Arabic as the final locale adapter.

### Selector contract

M01 defines only canonical selectors:

- `[data-text-role="display"]`;
- `[data-text-role="page-title"]`;
- `[data-text-role="section-title"]`;
- `[data-text-role="component-title"]`;
- `[data-text-role="subtitle"]`;
- `[data-text-role="body"]`;
- `[data-text-role="description"]`;
- `[data-text-role="label"]`;
- `[data-text-role="meta"]`;
- `[data-text-role="action"]`;
- `[data-text-role="longform"]`.

No role markers are added to rendered application markup during M01.

## 6. TOKEN BASIS

The initial ID/EN scale is intentionally normalized from stored M00 evidence rather than copying component extremes.

Important baseline evidence:

- section title is roughly 32px at mobile/tablet and about 48px desktop;
- current component-title examples vary from about 17px to about 42px;
- article descriptions are about 15.7px;
- metadata is commonly about 12-13px;
- native reader/canvas longform roots are roughly 18-21px.

M01 therefore establishes an initial role scale approximately covering:

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

Weights are restricted to 400/500/600/700 instead of perpetuating legacy requests such as 750-950.

These are shared target tokens, not claims that every legacy component already matches them. M02-M09 migration/runtime proof may tune shared tokens when evidence demonstrates a system-level issue; components must not invent independent replacements.

## 7. EXECUTION

Current M01 implementation on `main` includes:

- new `resources/css/text-system.css`, 149 lines;
- `resources/css/text-system.css` registered in `vite.config.js`;
- homepage loads it after welcome/about/hero CSS and before Arabic typography;
- public layout loads it after welcome CSS and before Arabic typography;
- admin layout loads it after `app.css` and before Arabic typography;
- article canvas loads it after canvas CSS and before Arabic typography.

No `data-text-role` rollout has started.

## 8. CONCURRENT-MAIN SAFETY EVENT

During execution, `main` was being changed concurrently by another process.

Observed parallel commits included:

- `784054ff8696c205eae8c87f7fde88cd12dbf6dd` — footer social channel simplification;
- `2e093baa383ca208718677a784e2d6bd254be576` — Indonesian home locale changes.

A non-fast-forward update was rejected. No force push was used.

The concurrent `lang/id/home.php` change temporarily fell out of current ancestry during competing contents writes. It was restored byte-for-byte using its original Git blob:

`24bb5383c320e30a1181df0a5626654b6bf85d06`

Current `main` resolves `lang/id/home.php` to that exact blob SHA.

This restoration is preservation of concurrent work, not part of the Unified Text System implementation scope.

## 9. SOURCE PROOF

Verified from current GitHub state:

- `resources/css/text-system.css` exists;
- source length is below the repository 200-line source limit;
- Vite input includes the new CSS entry;
- public/admin/canvas load order is `legacy -> text-system -> Arabic`;
- homepage load point has been restored on current HEAD;
- the M01 implementation diff from the pre-M01 implementation base contains only the shared CSS, Vite registration, and four layout load points;
- no DB/schema/controller/route/JS/component-CSS migration is included;
- current code search did not show application `data-text-role` markers before M01 rollout.

The source-only expectation is therefore that existing rendered typography is unchanged because no current application text nodes match the new role selectors.

## 10. MISSING PROOF

The repository has no GitHub commit status/check result for this batch.

Required local proof has not yet been executed against current `main`:

- `npm run check:structure`;
- `npm run build`;
- `php artisan view:clear`;
- `php artisan test`;
- `git diff --check`;
- clean/understood `git status --short`;
- local browser confirmation that loading the inert foundation does not visually rewrite existing surfaces.

Because these mandatory gates are not yet proven, M01 is not marked PASS.

## 11. STATUS

`BLOCKED_BY_MISSING_EVIDENCE`

This is not a code failure. Source execution is complete for the M01 foundation batch, but acceptance requires local automated/runtime proof.

## 12. NEXT VALID STEP

Pull current `main` locally and run the existing repository validation gates. Do not start M02 until this evidence is returned and M01 is closed as PASS.
