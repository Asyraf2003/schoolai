# MAP-06B — Complete homepage readiness before scroll

State: IMPLEMENTING (owner goal/inventory OWNER_ACCEPTED)
Work status: FAIL until corrected full-journey proof passes
Owner correction: 2026-10-02 (explicit current request supersedes old five-unit goal)
Repository: Asyraf2003/schoolai
Source main: 8c399a280b8cc5df1ae70c39aee8be72803f371c
Execution channel: Terminal Codex
Issue: #61; branch: fix/homepage-complete-readiness

## FACT → GAP → GOAL → IMPACT → DECISION

FACT: inspected current main and MAP-06B branch. SSR renders Hero, Vision,
Program, Values, Gallery, Testimonials, Articles and footer. Local production
DOM at 1440×900 has 69 images, three Vision previews and an intent-only dialog
video. Source retains testimonial proximity import, Vision/Gallery proximity
source hydration, native lazy images and geometry fitted after media loads.
Five-unit shell readiness and access bypass therefore violate the owner goal.
GAP FULL-READY-001: old proof checks one immediate scroll, not the entire journey.
GOAL: Hero and primary actions appear quickly; hold the journey at Hero while
background work prepares every required homepage dependency. Real progress reaches
100 only when the entire rendered experience is ready. Paint 100, finish the
existing opacity handoff, then unlock once. Do not shift work to first scroll.
IMPACT: longer honest preparation may be necessary; measure it separately from
Hero paint. Preserve composition, school copy, CSS cascade, media and motion.
DECISION: complete readiness barrier; no Vision-only release, shell-only media
success, timer-produced progress, or access-intent unlock. Navigation menus,
locale and PPDB remain interactive. Internal anchors queue until readiness.

## Dependency inventory: every row blocks 100

| Owner | Required dependency / evidence | Streaming after unlock |
|---|---|---|
| Hero | Existing Opening/carousel controller; visible shell; all rendered slide images/posters; active video first frame/future data or validated permanent poster mode | Remaining video bytes |
| Navigation | Current mega/language/mobile/login handlers, applicable CSS, logos/flags; cursor runtime/assets for fine pointer | Intent-only authentication network |
| Styles | SSR/deferred styles plus Program, mobile navigation, carousel when rendered, Testimonials CSS loaded/applied | None required |
| Fonts | Actual Inter/Cairo text/weight subsets across all rendered and hidden controls; readiness before final geometry | No required font |
| Images | All rendered Hero/Program/Gallery/Testimonial/Article/footer images including hidden Program details; decoded natural dimensions | None required |
| Backgrounds | Owned ornaments, cursor and CSS visual URLs decoded, including future Vision states | None required |
| Video previews | All three Vision and every rendered direct Gallery video; source, first frame, future buffered data, paused/offscreen controller | Subsequent ranges, transparent buffering |
| Vision | Styles/fonts/visuals complete; timeline and initial geometry/state painted | Playback and animation updates |
| Program | GSAP or installed canonical reduced controller, formation/heading/dialog controls and first state | User-triggered interaction timelines |
| Values | Existing geometry/state/first paint or stable reduced/capability mode; disabled spatial graph stays disabled | Existing scroll animation |
| Gallery | Existing CSS controller, all media fitting and initial frame; pattern enabled before barrier | Playback and scroll animation |
| Testimonials | Import JS/CSS before barrier; controller mounted, tracks measured/initial transforms painted; every card image decoded | Existing track animation |
| Articles | Existing heading/reveal owner initialized; rendered article data/images or SSR empty state, reserved cards | CSS hover/replay |
| Footer | SSR data/links/year/logo/social/partner assets and final layout | External link navigation |
| Final geometry | Fonts/images/styles applied; existing owners refresh/paint at Hero; stable reserved section geometry and state | Position reads and animation updates; resize may recompute geometry |

Data comes from existing Home controller and exact-view composers: bounded
Hero promotions/Gallery, static localized Program/Values/Testimonials, bounded
Articles query and shared footer/nav models. There is no homepage scroll fetch.
The active homepage Gallery uses DOM/CSS; no Three runtime is required. No engine
selection or graphics redesign is authorized. Intent-only fullscreen video may
use the already prepared preview URL when explicitly opened; no scroll hydration.

## Scope packet and ownership

Editable: existing critical gate/bootstrap/progress, readiness ledger/coordinator,
new bounded readiness owners under the existing welcome directory, existing
Vision/Gallery preview preparation/playback owners, testimonial mounting,
navigation preparation, proven geometry refresh paths, targeted tests and
browser proof, this blueprint/current-state/map/proof.
Read-only: Blade section composition, type scales/cascade/import order, content,
media URLs/config/assets, PHP business/query owners, dependencies and lab graph.
Forbidden: visual redesign, CSS mass deletion, activation/deactivation, new
engine, dependency changes, unrelated cleanup and accessibility-label repair.
One bootstrap gate is adopted once. One existing controller per surface remains.
Loader semantics/treatment stay in current Blade/CSS. Preparation yields between
bounded units after Hero paint; native lazy content is promoted only then.

