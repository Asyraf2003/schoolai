# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE
Audited: 2026-08-01
Repository: `Asyraf2003/schoolai`
Audited source `main`: `a580a509fecaaf912d718e56a38695212d41fcc3`

This ledger update is docs-only. It records the audited source state above and
does not imply production source changes.

## Active product goal

Build a distinctive Al Mustaqbal school experience with strong visual polish,
motion, spatial storytelling, and meaningful 3D while retaining:

- semantic school content and usable static/reduced fallbacks;
- fluid behavior across XS, SM, MD, LG, XL, and 2XL from 360px upward;
- ID/EN in LTR and AR in RTL;
- current Chromium and real Safari/WebKit proof;
- accessibility, input, orientation, zoom, and short-height behavior;
- Lighthouse/PageSpeed `100/100/100/100` as a measured target;
- isolated experiments and maintainable Blade/CSS/JS/WebGL ownership.

## B00 baseline result

Status: `COMPLETE_WITH_KNOWN_GAPS`

B00 was read-only. No production source, About, Testimonial, dependency, or
WebGL engine was changed.

### Repository and application facts

- The application is Laravel 13 + Blade + Vite 8.
- The audited local checkout matched remote `main` and started clean.
- Homepage render order is navbar, Hero, Vision/Mission, School Values,
  Featured Programs, Gallery, Articles, then footer.
- About is intentionally disabled in `welcome.blade.php`; its source remains.
- Testimonial source and standalone Vite entries remain, but the homepage does
  not render its markup or request it directly or transitively.
- `resources/css/pages/welcome.css` preserves 47 ordered imports.
- `source-module-equivalence.json` protects mechanical split/order only; it does
  not prove semantic ownership or computed-style winners.
- Homepage uses multiple Vite entries plus deferred mobile navigation.
- `public/css/welcome-gallery-desktop.css` remains outside Vite ownership and
  starts at `1025px`.
- No WebGL engine, renderer, scene, model pipeline, or implemented lab surface
  exists in the audited source.

### Automated proof

- `npm run check:structure`: `FAIL_BASELINE_DEBT`.
- Oversized files:
  - `resources/css/pages/welcome-vision-mission.css`: 271 lines;
  - `resources/js/pages/welcome-vision-mission-mobile.js`: 326 lines;
  - `resources/js/pages/welcome-vision-mission.js`: 610 lines.
- Hero source-equivalence drift is limited to intentional dead-selector removal
  in modules `005` and `009`; the equivalence ledger was not refreshed.
- `npx vite build`: `PASS`, 84 modules, no warning/error, about 976 ms.
- Largest emitted homepage CSS entry was `welcome.css`: 121.14 kB raw,
  22.35 kB gzip.
- Build output caused no tracked worktree change.
- `php artisan test`: 140 passed, one failed, 1330 assertions.
- The only failure is `HomeAboutReelTest`, which still requires the intentionally
  disabled About surface. Treat it as `STALE_TEST_FOR_DISABLED_SURFACE`.

### HTTP, locale, and asset proof

- Homepage returned HTTP 200.
- Main content and all active homepage section contracts were present.
- About and Testimonial markup were absent.
- Runtime locale proof:
  - EN: `lang="en"`, `dir="ltr"`;
  - ID: `lang="id"`, `dir="ltr"`, `og:locale="id_ID"`;
  - AR: `lang="ar"`, `dir="rtl"`, `og:locale="ar_AR"`.
- Locale forms for ID, EN, and AR use POST and include CSRF tokens.
- Testimonial assets were absent from direct HTML references and the homepage
  transitive Vite dependency closure.

### Chromium visual proof

Captured in Brave/Chromium 150 using headless screenshots with GPU disabled.
These are layout smoke tests, not GPU, animation-performance, or Safari proof.

- `390x844`: navbar and heading remain readable, but yellow Hero ornaments
  overlap the description. Status: `FAIL_HERO_ORNAMENT_COLLISION`.
- `1180x900`: hamburger mode, logo, text, and ornaments pass visually.
- `1181x900`: hamburger is absent and desktop navigation is not visible.
  Status: `FAIL_NAVIGATION_ABSENT_AT_BOUNDARY`.
- `1279x900` and `1280x900`: desktop navigation and Hero pass visually.
- Exact effective desktop-navigation switch between 1181 and 1279 was not
  pursued because B00 only needed to establish the contract mismatch.
- Real Safari proof remains deferred to Safari on macOS. Epiphany/WebKitGTK was
  intentionally not used as a Safari substitute.

## Open GAP

### `STRUCTURE-GAP-001`

The official structure gate fails because of three oversized Vision/Mission
files and stale Hero equivalence checksums.

Impact: `npm run build` remains blocked by its structure preflight even though
Vite compilation itself passes.

