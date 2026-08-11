# Release Readiness: Program → Character, Platform Hardening, and Proof Workflow

BLUEPRINT ID: `RELEASE-READINESS-001`
STATUS: `OWNER-ACCEPTED / PLANNING`
OWNER ACCEPTED: 2026-08-11
SOURCE MAIN SHA: `ce1aa637c08cc70f13cbe254ed4da32e8bafd254`
TARGET: `Asyraf2003/schoolai` `main`
CHECKLIST: `../UI_UX_RELEASE_READINESS_CHECKLIST.md`

## Goal

Prepare the current SchoolAI application for launch through a sequence of bounded
proof gates rather than one repository-wide redesign.

The active product goals are:

1. Program uses a white visual field.
2. The handoff into Character/Values transitions intentionally into the approved
   blue Character/Values field.
3. The application passes phone, tablet, desktop, Safari/WebKit, Chromium,
   ID/EN LTR, and AR RTL runtime proof.
4. Blade becomes presentation-only: no raw PHP blocks, no `@php` blocks, and no
   business/data-access logic inside `.blade.php` files.
5. All website content media moves to the approved Cloudflare media origin/CDN
   instead of Laravel `public/` or uncontrolled third-party hotlinks.
6. Login, authorization, session behavior, and sensitive-data handling pass a
   dedicated security gate before production is considered ready.

Gallery and Article visual/refactor work are deferred until separately opened by
the owner.

## Current FACT

- Remote `main` was resolved at `ce1aa637c08cc70f13cbe254ed4da32e8bafd254`
  before this blueprint was created.
- That source head contains the bounded automated-baseline repair for Program,
  Values, and production-only GA4/CSP ownership.
- The owner’s earlier local terminal was one commit behind, with a clean working
  tree, then reported the requested fast-forward completed.
- Fresh post-fast-forward automated proof has not yet been recorded in this
  blueprint, therefore baseline status remains `UNPROVEN`, not PASS.
- The previous `UI_UX_CURRENT_STATE.md` still described an older Values spatial
  line batch and older source head; this blueprint supersedes that active-scope
  description.

## Scope boundaries

### Active

- automated baseline proof;
- Program → Character/Values white-to-blue visual ownership;
- responsive/browser/locale/direction runtime proof;
- Blade presentation-boundary cleanup and enforcement;
- Cloudflare media migration and CSP/media-origin alignment;
- login/data security verification and bounded fixes required by proven gaps.

### Deferred

- Gallery visual redesign/refactor;
- Article visual redesign/refactor;
- unrelated Hero, Vision/Mission, About, Testimonial, or navigation redesign;
- Values worm/line optimization unless a proven regression blocks an active gate;
- broad package/framework rewrites not required by a proven gate.

Cross-cutting security or media inventory may inspect deferred surfaces without
opening their visual scope.

## Decision 1 — Program → Character visual ownership

Program’s accepted target resting field is white. Character/Values owns the
accepted blue field, currently `#2038ff` unless the owner explicitly changes the
brand token later.

The transition must have one visual owner at every scroll state. The implementation
must not solve the color change by adding a second uncontrolled overlay that can
steal pointer events, flash during reverse scroll, or remain stale during rapid
scroll.

Program controls and existing approved Program card/detail behavior are protected.
Character heading/cards/worm behavior is also protected unless runtime evidence
proves the transition cannot be correct without a bounded change.

## Decision 2 — Runtime proof is a matrix, not one screenshot

A UI surface cannot be labeled PASS from one desktop Chromium capture.

Required dimensions are:

- six canonical responsive tiers;
- phone, tablet, and desktop representatives;
- ID and EN in LTR;
- AR in RTL;
- forward, reverse, and rapid scroll where the surface is scroll-driven;
- physical click/tap for Program open/detail/back controls;
- Chromium;
- Safari on macOS/iPhone/iPad when an Apple runtime is available;
- WebKit as additional evidence, not as a false claim of physical Safari proof.

Failure at one required matrix cell keeps the affected gate open.

## Decision 3 — Blade is presentation-only

Literal “Blade contains no PHP” is normalized into a technically enforceable
contract:

- no raw `<?php ... ?>` blocks in production Blade;
- no `@php ... @endphp` blocks in production Blade;
- no database queries, storage/network calls, env/secret reads, business rules,
  normalization, or aggregation inside Blade;
- controllers/actions/services/view models/component classes prepare data before
  rendering;
- Blade may still use normal escaped output, translation, loops, conditional
  presentation, components, and slots because those are presentation concerns.

This boundary should be guarded by automated structure checks after cleanup.

## Decision 4 — Cloudflare owns content media

All content media must be inventoried and migrated to the approved Cloudflare
storage/origin/CDN. This includes logos, photos, illustrations, thumbnails,
Program/Values media, Gallery media, Article media, audio, and video.

The migration does not move Vite-built CSS/JS merely because they are stored
under web-visible paths. Those are application assets, not content media.

The final system must not silently mix media ownership among Laravel `public/`,
Unsplash/hotlinks, and Cloudflare. Explicit owner-approved exceptions must be
recorded if any remain.

CSP must authorize only the exact Cloudflare media origins actually used.
Upload/admin flows must be audited so newly created content does not regress back
to local-public storage after the migration.

## Decision 5 — Login and data security are release gates

The existing detailed authentication contract remains owned by
`2026-08-02-auth-account-access.md`. This blueprint adds a release gate around it.

Security proof must cover at minimum:

