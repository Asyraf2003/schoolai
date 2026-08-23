# Pre-Codex Hardening Readiness — 2026-08-23

Status: `DISCOVERY_ACTIVE / 45_PERCENT`
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Source main SHA at discovery checkpoint start: `6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`

## Purpose

Prepare a durable fact/decision/proof packet so Codex can execute H2-H7 without
spending the implementation session rediscovering project intent, inventing
architecture, or fixing unrelated debt. This discovery does not advance the
active implementation capability: H1 runtime acceptance remains pending.

The Codex execution prompt must not be produced until this readiness ledger is
complete and the owner-visible status reaches `CODEX_READY / 100_PERCENT`.

## Owner constraints

- Finish the requested hardening preparation without unnecessary
  over-engineering.
- Extra architecture/process work is allowed only when it is critical to the
  H2-H7 hardening workflow or prevents Codex from guessing.
- Repository docs are the durable system of record; chat history must not be a
  required implementation dependency.
- Codex must execute bounded capabilities from verified facts, owner decisions,
  known baseline debt, explicit proof gates, and forbidden-scope rules.

## Accepted responsive/fidelity direction

The six canonical width tiers remain the layout contract. Fidelity is adaptive
and does not equate a viewport width with hardware capability.

### Phone

- Preserve the full SchoolAI visual identity, content hierarchy, typography,
  section identity, and polished composition.
- Static or lightweight states are intentional finished designs, not broken
  fallbacks.
- Do not depend on hover or pointer-specific interaction.
- Prefer touch-appropriate motion and reduce continuous spatial work where it
  does not improve the phone experience.

### Tablet

- Preserve the same SchoolAI identity while using a semi-interactive,
  touch-oriented composition.
- Tablet is not a shrunken desktop; pointer-hover assumptions must not leak into
  touch interaction.
- Cinematic enhancement may be richer than phone while still respecting
  runtime capability.

### Desktop/laptop

- Full interactive/cinematic behavior is the default target.
- Pointer/hover-aware interaction and the richest approved choreography are
  allowed.
- A weak runtime may downgrade fidelity without losing content or interaction.

## Accepted loading and preparation direction

Product goal: information and the initial visual composition appear as quickly
as practical, while advanced assets are prepared aggressively in the
background.

Static readiness is an explicit product state. If enhancement is not ready when
an owner scrolls into a section, the section presents its complete polished
static composition rather than a blank area, loader-only shell, broken fallback,
or layout shift.

Preparation is sequential and aggressive after the initial critical state:

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

The pipeline continues even if the user scrolls faster than preparation and may
continue while the tab is hidden. Preparation is not the same as continuous
execution.

Accepted invariant:

```text
aggressive fetch/import/cache/preparation
+
selective visible/relevant runtime execution
```

Offscreen sections must not keep unnecessary continuous RAF/WebGL/video work
running merely because their assets are already prepared.

A prepared section remains prepared for reverse scrolling; it must not restart
its complete initialization/download lifecycle just because the user passed it.

Provisional per-section lifecycle vocabulary for H2/H3 audit:

```text
SEMANTIC
-> STATIC_READY
-> FETCHING
-> PREPARED
-> ENHANCEMENT_READY
-> ACTIVE
-> SUSPENDED
-> DISPOSED
```

Enhancement failure should resolve to a valid `STATIC_READY` experience where
possible, not an empty or visibly unfinished page.

The owner's approximate `0.1s` initial-display goal is currently a product
aspiration, not a proven universal network SLA. D8 must convert it into measured
critical-path budgets rather than inventing proof.

## H1 proof discovered during readiness work

Published H1 main SHA: `6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`.

Observed owner-terminal proof after publication:

- focused H1 contract test: PASS, `1 passed`, `7 assertions`;
- `git --no-pager diff --check`: PASS;
- `npm run build`: PASS, 141 modules transformed, with the existing graphics
  chunk warning;
- `npm run check:structure`: FAIL from existing unrelated structural debt;
- full `HomeDepthGalleryTest`: one stale continuity assertion that is already
  inconsistent with the pre-H1 source and is therefore not classified as an H1
  regression.

H1 runtime slow-forward/reverse acceptance is still not recorded as PASS. Do
not mark H1 complete or move durable implementation state to H2 from this
readiness document alone.

## Cloudflare/R2 discovery

### Infrastructure facts

- Cloudflare R2 is already provisioned; H6 is not a greenfield Cloudflare setup.
- Bucket: `almustaqbal`.
- Created: 2026-08-09.
- Location: Asia-Pacific (APAC).
- Default storage class: Standard.
- Public access: enabled.
- Production custom domain: `media.almustaqbal.sch.id`.
- Custom domain status: active.
- Public `r2.dev` development URL exists but is not the intended production
  application URL.
- R2 Data Catalog: disabled.
- CORS policy: none currently defined.
- Object lifecycle: default multipart-abort rule after 7 days is enabled.
- Bucket lock: none.
- Event notifications: not configured/required for the current design.
- On Demand Migration: disabled.
- Local Uploads beta: disabled.
- Bucket was empty during this discovery before the diagnostic probe.

### S3/Laravel contract facts

The Laravel filesystem config already contains a generic S3 disk, but the
checked-in environment example does not yet fully represent the required R2
contract. Relevant R2 configuration shape is:

