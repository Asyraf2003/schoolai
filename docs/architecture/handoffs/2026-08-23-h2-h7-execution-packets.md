# H2-H7 Hardening Execution Packets — 2026-08-23

Status: `OWNER-ACCEPTED / CODEX-EXECUTABLE`
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Runtime-source checkpoint for discovered facts:
`6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`

## Purpose

Freeze the remaining hardening work into bounded packets so an implementation
agent can execute without repeating project discovery or inventing owner-level
architecture. `AGENTS.md` and the mandatory architecture chain remain higher
priority than this handoff.

Do not execute H2 until the H1 runtime gate below is PASS.

## 0. Frozen baseline ledger

### Proven PASS

- H1 focused Gallery frame-health contract: 1 test / 7 assertions.
- H1 `git --no-pager diff --check`: PASS.
- H1 production Vite build: PASS, 141 modules transformed.
- Cloudflare R2 local Laravel write + exists: PASS.
- R2 public custom-domain read: HTTP 200 PASS.
- R2 delete + post-delete exists false: PASS.
- post-delete custom-domain request: HTTP 404 PASS.
- D1 media/data-owner discovery: PASS / durable.
- D2 Blade purity inventory: PASS / durable.
- D3 CSS/JS/runtime ownership inventory: PASS / durable.
- D4 functional interaction source contract: PASS / durable.
- D5 responsive/locale/degraded proof contract: PASS / durable.

### Frozen baseline FAIL / debt

`npm run check:structure` is known red from pre-existing debt. Observed classes:

- source files above the 200-line limit, including current Welcome cascade,
  Article story, Gallery end CTA, Gallery heading continuity, Program/Values
  controllers and Values spatial scene;
- unreferenced candidates:
  - `resources/css/surfaces/home/article-story/footer-release.css`;
  - `resources/js/surfaces/home/vision-story/entry.js`;
  - `resources/js/surfaces/home/vision-story/typography.js`;
- source-equivalence checksum drift for:
  - `resources/css/pages/welcome.css`;
  - `resources/css/pages/welcome-hero.css`.

A full `HomeDepthGalleryTest` run also has one stale continuity assertion that
already disagreed with pre-H1 source. It is baseline test debt, not an H1
regression.

The successful build warns about an approximately 549 kB `spatial-scene` /
Three-related chunk. Do not hide it by changing only the warning threshold.

### Missing evidence

- H1 repeated slow forward/reverse browser acceptance;
- fresh full `php artisan test` on the final implementation head;
- fresh G0 after H2-H6;
- six-tier x three-locale x Chromium/WebKit runtime matrix;
- physical Safari proof where unavailable;
- Lighthouse/PageSpeed final lab matrix;
- field p75 CWV.

Unavailable/unrun proof is `BLOCKED_BY_MISSING_EVIDENCE`, never PASS.

## 1. Mandatory H1 gate before H2

H1 source/test patch is already published. Do not redesign or rewrite H1 merely
because execution has moved to Codex.

Required browser proof on the current H1 behavior:

- repeated slow forward Gallery -> Article;
- deliberate pause inside Gallery ending/handoff;
- reverse Article -> Gallery;
- rapid forward/reverse repetition;
- no spontaneous static fallback;
- no dark `#071b18` shell exposure;
- no Gallery WebGL disappearance while renderer/context remain healthy;
- no `Depth gallery produced an invalid frame` failure from legitimate empty
  transition choreography;
- reverse path remains stable.

If this cannot be executed in the current channel, STOP H2 with
`BLOCKED_BY_MISSING_EVIDENCE` and request only this owner/browser proof. Do not
substitute another code change for missing acceptance evidence.

When PASS, update current-state/handoff and begin H2.

---

# H2 — Gallery lifecycle/state hardening

## Goal

Replace implicit/contradictory Gallery lifecycle combinations with one explicit,
idempotent lifecycle contract while preserving the accepted visual composition
and H1 frame-health behavior.

## FACT

- Gallery page entry has its own loaded/proximity state.
- Gallery controller currently tracks `disposed`, `inView`, `initializing`,
  `active`, engine existence and multiple CSS state classes.
