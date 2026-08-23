# UI/UX Engineering — Current State and Progress Ledger

Status: `HARDENING_ACTIVE / PRE_CODEX_DISCOVERY`
Updated: 2026-08-23
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Inspected runtime-source HEAD: `6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`
Durable hardening handoff: `handoffs/2026-08-23-homepage-hardening.md`
Pre-Codex readiness ledger: `handoffs/2026-08-23-pre-codex-readiness.md`
Blade purity inventory: `handoffs/2026-08-23-blade-purity-inventory.md`

## Current phase

Homepage visual polishing remains PAUSED. H1 has a published minimal source/test
patch, but its slow forward/reverse browser acceptance has not yet been recorded
as PASS. The owner has opened a bounded read-only/decision discovery phase to
prepare H2-H7 facts, constraints, baseline debt, and proof packets before Codex
execution.

This discovery does not authorize H2-H7 runtime mutation. Its purpose is to stop
future Codex sessions from wasting implementation time rediscovering the project
or making owner-level architecture decisions by inference.

## Current FACT

- H1 changed Gallery frame health so renderer/context health is no longer tied to
  whether a Gallery plane or end CTA is visually present during a legitimate
  transition frame.
- Published H1 main SHA is `6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`.
- The prior slow-scroll symptom was a dark `.nav-shell` exposure plus static
  Gallery fallback and broken downstream state. Slow/reverse runtime acceptance
  of the patch remains unrecorded.
- Gallery uses internal smoothed visual motion and handoff/document progress;
  H2/H3 still need explicit lifecycle and authoritative-clock contracts after H1
  is accepted.
- `npm run build` passes while warning about a graphics chunk above 500 kB. The
  warning is not evidence of the H1 failure and must not be hidden by merely
  raising the threshold.
- Three.js ownership remains inconsistent: Gallery has been observed loading
  `three@0.183.0` from a CDN while `package.json` declares `three ^0.185.1`.
- `npm run check:structure` currently has pre-existing structural debt including
  over-200-line source, unreferenced candidates, and source checksum drift.
- One existing `HomeDepthGalleryTest` continuity expectation is stale relative
  to source that already predates H1; it is baseline debt, not an H1 regression.
- Article visual work remains intentionally unfinished. Temporary viewport
  rulers/markers remain protected until visual work resumes.

## Owner-accepted durable direction

- Hardening comes before further visual polishing.
- Avoid unnecessary over-engineering. Extra workflow/architecture work is
  justified only when critical to H2-H7 hardening or necessary to prevent an
  implementation agent from guessing.
- Blade should ultimately become presentation-only: no raw PHP/`@php`, business
  or data access, collection shaping, or view-owned data preparation. The exact
  migration destination must follow actual source ownership rather than blindly
  moving every expression into controllers.
- Final product support remains six responsive width tiers, ID/EN/AR, LTR/RTL,
  Chromium and Safari/WebKit. This is a certification matrix, not permission to
  fork implementations.
- Phone, tablet, and desktop keep one SchoolAI identity but may have different
  interaction density because touch and pointer/hover are materially different
  input models.
- Phone static/light states must still be polished final compositions, never a
  visually unfinished fallback.
- Tablet is semi-interactive/touch-oriented rather than a shrunken desktop.
- Desktop/laptop is the richest approved interactive/cinematic target, with
  runtime downgrade allowed when capability requires it.
- Initial semantic/static content and the first visual composition should become
  usable as quickly as practical; the owner's approximate 0.1s ambition is an
  aspiration pending measured critical-path budgets, not an invented universal
  SLA.
- After initial readiness, enhancement preparation is aggressive and sequential:
  Hero -> Program -> Values -> Vision/Mission -> Gallery -> Article -> Footer.
- The preparation pipeline may continue during fast user scrolling and while the
  tab is hidden. Prepared sections must remain prepared for reverse scrolling.
- Aggressive fetch/import/cache/preparation is allowed, but unnecessary
  continuous offscreen RAF/WebGL/video execution is not.
- If enhancement is not ready when a section is reached, a complete
  `STATIC_READY` composition must remain available with no blank shell, loader-
  only state, broken layout, or missing primary information/action.
- Media CRUD/index/display should ultimately use Cloudflare-backed delivery.
  Database records remain metadata/relationship source of truth; binary objects
  are expected to use the R2 S3-compatible filesystem path.
- Product target remains Lighthouse/PageSpeed `100/100/100/100` on declared lab
  profiles; field CWV claims require real p75 field evidence.

