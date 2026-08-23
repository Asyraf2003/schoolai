# UI/UX Engineering — Current State and Progress Ledger

Status: `HARDENING_ACTIVE / H5_GROUP_3_PASS / H5_GROUP_4_READY`
Updated: 2026-08-23
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Inspected runtime-source checkpoint: `248499d745265f1121162f635933f032b9102e89`

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

## H3 scroll-clock gate

Published H3 source/test SHA:
`9b0eda2aa3651348ff66f77cdbf8dd9acf003818`.

Proven on HeadlessChrome 151, Linux x86_64, 1440 x 913:

- Gallery target/end/handoff progress is derived from current owned geometry;
  camera, plane opacity, heading and velocity retain the existing smoothed
  presentation clock;
- 33 samples across four end/handoff boundary positions, using slow/fast
  forward/reverse approaches plus deliberate pauses, produced one semantic
  signature per position with no link/class mismatch or pause drift;
- resize from 913px to 820px viewport height recalculated travel and preserved
  authoritative state without fallback, context failure or duplicate canvas;
- visual end-progress still varied across approaches while semantic state stayed
  fixed, proving presentation smoothing remains active;
- H1 runtime remained PASS across 302 samples and H2 lifecycle runtime remained
  PASS across observer, visibility, BFCache, context-loss and reduced paths;
- focused H3 contract: PASS, 1 test / 28 assertions; focused H1: PASS, 4 tests /
  154 assertions; focused H2: PASS, 1 test / 40 assertions;
- production build: PASS, 142 modules, with the existing 549.44 kB graphics
  warning;
- full MariaDB suite: 215 tests, 196 passed, 14 known assertion failures and 5
  GD-unavailable errors; no failure points to the H3 clock owner.

H3 is PASS. H1 coverage, H2 lifecycle, Article composition, Gallery visual
choreography and H4 runtime/dependency ownership remain unchanged.

## H4 graphics/loading gate

Published H4 source/test SHA:
`5836af2ac55f523594eba93bf9a8880729b87dba`.

Proven on HeadlessChrome 151, Linux x86_64, 1440 x 913:

- one sequential preparation coordinator completed Hero -> Program -> Values ->
  Vision -> Gallery -> Article -> Footer with no failed stage or runtime
  exception;
- normal motion requested only the project package Three graph; no production
  CDN Three request remained;
- reduced motion completed the same preparation order without requesting the
  Three package chunk or mounting Gallery before proximity;
- Gallery reached `ACTIVE` with one canvas, a healthy context and no GL error;
  explicit context loss returned to semantic `STATIC_READY` fallback;
- the proven Gallery proximity-mount gate remains in place, so early code/runtime
  preparation does not create an offscreen renderer or continuous loop;
- initial `welcome.js` output fell from 40.66 kB / 12.22 kB gzip to 16.18 kB /
  5.09 kB gzip;
- the deferred Three package chunk is 534.39 kB / 134.00 kB gzip versus the
  pre-H4 549.44 kB / 138.83 kB gzip spatial chunk; the existing >500 kB warning
  remains visible and its threshold was not raised;
- focused H4 contract: PASS, 1 test / 28 assertions; focused H1, H2 and H3
  regressions: PASS, 7 / 40 / 28 assertions;
- production build: PASS, 144 modules, with the reported deferred Three warning;
- full MariaDB suite: 216 tests, 197 passed, 14 known assertion failures and 5
  GD-unavailable errors; the new H4 test is the sole count delta and passes.

H4 is PASS. H1 handoff coverage, H2 lifecycle, H3 authoritative scroll clock,
visual composition, locale semantics and Values spatial disabled state remain
unchanged.

## H5 Blade-purity gate

Published H5 owner group 1 source/test SHA:
`f0997790fc70a6d59b165080e444161c4c5f815b`.

Proven for shared navbar/footer/meta/locale/admin-layout preparation:

- the seven group 1 Blade owners contain no raw PHP or `@php` blocks /
  expressions;