- fallback application mutates classes, accessibility state and engine lifetime.
- hidden tab, BFCache, reduced motion and IntersectionObserver already have
  partial lifecycle handling.
- accepted product lifecycle vocabulary is:

```text
SEMANTIC -> STATIC_READY -> FETCHING -> PREPARED
-> ENHANCEMENT_READY -> ACTIVE -> SUSPENDED -> DISPOSED
```

Failure resolves to a valid static-ready experience rather than blank output.

## Editable ownership

Prefer the smallest necessary subset of:

- `resources/js/surfaces/home/gallery-depth/controller.js`;
- a new bounded Gallery lifecycle/state module if it reduces contradictory state;
- `resources/js/pages/welcome-depth-gallery.js` only when page-entry state must
  align with the lifecycle;
- `resources/js/surfaces/home/gallery-depth/engine.js` only for lifecycle API
  boundaries, not choreography redesign;
- focused Gallery tests.

## Read-only by default

- Gallery Blade composition;
- Gallery motion/plane/trail art direction;
- Article composition;
- Program/Values/Vision/Footer;
- Three dependency/runtime selection (H4).

## Acceptance

- one explicit source of lifecycle truth;
- idempotent initialize/activate/start/stop/suspend/dispose;
- no contradictory ready/active/fallback/leaving classes;
- fallback remains semantic and accessible;
- reduced motion does not initialize unnecessary WebGL;
- hidden/offscreen does not continuously render;
- BFCache persisted exit stops/suspends without destructive duplicate remount;
- non-persisted exit disposes;
- repeated observer enter/exit does not create duplicate engine/listeners/canvas;
- H1 slow/reverse behavior remains PASS.

## Stop condition

If fixing lifecycle requires changing Gallery visual choreography or a global
scheduler, stop and record the narrower dependency for H3/H4 rather than
expanding H2.

---

# H3 — Scroll-clock reconciliation

## Goal

Separate authoritative lifecycle/handoff progress from smoothed visual motion so
slow, fast and reverse scrolling are deterministic without removing the accepted
smooth feel.

## FACT

- Gallery has an internal smoothed scroll/motion path plus document/handoff
  progress.
- H1 proved renderer health must not depend on whether a plane/CTA happens to be
  visible during a legitimate transition frame.
- visual smoothing is desirable; lifecycle and handoff decisions must not lag
  into contradictory state.

## Editable ownership

Only after source audit proves need:

- `resources/js/surfaces/home/gallery-depth/scroll.js`;
- Gallery handoff/end-CTA state owners;
- `engine-frame.js` only if preserving H1 contract requires interface cleanup;
- `gallery-motion.js` only for separation of visual vs authoritative progress;
- Gallery heading continuity only if directly part of the same authoritative
  handoff clock;
- focused tests.

Article/Values/Program clocks remain read-only unless direct shared ownership is
proven. Do not turn H3 into a site-wide scroll rewrite.

## Acceptance

- authoritative section/end/handoff state derives from deterministic document /
  owned geometry progress;
- smoothing affects presentation, not whether lifecycle believes the section is
  valid/ended;
- fast, slow and reverse reach equivalent semantic states for equivalent scroll
  position;
- pausing near boundaries does not drift into fallback or stale handoff;
- resize recalculates geometry without jumping to contradictory state;
- visual smoothness remains owner-equivalent;
- H1/H2 lifecycle/fallback tests remain green.

## Stop condition

If a different surface merely looks visually imperfect but is not part of the
Gallery authoritative clock, leave it for its own later visual scope.

---

# H4 — Graphics runtime, loading graph and bundle hardening

## Goal

Converge graphics/runtime ownership and implement the accepted static-first,
aggressive sequential preparation strategy without sacrificing visual fidelity or
creating invisible continuous work.

## FACT

- `package.json` declares Three `^0.185.1`.
- Gallery separately imports Three `0.183.0` from jsDelivr at runtime.
- Values spatial scene imports package Three/addons.
- Values spatial runtime is currently disabled but its dynamic graph still emits
  the large Three-related chunk.