## Cloudflare/R2 proven discovery

Cloudflare infrastructure is already provisioned; H6 is application migration
and contract work, not a new Cloudflare account/bucket setup.

- R2 bucket: `almustaqbal`.
- Location: APAC.
- Default storage class: Standard.
- Public access: enabled.
- Production custom domain: `media.almustaqbal.sch.id`, active.
- Public `r2.dev` URL exists for development but is not the intended production
  application URL.
- CORS policy is currently absent and must not be made permissive without actual
  browser/canvas/WebGL consumer evidence.
- Default multipart-abort lifecycle rule after seven days is enabled.
- Bucket lock, on-demand migration, and local uploads are not active.
- Laravel generic S3 configuration can target R2, while the checked-in env
  example still needs an eventual R2-specific contract update.
- Local Laravel R2 credential/configuration is working. Secret values are not
  durable documentation.
- Laravel S3 diagnostic write: PASS.
- Laravel S3 diagnostic exists check: PASS.
- Public read through `media.almustaqbal.sch.id`: HTTP 200 PASS.
- Laravel S3 diagnostic delete: PASS.
- Post-delete exists: false PASS.
- Post-delete public custom-domain request: HTTP 404 PASS.
- Observed Cloudflare response currently reports `cf-cache-status: DYNAMIC`;
  final media cache/version policy remains an H6 discovery gap.

Full decisions/proof and remaining Cloudflare gaps are recorded in
`handoffs/2026-08-23-pre-codex-readiness.md`.

## D1 media + DB/data-owner inventory

D1 is PROVEN for the current local database snapshot plus the audited source
owners relevant to H6 planning.

Current media ownership/origin classes are:

1. DB/admin-managed URL fields;
2. repository-owned static `/media/...` and `/images/...` assets;
3. external URLs, currently dominated by Unsplash in the sampled DB rows;
4. Article Canvas uploads, whose source path still writes to Laravel's `public`
   disk even though the current local article-content rows contain no embedded
   media URLs.

Proven current local DB distribution:

- `hero_slides.media_url`: 2 `/media/...`, 3 external, 0 `/storage/...`, 0
  `/images/...`, 0 R2 custom-domain URLs.
- `hero_slides.poster_url`: 1 `/images/...`, 4 external, 0 `/storage/...`, 0
  `/media/...`, 0 R2 custom-domain URLs.
- `gallery_items.media_url`: 6 external, 0 audited local/R2 classes.
- `gallery_page_media_items.media_url`: 12 external, 0 audited local/R2 classes.
- Current local schema has no `gallery_page_media_items.poster_url`; do not
  design H6 around that nonexistent field.
- `ppdb_showcase_items.media_url`: 6 external, 0 audited local/R2 classes.
- `testimonial_media.media_url`: 12 external, 0 audited local/R2 classes.
- `articles.thumbnail_url`: 10 external, 0 audited local/R2 classes.
- No audited current DB media row is already stored on
  `media.almustaqbal.sch.id`.

Article content uses `content_id`, `content_en`, and `content_ar`; there is no
`content_html` field in the current Article contract. Runtime DB proof found zero
embedded media URLs in all three content columns across `/storage`, `/media`,
`/images`, R2 custom-domain, and external HTTP(S) classes.

Source still proves Article Canvas media migration is required:

- upload purpose can be `content` or `thumbnail`;
- current writes use `store(..., 'public')`;
- current directories are `articles/content/{article_id}` and
  `articles/thumbnails/{article_id}`;
- URLs are generated through `Storage::url($path)`;
- thumbnail upload updates `articles.thumbnail_url`.

H6 therefore must migrate storage ownership deliberately; it must not be
implemented as a blind global `FILESYSTEM_DISK=s3` switch. Existing repo assets,
external URLs, local-path compatibility, upload/write/delete behavior, and DB
semantics remain distinct owners until an explicit H6 step owns them.

Object-key design, image/video variants, cache policy, actual CORS need,
migration/rollback, and production env/deploy wiring remain D10/H6 gaps.

## D2 Blade presentation-purity inventory

D2 is PROVEN and durable in
`handoffs/2026-08-23-blade-purity-inventory.md`.

Owner-terminal repository proof found:

- 38 Blade files containing `@php` blocks or expressions;
- no raw `<?php` hit reported inside those Blade templates;
- three raw PHP data/presentation files under
  `resources/views/partials/site-navbar/data/`.

The violations are classified into four H5 ownership groups: Home surface data
shaping, public page shaping, shared chrome/locale/meta preparation, and admin
form/list/archive/replacement preparation.