- the three navbar preparation PHP files were removed from the view tree after
  all consumers migrated;
- navbar, footer, head metadata, language-flag and admin-menu shaping now use
  bounded view composers plus two navbar presenters; every new source file is
  below 200 lines;
- navbar mega/modal/language and head-image copy now comes from shared ID/EN/AR
  translation contracts, while desktop/mobile consume one prepared menu model;
- home/public anchors, route activity, login placement, PPDB filtering, footer
  external-link semantics, JSON-LD/CSP/GA behavior, flags and admin active state
  remain covered;
- focused H5 group 1 proof: PASS, 11 tests / 272 assertions;
- focused H1 regression: PASS, 1 test / 7 assertions; focused H2-H4
  regressions: PASS, 3 tests / 96 assertions;
- production build: PASS, 144 modules, with the existing deferred 534.39 kB
  Three package warning;
- full MariaDB suite: 219 tests, 211 passed and 8 known baseline assertion
  failures; none points to an H5 group 1 owner;
- structure check still reports only the frozen pre-H5 over-limit,
  unreferenced-candidate and checksum debt; no new H5 file is reported.

At the group 1 checkpoint, H5 remained `IMPLEMENTING`: Home section shaping,
public page shaping and admin list/form/archive/replacement shaping were pending.
H1-H4 behavior and loading ownership remained unchanged.

Published H5 owner group 2 source/test SHA:
`4889ebf9cbd5225c29098a76b8c3dcd032c0f649`.

Proven for Home section shaping:

- the eight group 2 Blade owners contain no raw PHP or `@php` blocks /
  expressions; the remaining production Blade inventory is 23 files;
- final Hero collection/status shaping runs at the included Hero partial
  boundary, after the registered database Hero composer can replace slides;
- Home background kinetic lines, Program items/media/title scale, Vision/Mission
  locale text, Gallery presets/CTA/closing media, Values indices, Article
  opening/issue/closing state and the latent editorial heading split contract
  now use bounded exact-view composers plus one shared kinetic-line presenter;
- the original 8/20 kinetic line counts, six Program cards and repeated media,
  multibyte title thresholds, Arabic-only honorific expansion, five-preset
  Gallery cycle, last-two closing media, Article five-item cap, semantic DOM,
  data attributes and ID/EN/AR visible copy remain covered;
- every new source and every migrated Home Blade owner is below 200 lines;
- focused H5 group 2 proof: PASS, 21 tests / 599 assertions;
- focused H1 regression: PASS, 1 test / 7 assertions; focused H2-H4
  regressions: PASS, 3 tests / 96 assertions; H5 group 1 regression: PASS,
  11 tests / 271 assertions;
- production build: PASS, 144 modules, with the existing deferred 534.39 kB
  Three package warning;
- full MariaDB suite: 224 tests, 216 passed and the same 8 known baseline
  assertion failures; none points to an H5 group 2 owner;
- structure check still reports only the frozen pre-H5 over-limit,
  unreferenced-candidate and checksum debt; no new H5 file is reported.

H5 owner group 2 is PASS. H5 remains `IMPLEMENTING`: public page shaping and
admin list/form/archive/replacement shaping are pending. H1-H4 behavior/loading
ownership and H5 group 1 shared-chrome semantics remain unchanged.

Published H5 owner group 3 source/test SHA:
`248499d745265f1121162f635933f032b9102e89`.

Proven for public page shaping:

- the six group 3 Blade owners contain no raw PHP or `@php` blocks /
  expressions; the remaining production Blade inventory is 17 files;
- PPDB page/admission state, fixed parent/school audience partition, localized
  showcase presentation, restarted audience numbering and initial-tab choice
  now use one exact-view composer while model-owned URL/open-state behavior is
  unchanged;
- Article aliases, read label and fallback numbering, Gallery database-versus-
  locale precedence, deterministic six-image media fallback and provider/card
  metadata now use bounded exact-view composers plus one card presenter;
