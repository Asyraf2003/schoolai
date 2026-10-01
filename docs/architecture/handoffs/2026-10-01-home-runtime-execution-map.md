# Homepage runtime execution map

Date: 2026-10-01. Repository: `Asyraf2003/schoolai`. Target: `main`.
Source: `ebdc0db5df9ca18df719c0d80269cfe7a000096a`.
Channel: Terminal Codex. Work status: `IMPLEMENTING`.
Blueprint: `../blueprints/2026-10-01-home-runtime-preparation.md`.

## FACT → GAP → GOAL → IMPACT → DECISION

The current request authorizes Issue → branch → PR → verified merge to main.
The owner additionally authorized a separate baseline repair map, including
admin/auth owners, on 2026-10-01. Runtime changes remain sequential and bounded.
No dependency, school copy, business data, typography or art direction changes.

Fresh baseline: full SQLite suite PASS (304 tests, 3,487 assertions), focused
homepage suite PASS (45 tests, 1,013 assertions), build PASS (133 modules),
diff PASS. Structure FAIL: 19 line-limit findings, 29 unreferenced CSS files,
three checksum findings and one import-order finding. These supersede historical
G0 PASS for this source SHA. Build is not an implicit structure gate.

The initial local browser target returned a database exception. Runtime proof
uses an isolated migrated SQLite fixture in `/tmp`; it does not mutate local or
production business data. Chromium is 153.0.8010.47 (Linux, headless).

## Inspected runtime owners and classifications

| Owner | Classification | Current behavior / gap |
|---|---|---|
| welcome Blade / home controller | ACTIVE_CRITICAL | SSR Hero, navigation, PPDB and one locale DOM |
| welcome-critical/home-hero/type CSS | USED_AND_EFFECTIVE | Production inline critical entries; shared Latin/Arabic owners |
| welcome.js | ACTIVE_CRITICAL | Imports utilities, cursor and coordinator synchronously |
| Opening controller | ACTIVE_CRITICAL | Video `data-src`, `preload=none`; pointer/scroll/audio hydrates |
| Hero readiness | ACTIVE_CRITICAL | `playing`/error or 5-second timeout; shell/media conflated |
| carousel | DEFERABLE | Dynamic only for promoted Articles; idle active-video startup |
| coordinator | ACTIVE_NONCRITICAL | Hero → Vision → Program → Values → Gallery → Footer |
| Vision | ACTIVE_NONCRITICAL | Mount resolves before proximity-driven assets/timeline prepare |
| Program | ACTIVE_NONCRITICAL | `ready` resolves `armed`; GSAP starts on proximity/click |
| Values | ACTIVE_NONCRITICAL | Deferred mount, RO + IO, responsive direct clock; spatial disabled |
| Gallery | ACTIVE_NONCRITICAL | DOM media/window compositor; no active Three renderer |
| Testimonials | ACTIVE_NONCRITICAL | Rendered wall, dynamic import at 150% proximity |
| Articles | ACTIVE_NONCRITICAL | Rendered static showcase; native lazy media |
| navigation/editorial | ACTIVE_CRITICAL / NONCRITICAL | Separate bounded RAF, nav reads before geometry writes |
| cursor | DEFERABLE | Random cwo/cwe; five preloads; velocity/reversal/emotion timers |
| cursor CSS | USED_AND_EFFECTIVE | Fine-pointer adapter, transform, default/hover/emotion states |
| unused CSS candidates | UNKNOWN | No deletion until full consumer/history/state audit |

No local cursor bitmap exists. Cursor files point to remote R2 UI URLs; removing
references is distinct from deleting remote objects with unproven external use.

## Architectural drift decisions

The August startup handoff describes immediate metadata Hero loading, genuine
Program readiness and a static About cover. Current source/tests intentionally
changed these: Opening now waits for interaction (`fb026aba`); Program resolves
armed; Vision has three passive preview videos; Testimonials/Articles render.
The current task supersedes interaction-triggered Hero startup and armed-only
readiness. Existing Vision media and rendered sections must be preserved.
Historical engine-gap statements are descriptive: Three is already installed,
but current Gallery is DOM based. Do not select/install an engine or resurrect it.