```text
AWS_ACCESS_KEY_ID=<secret, environment only>
AWS_SECRET_ACCESS_KEY=<secret, environment only>
AWS_DEFAULT_REGION=auto
AWS_BUCKET=almustaqbal
AWS_URL=https://media.almustaqbal.sch.id
AWS_ENDPOINT=https://<cloudflare-account-id>.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Secret values are deliberately not recorded in repository documentation.
Local R2 credentials are configured and proved; production secret rotation and
least-privilege deployment are release/deploy concerns rather than blockers for
current local discovery.

Do not switch the application's default filesystem disk globally merely to
prove R2. Existing owners must be migrated intentionally during H6.

### R2 lifecycle proof

Owner-terminal diagnostic object: `diagnostics/r2-probe.txt`.

Proof sequence:

1. Laravel `Storage::disk('s3')->put(...)`: PASS.
2. Laravel `Storage::disk('s3')->exists(...)`: PASS.
3. Public read through `https://media.almustaqbal.sch.id/...`: HTTP 200 PASS.
4. Response was served through Cloudflare and supported byte ranges.
5. Laravel `Storage::disk('s3')->delete(...)`: PASS.
6. Post-delete Laravel `exists(...)`: false PASS.
7. Post-delete public custom-domain request: HTTP 404 PASS.

Observed public response currently reported `cf-cache-status: DYNAMIC`.
Therefore Cloudflare routing is proven, while the final media cache policy is
not yet designed or certified.

### R2 gaps that remain for H6

- Inventory every media owner and current `/storage`, external, DB, CSS, JS,
  seed, and admin-managed reference.
- Decide/store an object-key contract and URL-resolution ownership from actual
  data/model evidence.
- Define upload/replace/delete/soft-delete/restore semantics per current media
  owner.
- Determine CORS only from actual browser fetch/canvas/WebGL consumers; do not
  add permissive CORS speculatively.
- Define cache/version/immutability policy from actual object naming and update
  lifecycle.
- Define image variant/thumbnail/original policy from actual consumers.
- Confirm compressed video delivery requirements and whether direct R2 remains
  sufficient or any case genuinely requires another Cloudflare product.
- Define migration and rollback from current local/third-party media to R2.
- Update `.env.example`/deployment contract only after H6 architecture is
  explicit.

## Known pre-existing technical debt discovered so far

### Structure check

Known violations include source files over the enforced 200-line limit,
unreferenced source candidates, and source-equivalence checksum drift. These are
baseline debt until an active capability proves ownership and safe correction.
Do not mass-refactor them merely to make `check:structure` green.

Known examples include:

- `resources/css/pages/welcome/043-welcome-cascade-043.css`;
- Article story CSS/controller owners;
- Gallery `end-cta.css`;
- Gallery heading `desktop-continuity.js`;
- Program/Values controllers and Values spatial scene;
- `resources/views/home/sections/articles.blade.php`;
- unreferenced candidates `article-story/footer-release.css`,
  `vision-story/entry.js`, and `vision-story/typography.js`;
- checksum drift for `resources/css/pages/welcome-hero.css` and
  `resources/css/pages/welcome.css`.

### Graphics

- A successful production build currently warns about a graphics chunk above
  500 kB; the observed `spatial-scene` output was about 549 kB minified.
- Gallery runtime Three.js ownership is inconsistent with the package graph:
  Gallery has been observed using `three@0.183.0` from a CDN while
  `package.json` declares `three ^0.185.1`.
- H4 must measure and unify runtime ownership rather than hiding the warning by
  increasing the warning threshold.

### Blade

The project already has an accepted presentation-only target. A repository-wide
inventory is still required before H5 so `@php`/raw-PHP/data-shaping cases can
be classified by actual owner and migrated incrementally rather than blindly
moved into controllers.

## Pre-Codex readiness work remaining

The following six discovery batches are intentionally bounded and should be
completed before the final Codex implementation prompt is produced.

### D1 — Media and data-owner inventory

Map all public/admin media references, models/DB fields, storage calls, upload,
replace, delete, restore, object/public URL handling, third-party origins, and
static assets relevant to H6.

### D2 — Blade presentation-purity inventory

Enumerate production raw PHP/`@php`/data shaping and classify the correct
controller/action/service/view-model/component owner without changing rendered
semantics.

### D3 — CSS/JS/runtime ownership inventory

Classify active owners, duplicate owners, losing cascade, dead/unreferenced
candidates, legacy-required modules, RAF/listener/observer ownership, graphics
runtime, import graph, and measurable performance hotspots.

### D4 — Functional interaction matrix

Map primary controls and routes across navigation, locale, Program, Gallery,
Article, PPDB, auth/admin where release scope requires them. Record click,
keyboard, touch, repeated interaction, fast/reverse movement, failure, and
restoration expectations.

### D5 — Responsive/locale/degraded-runtime completion

Convert the accepted phone/tablet/desktop fidelity direction into proofable
contracts for all six width tiers, ID/EN/AR, LTR/RTL, normal/reduced motion,
network delay, fast scrolling, hidden tab, failed enhancement, orientation, and
Chromium/WebKit.

### D6 — Baseline proof and H2-H7 execution packets

Freeze current baseline failures/warnings, required commands and runtime proof,
then produce one bounded scope packet per H2-H7 containing facts, gaps,
decisions, editable/read-only/forbidden owners, acceptance, stop condition, and
proof requirements.

Only after D1-D6 are durable and reconciled with current `main` may readiness be
changed to `CODEX_READY / 100_PERCENT` and the Codex execution prompt be
created.

## Progress

Current readiness estimate: `45%`.

This percentage measures readiness to delegate H2-H7 to Codex without material
project rediscovery or owner-intent guessing. It is not a claim that 45% of the
hardening implementation itself is complete.

## NEXT VALID STEP

Read-only D1 media/data-owner inventory from current `main`. Do not mutate
runtime source while building this inventory.