- the redirected latent Article detail view received only its translation alias
  migration; its unrelated legacy contract was not revived or cleaned up;
- route names, PPDB open/closed behavior, Article filtering/external-link/XSS
  boundaries, Gallery lightbox data attributes, semantic DOM and ID/EN/AR copy
  remain covered;
- every new source and every migrated public Blade owner is below 200 lines;
- focused H5 group 3 proof: PASS, 24 tests / 305 assertions;
- focused H1 regression: PASS, 1 test / 7 assertions; focused H2-H4
  regressions: PASS, 3 tests / 96 assertions; H5 group 1 regression: PASS,
  11 tests / 271 assertions; H5 group 2 regression: PASS, 21 tests / 599
  assertions;
- production build: PASS, 144 modules, with the existing deferred 534.39 kB
  Three package warning;
- full MariaDB suite: 229 tests, 220 passed and 9 baseline/order-sensitive
  assertion failures. The additional PPDB archive public-response failure
  passes in isolation, as do all focused public-page owner gates;
- structure check still reports only the frozen pre-H5 over-limit,
  unreferenced-candidate and checksum debt; no new H5 file is reported.

H5 owner group 3 is PASS. H5 remains `IMPLEMENTING`: only admin
list/form/archive/replacement shaping remains. H1-H4 behavior/loading ownership
and H5 group 1-2 semantics remain unchanged.

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

- the durable starting inventory contained 38 Blade files with `@php`;
- H5 group 1 reduced the remaining count to 31;
- H5 group 2 reduced the remaining count to 23;
- H5 group 3 reduced the remaining count to 17;
- no non-Blade PHP preparation file remains under `resources/views`;
- H5 ownership is grouped into Home/public/shared chrome/admin shaping;
- migration must preserve semantics and use appropriate composers/presenters/
  application owners rather than a monolithic controller rewrite.

### D3 CSS/JS/runtime ownership

`PASS / DURABLE`.

- Welcome CSS aggregates are order-sensitive and have checksum debt;
- `welcome.js` now starts a bounded sequential preparation coordinator after the
  semantic/static document is ready;
- Program, Values, Vision, Gallery and Article code is prepared in accepted
  order while surface activation remains relevance-bounded;
- Program uses external GSAP 3.7.1 with functional fallback;
- package `three@0.185.1` is the sole Three authority for Gallery and the disabled
  Values spatial graph; no Gallery CDN runtime remains;
- Values spatial remains runtime-disabled and does not request a renderer;
- build still reports the measured 534.39 kB deferred shared Three package
  chunk, with a separate 21.17 kB disabled Values spatial scene chunk.

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

Fresh H5 group 1 full-suite checkpoint on MariaDB: 219 tests, 211 passed and 8
known assertion failures. Focused H1-H5 group 1 contracts pass; none of the
remaining failures points to the hardened graphics/loading or shared-chrome
owners.

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
- H3 Gallery scroll-clock reconciliation: PASS.
- H4 graphics runtime/loading graph hardening: PASS.
- H5 owner group 1 shared chrome presentation-purity migration: PASS.
- H5 owner group 2 Home section shaping migration: PASS.
- H5 owner group 3 public page shaping migration: PASS.
- H5 owner group 4: NOT STARTED.
- Cloudflare/R2 basic lifecycle: PASS.
- D1-D6: PASS / DURABLE.
- H6-H7 implementation: NOT STARTED.
- final responsive/locale/WebKit/performance/accessibility/security certification:
  `BLOCKED_BY_MISSING_EVIDENCE` until H7.

## Pre-Codex readiness

`CODEX_READY / 100_PERCENT`.

## NEXT VALID STEP

Begin H5 owner group 4 admin list/form/archive/replacement shaping from the
accepted execution packet. Keep H1-H4 behavior/loading ownership plus H5 group
1-3 semantics proven. Do not enter H6 or H7 until H5 group 4 and final H5
repository proof are complete.