### `TEST-GAP-001`

One stale About test contradicts the protected disabled production state.

Impact: the full Laravel test command is red despite 140 active tests passing.
Do not reactivate or modify About during unrelated work.

### `RESPONSIVE-GAP-001`

Chromium proves a Hero ornament collision at 390px and missing navigation at the
1181px boundary.

Impact: the certified XS baseline and exact navigation contract are not fully
met.

### `BROWSER-PERF-GAP-001`

Real Safari, reduced-motion visual behavior, keyboard/touch interaction,
orientation, short-height, 200% zoom, BFCache/repeat lifecycle, Lighthouse,
long-task/TBT, transfer, and field CWV evidence are not complete.

### `OWNERSHIP-GAP-001`

Current CSS import order is mechanically known, but computed winners and clean
surface ownership are not yet proven for the first migration pilot.

### `ENGINE-LAB-GAP-001`

WebGL is accepted as a future capability, but no engine ADR or isolated lab
implementation exists. This does not block DOM/CSS ownership migration.

## Accepted decisions

- Preserve one semantic DOM/content source by default.
- Preserve XS 360–639, SM 640–767, MD 768–1023, LG 1024–1279,
  XL 1280–1535, and 2XL 1536+.
- Preserve 360px certified minimum, 390px primary XS, and the intended
  navigation contract of hamburger through 1180 and desktop from 1181.
- Preserve server-rendered locale switching, Inter for ID/EN, Cairo for AR,
  and logical LTR/RTL geometry.
- About and Testimonial are protected and out of scope until explicitly enabled.
- Create target folders only while migrating a proven owner.
- Migrate one atomic surface under an owner-accepted blueprint.
- Production must never import or bundle lab code.
- Do not select/install a WebGL engine before an accepted cinematic scene
  blueprint and measured baseline justify it.
- Do not big-bang rewrite the homepage or append anonymous cascade patches.

## P00 pilot recommendation

| Option | Benefit | Cost/risk |
|---|---|---|
| A — Hero | highest immediate visual value | highest coupling to LCP, media, navigation, and motion |
| B — Vision/Mission | strong editorial value | oversized CSS/JS and complex responsive typography lifecycle |
| C — School Values | active, semantic, no media/WebGL, readable without JS | shared heading and fragmented CSS ownership still require proof |
| Hybrid | migrate School Values ownership, then build one isolated cinematic lab | requires strict separation and a second acceptance gate |

Recommendation: **C — School Values (`#nilai`)** as the first ownership pilot.
It gives the safest bounded test of responsive tiers, ID/EN/AR, RTL, hover,
touch, focus, reduced motion, shared typography, and CSS ownership.

Draft blueprint identifier: `HOME-SCHOOL-VALUES-OWNERSHIP-001`.
Status remains `DRAFT` until the owner accepts the pilot and scope.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G00 governance hardening | `PASS` | decision, session, matrix, DOD, and proof contracts |
| G01 execution foundation | `PASS` | migration, lab, prompt, and handoff contracts |
| B00 current baseline | `COMPLETE_WITH_KNOWN_GAPS` | repository, build, tests, HTTP, locale, asset, and Chromium evidence |
| P00 first pilot selection | `READY_FOR_OWNER_DECISION` | School Values recommended; blueprint still draft |
| S00 first surface migration | `BLOCKED_BY_DECISION` | owner-accepted pilot blueprint absent |
| E00 WebGL engine ADR | `BLOCKED_BY_MISSING_EVIDENCE` | first accepted cinematic scene absent |
| I00 cinematic implementation | `BLOCKED_BY_MISSING_EVIDENCE` | engine/assets/runtime proof absent |
| R00 PageSpeed/CWV acceptance | `BLOCKED_BY_MISSING_EVIDENCE` | lab and field evidence incomplete |

Foundation accounting:

- governance coverage for known owner requirements: `100%`;
- production source ownership migration: `0%`;
- new WebGL cinematic implementation: `0%`.

Documentation progress never implies visual/runtime progress.

## STATUS

- B00 evidence collection: `COMPLETE_WITH_KNOWN_GAPS`.
- Audited source mutation: `NONE`.
- Durable handoff: `PASS` after this docs-only update.
- Production migration: `NOT_STARTED`.

## NEXT VALID STEP

Start P00 in a new session.

Read `docs/architecture/README.md`, then this file. Evaluate only the proposed
School Values pilot and convert `HOME-SCHOOL-VALUES-OWNERSHIP-001` from `DRAFT`
to `OWNER_ACCEPTED` after confirming exact scope, target paths, responsive and
locale contracts, proof matrix, rollback, and exclusions.

Do not edit production source, About, Testimonial, or install a WebGL engine
before that blueprint is accepted.