## Progress, state and fallback

Real ledger units include navigation/styles/fonts/images/media, every rendered
surface and final geometry. Each unit settles once only on its inspected promise
and runtime/asset evidence. Progress is completed units / required units, rounded
down for display. Image/media checks within a unit all finish before it settles.
Hero's unit proves its decoded visible shell and mounted controller. The media
unit independently awaits actual startup/frame/future buffer for every rendered
Hero/Vision/Gallery video. This avoids depending on paused metadata to advance
before playback; neither partial unit permits unlock.
Final geometry also rechecks prepared playback from existing sources/buffers
after all controllers/layout are ready. Await actual startup/frame/future buffer
and pause all previews together before100, preserving offscreen pause at release.
100 requires every blocker; failed/aborted/pending work cannot count as ready.
Media errors/reduced motion may settle only a decoded, permanently selected
poster/static mode with controller and reserved geometry ready. No automatic
later source re-hydration on scroll. Missing required visuals stay below100.
A delayed/failed dependency shows native retry access; timer only reveals recovery,
never readiness. Retry reloads; PPDB/other routes remain usable. No hidden unlock.
No-JS retains native semantic content/navigation and ordinary page access.

Lifecycle: shell-visible/locked → preparing → all-ready/100-painted/locked
→ existing160ms opacity handoff (no animation under reduced motion) → unlocked.
Hidden/persisted history pauses handoff; restore reuses the same owner. Permanent
page exit aborts listeners/work. Opening owns root overflow only; modal/mobile
body overflow stays with its current owner. Pending anchors/history positions
are restored only after readiness; Escape/focus/menu clicks cannot bypass.
Normal wheel/touch/keyboard/focus/programmatic hash cannot enter the lower journey
while locked. Lower sections are temporarily inert, preserving accessible Hero,
navbar, locale and PPDB. No server permanent lock without JavaScript.

## Six tiers, locales and capability

XS360–639 (360/390), SM640–767, MD768–1023, LG1024–1279,
XL1280–1535, 2XL≥1536 share the same semantic DOM/barrier/controller graph.
Existing layout, media crop, type, touch/keyboard and motion remain. Test all
representatives/interiors and boundary pairs, including navigation1180/1181.
Short height/orientation/200% retains existing direct loader PPDB access.
ID/EN Inter/LTR and AR Cairo/RTL use actual locale text for font readiness.
Vertical loading/progress/time is shared; directional existing choreography
remains. Server locale switch aborts outgoing work and starts one new barrier.
Chromium/WebKit use capability tests, stable decoded poster under reduced motion
or failed playback, no UA forks. Offscreen videos pause; all sources necessary
for normal playback are ready before unlock. No new WebGL/renderer.
Actual decoded frames are prepared before media settles. Visible media waits
for its compositor callback; offscreen media copies its decoded frame through
one reusable temporary2D canvas because engines may suppress offscreen callbacks.
No canvas is mounted or animated. ImageBitmaps are closed immediately and the
context is released before100; buffered future playback remains a blocker.

## Budget and proof

Hero/nav/PPDB remain outside lower-page preparation blocking. No full-video
completion wait. Yield controller/asset work; pause hidden/offscreen playback.
Measure cold Hero FCP/LCP, total readiness duration, bytes, long tasks, layout,
scroll frame time and CLS separately. Three comparable cold mobile/desktop lab
runs: median/worst; Lighthouse100 remains target, not permission to defer work.
Physical Safari/deployed PSI/field CWV stay separately declared missing evidence.

Required browser acceptance after visible100+handoff+unlock:
Hero→footer →footer→Hero →Hero→footer again. Every crossing must show zero new
required JS/CSS/font/image requests, source hydrations, controller mounts,
first-entry geometry init or user-visible loading. Continued video byte ranges
are listed separately. Capture states/media signatures before/after each pass,
real native input, screenshots, errors, tasks and frames. Test six tiers/locales,
normal/reduced and Chromium/WebKit, held dependencies, early navbar/PPDB, anchors,
media errors, pending required images, hidden/BFCache, locale, zoom/orientation.
Run git diff --check, npm run check:structure, npm run build, executed Node
contracts, focused and full php artisan test; Pint for PHP changes.

## ACTIVE STEP → EXECUTION → PROOF → PROGRESS → STATUS → NEXT

ACTIVE: audited inventory and owner correction; implement the single complete
homepage readiness capability on Issue#61 branch, then test/browser proof.
PROOF: prior five-unit samples retained as historical evidence, not acceptance.
STATUS: FAIL. Blueprint OWNER_ACCEPTED from explicit owner goal.
NEXT channel: Terminal Codex; Issue→branch→implementation→tests→browser→PR→CI→merge.