## Ordered maps

Each map runs targeted tests + relevant regression + build + diff/structure.
Full PHP suite is required at merge. Runtime changes additionally require the
declared browser gates; an unavailable gate cannot be marked PASS/CLOSED.
Map status is separate from work `PASS/FAIL/BLOCKED_BY_MISSING_EVIDENCE`.

### MAP-00 — Baseline and inventory
GOAL: lock source, contracts, runtime inventory and comparable browser evidence.
FACTS: automated baseline above; first-scroll Hero hydration is source-proven.
DEPENDENCIES: fetched main and all mandatory docs (present).
FILES: owners listed above, source equivalence manifest, history and tests (read-only).
RISKS: stale historical PASS or browser fixture mistaken for production evidence.
TESTS: full PHP and the 12-file / 45-test focused run; structure/build/diff.
DoD: drift recorded, trace attribution and small ordered scopes documented.
PROOF: automated results above; three 1440×900 Chromium samples on isolated
testing SQLite, cold browser cache, no throttling, software graphics. Hero source
absent before input in all three; first scroll requested Hero plus three Vision
videos. Two samples reached coordinator complete with Vision unenhanced and no
Program GSAP. FCP median 816ms / worst 2,388ms; renderer-main Layout total median
168.15ms / worst 823.76ms. These variable local measurements are not production
PSI or decode/GPU attribution. Raw traces and summary are under `/tmp/schoolai-home-baseline-*`.
STATUS: CLOSED (read-only audit deliverable; structure failure explicitly frozen).

### MAP-00A — Owner-authorized baseline prerequisite
GOAL: restore canonical verification without changing application behavior.
FACTS: 52 structure findings; full PHP and build pass before changes.
DEPENDENCIES: MAP-00 inventory; exact owner approval received.
FILES: only structure-reported owners, bounded extracted siblings, manifest,
tests and governance. No runtime redesign in this map.
RISKS: extraction scope/closure/import mistakes; deleting unproven fallback CSS.
TESTS: owner feature regressions, semantic equivalence, full canonical gates.
DoD: no guard suppression, every source ≤200 lines, consumers retained.
PROOF: Issue #46; full PHP PASS 304 tests / 3,487 assertions; targeted
Article 1/85 and media/Hero/Gallery 7/234; Node fullscreen behavior PASS 1 test
(ID/EN/AR labels, WebKit API fallback, rejected exit). Structure PASS 592 files;
build PASS 142 modules; Pint and diff PASS. All 29 emitted CSS entry SHA256s
are identical to baseline. Chromium 153, 1440×900: zero runtime exceptions,
SSR heading visible, same section order/startup behavior; modal opens/closes,
fullscreen label remains Indonesian. One lost closure parameter was caught by
browser proof, repaired and regression-tested before publication. This map
certifies extraction equivalence, not new performance or full browser parity.
STATUS: CLOSED.

### MAP-01 — Cursor simplification
GOAL: random cwo/cwe + compositor tracking + useful hover/top-layer safety.
FACTS: synchronous five-state preload and gesture analysis; shared public consumer.
DEPENDENCIES: MAP-00A merged.
FILES: cursor.js, cursor CSS, welcome entry, focused cursor lifecycle tests.
RISKS: invisible native pointer, dialog/fullscreen layer and BFCache accumulation.
TESTS: pointer burst coalescing, both characters, coarse pointer, failure,
hover/disabled, hidden/pagehide/BFCache and modal/fullscreen behavior.
DoD: emotion/shake/timers removed, only required assets requested, low priority.
PROOF: pending.
STATUS: PLANNED.

