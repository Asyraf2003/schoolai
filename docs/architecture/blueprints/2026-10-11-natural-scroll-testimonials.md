# Natural scroll and Gallery–Testimonials continuity

STATUS: OWNER_ACCEPTED / IMPLEMENTING
Owner: Asyraf; issue #100; Terminal Codex; 2026-10-11.
Main: cdfd30980516bbe2022fd0f8c12e64e4a613c8c3.
Branch: feat/natural-scroll-testimonials, contains PR99 7d020133 and PR97 5518756f.
The current detailed owner request accepts this matching blueprint.

## FACT → GAP → GOAL → IMPACT → DECISION

FACT: clean local PR99 branch, both PRs OPEN. Integration preserves both parents.
One global1680px/s cap,112px debt and145ms input cutoff currently intercept wheel.
WebKit reproduces zero travel: first RAF arrives149–180ms after wheel and the
145ms cutoff discards all accepted input before the first scrollBy invocation.
No signature, pointer or nested-scroll defect explains the reproduced failure.
Values and GSAP read native scroll. Values exit is native1:1; Gallery scrub=true.
OLD Testimonials has three alternating scroll-linked tracks, photo2:1 cards,
2.333/3.333/5.333 visible-card widths, tight gaps, quote/top and cite/end corners.
Current locale content explicitly discloses samples. No new claims/names allowed.
Historical typography paths are absent; V2 foundation/fonts, tokens, locale/ar
and section CSS are the live owners. No .ai/rules directory exists.
GAP: exact maximum useful phase velocities and final contrast/geometry require
new frame evidence. Native Safari/devices and production performance unavailable.
GOAL: free rapid movement, short damped glide, immediate reversal; retain original
animation clocks/geometry, then Gallery dark→large black question→white OLD wall.
IMPACT: pacing core/policy/controller, Testimonials Blade/CSS/JS, heading locale
copy, composition lifecycle, focused tests/workflows and this ledger/blueprint.
DECISION: bounded input displacement with exponential response, no global speed
constant. A synchronous first bounded step prevents RAF cutoff swallowing input;
subsequent RAF uses native scroll and no autoplay/deferred input debt. Phase limits
come from actual CSS/WAAPI duration and native choreography distance; free phases
have no velocity clamp. Free ordinary112px impulses use224px debt/60ms response, giving approximately
2× the former1680px/s sustained wheel baseline. Larger input scales debt by the
square root of its impulse; no fixed distance cap hides a global velocity limit.
All unused debt expires180ms after release, regardless of impulse size.
Phase/frame budgets include boundary lookahead. Scroll-linked Values/Gallery
and intro phases use14 movement segments (reserve endpoints, require12 painted
intermediates); their maximum varies with measured frame cadence and geometry,
without arbitrary second-based caps. CSS/WAAPI uses actual remaining duration,
including the pending horizontal heading stage. WebKit demonstrated that12
segments can yield11 intermediate paints;14 fixes that reproduced sampling gap.

## Scope and ownership

