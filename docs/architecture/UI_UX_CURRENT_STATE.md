# UI/UX Engineering — Current State and Progress Ledger

Status: `HARDENING_ACTIVE / H2_PASS / H3_READY`
Updated: 2026-08-23
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Inspected runtime-source checkpoint: `b10bcb7e044fe4bf3d345aef6120dff5f5b9c7d6`

Durable references:

- hardening handoff: `handoffs/2026-08-23-homepage-hardening.md`
- pre-Codex readiness: `handoffs/2026-08-23-pre-codex-readiness.md`
- Blade inventory: `handoffs/2026-08-23-blade-purity-inventory.md`
- CSS/JS/runtime inventory: `handoffs/2026-08-23-css-js-runtime-inventory.md`
- functional matrix: `handoffs/2026-08-23-functional-interaction-matrix.md`
- responsive/locale/degraded proof contract:
  `handoffs/2026-08-23-responsive-locale-degraded-proof-contract.md`
- H2-H7 execution packets:
  `handoffs/2026-08-23-h2-h7-execution-packets.md`

## Current phase

Pre-Codex discovery is complete. Facts, owner decisions, known debt, interaction
contracts, responsive/locale/degraded proof requirements and H2-H7 implementation
packets are durable.

`CODEX_READY` means delegation readiness only. It does not mean hardening is
implemented or certified.

Homepage visual polishing remains PAUSED.

## H1 gate

Published H1 source/test SHA:
`6e5c3ee690773ab138583e31c5dc695bbfa6bf9a` for renderer health and
`19a86d1600943ce858da248171d99a1afc7972c1` for handoff coverage.

Proven:

- renderer health is no longer tied to transient Gallery plane/end-CTA
  visibility;
- focused H1 contract: PASS, 1 test / 7 assertions;
- diff check: PASS;
- Vite production build: PASS with existing graphics warning.

Codex runtime gate on H1 source, Linux x86_64, HeadlessChrome 151,
1440 x 913:

- two slow forward/pause/reverse cycles and five rapid forward/reverse cycles;
- 302 sampled states with no fallback after ready, no invalid-frame error, and
  no unhealthy drawing buffer/context;
- visual checkpoints at early/middle/pause/Article/reverse show the Article
  surface covering the shrinking Gallery viewport with no `#071b18` exposure.

H1 is PASS. The fix moved transition coverage from the transparent Gallery
end-CTA owner to `article-handoff.css`; scale/timing, semantic DOM, Article
composition, and Gallery JS remain unchanged.

## H2 lifecycle gate

Published H2 source/test SHA:
`b10bcb7e044fe4bf3d345aef6120dff5f5b9c7d6`.

Proven on HeadlessChrome 151, Linux x86_64, 1440 x 913:

- one explicit Gallery lifecycle owns semantic/static/fetch/prepared/ready/
  active/suspended/disposed state and synchronizes DOM/accessibility classes;
- four offscreen/onscreen cycles, hidden/visible tab, persisted BFCache restore,
  context-loss fallback and repeated non-persisted disposal produced no
  contradictory state, runtime error, duplicate canvas, listener remount or
  unhealthy active context;
- reduced motion remained semantic `STATIC_READY` with no Three runtime request;
- the H1 slow/pause/reverse/rapid gate remained PASS across 302 samples;
- focused H2 contract: PASS, 1 test / 40 assertions; focused H1 regression:
  PASS, 3 tests / 61 assertions;
- production build: PASS, 142 modules, with the existing 549.44 kB graphics
  warning;
- full MariaDB suite: 214 tests, 200 passed, 9 known assertion failures and 5
  GD-unavailable errors; no failure points to the H2 lifecycle owner.

H2 is PASS. Gallery/Article choreography, scroll-clock ownership and Three
runtime selection remain unchanged.

## Owner-accepted runtime direction

- avoid unnecessary over-engineering;
- one semantic product across six responsive tiers and ID/EN/AR;
- phone: polished final static/light, touch-first;
- tablet: semi-interactive/touch-oriented;
- desktop/laptop: richest approved cinematic/pointer/hover experience;
- degraded capability may reduce enhancement but not semantic content/actions;
- initial semantic/static viewport becomes usable as quickly as practical;
- background preparation order is Hero -> Program -> Values -> Vision/Mission ->
  Gallery -> Article -> Footer;
- preparation may continue during fast scroll/hidden tab and persists for reverse
  scroll;
- prepared assets do not justify invisible continuous RAF/WebGL/video work;
- Lighthouse/PageSpeed target remains 100/100/100/100 on declared lab profiles;
- field CWV requires real p75 evidence.

Accepted lifecycle vocabulary:

```text
SEMANTIC -> STATIC_READY -> FETCHING -> PREPARED
-> ENHANCEMENT_READY -> ACTIVE -> SUSPENDED -> DISPOSED
```

## Cloudflare/R2 proven state

- bucket `almustaqbal`, APAC, Standard;
- custom domain `media.almustaqbal.sch.id` active;
- Laravel S3 write/exists PASS;
- public custom-domain read HTTP 200 PASS;
- delete/post-delete exists false PASS;
- post-delete public HTTP 404 PASS;
- secrets remain environment-only;
- observed cache state currently `DYNAMIC`.

H6 decisions are frozen in the execution packet: preserve existing URL-shaped DB
contract, store canonical custom-domain URLs for R2-owned media, derive owned
keys centrally, use immutable owner-scoped object keys, retain binaries for soft
delete/restore, use exact-origin CORS where WebGL textures require it, and do not
invent Stream/transformation infrastructure without proof.

## Pre-Codex discovery status

### D1 media/data ownership

`PASS / DURABLE`.