The navbar is the highest-confidence shared-chrome migration owner because the
view tree currently performs locale/copy assembly, route/anchor construction,
menu mutation/filtering, login insertion, and presentation-media construction.
Existing `AdminPpdbEditComposer` and `AdminGalleryIndexComposer` prove that the
repo already has a suitable render-data preparation pattern for admin shaping.

H5 must not become a giant controller refactor. Non-trivial shaping moves to its
actual controller/composer/presenter/model/service owner; trivial presentation
conditions can remain direct Blade directives/expressions without `@php`.

## Hardening sequence

The sequence below remains the implementation backlog. Pre-Codex discovery may
inspect later capabilities read-only, but runtime mutation must stay bounded.

1. `H1 Gallery false-fallback hardening`
   - source/test patch published;
   - slow forward/reverse owner runtime acceptance still pending.
2. `H2 Gallery lifecycle/state hardening`
   - make loading/static-ready/prepared/active/ending/handoff/fallback semantics
     explicit and prevent contradictory state/class transitions.
3. `H3 Scroll-clock reconciliation`
   - keep smoothing for visual motion where useful, but make section lifecycle
     and handoff progress deterministic from authoritative state/progress.
4. `H4 Graphics runtime/bundle hardening`
   - converge on one Three.js runtime strategy and measure/code-split graphics
     without disguising bundle warnings.
5. `H5 Blade presentation-purity migration`
   - migrate the durable D2 inventory incrementally with unchanged rendered
     semantics.
6. `H6 Cloudflare media migration`
   - inventory media/data owners, object-key/URL contract, CRUD lifecycle,
     variants, CORS/cache policy, migration, rollback, and deployment env.
7. `H7 Release certification`
   - certify six tiers x three locales x Chromium/WebKit plus performance,
     accessibility, failure, lifecycle, and input gates.

## Protected / deferred

- Do not redesign Gallery, Article, Program, Values, Vision/Mission, navigation,
  or Footer during discovery/hardening unless the active accepted capability
  explicitly requires it.
- Do not tune Article ruler coordinates during engine/readiness work.
- Do not mass-migrate storage, Blade, CSS, JS, or Three.js merely because the
  discovery finds debt.
- Do not mass-delete unreferenced candidates without runtime/import ownership
  proof.
- Do not suppress the >500 kB warning by changing only a warning threshold.
- Do not claim Safari/WebKit, responsive, locale, Lighthouse/PageSpeed, CWV, or
  accessibility PASS without actual proof.

## Current proof status

- Focused H1 contract: PASS, 1 test / 7 assertions.
- `git --no-pager diff --check`: PASS.
- `npm run build`: PASS, 141 modules transformed, graphics chunk warning remains.
- `npm run check:structure`: FAIL from known baseline structural debt; none of
  the reported violations targets the H1 `engine-frame.js` change.
- Full `HomeDepthGalleryTest`: blocked by one stale pre-H1 continuity assertion;
  focused H1 contract passes.
- Cloudflare/R2 create/read/delete diagnostic lifecycle: PASS as recorded above.
- D1 media + DB/data-owner inventory: PASS for current local DB snapshot and
  audited source owners.
- D2 Blade presentation-purity inventory: PASS, 38 Blade files plus three raw
  navbar PHP files classified for H5.
- H1 browser slow/reverse acceptance: `BLOCKED_BY_MISSING_EVIDENCE`.
- Six-tier/three-locale/WebKit/PageSpeed/accessibility release proof:
  `BLOCKED_BY_MISSING_EVIDENCE`.

## Pre-Codex readiness

Current estimate: `65%` toward `CODEX_READY`.

Completed bounded discovery batches:

1. media + DB/data-owner inventory — PASS and durable;
2. Blade presentation-purity inventory — PASS and durable.

Remaining bounded discovery batches:

1. CSS/JS/runtime ownership and performance-hotspot inventory;
2. functional interaction matrix;
3. responsive/locale/degraded-runtime proof contract completion;
4. baseline proof ledger plus bounded H2-H7 execution packets.

The percentage measures delegation readiness, not implementation completion.
The Codex execution prompt must not be created until the readiness ledger is
complete and recorded as `CODEX_READY / 100_PERCENT`.

## NEXT VALID STEP

Read-only D3 CSS/JS/runtime ownership inventory from current `main`, starting
from the frozen `check:structure` debt and actual Vite/import/runtime ownership.
Do not mutate source merely to make structural checks green.