### MAP-02 — Hero shell and media startup
GOAL: usable SSR shell, automatic active-video warm-up before first scroll.
FACTS: Opening interaction hydration conflicts with task; carousel already deferred.
DEPENDENCIES: MAP-01 merged.
FILES: Hero entry/opening/readiness/media, focused Hero tests; Blade only if needed.
RISKS: autoplay rejection, reduced motion, hidden/BFCache and inactive-slide transfer.
TESTS: no-input hydration, shell independent of playing, media error/blocked
autoplay, audio intent, hidden/restore, Opening and promoted carousel regression.
DoD: first-scroll performs no media startup; native range fetch remains bounded
by metadata/active playback; poster remains usable on failure.
PROOF: pending.
STATUS: PLANNED.

### MAP-03 — First journey readiness and access-safe scroll gate
GOAL: prepare Vision truthfully before meaningful scroll; progressive next owners.
FACTS: Vision mount resolves early; no existing experience scroll gate.
DEPENDENCIES: MAP-02 merged.
FILES: coordinator, Vision preparation/controller, bounded gate owner, existing
media-preview owner, optional localized status, focused runtime/feature tests.
RISKS: navigation/modal lock collision, slow/error assets and inaccessible content.
TESTS: unresolved/ready/static/failed/disposed; PPDB/nav/keyboard remain reachable;
hash/restore paths, locale switch, resize, reduced motion and no-JS access.
DoD: readiness reports actual prepared/fallback state; no fake timer progress or
infinite lock; section N+1 preparation begins before viewport entry.
PROOF: pending.
STATUS: PLANNED.

### MAP-04 — Progressive Program, Values, Gallery and wall preparation
GOAL: remove remaining proximity-triggered initialization bursts with bounded work.
FACTS: Program resolves armed; other surfaces mix mounting with proximity media.
DEPENDENCIES: MAP-03 merged.
FILES: section-owned readiness/lifecycle code, coordinator, focused contracts.
RISKS: unnecessary distant downloads, changed choreography or offscreen RAF/video.
TESTS: real GSAP/fallback ready, bounded ahead-window, fast/reverse scroll,
Values deterministic clock, Gallery media, wall, hidden/BFCache/reduced motion.
DoD: one persistent sequential coordinator, no invisible continuous work.
PROOF: pending.
STATUS: PLANNED.

### MAP-05 — Measured scheduler and dead-code audit
GOAL: consolidate only demonstrated redundant work while preserving motion clocks.
FACTS: navigation, Vision, formation, world, Values, Gallery and wall own RAF;
formation currently reads card geometry inside the scroll callback.
DEPENDENCIES: MAP-04 merged and comparable traces.
FILES: only proven duplicate samplers/schedulers; consumer ledger before cleanup.
RISKS: smoothing changes, cross-surface write/read dependency and stale geometry.
TESTS: same offset/same semantic state, burst frame coalescing, reads-before-writes,
resize/orientation, reverse, hidden and disposal; retain efficient local clocks.
DoD: evidence-backed reduction; UNKNOWN CSS/JS retained; no generic architecture.
PROOF: pending.
STATUS: PLANNED.

### MAP-06 — Full proof and documentation closure
GOAL: certify final main and close linked Issues/PRs with durable proof.
FACTS: baseline source tests do not prove runtime or field CWV.
DEPENDENCIES: all prior maps merged and individually CLOSED.
FILES: tests, proof records, handoff/current-state and relevant obsolete descriptions.
RISKS: calling headless/one-locale proof browser parity or field evidence.
TESTS: full canonical gates, six tiers + boundaries/nav 1180/1181, ID/EN/AR,
Chromium/WebKit, input/zoom/reduced/lifecycle/failure; ≥3 comparable trace samples.
DoD: global task checklist satisfied; median/worst by workload and final main SHA.
PROOF: pending.
STATUS: PLANNED.

## ACTIVE STEP / NEXT

MAP-00 and MAP-00A CLOSED. Publish verified MAP-00A before starting MAP-01.
Exactly one execution channel: Terminal Codex.
Rollback point: source baseline SHA above. Do not merge a failing gate.