- Program loads jsDelivr GSAP 3.7.1 and already has a functional failure fallback.
- `welcome.js` currently synchronously imports Program, Values and Article.
- Vision is deferred.
- Gallery is proximity-loaded.
- accepted future preparation order is Hero -> Program -> Values -> Vision ->
  Gallery -> Article -> Footer after semantic/static critical readiness.

## Decision

Use the project package graph as the single Three.js version/runtime authority.
Do not keep a separate CDN Three version for Gallery.

Preparation may import/download/cache later section code aggressively in sequence,
but activation/render loops remain visibility/relevance bounded. A page-level
preparation coordinator is allowed if needed; a giant global RAF scheduler is
not required unless measurement proves it.

## Editable ownership

- `package.json` / lock only if dependency normalization actually requires it;
- Vite entry/dynamic-import graph;
- `resources/js/pages/welcome.js` and bounded boot/preparation modules;
- `resources/js/pages/welcome-depth-gallery.js`;
- `resources/js/surfaces/home/gallery-depth/three-runtime.js`;
- Values spatial bridge/scene ownership only as required to stop disabled or
  duplicate graphics ownership;
- Program GSAP loader only if dependency/runtime measurement justifies changing
  it while preserving its existing fallback;
- focused runtime/import-graph tests.

## Acceptance

- one Three version authority from the package graph;
- no production CDN Three runtime;
- no duplicate Three versions/contexts caused by separate loaders;
- initial semantic/LCP path does not require Gallery/Values WebGL;
- accepted sequential preparation order is implemented without blank sections;
- prepared sections stay prepared for reverse scroll;
- offscreen/hidden prepared sections do not run continuous RAF/WebGL/video;
- Values spatial disabled state does not activate/request a renderer;
- graphics chunks and request timing are measured and reported;
- a remaining large deferred Three chunk may be documented if it is genuinely
  non-critical and measured performance passes, but warning thresholds may not
  be raised merely to hide it;
- context loss/failure returns to static semantic output;
- no visual redesign.

## Stop condition

Do not introduce a framework, worker architecture, renderer pool, or global
scheduler only for theoretical elegance. Add machinery only when measured
ownership/lifecycle needs it.

---

# H5 — Blade presentation-purity migration

## Goal

Remove production view-owned PHP/data shaping while preserving rendered semantic
output and existing route/controller behavior.

## Source of truth

`handoffs/2026-08-23-blade-purity-inventory.md`.

Current inventory:

- 38 Blade files containing `@php` blocks/expressions;
- three raw PHP navbar preparation files under the view tree;
- no raw `<?php` reported inside Blade templates.

## Decision

Migration owner follows responsibility, not a blanket “put everything in the
controller” rule.

Use existing patterns such as view composers/presenters/application-side data
builders for render-data shaping. Blade may retain normal presentation
directives, iteration, conditions and escaped expressions.

Execute H5 in small owner groups:

1. shared navbar/footer/meta/locale preparation;
2. Home section shaping;
3. public page shaping;
4. admin list/form/archive/replacement shaping.

## Editable ownership

Only files listed by the durable D2 inventory plus the directly responsible
controller/composer/presenter/support/test owners.

## Acceptance

Repository-wide production views have:

- zero raw `<?php` preparation;
- zero `@php` blocks/expressions;
- no database/storage/external-service/data-normalization/business decisions in
  Blade;
- unchanged route names, URLs, forms, accessibility semantics, data attributes,
  visible ID/EN/AR content and ordering;
- navbar retains one semantic desktop/mobile data contract;
- admin archived/replacement candidate behavior remains unchanged;
- focused feature tests cover moved contracts;
- source files remain <=200 lines.

## Stop condition

Do not redesign HTML/CSS or move trivial conditional presentation into new
service classes merely to chase abstraction purity.

---

# H6 — Cloudflare R2 media migration

## Goal

Make Cloudflare R2/custom-domain delivery the first-party content-media owner
while preserving existing admin CRUD, external embed compatibility and public
rendering behavior.

## Proven infrastructure