Editable: resources/js/sections/scroll-pacing*.js, new testimonials*.js,
resources/views/landing/testimonials.blade.php, sections/testimonials.css,
sections/gallery.css (only release the old Testimonials font selector to its
locale adapter; Gallery treatment and choreography stay intact),
locale/ar.css (narrow type adapter), lang/*/testimonials.php (heading only),
resources/js/index.js, related tests/workflows, existing current-state ledger.
Read-only: About, Program, Values/card/SVG/heading controllers and CSS; Gallery
geometry/timeline/media/entry; Testimonials presenter/data/media config; OLD.
Forbidden: Article, navigation/Hero redesign, DB/schema/admin/media mutations,
dependencies, main writes, merge, unrelated cleanup. PR97 changes stay intact.

## Semantic and motion storyboard

One localized H2 and existing disclosure, followed by all21 existing quotes/photos
in semantic order and native accessible row controls, then empty white space.
No-JS/capability failure: readable white surface, horizontally scrollable rows.
Reduced: white final state and wrapping grid, all content visible, no inertia.
Normal: Gallery fully finishes its existing black hold. Testimonials starts below
it without overlap/negative margin. A CSS-owned sticky intro reserves a viewport
and native scroll runway. Background brightens first; only once luminance gives
black contrast>=7:1 does the question rise/fade in. Question remains in native
flow and scrolls out, wall enters on white; reversible alternating OLD travel.
Scroll alone drives transition; no timeout, fixed overlay or new animation engine.
Resize remeasures native bounds without writing scroll position. Direct anchors,
keyboard and scrollbar bypass wheel physics; outgoing locale/page/visibility
suspend cancels debt/frames; BFCache resumes from actual bounds with empty debt.
Failure/dispose removes enhancement variables/state and restores semantic CSS.

## Six tiers and locale

XS360/390: fluid2.333 card media strip, readable enlarged min card width; large
question2–3 lines with logical gutter. SM640: same strip, more room; reduced2grid.
MD768:3.333 strip; LG1024: same. XL1280/1440 and2XL1536/1920:5.333 strip.
Question uses Gallery foreground weight/tracking/scale adapted to viewport/start
alignment; CSS bounds measure/line composition, no JS font-size/breakpoint owner.
Short height/200% text: intro minimum height follows content; never clips copy.
Global boundaries639/640,767/768,1023/1024,1279/1280,1535/1536 tested.
ID/EN Inter/LTR, natural question left/start. AR Cairo/RTL, joined words and
right/start, normal tracking and1.45 leading. Vertical time unchanged; horizontal
track direction mirrored, photographs unchanged. Server locale reload reconstructs
one DOM, clears pending input and recomputes bounds. Navigation contract unchanged.

## Budgets and accessibility

No dependency, WebGL, renderer or copied OLD import. Lazy existing photographs
reserve2:1 geometry, stable layouts; no extra critical media or active idle loop.
One event-driven RAF per controller; no work offscreen/hidden/disposed. Pure scroll
math tests include stall, fractional movement, idle stop, reversal and boundary
budgets. Black title appears only against>=7:1 background. Photo text uses a
contrast layer; keyboard can reach every quote via native row movement controls.
Focus, reduced, touch pan-y, nested scroll, zoom, no-JS and failure remain usable.
Performance: compare three declared local runs; report median/worst and reject
new recurring long tasks/layout shifts attributable to this patch. No100/CWV claim.

## Ordered execution and proof

1. COMPLETE: pacing and first-delivery/coast proof PASS both engines.
2. COMPLETE: coupled Testimonials matrix and OLD motion PASS both engines.
3. ACTIVE: publish verified scoped changes and inspect CI, without merge.
After each proof checkpoint update UI_UX_CURRENT_STATE before naming the next.
Chromium/WebKit: physical aggressive wheel, reversal during motion, no long tail,
all original Gallery phases and native Values1:1; sustained per-phase samples.
Testimonials: all locales×9 widths plus affected boundaries/short/orientation,
contrast/title reveal/white wall/white ending, reverse/resize/reentry, reduced,
no-JS/missing capability, keyboard/touch, all six locale-switch directions.
Run git diff --check, npm run check:structure, npm run build, php artisan test;
focused PHP/Node/browser and Pint on PHP changes. Keep baseline failures explicit.
Local proof: Node17, PHP16/259, structure317/build/Pint/diff PASS;
Chromium153/WebKit26.6 new pacing4, Testimonials6,42cells per engine,
and original Gallery/Values11 per engine PASS. Native Values and Gallery JS
remain byte-identical to combined parents. Three local comparative frame samples
PASS, zero long tasks/idle writes. Full PHP345 remains71FAIL/75ERROR, identical
names to baseline342, with three additional PASS. Initial WebKit media error4
was missing local MP4/H264 plugins; restored environment passes the strict gate.
Proof: ../../../docs2/proof/natural-scroll-testimonials/proof.json.
STATUS: PASS local scoped; FAIL existing PHP baseline;
BLOCKED_BY_MISSING_EVIDENCE CI/native/physical/speech/PSI/field certification.
Blueprint remains IMPLEMENTING, not full-DOD PROVEN.
NEXT channel Terminal Codex: publish the continuation PR and verify CI; no merge.
