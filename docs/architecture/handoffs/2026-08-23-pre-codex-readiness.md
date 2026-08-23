# Pre-Codex Hardening Readiness — 2026-08-23

Status: `CODEX_READY / 100_PERCENT`
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Runtime-source checkpoint for discovery facts:
`6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`

## Meaning of CODEX_READY

`CODEX_READY / 100_PERCENT` means the facts, owner decisions, ownership maps,
known baseline debt, proof contracts and H2-H7 execution boundaries are durable
in repository docs. It does not mean H1-H7 implementation is complete.

H1 browser slow-forward/reverse acceptance remains an explicit gate. Codex must
not mutate H2 until that gate is PASS. If its execution environment cannot prove
H1 runtime behavior, it must stop with `BLOCKED_BY_MISSING_EVIDENCE` and request
only that proof.

## Owner constraints

- Finish hardening without unnecessary over-engineering.
- Add architecture only when critical to the actual hardening workflow.
- Repository docs are the durable system of record; chat history is not required.
- One active capability at a time.
- Preserve protected/unrelated visual work.
- No force push.
- Never convert unrun runtime evidence into PASS.

## Durable owner direction

### Fidelity

- one semantic SchoolAI product across six responsive tiers and ID/EN/AR;
- phone: polished final static/light, touch-first;
- tablet: semi-interactive/touch-oriented;
- desktop/laptop: richest approved cinematic/pointer/hover experience;
- degraded capability may reduce enhancement but never semantic content or
  primary actions.

### Preparation/runtime

Accepted preparation order after critical semantic/static readiness:

```text
Hero -> Program -> Values -> Vision/Mission -> Gallery -> Article -> Footer
```

Preparation is aggressive, sequential and persistent for reverse scroll.
Invisible continuous RAF/WebGL/video work is not justified by preparation.

Accepted lifecycle vocabulary:

```text
SEMANTIC -> STATIC_READY -> FETCHING -> PREPARED
-> ENHANCEMENT_READY -> ACTIVE -> SUSPENDED -> DISPOSED
```

Failure should resolve to a complete `STATIC_READY` state where possible.

The owner's approximate 0.1s first-display ambition remains a product aspiration
until H7 measurement produces real critical-path evidence.

## H1 gate

Published H1 source/test SHA:
`6e5c3ee690773ab138583e31c5dc695bbfa6bf9a`.

Proven:

- focused H1 contract: PASS, 1 test / 7 assertions;
- H1 diff check: PASS;
- H1 build: PASS with existing graphics warning;
- H1 frame health no longer depends on transient plane/end-CTA visibility.

Still required before H2:

- repeated slow forward Gallery -> Article;
- pause inside ending/handoff;
- reverse Article -> Gallery;
- rapid forward/reverse;
- no static fallback spawn;
- no dark `#071b18` shell;
- no healthy WebGL loss / invalid-frame error.

## Cloudflare/R2 proven baseline

- bucket `almustaqbal`, APAC, Standard;
- custom domain `media.almustaqbal.sch.id` active;
- Laravel S3 write/exists PASS;
- public custom-domain read HTTP 200 PASS;
- delete/post-delete exists false PASS;
- post-delete public HTTP 404 PASS;
- secrets are environment-only;
- observed cache state is currently `DYNAMIC`;
- H6 owns final cache/CORS/object-key/migration policy.

## Completed readiness packs

### D1 — Media + DB/data-owner inventory

Status: `PASS / DURABLE`.

Key facts:

- DB/admin URL fields, repo static assets, external URLs and Article Canvas local
  uploads are distinct owners;
- current sampled DB media is primarily external and no audited row already uses
  R2 custom domain;
- Article content is `content_id`, `content_en`, `content_ar` with no embedded
  media URL found in current local data;
- Article Canvas still writes to Laravel `public` disk;
- H6 must not be a blind global filesystem-disk switch.

### D2 — Blade purity inventory

Status: `PASS / DURABLE`.

Reference:
`handoffs/2026-08-23-blade-purity-inventory.md`.

- 38 Blade files contain `@php` blocks/expressions;
- three raw PHP navbar preparation files exist under `resources/views`;
- migration ownership is grouped by Home/public/shared chrome/admin preparation;
- existing view composers provide a proven shaping pattern.

### D3 — CSS/JS/runtime ownership

Status: `PASS / DURABLE`.

Reference:
`handoffs/2026-08-23-css-js-runtime-inventory.md`.

- Welcome CSS aggregates are order-sensitive and have checksum debt;
- `welcome.js` currently synchronously owns Program/Values/Article helpers;
- Vision is deferred;
- Gallery is proximity-loaded rather than following the future persistent
  sequential preparation contract;
- Program uses external GSAP 3.7.1 with functional failure fallback;
- Three ownership is split: package `^0.185.1`, Gallery CDN `0.183.0`, Values
  package Three/addons;
- Values spatial is runtime-disabled but still emits a large Three-related chunk;
- build warning around 549 kB remains measured debt, not a threshold-edit target.

