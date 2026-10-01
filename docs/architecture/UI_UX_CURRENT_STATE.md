# UI/UX Engineering — Current State and Progress Ledger

Status: `HOME_RUNTIME_REFACTOR_ACTIVE / BASELINE_VERIFIED`
Updated: 2026-10-02
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Historical runtime-source checkpoint: `1b79a2b3505f956ac9eccc0cce2f0fcf4bacae3b`

Durable references:

- current execution map: `handoffs/2026-10-01-home-runtime-execution-map.md`;
- current accepted runtime blueprint: `blueprints/2026-10-01-home-runtime-preparation.md`.

## Active publication checkpoint

MAP-03 is merged after green CI (#53): main
`c9f93c0d91de862c6351d259996707ad65d00597`. Its 86 browser cases and
18 Node contracts remain durable proof, not production certification.
MAP-04 merged after CI PASS (#55): main
`9ec739fac9be6cf62528178dd97ed13ef72a529b`. Its 86 browser cases and
25 Node contracts remain durable proof.
MAP-05 verified production build `088acb7e`: 33 executed Node contracts,
305 PHP tests / 3,504 assertions, structure 597, build 147, Pint/diff PASS.
86 Chromium/WebKit cases and three cold traces PASS. Card event reads 160 -> 0,
shared frame reads 8; three observed settled idle windows have zero shared work.
Existing motion formulas and local efficient clocks remain. Proof is in the
scheduler key of the durable JSON; timing variance is explicitly retained.
MAP-05 merged after final-head CI PASS (#57): main
`efe558fe1ccac7bf302f169e59526d6c4bd86e41` (fresh fetch + fast-forward).
MAP-06 browser proof found a pre-existing conflicting language-modal opener:
flag partial capture listener stopImmediatePropagation blocks canonical behavior,
so close has no saved focus/overflow. Executed rendered-script diagnostics show
canonical open never ran; only fallback dialog focus ran. Source is unchanged
from baseline; history 87bfb14c restored that fallback with flag appearance.
MAP-06A browser proof also recorded first-frame focus while dialog visibility
was hidden (Chromium, ID reduced motion, 1180px). Computed visibility gates focus; the existing visibility transition supplies
a retry, with cancellation on close/exit. CSS visibility/motion stays unchanged.
Exactly one NEXT: Terminal Codex verifies bounded MAP-06A modal lifecycle repair.
Final MAP-06 resumes only after this prerequisite is verified/merged.
This map changes no production runtime/style/content unless new failure evidence
requires reopening its proven owner; such evidence must be recorded first.

## Current execution authority — 2026-10-01

The owner has authorized homepage runtime refactoring through verified Issue,
branch, PR and merge to `Asyraf2003/schoolai` main. The owner separately approved
baseline structure repair outside homepage (including reported admin/auth owners).
This current scope supersedes historical NEXT/stop instructions below.

Fresh fetched main is `ebdc0db5df9ca18df719c0d80269cfe7a000096a`.
Full SQLite suite PASS: 304 tests / 3,487 assertions. Focused homepage regression
PASS: 45 tests / 1,013 assertions. Build PASS: 133 modules, deferred 549.47 kB
spatial chunk warning retained. Diff PASS. Structure FAIL: 52 findings (19 line
limits, 29 unreferenced CSS files, three checksums and one import-order finding).
Historical G0 PASS is checkpoint evidence and does not certify current source.

Original baseline source/runtime confirmed interaction-driven Opening video hydration,
five-state cursor emotion/shake machinery, and coordinator promises that do not
wait for Vision assets/timeline or Program GSAP readiness. Current rendered
Testimonials/Articles and three Vision video previews supersede old disabled/
static-cover descriptions. The read-only audit froze this baseline; closed maps below supersede it.

Work follows the linked map; publication never substitutes for runtime proof.
MAP-00 read-only audit is CLOSED: three local Chromium 1440×900 runs reproduce
Hero hydration plus three Vision requests after first scroll. Two runs show
coordinator complete before Vision/Program enhancement readiness. Local FCP
median 816ms / worst 2,388ms; no production/decode/GPU/CWV claim.
MAP-00A CLOSED: structure PASS (592 files ≤200 lines), full PHP PASS
304 / 3,487, build PASS (142 modules), Pint/diff PASS. All 29 emitted CSS
entries are byte-identical to the original baseline. Dead CSS removal ledger:
`handoffs/2026-10-01-baseline-owner-audit.md`. Node fullscreen behavior test and
Chromium modal/bootstrap smoke PASS with zero runtime exceptions. The lost
locale-label closure found by smoke was repaired before publication.
MAP-00A published: Issue #46 / PR #47, main `bcc6d3876acb8c2dec904425d85fdcf0c0d776c7`, CI PASS.
MAP-01 CLOSED: executed Node 5 PASS, PHP 304/3,487, structure 591,
build 141, Pint/diff PASS. 72 final Chromium/WebKit locale/tier/motion cases,
real fullscreen/modal/error/native-pointer/zoom/resize/disposal PASS. Native
Chromium BFCache restored one cursor; WebKit history recreated one. Persisted
handler tests cover both. Emotion/shake and asset refs 3/4/5 retired; remote
objects untouched. Evidence: `handoffs/2026-10-01-home-runtime-proof.json`.
MAP-01 merged after CI PASS (#49).
MAP-02 CLOSED: shell-painted readiness independent of media; automatic active
video startup after shell, real frame/error states, reduced/hidden/offscreen and
BFCache safety. Node 10, focused PHP 9/228, full 304/3,488, structure/build/diff
PASS. Final 76 Chromium/WebKit cases PASS; all three traces play before input,
first scroll adds no Hero load. Local FCP 492ms median / 1,416ms worst; Layout
157.75ms / 587.85ms. Proof JSON contains declared profile and raw-trace hashes.
MAP-02 merged after green CI (#51): main `eecaf918ae4eb4422411a84a2dc9b862693bcc22`.
MAP-03 CLOSED: real first-preview/font/stylesheet/timeline preparation precedes
normal unlock. Access/deadline cancels late enhancement into static semantic
fallback; no-JS stays unlocked. Visible mask range owns preview playback.
Node 18, PHP 304/3,491, structure 593, build 143, Pint/diff PASS; final 86
Chromium/WebKit cases PASS. Three traces: Vision ready before unlock, no first-
scroll requests; FCP 612/1,008ms median/worst, Layout 117.004/383.435ms.
ACTIVE: publish verified MAP-03 (#53). NEXT CHANNEL: Terminal Codex.
NEXT: merge/sync then MAP-04 progressive section preparation.

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

Published H5 owner group 4 source/test SHA:
`c598e5835ecb7fd06c8dec6fabf631761ce00a8f`.

Proven for admin list/form/archive/replacement shaping and the final H5 gate:

- the final 17 group 4 Blade owners contain no raw PHP or `@php` blocks /
  expressions; the repository-wide production Blade inventory is now zero;
- PPDB audience groups/form state, Site Statistic create/edit rows, Hero,
  Gallery item/section/media, Article, Testimonial and placeholder presentation
  state now use bounded exact-view composers plus one language-tab presenter;
- controller/model-owned archive eligibility, ordering, maximum counts,
  identity matching and atomic restore/replacement transactions remain
  unchanged;
- the PPDB archive response failure was reproduced on MariaDB as a fixture
  precondition: the test depended on the externally resolved default URL. Its
  fixture now establishes an active deterministic `.example.test` admission
  URL; the ordered Hero-slide then PPDB-archive proof passes, 15 tests / 85
  assertions;
- focused group 4 proof: PASS, 50 tests / 360 assertions; repository-wide
  zero-Blade-PHP search, Pint and diff check: PASS;
- focused H1 regression: PASS, 1 test / 7 assertions; focused H2-H4
  regressions: PASS, 3 tests / 96 assertions; focused shared/public H5
  regression: PASS, 31 tests / 474 assertions;
- production build: PASS, 144 modules, with the existing deferred 534.39 kB
  Three package warning;
- default full suite: 231 tests, 226 passed and 5 frozen baseline assertion
  failures; MariaDB full suite: 231 tests, 218 passed and 13 baseline /
  driver-state-sensitive failures. Focused group 4 and ordered PPDB ownership
  proofs pass, and none of the remaining failures points to new Blade shaping;
- structure check still reports exactly the frozen 11 over-limit files, three
  unreferenced candidates and two checksum drifts; no H5 source is reported.

H5 is PASS and its final repository-wide Blade-purity acceptance is PROVEN.
H1-H4 behavior/loading ownership and H5 group 1-3 semantics remain unchanged.

## H6 R2 media gate

Published H6 source/test SHA:
`1b79a2b3505f956ac9eccc0cce2f0fcf4bacae3b`.

Proven in source and bounded owner/lifecycle tests:

- one exact canonical URL resolver recognizes only owned
  `https://media.almustaqbal.sch.id/...` keys and rejects endpoint/lookalike,
  encoded, query, fragment and traversal forms;
- immutable owner/scope keys, content type and one-year immutable cache metadata
  are applied by one R2 storage owner without unsupported object ACLs;
- Hero, homepage Gallery, Gallery page media, PPDB showcase, testimonials,
  Article thumbnails and Article Canvas content now publish first-party uploads
  to R2;
- replacement uploads and verifies first, persists the canonical URL, then
  deletes only the prior resolver-owned object; persistence failure cleans up
  the new object, while soft delete retains the object for restore;
- `media:migrate-r2` scans active and archived records one owner at a time,
  migrates eligible local public/repository media and embedded Article content,
  is dry-run/idempotent, preserves external/R2 URLs and fails safely when a
  binary is missing;
- CSP includes the exact media origin and the deploy contract includes exact
  GET/HEAD CORS, ranged-media headers, cache purge and per-owner migration proof;
- explicit non-R2 exceptions are third-party social/video embeds, existing
  Unsplash seed/demo/fallback and Article Canvas provider search URLs, plus
  release-bundled brand/chrome/semantic-static assets. They are not R2-owned or
  deleteable. New first-party content uploads are not excepted.

Proof on Linux x86_64, PHP 8.5.9:

- focused H6 gate: PASS, 20 tests / 237 assertions;
- focused H1 regression: PASS, 1 test / 7 assertions; focused H2-H4
  regressions: PASS, 3 tests / 96 assertions; focused H5 presentation
  regressions: PASS, 15 tests / 256 assertions;
- Pint, diff check and production build: PASS; build remains 144 modules with
  the existing deferred 534.39 kB Three package warning;
- default full suite: 247 tests, 242 passed and the same five frozen pre-H6
  assertion failures; none points to an H6 owner;
- structure check still reports exactly the frozen 11 over-limit files, three
  unreferenced candidates and two checksum drifts; no H6 source is reported;
- live localhost response is HTTP 200 and CSP contains the exact media origin in
  `img-src`, `connect-src` and `media-src`;
- a temporary real-bucket object completed upload, exists, custom-domain ranged
  read and delete/post-delete exists false. Public response was HTTP 206 with
  correct content type, immutable cache control, content range and accept-ranges.

Owner-supplied production proof accepted on 2026-08-24:

- the current H6 source is deployed and `media:migrate-r2` exists in production;
- Cloudflare SSL mode is `Full (strict)` and the public origin returns HTTP/2
  200;
- the R2 CORS policy was applied and read back with the exact production origin,
  GET/HEAD, `Range` and the expected exposed headers;
- an exact-origin ranged R2 request returned HTTP 206 with `Content-Range`,
  `Accept-Ranges` and the expected `Access-Control-Allow-Origin`;
- a real immutable WebP returned `cf-cache-status: MISS` on its first request,
  then `HIT` with `age: 2`; both retained
  `Cache-Control: public, max-age=31536000, immutable`;
- production media configuration uses disk `s3`, the canonical
  `https://media.almustaqbal.sch.id` public URL and immutable cache policy; all
  required AWS/R2 environment entries are present;
- owner audits completed with zero failures: homepage Gallery 6 scanned / 0
  eligible, Gallery page 12 / 0, PPDB 6 / 0, testimonials 12 / 0 and Articles
  10 / 0;
- Hero had one eligible object and migrated it successfully. One separate
  legacy `HeroSlide#5` binary was already missing from old production storage;
  the owner explicitly accepts that existing missing-data condition and it must
  not be recreated or deleted as H6 work;
- temporary `h6-proof/cors.txt` and `h6-proof/cache-logo.webp` objects were
  deleted after proof.

Every H6 acceptance item is now satisfied. H6 is PASS and H7 is READY; no H7
implementation has started.

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
- immutable object metadata and ranged HTTP 206 read PASS;
- secrets remain environment-only;
- exact-origin GET/HEAD CORS with ranged-media headers: PASS;
- immutable custom-domain cache MISS -> HIT with `age: 2`: PASS;
- production owner audit/migration: PASS, with the owner-accepted pre-existing
  missing `HeroSlide#5` binary excluded from H6 recovery work.

H6 decisions are frozen in the execution packet: preserve existing URL-shaped DB
contract, store canonical custom-domain URLs for R2-owned media, derive owned
keys centrally, use immutable owner-scoped object keys, retain binaries for soft
delete/restore, use exact-origin CORS where WebGL textures require it, and do not
invent Stream/transformation infrastructure without proof.

## Pre-Codex discovery status

### D1 media/data ownership

`PASS / DURABLE`.

DB/admin URLs, repo static assets, external URLs and Article Canvas local uploads
are distinct owners. New Article Canvas and audited admin first-party uploads now
use R2. The bounded migration command converts eligible legacy local rows and
embedded Article content during deployment; provider URLs remain explicit
non-owned exceptions.

### D2 Blade purity

`PASS / DURABLE`.

- the durable starting inventory contained 38 Blade files with `@php`;
- H5 group 1 reduced the remaining count to 31;
- H5 group 2 reduced the remaining count to 23;
- H5 group 3 reduced the remaining count to 17;
- H5 group 4 reduced the remaining count to zero;
- no non-Blade PHP preparation file remains under `resources/views`;
- H5 ownership is grouped into Home/public/shared chrome/admin shaping;
- the completed migration preserved semantics through bounded composers,
  presenters and existing application owners rather than a monolithic
  controller rewrite.

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

## H7 G0 automated gate

Published H7-G0 source/test checkpoint:
`8f13f716` (`h7: close automated baseline debt`).

Proven on the intended source:

- the five legacy test owners now assert the accepted production contracts;
- both checksum drifts were reconciled after ordered-import ownership was proven;
- three unreferenced candidates were deleted only after supersession/deadness was
  proven;
- all 11 over-limit files were split by existing responsibility with import and
  source-contract coverage preserved;
- `/.wrangler/` is ignored as generated Miniflare local state while its local R2
  data remains preserved in place;
- `git diff --check`, `npm run check:structure`, `npm run build`, and the full
  247-test / 2,539-assertion suite pass.

H7 runtime, browser, responsive/locale, accessibility, security and performance
certification has not started. No runtime certification status is implied by G0.

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
- H5 owner group 4 admin shaping migration: PASS.
- H5 final repository-wide zero-Blade-PHP gate: PASS.
- Cloudflare/R2 basic lifecycle: PASS.
- D1-D6: PASS / DURABLE.
- H6 source/lifecycle implementation: PASS.
- H6 production CORS/cache rollout and per-owner data migration: PASS.
- H6 final acceptance: PASS.
- H7 G0 automated baseline: PASS.
- H7 runtime certification: NOT STARTED.
- final responsive/locale/WebKit/performance/accessibility/security certification:
  `BLOCKED_BY_MISSING_EVIDENCE` until H7.

## Pre-Codex readiness

`CODEX_READY / 100_PERCENT`.

## NEXT VALID STEP

Owner-authorized media architecture E1 protects Article-owned R2 objects across
thumbnail replacement. E2 then replaces mixed HeroSlide runtime/admin ownership
with one `HeroSetting` copy/CTA singleton and nullable `articles.hero_position`.
Opening media is fixed by config; promoted Article slides derive their localized
title, excerpt, thumbnail and URL from a bounded selected-column query. There is
no automatic latest-Article fallback and no Canvas body hydration.

The additive E2 migration preserves the legacy `hero_slides` table and objects,
migrates active Article placements in order, and creates deterministic Opening
copy when no independent legacy row exists. Old Hero upload/delete routes and
their source owners are retired without deleting stored objects.

Focused E2 proof passes with 27 tests / 298 assertions. Repository gates pass:
`git diff --check`, the 598-file structure gate, Vite production build, and the
full 245-test / 2,366-assertion suite. The existing unresolved runtime asset and
deferred graphics chunk build warnings remain visible and were not changed.

E3 is now `PASS` on the owner-authorized Hero fast-path contract. The server marks
the sole Opening as `opening`; its fixed video loops and retains audio, play/pause,
visibility, and Hero-readiness ownership in a bounded controller. It renders no
arrow, dots, counter, or progress controls and does not mount timer, keyboard,
swipe, transition, or deferred-slide hydration machinery.

Only a published promoted Article changes the server contract to `carousel`.
That branch dynamically imports the existing carousel behavior and its extracted
CSS chunk. Browser network proof on the same temporary SQLite runtime showed zero
`carousel-*.js` / `carousel-*.css` requests for Opening-only, then both chunks and
six Article controls after seeded promotions. The sequential homepage preparation
state still reached `footer` after the Hero-ready event.

Focused E3 proof passes with 12 tests / 208 assertions. Repository gates pass:
`git diff --check`, the 603-file structure gate, Vite production build, Pint, and
the full 246-test / 2,404-assertion suite. Existing unresolved asset and deferred
graphics chunk build warnings remain visible and were not changed.

E4 is now `PASS` for homepage query ownership. The homepage controller no longer
builds or exposes data for the disabled Article surface, the non-rendered legacy
statistics surface, unused quick-info data, or unused PPDB presentation data.
Consequently, it no longer runs latest-Article or `site_statistics` queries.

The remaining model queries correspond to rendered owners: one bounded promoted
Article selection for Hero and one Gallery collection query. `testimonial_media`
remains zero-query. `ppdb_settings` is zero-query for an empty/non-PPDB Opening
CTA and is queried only when `/ppdb` status must decide whether that CTA is safe
to render. Query-listener feature proof covers both branches.

E5 is now `PASS` for canonical Gallery ownership. `gallery_items` is the single
mutable Gallery media collection. Landing-page and full-page placement are
explicit booleans; section placement uses the Gallery-specific
`gallery_item_gallery_page_section` pivot, so one upload can appear in multiple
sections without duplicate rows or objects. New uploads use `gallery/media/`.

The additive migration preserves the legacy `gallery_page_media_items` table and
its R2 references for inventory and rollback. Backfill reuses an exact active
canonical type/URL match, imports unmatched objects with a legacy identifier, and
preserves archived/unpublished placement state on the pivot. Runtime and admin no
longer expose the duplicate per-section upload CRUD. Delete/replace checks retain
legacy references, so this packet deletes no legacy R2 object.

Homepage media keeps the existing scroll/window/reveal lifecycle while images now
scale proportionally inside max-width/max-height bounds with `object-fit: contain`.
The full Gallery wall already uses the same no-crop contract. Browser smoke proof
rendered `/galeri` at 390x844 and 1440x1000 from the migrated/seeded SQLite runtime.

Focused E5 proof passes with 10 tests / 63 assertions; the complete pre-final
repository gate passes with 244 tests / 2,366 assertions, the 592-file structure
gate, Vite production build, Pint, and diff check. The existing two unresolved
Gallery ornament URLs and deferred 549.47 kB Three chunk warning remain visible.

E6 is now `PASS`. Testimonial and Statistics are no longer database-managed
homepage domains: their public/admin routes, controllers, mutation concerns,
admin views/composers/menu/dashboard actions and dead-environment seeders are
retired. The `testimonial_media` and `site_statistics` tables/models remain as
an explicit inventory boundary; no migration drops data and no R2 object is
deleted. Static statistic copy remains in the ID/EN/AR locale sources.

The dashboard no longer queries the two retired tables or the retained legacy
`gallery_page_media_items` inventory. Homepage and dashboard query proof both
show zero access to those tables. The disabled Testimonial surface now also has
no Vite entry, dynamic import, admin injection or shared `app.css` import.
Production transformation fell from 133 to 119 modules; `app.css` fell from
59.84 kB / 11.08 kB gzip to 55.68 kB / 10.47 kB gzip, and admin `app.js` fell
from 1.25 kB / 0.72 kB gzip to 0.40 kB / 0.31 kB gzip. The prior 4.12 kB and
8.14 kB Testimonial CSS outputs plus 5.97 kB and 13.02 kB Testimonial JS outputs
are absent from the manifest.

Browser proof at 390x844 loaded the homepage successfully and requested no
Testimonial bundle or API. Focused retirement proof passes with 16 tests / 139
assertions. Repository gates pass with 235 tests / 2,301 assertions, the
552-file structure gate, Vite production build, Pint, and diff check. The two
known unresolved Gallery ornament URLs and deferred 549.47 kB Three warning are
unchanged.

E1-E6 are complete. Per owner instruction, execution stops here. Static-media
migration, direct-to-R2 upload, Testimonial UI, Article homepage UI and image
transformation remain outside this run. Broad runtime certification remains
`BLOCKED_BY_MISSING_EVIDENCE`.

## PageSpeed P0 Inter critical-path batch

The owner opened one bounded PageSpeed batch on 2026-08-29. Source at
`12d7d97edd5f4c2317db40852db54b37e3e6e574` still placed the ID/EN text LCP
behind the Google Fonts stylesheet and Inter WOFF2 request documented in the
production baseline. The public Latin typography adapter now owns one licensed
48,256-byte Inter variable Latin WOFF2 through Vite, while the shared head no
longer emits Google Fonts stylesheet or preconnect requests. AR/Cairo, DOM,
copy, layout, responsive behavior, motion, media and analytics are unchanged.

Focused shared-chrome proof passes with 4 tests / 92 assertions. The full suite
passes with 259 tests / 2,689 assertions, Pint and the Vite production build
pass, and Vite emits one hashed first-party Inter WOFF2. The current structure
gate fails on pre-existing over-limit, unreferenced-module, checksum and import-
order findings outside this patch; none names the changed font/head/test owners.
Chromium/WebKit visual parity and comparable production PageSpeed runs remain
`BLOCKED_BY_MISSING_EVIDENCE` until deployment.

The one valid next step is owner/AI diff review followed by deployment and at
least three comparable cold mobile and desktop PageSpeed runs. Critical CSS
restructuring remains a separate later batch so its effect stays attributable.
