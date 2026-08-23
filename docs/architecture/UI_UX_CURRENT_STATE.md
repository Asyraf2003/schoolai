# UI/UX Engineering — Current State and Progress Ledger

Status: `HARDENING_ACTIVE / PRE_CODEX_DISCOVERY`
Updated: 2026-08-23
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Inspected runtime-source checkpoint: `6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`

Durable references:

- hardening handoff: `handoffs/2026-08-23-homepage-hardening.md`
- pre-Codex readiness: `handoffs/2026-08-23-pre-codex-readiness.md`
- Blade inventory: `handoffs/2026-08-23-blade-purity-inventory.md`
- CSS/JS/runtime inventory: `handoffs/2026-08-23-css-js-runtime-inventory.md`
- functional matrix: `handoffs/2026-08-23-functional-interaction-matrix.md`
- responsive/locale/degraded proof contract:
  `handoffs/2026-08-23-responsive-locale-degraded-proof-contract.md`

## Current phase

Homepage visual polishing remains PAUSED. H1 has a published minimal source/test
patch, but slow forward/reverse browser acceptance has not yet been recorded as
PASS.

Pre-Codex discovery may inspect later capabilities and record durable facts and
proof contracts, but does not authorize H2-H7 runtime mutation.

## Current FACT

- Published H1 source/test SHA:
  `6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`.
- H1 renderer health is no longer tied to transient Gallery plane/end-CTA
  visibility.
- Focused H1 contract passes: 1 test / 7 assertions.
- Production build passes but has an existing graphics chunk warning.
- Structure check has frozen pre-existing debt.
- Full Gallery feature test has one stale pre-H1 continuity expectation.
- H1 manual slow/reverse runtime acceptance remains missing.
- Article visual work remains intentionally unfinished; debug rulers/markers are
  protected until visual work resumes.

## Owner-accepted product/runtime direction

- Avoid unnecessary over-engineering; add architecture only when critical to
  hardening or needed to stop an implementation agent from guessing.
- One semantic product across six responsive width tiers and ID/EN/AR.
- Phone is polished final static/light and touch-first.
- Tablet is semi-interactive/touch-oriented.
- Desktop/laptop is the richest approved cinematic/pointer/hover target.
- Static/degraded state must remain complete and intentional, never blank or
  loader-only.
- Initial semantic/static viewport should become usable as quickly as practical.
- Background preparation after initial readiness is aggressive and sequential:
  Hero -> Program -> Values -> Vision/Mission -> Gallery -> Article -> Footer.
- Preparation may continue during fast scroll/hidden tab and persists for reverse
  scroll.
- Prepared assets do not justify invisible continuous RAF/WebGL/video work.
- Product target remains Lighthouse/PageSpeed 100/100/100/100 on declared lab
  profiles; field CWV requires actual p75 field evidence.

## Cloudflare/R2 status

Infrastructure and basic Laravel lifecycle are PROVEN:

- bucket `almustaqbal`, APAC, Standard;
- custom domain `media.almustaqbal.sch.id` active;
- Laravel authenticated S3 write/exists PASS;
- public custom-domain read HTTP 200 PASS;
- delete/post-delete exists false PASS;
- post-delete public request HTTP 404 PASS.

Secrets remain environment-only. Current public response was observed as
`cf-cache-status: DYNAMIC`. CORS/cache/object-key/migration policy belongs to H6
and must follow actual consumers/data ownership.

## D1 media/data ownership — PASS / DURABLE

Current media ownership is split among DB/admin URL fields, repo static assets,
external URLs, and Article Canvas local-public-disk uploads.

Current sampled DB rows are primarily external and none of the audited media
fields already use the R2 custom domain. Article content fields are
`content_id`, `content_en`, `content_ar` and current local rows contain no
embedded media URLs. Article Canvas remains an H6 write owner because it
currently stores content/thumbnail files on Laravel's `public` disk.

H6 must not be implemented as a blind global filesystem-disk switch.

## D2 Blade presentation purity — PASS / DURABLE

Repository proof found:

- 38 Blade files with `@php` blocks/expressions;
- no raw `<?php` hit reported inside those Blade files;
- three raw PHP navbar preparation files under `resources/views`.

H5 ownership groups are Home shaping, public-page shaping, shared chrome/meta/
locale, and admin form/list/archive preparation. Existing admin view composers
prove a suitable shaping mechanism already exists. H5 must remain incremental
and semantics-preserving.

## D3 CSS/JS/runtime ownership — PASS / DURABLE

Key facts:

- `welcome.css` is a 47-module ordered aggregate with high cascade/import-order
  sensitivity;
- `welcome-hero.css` is a nine-module ordered aggregate;
- both currently have source-equivalence/checksum drift;
- Gallery/Article newer surface CSS has clearer bounded ownership;
- `welcome.js` synchronously owns Program, Values and Article control graph plus
  navigation/public helpers;
- Vision uses deferred dynamic enhancement after Hero;
- Gallery currently uses proximity-triggered dynamic loading, differing from the
  accepted future persistent sequential preparation direction;
