# Homepage sequential optimization — active handoff

Updated: 2026-10-02. Execution channel: Terminal Codex only.
Repository: Asyraf2003/schoolai. Owner authorizes scoped PR/CI/merge to main.

## Active target

- MAP-HOME-01 — About / Vision / Mission runtime.
- Status: VERIFYING. Proof status: BLOCKED_BY_MISSING_EVIDENCE.
- SOURCE MAIN SHA: `8c399a280b8cc5df1ae70c39aee8be72803f371c` (fresh fetched).
- Branch: `fix/map-home-01-vision-runtime`.
- Worktree: `/home/asus/projects/schoolai-home-01`.
- Issue: https://github.com/Asyraf2003/schoolai/issues/62
- PR: none. Resulting main SHA: not merged.
- Blueprint: `../blueprints/2026-10-02-vision-runtime.md` (IMPLEMENTING).

## FACT → GAP → GOAL → IMPACT

Main renders three story panels and three video previews in one semantic DOM.
Desktop >=1024 has centered sticky visuals, absolute clip-path stack and
scroll-linked text. Smaller/reduced layouts retain sequential semantic content.
Geometry is cached at preparation/resize; scroll reads scrollY, not rectangles.
Timeline writes every clip/transform each frame, including unchanged end values.
Background writes static palette/pattern properties every frame; two full-section
layers animate blur/opacity/drift. Desktop palettes start at1280; initial query
is captured once and does not adapt when resizing across1280.
Preparation awaits only preview0; preview1/2 prepare after unlock/proximity.
Loadeddata is treated as playable without checking future buffer/play success.

GAP: source identifies costs but does not prove the cause of visible hitches.
GOAL: existing cinematic UX, smooth active videos and first/reverse/second
traversal with no visible initialization, preserving responsive/RTL/a11y/lifecycle.
IMPACT: measure background, media, JS and compositor before selecting a change.

## Decision and current implementation

Implementation: all3 Vision previews prepare together; readiness waits for muted
play start plus buffered future playback; pause after warm start so offscreen
clock cannot consume its buffer; viewport IO cannot interrupt preparation.
Timeline/background skip unchanged writes; desktop palette responds to1280
crossing. All CSS/Blade remain byte-identical. Coupled gate deadline now marks
delay instead of cancelling valid preparation; source3-run evidence proved the
8s abort destroys cinematic layout. No section ordering/global readiness rewrite. Owner permits one simpler
background only if browser comparison supports visual value/cost tradeoff.
No redesign or whole-homepage readiness changes in this map.
Old MAP-06B #61 remains unmerged FAIL on `fix/homepage-complete-readiness`,
checkpoint `c57cc050`; no cherry-pick of its whole-homepage diff.

## Scope packet / files

Editable: Vision story runtime modules; welcome/video-previews.js and
video-readiness.js (Vision-only); Vision CSS and Blade only for proven owners;
scoped tests, blueprint, this handoff, current-state ledger.
Editable dependency: scroll-gate.js deadline only, proven blocker to Vision.
Read-only: typography, locale content, config/media, Vite, navigation.
Forbidden: Program internals, seam gradient/Program opening, Values/Gallery,
Testimonials/Articles/footer, unrelated CSS, data/DB content/dependencies.
Files touched: Vision controller/timeline/background-compositor/preparation;
welcome/video-readiness,video-previews,scroll-gate; HomeAboutVideoTest;
HomeReadinessRuntime/VisionTimelineRuntime; blueprint/handoff/current-state/index.
CSS classification: sticky/clip/media layout USED_EFFECTIVE; background
blur/drift/pattern USED_EFFECTIVE with value/cost unproven; repeated JS writes
candidate redundant. No CSS classified DEAD_CONFIRMED; none removed.

## Tests and browser proof

Baseline production build PASS (Vite existing large-chunk advisory).
Production proof server: port8021, isolated SQLite fixture
`/home/asus/.cache/schoolai-proof/homepage.sqlite`; business DB untouched.
Windows owned Chrome CDP9226 / native mouse1200px/s /1440x900/DPR1:
three cold baseline runs captured; raw output outside repo in
`C:/Users/ASUS/AppData/Local/Temp/SchoolAICompleteTools/vision-main-baseline`.
Driver `/home/asus/.cache/schoolai-proof/vision-baseline.mjs`.
44 Node runtime contracts PASS. FullPHP305 tests/3506 assertions PASS.
Structure597/build/diff PASS; Pint PASS. No browser/CI/completion PASS.
Corrected-media run confirms all3 frame-ready at unlock; first/reverse/second
still saw233.2/100.2/34.3ms in one run. A traced run66.8/83.1/50.1ms is diagnostic,
not sufficient to erase worst case. Native RAF now cancels callback per pass.
Background opacity-only comparison is ongoing. Raw variants:vision-buffer-held,
vision-trace-ready,vision-opacity-only beside baseline in Windows proof folder.

## NEXT MAP (read-only findings)

1. MAP-HOME-02 Vision→Program seam: Vision end gradient + Program opening.
2. MAP-HOME-03 Program runtime, then its next seam; continue section by section.
3. Global readiness: main still unlocks after Vision and has deadline fallback;
   owner full-homepage readiness goal is not satisfied by main. Reconcile in a
   later bounded readiness map after section maps; do not call homepage PASS.

## Exact NEXT VALID STEP

Inspect opacity-only comparison against corrected-media baseline; attribute
remaining first-pass hitches, retaining all CSS until visual/cost proof supports
change. Add durable browser driver and complete lifecycle/locale/tier proof.
Then update blueprint decision/current-state, implement only MAP-HOME-01,
run tests and before/after browser matrix, PR/CI/merge only when scoped PASS.
Do not enter MAP-HOME-02 until MAP-HOME-01 is merged and CLOSED.