DB/admin URLs, repo static assets, external URLs and Article Canvas local uploads
are distinct owners. Current sampled DB media is mainly external; no audited row
already uses R2. Article Canvas remains an H6 local-public-disk write owner.

### D2 Blade purity

`PASS / DURABLE`.

- 38 Blade files contain `@php` blocks/expressions;
- three raw PHP navbar preparation files exist under the view tree;
- H5 ownership is grouped into Home/public/shared chrome/admin shaping;
- migration must preserve semantics and use appropriate composers/presenters/
  application owners rather than a monolithic controller rewrite.

### D3 CSS/JS/runtime ownership

`PASS / DURABLE`.

- Welcome CSS aggregates are order-sensitive and have checksum debt;
- `welcome.js` currently synchronously owns Program/Values/Article helpers;
- Vision is deferred;
- Gallery is proximity-loaded rather than future sequentially prepared;
- Program uses external GSAP 3.7.1 with functional fallback;
- Three ownership is split between package `^0.185.1`, Gallery CDN `0.183.0`, and
  package Three/addons for Values;
- Values spatial is runtime-disabled but still emits the large Three-related
  chunk;
- build warns around 549 kB for the spatial/Three chunk.

### D4 functional interaction matrix

`PASS / DURABLE / SOURCE-CONTRACT`.

Hero, navigation, locale, Program, Values, Vision, Gallery, Article, PPDB,
auth/session and protected admin CRUD outcomes are bounded. Two source-level H7
accessibility checks remain explicit: Gallery lightbox focus containment and PPDB
ARIA-tab keyboard semantics.

### D5 responsive/locale/degraded proof contract

`PASS / DURABLE / PROOF-CONTRACT`.

- 36 logical cells = 6 tiers x 3 locales x 2 engine families;
- representatives: 360, 390, 640, 768, 1024, 1280, 1440, 1536, 1920;
- boundary proof includes global boundaries and nav 1180/1181;
- normal/reduced motion, keyboard/pointer/touch, failure, WebGL context loss,
  hidden tab, BFCache, fast/reverse/repeat, orientation, short height and 200%
  zoom are part of H7 proof;
- physical Safari and automated WebKit evidence are labeled separately;
- unavailable required evidence is `BLOCKED_BY_MISSING_EVIDENCE`;
- performance-sensitive lab proof uses >=3 cold runs and median + worst.

### D6 baseline + H2-H7 packets

`PASS / DURABLE / CODEX-EXECUTABLE`.

`handoffs/2026-08-23-h2-h7-execution-packets.md` freezes:

- known PASS/FAIL/BLOCKED baseline;
- H1 prerequisite;
- H2 Gallery lifecycle/state scope;
- H3 scroll-clock reconciliation scope;
- H4 single Three authority and sequential preparation/loading graph;
- H5 bounded Blade-purity migration groups;
- H6 R2 media architecture/migration contract;
- H7 baseline debt closure and certification matrix;
- editable/read-only/forbidden ownership, acceptance and stop conditions;
- sequential docs/proof checkpoint discipline.

## Frozen baseline debt

Known structure debt includes:

- source files above 200 lines;
- unreferenced candidates:
  - `resources/css/surfaces/home/article-story/footer-release.css`;
  - `resources/js/surfaces/home/vision-story/entry.js`;
  - `resources/js/surfaces/home/vision-story/typography.js`;
- checksum drift for `resources/css/pages/welcome.css` and
  `resources/css/pages/welcome-hero.css`.

Known Gallery feature-test debt: one stale continuity assertion predating H1.

Fresh full-suite baseline on MariaDB: 213 tests, 194 passed, 14 assertion
failures, and 5 GD-dependent errors because GD is unavailable. Focused H1
contracts pass; none of the remaining failures points to the changed handoff
coverage owner.

Do not mass-refactor this debt merely to make a command green. Resolve it under
its proven owner or H7 G0 with semantics-preserving structural work.

## Hardening execution order

```text
H1 browser acceptance
-> H2 lifecycle
-> proof/docs checkpoint
-> H3 scroll clock
-> proof/docs checkpoint
-> H4 graphics/loading graph
-> proof/docs checkpoint
-> H5 Blade purity groups
-> proof/docs checkpoint
-> H6 R2 media migration
-> proof/docs checkpoint
-> H7 G0 closure + certification
```

For each capability: resolve current `main`, read `AGENTS.md` + mandatory docs,
reconcile source drift, edit only proven owners, run focused proof + available DOD
gates, update durable state, and never force-push.

## Protected / deferred

- no unrelated redesign or visual polishing during hardening;
- no Article ruler cleanup/tuning;
- no mass CSS/JS/Blade/storage/Three cleanup;
- no deletion of unreferenced candidates without proof;
- no warning-threshold-only bundle fix;
- no browser/locale/performance/accessibility PASS without evidence.

## Current proof status

- H1 source/test patch: published.
- H1 focused test: PASS.
- H1 browser slow/reverse mechanics: PASS for renderer/context/fallback health.
- H1 no-dark-shell acceptance: PASS.
- H2 Gallery lifecycle/state hardening: PASS.
- Cloudflare/R2 basic lifecycle: PASS.
- D1-D6: PASS / DURABLE.
- H3-H7 implementation: NOT STARTED.
- final responsive/locale/WebKit/performance/accessibility/security certification:
  `BLOCKED_BY_MISSING_EVIDENCE` until H7.

## Pre-Codex readiness

`CODEX_READY / 100_PERCENT`.

## NEXT VALID STEP

Begin H3 scroll-clock reconciliation from the accepted execution packet. Keep
H1 handoff coverage, H2 lifecycle behavior, Article composition and H4 ownership
read-only.
