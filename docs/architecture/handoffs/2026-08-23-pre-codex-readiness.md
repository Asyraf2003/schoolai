# Pre-Codex Hardening Readiness — 2026-08-23

Status: `DISCOVERY_ACTIVE / 92_PERCENT`
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
- ID/EN use Inter/LTR; AR uses Cairo/RTL.

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

Preparation is aggressive/sequential and may continue during fast scroll or a
hidden tab. Prepared state persists for reverse scroll. Preparation does not
justify invisible continuous RAF/WebGL/video execution.

If enhancement is not ready or fails, a polished `STATIC_READY` composition
remains available with semantic content and primary actions.

Provisional lifecycle vocabulary for H2/H3:

```text
SEMANTIC -> STATIC_READY -> FETCHING -> PREPARED
-> ENHANCEMENT_READY -> ACTIVE -> SUSPENDED -> DISPOSED
```

The owner's approximate 0.1s initial-display target remains an aspiration until
measurement converts it into a real critical-path budget.

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

- owner/browser slow-forward/reverse Gallery acceptance.

Therefore H1 is not yet marked complete and implementation must not silently
advance to H2.

## Cloudflare/R2 checkpoint

Infrastructure and application connectivity are proven:

- bucket `almustaqbal`, APAC, Standard;
- custom domain `media.almustaqbal.sch.id` active;
- Laravel S3 write/exists PASS;
- public custom-domain read HTTP 200 PASS;
- delete/post-delete exists false PASS;
- post-delete public HTTP 404 PASS;
- secrets remain environment-only;
- current response observed as `cf-cache-status: DYNAMIC`;
- CORS/cache/object-key/migration remain H6 contracts based on actual consumers.

## D1 — Media + DB/data-owner inventory

Status: `PASS / DURABLE`.

Key facts:

- DB/admin URL fields, repo static assets, external URLs, and Article Canvas
  uploads are distinct owners;
- sampled DB media is primarily external/Unsplash and no audited row already uses
  the R2 custom domain;
- Article content fields are `content_id`, `content_en`, `content_ar`, with no
  embedded media URL found in the current local dataset;
- Article Canvas still writes to Laravel `public` disk;
- `gallery_page_media_items.poster_url` does not exist in current local schema.

H6 must not be a blind global filesystem-disk switch.

## D2 — Blade presentation-purity inventory

Status: `PASS / DURABLE`.

Detailed inventory:
`handoffs/2026-08-23-blade-purity-inventory.md`.

- 38 Blade files contain `@php` blocks/expressions;
- no raw `<?php` hit was reported inside those Blade templates;
- three non-Blade PHP navbar preparation files exist under `resources/views`;
- H5 migration ownership is grouped into Home shaping, public-page shaping,
  shared chrome/meta/locale, and admin render-data preparation;
- existing admin composers prove an appropriate shaping pattern already exists.

## D3 — CSS/JS/runtime ownership inventory

Status: `PASS / DURABLE`.

Detailed inventory:
`handoffs/2026-08-23-css-js-runtime-inventory.md`.

- `welcome.css`: active 47-module ordered aggregate;
- `welcome-hero.css`: active nine-module ordered aggregate;
- both have source-equivalence/checksum drift;
- `welcome.js` synchronously owns Program/Values/Article plus public helpers;
- Vision is deferred;
- Gallery is currently proximity-loaded, unlike the accepted future sequential
  background preparation contract;
- Program uses external GSAP 3.7.1 with functional reduced/failure fallback;
- package Three is `^0.185.1`, Gallery separately loads CDN Three `0.183.0`, and
  Values spatial imports package Three/addons while currently runtime-disabled;
- successful build warns around a 549 kB spatial/Three chunk.

H4 must measure/converge ownership, not raise the warning threshold as a fake fix.

## D4 — Functional interaction matrix

Status: `PASS / DURABLE / SOURCE-CONTRACT`.

Detailed matrix:
`handoffs/2026-08-23-functional-interaction-matrix.md`.

Release-critical behavior is bounded for Hero, desktop/mobile navigation, locale,
Program, Values, Vision, homepage Gallery, Gallery page/lightbox, homepage
Article, Article index/detail routing, PPDB, auth/session routes and protected
admin Gallery/Article/PPDB CRUD.

Known certification gaps are recorded rather than auto-fixed:

- Gallery lightbox has no explicit focus trap proven in source;
- PPDB ARIA tabs have no dedicated ArrowLeft/ArrowRight roving-focus behavior
  proven in source.

D4 is a source contract, not a runtime PASS claim.

## D5 — Responsive / locale / degraded-runtime proof contract

Status: `PASS / DURABLE / PROOF-CONTRACT`.

Detailed contract:
`handoffs/2026-08-23-responsive-locale-degraded-proof-contract.md`.

Frozen release proof shape:

- 36 logical base cells = 6 width tiers x 3 locales x 2 engine families;
- representatives: 360, 390, 640, 768, 1024, 1280, 1440, 1536, 1920;
- affected boundaries: 639/640, 767/768, 1023/1024, 1279/1280,
  1535/1536 and 1180/1181 for navigation-sensitive work;
- ID/EN LTR and AR RTL;
- pointer/keyboard/touch as applicable;
- normal/reduced motion;
- JS/enhancement failure, WebGL failure/context loss, delayed network,
  third-party enhancement failure, hidden/visible tab and BFCache;
- fast/reverse/repeated scroll/interactions;
- orientation, short height and 200% zoom;
- Chromium-family and Safari/WebKit proof labeled accurately;
- physical Safari unavailable means `BLOCKED_BY_MISSING_EVIDENCE`, never an
  invented PASS from WebKit automation;
- performance-sensitive H7 proof uses >=3 comparable cold lab runs and reports
  median + worst;
- Lighthouse 100/100/100/100 remains the lab target; field CWV remains a separate
  p75 RUM/CrUX claim.

Static-first/fidelity policy is now proofable: phone static/light remains final
quality, tablet remains semi-interactive/touch-oriented, desktop gets richest
cinematic behavior when capability/performance allows it, and no downgrade may
remove semantic content or primary actions.

## Remaining pre-Codex discovery

One bounded batch remains.

### D6 — Baseline proof + H2-H7 execution packets

Freeze:

- current known baseline PASS/FAIL/BLOCKED evidence;
- non-regression commands and runtime proof requirements;
- one bounded implementation packet each for H2-H7;
- editable/read-only/forbidden ownership;
- acceptance and stop conditions;
- the exact handoff order and H1 gate before H2.

After D6 is durable and reconciled with current `main`, readiness may become
`CODEX_READY / 100_PERCENT` and the final Codex execution prompt may be created.

## Progress

Current readiness: `92%`.

Completed:

- D1 media + DB/data-owner inventory — PASS / DURABLE;
- D2 Blade presentation-purity inventory — PASS / DURABLE;
- D3 CSS/JS/runtime ownership inventory — PASS / DURABLE;
- D4 functional interaction matrix — PASS / DURABLE / SOURCE-CONTRACT;
- D5 responsive/locale/degraded-runtime proof contract — PASS / DURABLE /
  PROOF-CONTRACT.

Remaining:

- D6 frozen baseline proof + bounded H2-H7 execution packets.

This percentage measures delegation readiness, not hardening implementation
completion.

## NEXT VALID STEP

Freeze D6 from the already-proven baseline plus H1-H7 facts. Do not rerun or
invent broad product work merely to fill the packet; mark unavailable runtime
proof as `BLOCKED_BY_MISSING_EVIDENCE` and preserve H1 as the gate before H2.