- bucket `almustaqbal`;
- custom domain `https://media.almustaqbal.sch.id`;
- Laravel S3 write/read/delete lifecycle PASS;
- public custom-domain HTTP 200/404 behavior PASS;
- current response observed `cf-cache-status: DYNAMIC`;
- current bucket CORS is absent.

Secrets remain environment-only.

## Data/storage decisions

### Existing database contract

Do not perform an unnecessary schema rewrite from URL columns to object-key-only
columns. Existing `media_url`, `thumbnail_url`, `poster_url`-style contracts may
continue to store URLs.

For R2-managed first-party media:

- store the canonical public custom-domain URL, never the S3 API endpoint;
- derive the owned object key through one centralized resolver that recognizes
  only approved R2/custom-domain URLs;
- external/provider URLs remain distinguishable and are never accidentally
  deleted from R2;
- repo/local legacy URLs remain readable during migration until their owner is
  explicitly converted.

### Object-key contract

Use an owner namespace plus record identity where available and an immutable
unique/versioned filename, for example conceptually:

```text
<owner>/<record-or-scope>/<immutable-id>.<ext>
```

Owner examples include Hero, homepage Gallery, Gallery page media, PPDB showcase,
testimonials, Article thumbnail/content, and migrated static content media.

Do not use the original uploaded filename as the sole key. Replacement publishes
a new key/URL so long-lived immutable cache is safe.

### Mutation lifecycle

For replace/upload:

1. validate file;
2. upload new R2 object with correct content type/cache metadata;
3. verify storage result;
4. persist canonical public URL;
5. after successful persistence, delete the prior object only when it is proven
   owned by the R2 resolver;
6. if DB persistence fails, clean up the newly created object where safe.

Soft delete keeps the object so restore remains valid. Restore must not require
re-upload. A future hard purge may delete owned objects deliberately.

### Cache

Immutable/versioned objects should use long-lived public caching, e.g. a
one-year immutable policy where appropriate. Mutable URLs must not receive an
immutable contract. H6 must verify actual Cloudflare response headers/status
rather than assuming custom-domain routing equals cache optimization.

### CORS

CORS is required where R2 assets are loaded cross-origin into Gallery WebGL
textures. Configure only the exact production application origin plus verified
local development origins needed for testing, with the minimum GET/HEAD behavior.
Do not add credentialed wildcard CORS.

### Images/video

Do not invent a transformation platform. Preserve current validated image
formats and only create variants required by existing consumers. Direct R2 video
remains acceptable while measured playback/range behavior passes. Do not add
Cloudflare Stream without a proven product need.

Third-party social/video embeds may remain explicit provider exceptions because
they are provider interactions, not first-party binary hotlinks. First-party
photos/illustrations/posters/thumbnails/direct media should move to R2; remaining
exceptions must be documented.

## Editable ownership

- filesystem/media configuration and `.env.example` contract;
- centralized media URL/key/storage support;
- known admin media upload/replace/delete owners from D1;
- Article Canvas media owner;
- DB/seed/static first-party media references that must migrate;
- exact CSP/CORS/deploy docs required by the resulting origins;
- focused storage/media tests.

## Acceptance

- new first-party content uploads publish to R2 and render through
  `media.almustaqbal.sch.id`;
- no S3 endpoint or secret leaks into HTML/DB/docs;
- replace/delete/soft-delete/restore semantics are correct;
- legacy/external URLs are not incorrectly deleted;
- Gallery WebGL can consume R2 textures under exact CORS;
- migrated content media no longer depends on Laravel public storage or
  Unsplash hotlinks unless an explicit exception is recorded;
- custom-domain public read and missing-object behavior pass;
- cache headers/status are measured;
- `.env.example` documents variable names/default shape without secrets;
- production deploy remains a separate environment-secret action.

## Stop condition

Do not change auth/business schema, add image/CDN vendors, or introduce Stream/
transcoding unless actual media requirements prove R2 insufficient.

---

# H7 — Release certification and baseline debt closure

## Goal

Close structural/test baseline debt caused or exposed by the hardened owners,
then certify the product against the durable D4/D5 contracts without relabeling
missing evidence as PASS.

