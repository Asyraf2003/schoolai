# Homepage runtime execution map

Date: 2026-10-01. Repository: `Asyraf2003/schoolai`. Target: `main`.
Source: `ebdc0db5df9ca18df719c0d80269cfe7a000096a`.
Channel: Terminal Codex. Work status: `PASS` for closed prerequisites.
Active map readiness is tracked separately below.
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
PROOF: Issue #46 / PR #47, merged main `bcc6d387`; full PHP PASS 304 tests / 3,487 assertions; targeted
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
FILES: cursor owners/CSS, welcome entry, focused runtime tests, existing CI gates.
RISKS: invisible native pointer, dialog/fullscreen layer and BFCache accumulation.
TESTS: pointer burst coalescing, both characters, coarse pointer, failure,
hover/disabled, hidden/pagehide/BFCache and modal/fullscreen behavior.
DoD: emotion/shake/timers removed, only required assets requested, low priority.
PROOF: Issue #48 / PR #49, source `46b9ccb7`, merged after CI PASS. Executed Node contracts 5 PASS;
focused PHP 15/256 and full 304/3,487 PASS; structure 591 sources, build 141,
Pint/diff PASS. Final build: 72 Chromium 153.0.8010.12 / Linux WebKit 26.6
cases (six tiers × ID/EN/AR × normal/reduced), zero exceptions. Coarse requests
zero cursor assets; fine loads default + hover only. Real native fullscreen,
modal, asset-failure/native-pointer, 200% zoom, resize and disposal PASS both.
Native Chromium BFCache restores exactly one cursor; WebKit native back reloads
one working cursor. Persisted events/hidden are additionally executed in both
browser/unit contracts. Proof JSON: `2026-10-01-home-runtime-proof.json`.
Remote emotion objects retained as external usage UNKNOWN; all repository
emotion/shake producers, CSS and asset references removed. CI gates expanded
to execute source structure and frontend runtime tests. No FPS/CWV claim.
STATUS: CLOSED.

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
PROOF: Issue #50 / PR #51; source `68f6c19e`. Executed Node 10 PASS;
focused PHP 9/228, full 304/3,488, structure 591, build 141, Pint/diff PASS.
76 final Chromium 153 / Linux WebKit 26.6 cases cover six tiers × three
locales × normal/reduced, source failure and real controller carousel fixture.
No-input warm-up, first-frame playback, zero additional Hero load calls on
scroll, active-only source and usable failure poster PASS, zero exceptions.
Three isolated Chromium runs: media playing before input in all three; first
scroll starts no Hero hydration. FCP median 492ms / worst 1,416ms; renderer-main
Layout median 157.75ms / worst 587.85ms, JS FunctionCall 89.29ms / 159.10ms.
These variable local samples do not attribute media decode/GPU or prove CWV.
Native R2 range probe returns HTTP 206, 1,024 bytes of a 94,907,995-byte asset;
ongoing playback may buffer further. Durable results/hashes: runtime proof JSON.
Linux WebKit codec environment was repaired only in /tmp before final playback
proof; initial unsupported-codec and request-count-only runs were invalidated.
STATUS: CLOSED (merged #51 after CI; main `eecaf918`).

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
PROOF: Issue #52 / PR #53; source `f30301cf`. Executed Node 18 PASS,
focused PHP 8/273, full 304/3,491, structure 593, build 143, Pint/diff PASS.
86 Chromium/WebKit cases PASS: six tiers × three locales × normal/reduced,
pending wheel, Escape/deadline cancellation, PPDB, hash, no-JS, source failure,
visible media range/reverse and persisted restore. Real font API scopes glyphs
rather than waiting on a global FontFaceSet.ready WebKit hang. Three isolated
Chromium traces show actual Vision prepared/frame-ready before unlock and zero
first-scroll network requests. FCP median/worst 612/1,008ms; renderer-main
Layout 117.004/383.435ms; FunctionCall 83.255/151.836ms. Local variable evidence,
not field CWV or attributed decode/GPU. Raw-trace hashes/results in proof JSON.
STATUS: CLOSED (merged #53 after CI PASS; main `c9f93c0d`).

### MAP-04 — Progressive Program, Values, Gallery and wall preparation
GOAL: remove remaining proximity-triggered initialization bursts with bounded work.
FACTS: Program resolves armed; other surfaces mix mounting with proximity media.
DEPENDENCIES: MAP-03 merged.
FILES: section-owned readiness/lifecycle code, coordinator, focused contracts.
RISKS: unnecessary distant downloads, changed choreography or offscreen RAF/video.
TESTS: real GSAP/fallback ready, bounded ahead-window, fast/reverse scroll,
Values deterministic clock, Gallery media, wall, hidden/BFCache/reduced motion.
DoD: one persistent sequential coordinator; inactive media suspended. Redundant
handoff frame polling is separately measured and owned by MAP-05.
PROOF: Issue #54 / PR #55, source `4022eba0`. Node 25 PASS; focused PHP
16/508, full 305/3,500, structure 595, build 145, Pint/diff PASS.
86 browser cases PASS: 72 tier/locale/motion cells, GSAP error/deadline/abort,
Gallery actual-media preparation/playback, lifecycle, reduced interruption and
real pointer controls in Chromium/WebKit. Matrix source `6c52cc0c`; the subsequent
Program class reset is covered by final-build pointer cases and executed tests.
Three isolated cold Chromium traces show all required surfaces prepared before
input and zero first-scroll requests/errors. FCP median/worst 752/816ms;
renderer-main Layout 147.143/428.690ms, FunctionCall 96.929/186.237ms.
Local variable samples, not field/decode/GPU evidence. Durable proof JSON.
STATUS: CLOSED (merged #55 after CI PASS; main `9ec739fa`).

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
PROOF: Issue #56 / PR #57; runtime build `088acb7e`. Node 33 PASS;
focused PHP 7/257, full 305/3,504, structure 597, build 147, Pint/diff PASS.
86 Chromium/WebKit cases PASS (72 tier/locale/motion + 12 failure/lifecycle +
2 real-pointer interruption cases). Twenty synchronous scroll events: 160 ->
0 immediate card reads; 8 reads in shared frame; one queued shared RAF; Gallery
own read-after-write count 18 -> 0. Initial idle window retained two bounded
settling frames; all three subsequent 600ms windows had zero shared frames/card
reads. Old Gallery idle polling: 37/600ms -> 0. Counts are not measured CPU/FPS.
Three isolated cold traces: every required readiness outcome before input,
zero first-scroll requests/errors. FCP median/worst 604/636ms; renderer-main
Layout 150.433/291.449ms, FunctionCall 136.948/155.449ms. Variance and changed
pre-input media playback prevent claiming all timing differences as savings.
Durable scheduler proof JSON includes raw trace hashes and every idle window.
No CSS/assets deleted; UNKNOWN owners retained. World endpoints/reverse/dispose
are covered by an additional executed contract; production build is unchanged.
STATUS: CLOSED (merged #57 after final-head CI PASS; main `efe558fe`).

### MAP-06 — Full proof and documentation closure
GOAL: certify final main and close linked Issues/PRs with durable proof.
FACTS: baseline source tests do not prove runtime or field CWV.
DEPENDENCIES: all prior maps merged and individually CLOSED.
FILES: tests, proof records, handoff/current-state and relevant obsolete descriptions.
RISKS: calling headless/one-locale proof browser parity or field evidence.
TESTS: full canonical gates, six tiers + boundaries/nav 1180/1181, ID/EN/AR,
Chromium/WebKit, input/zoom/reduced/lifecycle/failure; ≥3 comparable trace samples.
DoD: global task checklist satisfied; median/worst by workload and final main SHA.
PROOF: source `efe558fe`; 108 Chromium boundary/locale/motion/zoom/orientation
cases plus eight early-access/fallback/history cases PASS. Keyboard focus FAIL:
flag capture opener blocks canonical opener, leaving saved focus null.
STATUS: BLOCKED (owned runtime failure; MAP-06A repairs it before final resume).

## ACTIVE STEP / NEXT

MAP-00/MAP-00A CLOSED and merged (#47). MAP-01 merged (#49). MAP-02 merged (#51). MAP-03 merged (#53). MAP-04 merged (#55); MAP-05 merged (#57); MAP-06 awaits the sole active MAP-06A prerequisite.
Exactly one execution channel: Terminal Codex.
Rollback point: source baseline SHA above. Do not merge a failing gate.

## MAP-01 FACT / GAP / DECISION / removal ledger

Issue #48. Active producer: cursor.js → cursor-gesture.js only. No dynamic import,
other caller, injected state or test consumer requires gesture behavior. Owner
explicitly retires it. Delete that module after removing its import/calls.
`048-custom-cursor.css` emotion variables (cwo/cwe 3/4/5), emotion-state
selectors and their centered hotspot rule have only that producer; all are
DEAD_CONFIRMED under the accepted contract. Fine-pointer media query contains
them; no reduced/mobile/modal/fullscreen fallback consumes these states. Keep
all default/interactive/native-disabled/fine-pointer/transform rules. Remote
objects 3/4/5 are UNKNOWN for external consumers: remove repository references,
retain bucket objects. `cursor-layer.js` duplicate character constant is unused
in all scopes and DEAD_CONFIRMED. Replace five-state preload helper with default
load plus hover intent; preserve all interactive selectors/top-layer policy.

## MAP-03 bounded packet — FACT / GAP / DECISION

FACT: Vision is immediately after Hero. The schoolImages collection still contains retired paper URLs
protected by an explicit no-paper test; three preview videos currently lack posters and share 180px
playback proximity. Controller prepares only when near/wide and returns early;
fonts/decode races resolve fake success after 700/900ms. Current CSS owns all
media crop/fade and timeline geometry; no losing selector is removed.
GAP: first-scroll changes layout and starts three media workloads after the
coordinator has already reported prepared. No-JS/reduced video boxes are black. Retired paper assets are not valid fallback
owners; keep their no-render contract and retain unknown legacy config.
DECISION: await actual first poster/frame, applicable stylesheet/fonts and
existing timeline before normal unlock. Deadline/error/access intent cancels
late enhancement and selects the existing static semantic composition. Reuse the active Hero school-photo asset (already loaded) beneath the existing video fade; preserve DOM story order,
layout, controls, crop and animation durations. Gate CSS owns only root overflow
and reserved scrollbar geometry; navigation continues to own body overflow.
Immediate hash/restored position, links, focus outside Hero and Escape select
static fallback before access. No-JS has no gate attribute. Remaining previews
prepare sequentially ahead; playback is actual visible geometry only.
Editable: coordinator/gate, Vision wrapper/controller/preparation, previews and
bounded readiness helper, Vision Blade/poster CSS, critical gate CSS, tests/docs.
Read-only: business/locale data, typography, Hero, Program/Values/Gallery motion.
Proof: pending gate cannot scroll, ready and static fallback can; primary actions
work immediately, aborted work cannot later mutate timeline; actual browser
matrix and executed contracts plus existing canonical gates.

MAP-03 playback audit: at 1440×900, first transition had two prepared previews
playing plus a third preparing beneath fully covered masks. Proximity alone
cannot express stacked cinematic visibility. Keep existing timeline curves;
emit only changes to the integer floor/ceil visible media range, and pause
prepared previews outside that range. Warm-up remains a bounded first-frame
operation. Without IO keep semantic posters rather than continuous blind play.

MAP-03 WebKit readiness proof: stylesheet and first frame were ready; font faces
reported loaded, but global FontFaceSet.ready stayed pending until the eight-
second static deadline. Await only the computed heading/body font descriptors
and actual locale glyphs used by Vision (check/load); preserve Inter/Cairo,
weights, font-display and all typography owners. Executed regression keeps the
global promise pending while proving local glyph readiness settles. No UA fork.

## MAP-04 bounded packet — FACT / GAP / DECISION

Main: `c9f93c0d91de862c6351d259996707ad65d00597`. Channel: Terminal Codex.
FACT: Program returns armed before GSAP mount; its 100% observer initiates script
work. Values already prepares geometry in its initial RAF but discards cleanup.
Gallery loads and plays videos together at 180px, including reduced motion;
image fit registers duplicate once-listeners on resize before media loads.
Wall already imports 150% ahead and samples only on scroll, so keep that path.
GAP: coordinator cannot distinguish initialized/fallback outcomes; invisible
media plays, permanent exit retains Values/wall ownership.
DECISION: Program begins background GSAP loading after Vision unlock, awaits
installed controls or real static fallback, with a bounded cancellation deadline.
Values awaits its first computed/painted frame or existing static mode. Gallery
media prepares one item at a time in a 100% ahead window, pauses after its first
frame and plays only in the actual viewport; reduced motion keeps media static.
Keep all easing, geometry, fitting, DOM, locale and CSS. Missing capabilities
select usable semantic/static behavior. Dispose permanent exit, suspend BFCache.
Editable: Program readiness/lifecycle, Values controller/lifecycle/preparation,
Gallery media/fit/lifecycle, wall lifecycle, coordinator, tests and proof/docs.
Read-only: all CSS/Blade/type/content/media URLs; forbidden: dependencies,
renderer activation, business data, unrelated cleanup and motion redesign.
Tests: executed deferred GSAP/error/deadline/disposal, sequential ahead-media,
reduced/hidden/restore, actual first paint; existing motion/DOM PHP regressions,
full canonical gates, six-tier ID/EN/AR Chromium/WebKit browser proof.
DoD: coordinator awaits actual outcomes, scrolling never initiates Program
mount, no invisible continuous playback and no late mount after cancellation.

MAP-04 media owner audit: Gallery SSR already receives thumbnail_url for direct
video items but does not attach it as poster. Add only that existing optional
poster attribute (no new asset/copy/data). This narrowly extends editable Blade
for reduced/error fallback. No CSS rule is deleted or changed.

MAP-04 final runtime proof: computed opacity must be sampled after media-query
rendering settles, not immediately after emulation acknowledges the query.
WebKit transient sampling was invalidated; final real-pointer cases assert
settled opacity 1, pointer auto, no stale dialog classes, Escape and re-enhancement.

MAP-05 read-only baseline: twenty synchronous scroll events caused 160 card
geometry reads before any RAF. Formation/world/Gallery each scheduled a frame.
Gallery made 18 geometry reads after its own writes during the burst interval.
At a settled Values/Gallery handoff (exit 1, Gallery top ~1000px), Gallery
scheduled 37 further RAFs in 600ms with no user input. These are work counts;
no decoder/GPU attribution or measured frame-drop claim. Current source/history
shows polling follows Values handoff; retain its clock, notify consumers when
paint changes, and consolidate proven samplers with explicit read/write phases.

## MAP-05 bounded packet — FACT / GAP / DECISION

Main `9ec739fac9be6cf62528178dd97ed13ef72a529b`; channel Terminal Codex.
FACT: source/history preserve Formation's 105ms settle and Values spring/heading
clock. Actual browser counters reproduce 160 immediate card reads for 20 scroll
events, separate world/Formation/Gallery RAF scheduling, Gallery geometry after
its own style writes, and 37 redundant handoff RAFs over 600ms settled idle.
Navigation already coalesces and reads before navbar geometry changes; Vision
has cached geometry and bounded 88ms settle; Values is a bounded deterministic
clock; wall uses cached travel with a proximity guard. Keep these local clocks.
GOAL: one bounded sampler for the proven three owners, preserve their output.
IMPACT: event callbacks mark dirty; one frame reads all selected geometry before
any selected owner writes. Formation continues its same exponential settle
while reusing targets until scroll/resize/owned geometry invalidates them.
DECISION: simple shared homepage frame queue and viewport snapshot, no enterprise
ports/adapters. World becomes a read/write subscriber; Gallery reads all media
geometry before painting. Values publishes its existing rounded handoff value
and a paint notification; Gallery requests a frame from that notification rather
than polling the same CSS state forever. CSS properties remain for other owners.
Editable: bounded queue, Formation/world/Gallery samplers, Gallery frame helper,
Values handoff paint notification, targeted runtime/source tests and proof/docs.
Read-only/forbidden: all CSS/Blade, copy/type/business/media/dependencies, motion
easing/distances/timings, Hero/cursor and retained navigation/Vision/wall clocks.
Six tiers and ID/EN/AR retain existing formulas; shared vertical time is not RTL
mirrored. Reduced mode stays static, hidden/BFCache cancels/resumes, permanent exit
disposes clients/listeners. No unsupported IO/RO branch becomes content-blocking.
TESTS: executed burst coalescing, all reads before writes, reentrant scheduling,
subscriber disposal, hidden/restore; Formation targets/output and same 105ms
clock; existing Gallery/Values/Program regressions; canonical gates and functional
Chromium/WebKit matrix plus exact before/after count audit and cold traces.
DoD: immediate geometry reads 160 -> 0, one shared queued frame, Gallery idle
polling -> 0, same final semantic/visual states and no motion timing redesign.

MAP-05 cleanup ledger: old per-owner RAF/event producers for the three migrated
owners are ACTIVE_REDUNDANT, replaced only after the shared path is proven.
Values CSS handoff properties remain USED_AND_EFFECTIVE (including legacy
Gallery-heading consumers). Legacy Gallery-heading modules are LEGACY_REQUIRED
or UNKNOWN outside this runtime graph; keep source/tests. Gallery/Article/wall
media and deferred disabled spatial graph remain ACTIVE_NONCRITICAL/LEGACY_REQUIRED.
No CSS/assets are cleanup candidates in this map. No UNKNOWN module is removed.

MAP-05 proof interpretation: a burst can reuse a frame already queued by real
preparation/resize work; the browser counter counts new schedules, while the
executed queue contract proves one outstanding frame. Initial settling work is
recorded separately from three observed zero-work idle windows. No timer or
animation personality was changed to produce those counts.

## MAP-06 bounded packet — FACT / GAP / DECISION

Fresh main `efe558fe1ccac7bf302f169e59526d6c4bd86e41`; Terminal Codex only.
FACT: each prior runtime map has real imported-module contracts, green canonical
verification, Chromium/WebKit tier/locale/motion proof and isolated cold traces.
GAP: final combined source still needs expanded boundaries/zoom/orientation,
early primary-action intent and combined lifecycle/failure/accessibility proof.
GOAL: verify the complete runtime, document measured results and close publication.
IMPACT/DECISION: no new visual/runtime architecture; reuse canonical gates and
add actual browser cases for final source. Keep trace CPU/network/render categories
separate, record unavailable physical/field evidence rather than inventing PASS.
Editable: proof JSON, execution map, accepted blueprint/current-state and final
handoff report. Read-only: all production owners/assets/content/CSS/dependencies.
Tests: all 33 executed Node contracts; full PHP, structure, build and diff;
Chromium/WebKit six-tier + 360/1440/1920/boundary/nav, ID/EN/AR, motion, touch/
keyboard/pointer/200% zoom/short height, early PPDB/navigation, failures, hidden/
BFCache/permanent cleanup; three cold traces and declared throttled lab evidence.
DoD: prior maps remain CLOSED; final source evidence and limits are reviewable;
all required automated gates green and final PR/main CI green; final report.
No automated test changes are needed unless observed behavior changes; this map
adds browser runtime verification and reuses the executed production contracts.

### MAP-06A — Proven language-modal ownership/lifecycle prerequisite
GOAL: early navigation language control keeps one opener and restores focus/lock.
FACTS: `language-flag.blade.php` capture callback opens and focuses modal then
stopImmediatePropagation prevents canonical `behavior.blade.php` opener. Native
Enter/Escape leaves focus in the closed dialog; canonical saved origin is null.
History 87bfb14c restored this fallback with visual flags; its load-order reason
is legitimate, but independent state owners conflict. The defect predates task.
DEPENDENCIES: MAP-05 green main `efe558fe`; observed MAP-06 keyboard failure.
FILES: only language flag JS producer, canonical navbar behavior, Node/PHP tests,
proof/current-state/execution map. All flag/modal CSS/media/DOM/copy are read-only.
RISKS: losing early load-order reliability or saved mobile scroll state.
TESTS: execute actual inline owner before DOMContentLoaded, pointer/keyboard
open-close, mobile origin, previous body overflow, immediate Escape cancels queued
focus, duplicate boot/disposal; SSR owner contract, canonical gates; Chromium/
WebKit ID/EN/AR at 390/1180/1181/1440 plus held-journey access.
DoD: one immediate delegated canonical opener; shared gate capture still runs;
Escape restores actual origin/previous overflow; no stale focus callback; flag
chooser appearance unchanged. Only redundant JS fallback is migrated/removed.
PROOF: native keyboard failure at source `efe558fe` and rendered instrumentation
recorded in `navigationDiscovery` of durable proof; no repository mutation during
diagnosis. Native focus remains dialog after close; canonical open never executes.
STATUS: IN_PROGRESS.

MAP-06A bounded decision: turn the already rendered canonical behavior into an
immediate guarded owner, delegate capture click after full navbar/modal markup,
preserve mobile-close semantics and support early input independent of deferred
homepage JS. Replace only the conflicting flag opener (ACTIVE_REDUNDANT after
migration); all its style rules are USED_AND_EFFECTIVE, kept byte-for-byte.
Track/cancel only its one queued focus frame on close/permanent exit. Keep Tab
inside the existing dialog using visible controls; the hidden flag-only close
control must not receive focus. Native keyboard verification covers both ends. No new
framework, layout, typography, business or motion animation changes. Terminal
Codex is the sole channel. Durable proof must pass before final map resumes.

MAP-06A new browser evidence: Chromium ID reduced motion at 1180px recorded the
first focus callback while dialog visibility was still hidden (then visible).
The scheduled focus now follows two paint frames, preserving the exact CSS
visibility transition. Both queued stages share cancellation on Escape/exit;
unit proof checks no early focus, actual final focus and stale callback disposal.

Follow-up proof: two RAFs alone also observed hidden visibility at 390px reduced
motion. Frame count is not visibility readiness. The bounded owner will focus
only after computed visibility is visible; its existing CSS visibility
transition completion supplies the retry, cancelled/disposed with the owner.
No duration/timeout/stronger selector is introduced.

Final visibility root cause: the existing global reduced-motion rule gives every
element a 0.01ms transition duration. Descendants with default transition-property
all also transition inherited visibility. WebKit recorded visible dialog but
hidden flag buttons during native Tab; Chromium reproduced the same focus miss.
Keep that visual owner untouched. Canonical initial focus must wait for both
dialog and actual flag controls to be visible; bubbling visibility completion
reschedules the one cancellable frame. Tab considers only truly visible controls.