### D4 — Functional interaction matrix

Status: `PASS / DURABLE / SOURCE-CONTRACT`.

Reference:
`handoffs/2026-08-23-functional-interaction-matrix.md`.

Release-critical behavior is bounded for Hero, navigation, locale, Program,
Values, Vision, homepage Gallery, Gallery lightbox, Article, PPDB, auth/session
and protected admin Gallery/Article/PPDB CRUD.

Known H7 certification gaps:

- Gallery lightbox has no explicit focus trap proven;
- PPDB ARIA tabs have no dedicated ArrowLeft/ArrowRight roving-focus behavior
  proven.

### D5 — Responsive / locale / degraded proof contract

Status: `PASS / DURABLE / PROOF-CONTRACT`.

Reference:
`handoffs/2026-08-23-responsive-locale-degraded-proof-contract.md`.

Frozen proof shape:

- 36 logical cells = six tiers x ID/EN/AR x Chromium/Safari-WebKit family;
- representatives 360, 390, 640, 768, 1024, 1280, 1440, 1536, 1920;
- affected boundaries plus nav 1180/1181;
- normal/reduced motion;
- keyboard/pointer/touch;
- delayed/failing JS/enhancement, WebGL/context failure, hidden tab, BFCache;
- fast/reverse/repeat, resize/orientation, short height, 200% zoom;
- physical Safari and automated WebKit must be labeled separately;
- performance-sensitive lab evidence requires >=3 comparable cold runs and
  median + worst;
- Lighthouse target remains 100/100/100/100;
- field CWV remains a separate p75 RUM/CrUX claim.

### D6 — Frozen baseline + H2-H7 execution packets

Status: `PASS / DURABLE / CODEX-EXECUTABLE`.

Reference:
`handoffs/2026-08-23-h2-h7-execution-packets.md`.

The packet freezes:

- current proven baseline and missing evidence;
- H1 runtime prerequisite;
- H2 Gallery lifecycle/state scope, owners, acceptance and stop condition;
- H3 authoritative scroll-clock scope and proof;
- H4 single Three authority plus sequential preparation/loading graph contract;
- H5 bounded Blade purity migration groups;
- H6 R2 URL/object-key/cache/CORS/upload/delete/restore/migration contract;
- H7 G0 baseline debt closure plus responsive/locale/browser/performance/
  accessibility/security certification;
- exact sequential execution and docs checkpoint discipline.

## H6 architectural decisions frozen for implementation

To avoid unnecessary schema churn:

- existing URL-shaped DB columns remain URL-shaped;
- R2-managed first-party records store canonical
  `https://media.almustaqbal.sch.id/...` URLs, never S3 API endpoints;
- one centralized resolver derives/deletes only proven owned R2 keys;
- object keys use owner namespace + record/scope + immutable unique filename;
- replacement uploads a new immutable key before swapping DB URL;
- soft delete retains binary object so restore works;
- long immutable cache is used only for versioned/immutable object URLs;
- exact-origin GET/HEAD CORS is required for cross-origin Gallery WebGL texture
  consumption; no credentialed wildcard CORS;
- no image transformation platform or Cloudflare Stream is introduced without a
  proven need;
- third-party social/video embeds may remain explicit provider exceptions, while
  first-party content binaries move to R2.

## Frozen baseline debt for execution

Known `check:structure` debt includes:

- >200-line source owners;
- unreferenced candidates:
  - `article-story/footer-release.css`;
  - `vision-story/entry.js`;
  - `vision-story/typography.js`;
- source-equivalence checksum drift for `welcome.css` and `welcome-hero.css`.

Known full Gallery test debt: one stale continuity assertion predating H1.

These are assigned to the relevant H2-H7 owner or H7 G0 closure. Do not mass
refactor them just to make the command green.

## Codex execution order

```text
H1 browser acceptance
-> H2 lifecycle
-> checkpoint/proof
-> H3 scroll clock
-> checkpoint/proof
-> H4 graphics/loading graph
-> checkpoint/proof
-> H5 Blade purity groups
-> checkpoint/proof
-> H6 R2 media migration
-> checkpoint/proof
-> H7 baseline closure + certification
```

Every capability resolves current `main`, reads `AGENTS.md` + mandatory docs,
reconciles source drift, edits only proven owners, runs focused proof plus
available DOD gates, updates durable state, and never force-pushes.

## Progress

`CODEX_READY / 100_PERCENT`.

This is delegation readiness. Implementation status remains:

- H1 source/test patch published; runtime acceptance pending;
- H2-H7 not yet executed.

## NEXT VALID STEP

Launch Codex with a short prompt that points to `AGENTS.md`, the mandatory
architecture chain, this readiness ledger, and
`handoffs/2026-08-23-h2-h7-execution-packets.md`.

Codex's first action is read-only reconciliation + H1 runtime gate. It must not
start H2 mutation until H1 acceptance is proven.
