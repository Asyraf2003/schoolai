# Pre-Codex Hardening Readiness — 2026-08-23

Status: `DISCOVERY_ACTIVE / 75_PERCENT`
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Source runtime checkpoint: `6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`

## Purpose

Prepare a durable fact/decision/proof packet so Codex can execute H2-H7 without
rediscovering project intent, inventing architecture, or fixing unrelated debt.
This discovery does not authorize H2-H7 runtime mutation. H1 runtime acceptance
remains pending.

The final Codex execution prompt must not be produced until readiness reaches
`CODEX_READY / 100_PERCENT`.

## Owner constraints

- Finish the requested hardening preparation without unnecessary
  over-engineering.
- Add architecture/process only when critical to H2-H7 hardening or necessary
  to stop an implementation agent from guessing.
- Repository docs are the durable system of record; chat history must not be a
  required implementation dependency.
- Codex receives bounded capability packets with verified facts, owner
  decisions, baseline debt, proof gates, and forbidden scope.
- No force push and no unrelated cleanup/redesign.

## Durable owner direction

### Responsive fidelity

- One semantic SchoolAI identity across six canonical width tiers.
- Phone: polished final static/light experience, touch-first, never an unfinished
  fallback.
- Tablet: semi-interactive and touch-oriented, not a shrunken desktop.
- Desktop/laptop: richest approved cinematic/pointer/hover experience.
- Runtime capability may reduce enhancement without losing semantic content,
  identity, primary information, or primary actions.
- ID/EN use Inter/LTR; AR uses Cairo/RTL under the existing locale contract.

### Loading/preparation

Accepted direction:

```text
initial semantic/static viewport
-> Hero enhancement
-> Program preparation
-> Values preparation
-> Vision/Mission preparation
-> Gallery preparation
-> Article preparation
-> Footer preparation
```

Preparation is aggressive and sequential and may continue while the user scrolls
quickly or while the tab is hidden. Prepared state persists for reverse scroll.
Preparation is not permission for invisible continuous RAF/WebGL/video work.

If enhancement is not ready or fails, the section remains a complete polished
`STATIC_READY` composition rather than blank/loader-only/broken output.

Provisional lifecycle vocabulary for H2/H3:

```text
SEMANTIC -> STATIC_READY -> FETCHING -> PREPARED
-> ENHANCEMENT_READY -> ACTIVE -> SUSPENDED -> DISPOSED
```

The owner's approximate 0.1s initial-display target is an aspiration pending
measurement, not a fabricated universal SLA.

## H1 checkpoint

Published H1 source/test SHA:
`6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`.

Proven:

- focused H1 contract: PASS, 1 test / 7 assertions;
- `git --no-pager diff --check`: PASS;
- production build: PASS, with existing graphics chunk warning;
- structure check: FAIL from frozen pre-existing debt;
- full Gallery feature test has one stale pre-H1 continuity assertion.

Still missing:

- owner browser slow-forward/reverse Gallery acceptance.

Therefore H1 is not yet marked complete and implementation must not silently
advance to H2.

## Cloudflare/R2 checkpoint

Infrastructure is provisioned and application connectivity is proven:

- bucket `almustaqbal`, APAC, Standard;
- public custom domain `media.almustaqbal.sch.id` active;
- Laravel S3-compatible configuration works locally;
- write PASS;
- exists PASS;
- public custom-domain read HTTP 200 PASS;
- delete PASS;
- post-delete exists false PASS;
- post-delete custom-domain HTTP 404 PASS;
- secret values remain environment-only and are not stored in docs;
- observed response is currently `cf-cache-status: DYNAMIC`;
- CORS is currently absent and must be based on actual browser/WebGL consumers.

H6 is therefore an application/media migration problem, not a Cloudflare setup
problem.

## D1 — Media + DB/data-owner inventory

Status: `PASS / DURABLE`.

Durable facts are recorded in this ledger/current state. Key findings:

- DB/admin URL fields, repo static assets, external URLs, and Article Canvas
  uploads are distinct media owners;
- current sampled DB media is primarily external/Unsplash;
- no audited DB row already points at the R2 custom domain;
- current Article content fields are `content_id`, `content_en`, `content_ar`;
- current local Article content contains no embedded media URLs;
- Article Canvas still writes to Laravel `public` disk and remains an H6 owner;
- `gallery_page_media_items.poster_url` does not exist in current local schema.

H6 must not be implemented as a blind global `FILESYSTEM_DISK=s3` switch.

## D2 — Blade presentation-purity inventory

Status: `PASS / DURABLE`.

Detailed inventory:
`handoffs/2026-08-23-blade-purity-inventory.md`.

Proven repository inventory:

- 38 Blade files contain `@php` blocks/expressions;
- no raw `<?php` hit was reported inside those Blade templates;
- three non-Blade PHP files exist under
  `resources/views/partials/site-navbar/data/`.

Migration classes:

1. Home surface data shaping;
2. public page shaping;
3. shared chrome/locale/meta preparation;
4. admin form/list/archive/replacement preparation.

The navbar is the clearest violation because view-tree PHP currently performs
locale copy assembly, routing/anchor construction, menu mutation/filtering,
login insertion, and media presentation preparation.

Existing `AdminPpdbEditComposer` and `AdminGalleryIndexComposer` prove the repo
already has a suitable render-data preparation pattern. H5 must not become a
giant controller refactor; trivial presentation conditions can remain direct
Blade directives/expressions without `@php`.

## D3 — CSS/JS/runtime ownership inventory

Status: `PASS / DURABLE`.

Detailed inventory:
`handoffs/2026-08-23-css-js-runtime-inventory.md`.

### CSS facts

- `welcome.css` is an active 47-module ordered legacy/cascade aggregate.
- `welcome-hero.css` is an active nine-module ordered Hero aggregate.
- both have frozen source-equivalence/checksum drift.
- bounded Gallery and Article surface aggregates have clearer ownership.
- Article debug-ruler CSS remains intentionally protected unfinished visual work.
- `article-story/footer-release.css` is an unreferenced candidate, not proven
  safe-to-delete.

### JS/runtime facts

- `welcome.js` synchronously owns navigation, Program, Values, Article, public
  content, gallery-wall, and lazy-media imports.
- Vision enhancement is deferred after Hero via dynamic import/idle scheduling.
- Gallery is currently proximity-loaded: its page entry uses IntersectionObserver
  before importing the controller, and controller proximity then initializes the
  Three engine. This differs from the accepted future persistent sequential
  preparation strategy.
- Program uses external jsDelivr GSAP `3.7.1` with reduced/failure fallback.
- Program and Article mount functions return cleanup handles, but current page
  bootstraps do not retain those handles. This is lifecycle proof debt, not proof
  of a current leak.

### Graphics hotspot

- package graph declares Three `^0.185.1`;
- Gallery separately loads external Three `0.183.0`;
- Values spatial scene imports package Three/addons;
- Values currently sets `VALUES_SPATIAL_ENABLED = false`;
- its dynamic import still causes Vite to emit the large spatial/Three chunk;
- successful build warns around a 549 kB `spatial-scene` chunk.

Classification: split Three ownership plus runtime-disabled-but-built Values
spatial is the clearest H4 bundle/runtime hotspot. H4 must measure/converge it,
not hide it by raising the warning threshold.

### Lifecycle facts

Gallery and Values have explicit visibility/page/observer cleanup/suspension.
Article and Program expose disposer logic but current page bootstraps discard the
returned handles. Multiple RAF owners are not by themselves evidence that a
giant global scheduler is required. Consolidation is allowed only when actual
measurement/lifecycle proof shows duplicate work or accumulation.

### Frozen structure debt

Known baseline includes over-200-line files, the unreferenced candidates:

- `article-story/footer-release.css`;
- `vision-story/entry.js`;
- `vision-story/typography.js`;

and checksum drift for `welcome.css` / `welcome-hero.css`.

Do not mass-refactor these merely to make `check:structure` green.

## Remaining pre-Codex discovery

Three bounded batches remain.

### D4 — Functional interaction matrix

Map primary controls/routes across navigation, locale, Program, Gallery, Article,
PPDB and release-relevant admin/auth behavior. Record pointer/touch/keyboard,
repeat interaction, fast/reverse motion, failure and restoration expectations.

### D5 — Responsive/locale/degraded-runtime proof contract

Freeze proof expectations across six width tiers, ID/EN/AR, LTR/RTL,
normal/reduced motion, delayed network, fast scrolling, hidden tab, failed
enhancement, orientation, Chromium and WebKit.

### D6 — Baseline proof + H2-H7 execution packets

Freeze baseline failures/warnings and required proof, then create one bounded
packet per H2-H7 with facts, gaps, editable/read-only/forbidden scope,
acceptance, stop conditions and proof commands.

## Progress

Current readiness: `75%`.

Completed:

- D1 media + DB/data-owner inventory — PASS / DURABLE;
- D2 Blade presentation-purity inventory — PASS / DURABLE;
- D3 CSS/JS/runtime ownership inventory — PASS / DURABLE.

Remaining:

- D4 functional interaction matrix;
- D5 responsive/locale/degraded-runtime proof contract;
- D6 baseline proof ledger + H2-H7 execution packets.

This percentage measures delegation readiness, not hardening implementation
completion.

## NEXT VALID STEP

Build D4 read-only from current routes, Blade controls, and JS interaction
owners. Do not change runtime behavior while mapping the contract.
