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

## Current phase

Homepage visual polishing remains PAUSED. H1 has a published minimal source/test
patch, but slow forward/reverse browser acceptance has not yet been recorded as
PASS.

Pre-Codex discovery is allowed to inspect later capabilities and record durable
facts/decisions, but does not authorize H2-H7 runtime mutation.

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
- Laravel authenticated S3 write PASS;
- exists PASS;
- public custom-domain read HTTP 200 PASS;
- delete PASS;
- post-delete exists false PASS;
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
embedded media URLs. Article Canvas nevertheless remains an H6 write owner
because it currently stores content/thumbnail files on Laravel's `public` disk.

H6 must not be implemented as a blind global filesystem-disk switch.

## D2 Blade presentation purity — PASS / DURABLE

Repository proof found:

- 38 Blade files with `@php` blocks/expressions;
- no raw `<?php` hit reported inside those Blade files;
- three raw PHP navbar preparation files under `resources/views`.

H5 ownership groups are Home shaping, public-page shaping, shared chrome/meta/
locale, and admin form/list/archive preparation. Existing admin view composers
prove a suitable shaping mechanism already exists. H5 must remain incremental
and semantics-preserving rather than becoming a giant controller refactor.

## D3 CSS/JS/runtime ownership — PASS / DURABLE

Key facts:

- `welcome.css` is a 47-module ordered aggregate with high cascade/import-order
  sensitivity;
- `welcome-hero.css` is a nine-module ordered aggregate;
- both currently have source-equivalence/checksum drift;
- Gallery/Article newer surface CSS has clearer bounded ownership;
- the known unreferenced candidates remain candidates, not deletion approval.

Runtime ownership:

- `welcome.js` synchronously owns Program, Values and Article control graph plus
  navigation/public helpers;
- Vision uses deferred dynamic enhancement after Hero;
- Gallery currently uses proximity-triggered dynamic loading, which differs from
  the accepted future persistent sequential preparation direction;
- Program loads external jsDelivr GSAP 3.7.1 with reduced/failure fallback;
- Program and Article expose cleanup handles that current bootstraps do not
  retain, creating lifecycle proof debt rather than proven leaks.

Graphics hotspot:

- package Three is `^0.185.1`;
- Gallery separately loads CDN Three `0.183.0`;
- Values spatial imports package Three/addons;
- Values spatial runtime flag is currently false, yet Vite still emits its large
  chunk;
- successful build warns around a 549 kB `spatial-scene` chunk.

H4 must measure and converge this ownership. Raising a warning threshold alone
is forbidden as a solution.

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
- H1 browser slow/reverse acceptance: `BLOCKED_BY_MISSING_EVIDENCE`.
- final six-tier/three-locale/WebKit/performance/accessibility certification:
  `BLOCKED_BY_MISSING_EVIDENCE`.

## Pre-Codex readiness

Current estimate: `75%` toward `CODEX_READY`.

Completed:

1. D1 media + DB/data-owner inventory;
2. D2 Blade presentation-purity inventory;
3. D3 CSS/JS/runtime ownership inventory.

Remaining:

1. D4 functional interaction matrix;
2. D5 responsive/locale/degraded-runtime proof contract;
3. D6 frozen baseline proof + bounded H2-H7 execution packets.

The Codex execution prompt must not be produced until readiness is recorded as
`CODEX_READY / 100_PERCENT`.

## NEXT VALID STEP

Build D4 read-only from current routes, rendered controls, and JS interaction
owners. Record semantic action, pointer/touch/keyboard behavior, failure/retry,
repeat interaction and restoration requirements without changing runtime source.