- admin/guru/murid/guest/wrong-role/inactive route isolation;
- Google pre-provisioned account binding rules;
- student login neutral failure behavior and throttling;
- session regeneration and single-session/session-version invalidation;
- logout and stale-session behavior;
- CSRF and server-side authorization on sensitive mutations;
- password hashing and absence of plaintext credentials;
- secret/token/session leakage into HTML, JS, logs, audit events, or repository;
- IDOR/BOLA negative paths;
- mass-assignment boundaries;
- XSS-sensitive rendering and untrusted content handling;
- safe parameterized data access;
- production security headers/CSP;
- backup/restore and destructive migration expectations for production data.

Automated security tests are necessary but not sufficient. Manual negative-path
runtime proof is required for the highest-risk flows.

## Workflow

The work must proceed in bounded phases. A later phase does not become active
merely because its code looks easy.

### Phase A — Restore and prove baseline

Goal: establish a trustworthy source head before further UI work.

Required proof:

```bash
git diff --check
npm run check:structure
npm run build
php artisan test
```

Exit criterion: all mandatory commands green, or a new bounded incident is opened
for each proven baseline defect. No visual PASS is implied by this phase.

### Phase B — Program → Character visual transition

Goal: implement white Program field → blue Character field without changing
protected card/detail/worm behavior.

Method:

1. inspect actual color/background/stack owners;
2. choose one bounded owner for the transition;
3. implement smallest source change;
4. run focused automated tests/build;
5. run forward/reverse/rapid runtime proof;
6. physically click/tap Program controls around the handoff.

Exit criterion: visual contract passes representative desktop evidence without
regression, then proceed to the full matrix rather than declaring completion.

### Phase C — Responsive, browser, locale, and direction matrix

Goal: prove the active homepage flow across six tiers and ID/EN/AR directions.

Use `UI_UX_RESPONSIVE_LOCALE_MATRIX.md` as the canonical tier/boundary contract.
Record failures by exact width, locale, direction, engine, direction of scroll,
and control/state involved.

Exit criterion: all required cells for the active surface pass. Safari evidence
must be named honestly as Safari; WebKit-only proof is recorded separately.

### Phase D — Blade presentation-boundary cleanup

Goal: remove raw PHP/business/data logic from Blade without changing visual or
semantic output unnecessarily.

Method:

1. inventory all `.blade.php` files for raw PHP, `@php`, queries, env access,
   service/storage calls, and non-presentation transformations;
2. classify each finding as presentation-safe or misplaced logic;
3. move misplaced logic to the smallest appropriate owner;
4. preserve one semantic DOM and localization behavior;
5. add/extend structure tests preventing regression;
6. run full automated proof.

Exit criterion: inventory is zero for forbidden Blade patterns and runtime output
remains equivalent except for separately approved changes.

### Phase E — Cloudflare media migration

Goal: make Cloudflare the canonical content-media origin.

Method:

1. inventory every media reference and its current owner;
2. map each object to its Cloudflare destination/key;
3. upload/migrate objects before changing references;
4. update application/config/database references in bounded groups;
5. update CSP only for exact required Cloudflare origins;
6. prove cache/failure/layout behavior;
7. audit upload/admin paths for future media ownership;
8. remove obsolete public media only after reference scans and runtime proof are
   clean.

Exit criterion: repository/runtime scans show no unapproved content media served
from Laravel `public/` or third-party hotlinks.

### Phase F — Login and data security gate

Goal: prove the authentication and sensitive-data contract rather than assuming
existing tests cover every abuse path.

Method:

1. reconcile source against `AUTH-ACCOUNT-001`;
2. run focused automated auth/security suites;
3. run role-isolation and direct-route negative tests;
4. run login throttling/session invalidation tests;
5. run IDOR/sensitive mutation negative paths;
6. inspect response DOM/JSON/log/audit behavior for secret or sensitive leakage;
7. inspect production CSP/security headers;
8. fix only proven gaps, then rerun full suite.

Exit criterion: automated and manual security checklist entries are green with
source/deploy evidence.

### Phase G — Final release regression

Goal: prove that combining the accepted phases did not create a cross-surface
regression.

Required minimum:

- clean automated baseline;
- Chromium smoke;
- Safari smoke;
- ID/EN/AR smoke;
- phone/tablet/desktop smoke;
- Program → Character forward/reverse/open/back smoke;
- login/data negative-path smoke;
- Cloudflare media smoke and failed-media behavior;
- deployed CSP/security-header inspection.

Only after this phase may the release-readiness checklist be considered complete.

## Proof discipline

Use the project workflow:

`FACT → GAP → GOAL → IMPACT → DECISION → BLUEPRINT → ACTIVE STEP → EXECUTION → PROOF → PROGRESS → STATUS → NEXT VALID STEP`

Rules:

- one active bounded step at a time;
- source/runtime evidence outranks stale docs and brittle tests;
- do not change production UI merely to make a stale test green;
- do not weaken CSP/security assertions merely to hide a real integration gap;
- do not label owner visual approval as browser matrix proof;
- do not label WebKit as Safari unless Safari was actually tested;
- do not mark a checklist item `[x]` without evidence tied to a source/deploy SHA;
- after a gate is green, stop and record status before opening the next phase.

## Completion definition

`RELEASE-READINESS-001` is complete only when every non-deferred entry in
`UI_UX_RELEASE_READINESS_CHECKLIST.md` is green with evidence. Gallery and Article
visual work remain separate future blueprints and do not block this blueprint
unless a cross-cutting security or media defect requires repository-wide action.
