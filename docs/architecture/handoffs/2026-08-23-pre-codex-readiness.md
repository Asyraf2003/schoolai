# Pre-Codex Hardening Readiness — 2026-08-23

Status: `DISCOVERY_ACTIVE / 65_PERCENT`
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

## D1 — Media and data-owner inventory

Status: `PASS / DURABLE` for the current local database snapshot and audited H6
source owners.

### Proven media origin classes

SchoolAI currently has four relevant media ownership/origin classes:

1. DB/admin-managed URL fields;
2. repository-owned static assets under `/media/...` and `/images/...`;
3. external URLs, with Unsplash dominating the current sampled DB rows;
4. Article Canvas uploads that still use Laravel's `public` disk.

This proves H6 must migrate owners deliberately. A blind global default-disk
switch is not an acceptable migration design.

### Current local DB URL inventory

`hero_slides.media_url`:

- `/media/...`: 2
- external: 3
- `/storage/...`: 0
- `/images/...`: 0
- R2 custom-domain: 0

`hero_slides.poster_url`:

- `/images/...`: 1
- external: 4
- `/storage/...`: 0
- `/media/...`: 0
- R2 custom-domain: 0

`gallery_items.media_url`:

- external: 6
- all audited local/R2 classes: 0

`gallery_page_media_items.media_url`:

- external: 12
- all audited local/R2 classes: 0

The current local schema does not contain
`gallery_page_media_items.poster_url`; any later H6 design must follow actual
schema/source rather than retain that earlier assumption.

`ppdb_showcase_items.media_url`:

- external: 6
- all audited local/R2 classes: 0

`testimonial_media.media_url`:

- external: 12
- all audited local/R2 classes: 0

`articles.thumbnail_url`:

- external: 10
- all audited local/R2 classes: 0

No audited current DB row above already uses `media.almustaqbal.sch.id`.

### Article content media

Article content is stored per locale in:

- `content_id`
- `content_en`
- `content_ar`

There is no current `content_html` Article field.

Owner-terminal DB proof found zero embedded media URLs in all three content
columns across these classes:

- `/storage/...`
- `/media/...`
- `/images/...`
- `media.almustaqbal.sch.id`
- external HTTP(S)

This describes the current local dataset only; it does not mean Article Canvas
lacks media capability.

### Article Canvas write owner

Current source proves Article Canvas remains an H6 migration owner:

- purpose is `content` or `thumbnail`;
- uploads call `store(..., 'public')`;
- paths are `articles/content/{article_id}` or
  `articles/thumbnails/{article_id}`;
- public URL generation uses `Storage::url($path)`;
- thumbnail uploads update `articles.thumbnail_url`.

Therefore H6 must cover future upload/write/delete/public-URL behavior even
though the current local article-content rows contain no embedded media URLs.

### D1 conclusions

- Current DB media is primarily third-party/external, not yet R2-backed.
- Repo-owned Hero/static media must remain a separate owner from DB/admin media.
- Existing local and external URL compatibility must survive until an explicit
  migration step owns replacement.
- H6 still needs object-key/URL-resolution design, upload/replace/delete/restore
  semantics, variants, cache policy, actual CORS consumer proof, migration and
  rollback, `.env.example`, and production deploy wiring.

## D2 — Blade presentation-purity inventory

Status: `PASS / DURABLE`.

Detailed file-level inventory and H5 classification are recorded in:

`handoffs/2026-08-23-blade-purity-inventory.md`

Owner-terminal repository proof found:

- 38 Blade files containing `@php` blocks/expressions;
- no raw `<?php` hit reported inside those Blade files;
- three non-Blade PHP files under `resources/views`, all owned by navbar data/
  presentation preparation.

The three raw view-tree PHP owners are:

- `resources/views/partials/site-navbar/data/context.php`;
- `resources/views/partials/site-navbar/data/menu.php`;
- `resources/views/partials/site-navbar/data/presentation.php`.

The inventory is classified into four migration ownership groups:

1. Home surface data shaping;
2. public page shaping;
3. shared chrome/locale/meta preparation;
4. admin form/list/archive/replacement preparation.

The navbar is the strongest shared-chrome violation: its view-tree PHP performs
locale resolution, localized mega-menu copy construction, route/anchor
construction, menu mutation/filtering, login insertion, and presentation-media
preparation. H5 must move this preparation to a presenter/composer/application
owner while preserving one semantic navbar for desktop/mobile.

H5 must not respond by blindly moving every expression into controllers.
Existing `AdminPpdbEditComposer` and `AdminGalleryIndexComposer` already prove
that composers are an appropriate owner for admin render-data shaping. Trivial
presentation checks may remain as direct Blade conditions/expressions without
creating new service layers.

H5 completion proof must include a repository-wide zero result for production
`@php`/raw-PHP view preparation plus unchanged public/admin behavior, locale,
routes, and test semantics.

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

D2 is complete. The current problem is broad but bounded: 38 Blade files plus
three raw navbar PHP files. H5 execution must follow the durable classification
in `handoffs/2026-08-23-blade-purity-inventory.md` and preserve rendered
semantics rather than perform a monolithic controller refactor.

## Pre-Codex readiness work remaining

D1 and D2 are complete. Four bounded discovery batches remain before the final
Codex implementation prompt is produced.

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

Only after the remaining batches are durable and reconciled with current `main`
may readiness be changed to `CODEX_READY / 100_PERCENT` and the Codex execution
prompt be created.

## Progress

Current readiness estimate: `65%`.

Completed:

- D1 media + DB/data-owner inventory — PASS / DURABLE;
- D2 Blade presentation-purity inventory — PASS / DURABLE.

Remaining:

- CSS/JS/runtime ownership inventory;
- functional interaction matrix;
- responsive/locale/degraded-runtime completion;
- baseline proof ledger + H2-H7 execution packets.

This percentage measures readiness to delegate H2-H7 to Codex without material
project rediscovery or owner-intent guessing. It is not a claim that 65% of the
hardening implementation itself is complete.

## NEXT VALID STEP

Read-only D3 CSS/JS/runtime ownership inventory from current `main`. Start from
the frozen `check:structure` debt and Vite/import/runtime ownership evidence;
do not mutate source merely to make structural checks green.