- Program loads external jsDelivr GSAP 3.7.1 with reduced/failure fallback;
- Program and Article expose cleanup handles current bootstraps do not retain,
  creating lifecycle proof debt rather than proven leaks;
- package Three is `^0.185.1`, Gallery separately loads CDN Three `0.183.0`, and
  Values spatial imports package Three/addons while currently runtime-disabled;
- successful build warns around a 549 kB `spatial-scene` chunk.

H4 must measure and converge this ownership. Raising a warning threshold alone
is forbidden as a solution.

## D4 functional interaction matrix — PASS / DURABLE / SOURCE-CONTRACT

Detailed contract:
`handoffs/2026-08-23-functional-interaction-matrix.md`.

Release-critical interaction owners are bounded for Hero, navigation, locale,
Program, Values, Vision, homepage Gallery, Gallery lightbox, Article, PPDB,
auth/session routes and protected admin Gallery/Article/PPDB CRUD.

Known certification gaps are recorded, not silently fixed:

- Gallery lightbox has no explicit focus trap proven in source;
- PPDB `role=tab` controls have no dedicated ArrowLeft/ArrowRight roving-focus
  behavior proven in source.

D4 is a source contract, not runtime certification.

## D5 responsive/locale/degraded-runtime proof — PASS / DURABLE / PROOF-CONTRACT

Detailed contract:
`handoffs/2026-08-23-responsive-locale-degraded-proof-contract.md`.

Frozen proof shape:

- 36 logical base cells = six tiers x ID/EN/AR x Chromium/Safari-WebKit family;
- representatives 360, 390, 640, 768, 1024, 1280, 1440, 1536, 1920;
- affected boundaries 639/640, 767/768, 1023/1024, 1279/1280,
  1535/1536, plus 1180/1181 for navigation-sensitive work;
- normal/reduced motion, keyboard/pointer/touch, delayed/failed enhancement,
  WebGL failure/context loss, hidden/visible tab, BFCache, fast/reverse/repeat,
  orientation, short height and 200% zoom;
- physical Safari and automated WebKit evidence must be labeled separately;
- unavailable required runtime proof is `BLOCKED_BY_MISSING_EVIDENCE`, never
  inferred PASS;
- performance-sensitive H7 lab proof uses at least three comparable cold runs and
  reports median + worst;
- Lighthouse 100/100/100/100 remains the lab target, while field CWV remains a
  separate p75 evidence claim.

Phone static/light, tablet semi-interactive, and desktop full-cinematic are now
proofable quality contracts rather than vague device labels. Capability downgrade
may change enhancement density but may not remove semantic content/actions.

## Hardening sequence

1. `H1 Gallery false-fallback hardening`
   - source/test patch published;
   - manual slow/reverse runtime proof pending.
2. `H2 Gallery lifecycle/state hardening`
3. `H3 Scroll-clock reconciliation`
4. `H4 Graphics runtime/bundle hardening`
5. `H5 Blade presentation-purity migration`
6. `H6 Cloudflare media migration`
7. `H7 Release certification`

Do not advance implementation sequence from discovery alone.

## Protected / deferred

- no unrelated redesign or visual polishing during hardening discovery;
- no Article ruler cleanup/tuning;
- no mass CSS/JS/Blade/storage/Three cleanup;
- no deletion of unreferenced candidates without route/runtime proof;
- no warning-threshold-only bundle "fix";
- no Safari/WebKit/responsive/locale/performance/accessibility PASS claim without
  actual evidence.

## Current proof status

- H1 focused contract: PASS.
- `git --no-pager diff --check`: PASS at H1 checkpoint.
- production build: PASS with graphics warning.
- structure check: FAIL from frozen baseline debt.
- Cloudflare/R2 create/read/delete lifecycle: PASS.
- D1 media/data-owner inventory: PASS / DURABLE.
- D2 Blade purity inventory: PASS / DURABLE.
- D3 CSS/JS/runtime inventory: PASS / DURABLE.
- D4 functional interaction matrix: PASS / DURABLE / SOURCE-CONTRACT.
- D5 responsive/locale/degraded proof: PASS / DURABLE / PROOF-CONTRACT.
- H1 browser slow/reverse acceptance: `BLOCKED_BY_MISSING_EVIDENCE`.
- final six-tier/three-locale/WebKit/performance/accessibility certification:
  `BLOCKED_BY_MISSING_EVIDENCE`.

## Pre-Codex readiness

Current estimate: `92%` toward `CODEX_READY`.

Completed:

1. D1 media + DB/data-owner inventory;
2. D2 Blade presentation-purity inventory;
3. D3 CSS/JS/runtime ownership inventory;
4. D4 functional interaction matrix;
5. D5 responsive/locale/degraded-runtime proof contract.

Remaining:

1. D6 frozen baseline proof + bounded H2-H7 execution packets.

The Codex execution prompt must not be produced until readiness is recorded as
`CODEX_READY / 100_PERCENT`.

## NEXT VALID STEP

Freeze D6 from existing proven baseline and durable D1-D5 facts. Create bounded
H2-H7 implementation packets with scope, acceptance, stop conditions and proof.
Preserve H1 slow/reverse browser acceptance as an explicit gate before H2.