## G0 automated gate

On final intended source head run and record:

```bash
git diff --check
git status --short
npm run check:structure
npm run build
php artisan test
```

All must be green for final source acceptance. Structural failures that remain
must be resolved by ownership-preserving splits/references/manifest corrections,
not visual redesign or warning suppression.

Known structural debt closure rules:

- over-200-line files: split by existing responsibility without changing visual
  or runtime semantics;
- checksum drift: reconcile source-equivalence manifest only after proving the
  current ordered imports are intentional;
- unreferenced candidates: prove dead before delete; otherwise restore explicit
  ownership/reference;
- stale Gallery test: update only after proving the current intended source
  contract, not merely to make the test green.

## Runtime certification

Use:

- `handoffs/2026-08-23-functional-interaction-matrix.md`;
- `handoffs/2026-08-23-responsive-locale-degraded-proof-contract.md`;
- canonical DOD/performance/release docs.

Minimum logical matrix:

```text
6 tiers x 3 locales x Chromium/Safari-WebKit family = 36 base cells
```

Representatives:

```text
360 390 640 768 1024 1280 1440 1536 1920
```

Affected boundaries include 639/640, 767/768, 1023/1024, 1279/1280,
1535/1536 and navigation 1180/1181.

Prove applicable keyboard/pointer/touch, normal/reduced motion, delayed/failing
enhancement, WebGL failure/context loss, fast/reverse/repeat, resize/orientation,
hidden/visible, BFCache, short-height and 200% zoom behavior.

Explicitly inspect the two D4 accessibility gaps:

- Gallery lightbox focus containment;
- PPDB ARIA-tab keyboard semantics.

Only implement the smallest accessibility correction if runtime proof fails.

## Browser evidence

- name the exact Chromium-family browser/version/OS;
- automated WebKit is supporting evidence;
- physical Safari/macOS/iPhone/iPad must be labeled separately;
- unavailable physical Safari proof is `BLOCKED_BY_MISSING_EVIDENCE`, not PASS.

## Performance

For declared performance-sensitive profiles run >=3 comparable cold lab samples,
report median + worst, and retain raw metrics/trace references where possible.

Lab target:

```text
Performance 100
Accessibility 100
Best Practices 100
SEO 100
```

Field CWV remains a separate p75 RUM/CrUX claim:

- LCP <=2.5s;
- INP <=200ms;
- CLS <=0.1.

Do not infer field 3/3 from Lighthouse.

## Security/release regression

Preserve and exercise release-critical auth/session/admin boundaries from the
existing release checklist, especially CSRF, role isolation, active-session /
active-account middleware, login/logout and protected media/content mutations.

## Final statuses

Use only:

- `PASS`;
- `FAIL`;
- `BLOCKED_BY_MISSING_EVIDENCE`.

A final deployment may have code-ready PASS while a separate physical-Safari or
field-CWV line remains blocked; report that distinction explicitly rather than
fabricating certification.

---

# Execution order and mutation discipline

```text
H1 runtime acceptance
-> H2 lifecycle
-> proof + docs checkpoint
-> H3 scroll clock
-> proof + docs checkpoint
-> H4 graphics/loading graph
-> proof + docs checkpoint
-> H5 Blade purity in bounded groups
-> proof + docs checkpoint
-> H6 R2 media migration
-> proof + docs checkpoint
-> H7 baseline closure + certification
```

For every capability:

1. resolve current `main` immediately before edit;
2. read `AGENTS.md` + mandatory architecture chain + this packet;
3. reconcile source drift;
4. inspect exact owners before mutation;
5. implement only the active capability;
6. run focused proof plus available DOD gates;
7. update `UI_UX_CURRENT_STATE.md` and relevant handoff;
8. publish only with explicit repository/branch/scope authorization;
9. never force-push;
10. stop on material unexpected source/intent conflict rather than expanding
    scope by assumption.

This packet intentionally does not prescribe every line of implementation. It
freezes the result, ownership boundaries, architecture decisions and proof so the
implementation agent can still choose the smallest correct code change.
