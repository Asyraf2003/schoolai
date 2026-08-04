# Homepage Motion Smoothness Comparison — Source Audit

Status: `SOURCE_AUDIT / BLOCKED_BY_MISSING_EVIDENCE`
Date: 2026-08-04
Repository: `Asyraf2003/schoolai`
Inspected main: `7eddaeb3259cfb1ae3d11eabee69487fb599d819`
Scope: Main Menu, homepage Gallery, Vision/Mission, and Values

## Owner-observed symptom

The Main Menu and homepage Gallery feel fluid and closely connected to input;
Vision/Mission and Values feel subtly interrupted or less tied to the gesture.

The observation is accepted as runtime symptom evidence. This audit explains
source risks; no comparable browser trace has proved a missed frame budget.

## Executive conclusion

The smoother surfaces give one motion system a small, coherent render target:

- Main Menu animations are finite, event-driven CSS/Web Animations effects.
  They do not continuously reconstruct a scroll story.
- Gallery maps scroll to one camera and renders most visible motion inside one
  canvas from one RAF loop. State is interpolated in JavaScript objects, then
  submitted as a coherent WebGL frame.
- Vision/Mission and Values map native scroll to many DOM style mutations,
  filtered/sticky/3D layers, and their own independent RAF controllers.
- Around the Vision-to-Values boundary, those local controllers can overlap
  with each other and with the always-present navigation RAF. The page has no
  single read/write scheduler.

Lusion's virtual-scroll pipeline explains why the whole Lusion page can feel
more unified than SchoolAI. It does **not** explain the relative difference
between SchoolAI Gallery and Vision/Values: all SchoolAI surfaces still consume
native document scroll. Their per-frame workload and scheduling are different.

## Source comparison

| Surface | Input and clock | Main animated target | Per-frame shape | Source-level result |
|---|---|---|---|---|
| Main Menu | pointer/focus/click; finite CSS/WAAPI timing | characters and one fixed overlay | no scroll-story loop | browser samples a bounded compositor-friendly transition |
| Gallery | native scroll -> fixed-factor interpolation -> one engine RAF | one camera, WebGL planes/background/trail | one coordinated canvas frame; small DOM label/CTA updates | spatial elements share one clock and coordinate system |
| Vision/Mission | native scroll -> delta-adjusted lerp + velocity coast -> local RAF | six sticky scenes, artwork, text containers, split text units | scene loop plus per-unit inline opacity/filter/transform writes | main-thread DOM/paint fan-out grows during active text motion |
| Values | native scroll -> critically damped progress spring -> local RAF | four nested CSS-3D cards, heading, SVG trail | 39 style/attribute writes per painted frame plus SVG point lookup | coherent math, but a wider DOM/compositor workload than Gallery |

## Main Menu — why it reads as smooth

- `resources/views/partials/site-navbar/mega-roll-script.blade.php`
  - `playRoll()` uses `Element.animate()` for a finite `520ms` character roll;
  - existing animations are cancelled before replay;
  - a `180ms` replay guard prevents rapid duplicate work.
- `resources/js/pages/mobile-navigation-cinematic.js`
  - open/close changes state once and lets CSS interpolate the result;
  - the forced `offsetWidth` read occurs once before opening, not every frame.
- `resources/css/pages/mobile-navigation-cinematic.css`
  - the primary motion uses `translate3d`, opacity, and scale;
  - blur/filter remains a finite paint risk, not a permanent scroll loop.

This is input-triggered animation, not proof that the page's scroll pipeline is
globally smooth. `resources/js/pages/welcome/navigation-state.js` still schedules a navigation RAF during
scroll and reads section rectangles/heights, which is a small global tax.

## Gallery — why scroll feels continuous

- `resources/js/surfaces/home/gallery-depth/controller.js` activates one engine near the section and stops
  it when offscreen or hidden.
- `resources/js/surfaces/home/gallery-depth/engine.js` owns one `DepthGalleryEngine` RAF.
- `resources/js/surfaces/home/gallery-depth/engine-frame.js::renderDepthFrame()` updates scroll, camera, CTA, trail,
  planes, label, background, and both render passes in a fixed order.
- `resources/js/surfaces/home/gallery-depth/scroll.js::DepthScroll.update()` interpolates one scroll target, velocity,
  progress, and camera Z.
- `resources/js/surfaces/home/gallery-depth/gallery-motion.js` derives opacity, parallax, drift, tilt, and scale from that
  same camera/velocity state.

Most visible change stays inside one GPU canvas. DOM is not rebuilt for every
plane: the label changes only when its active index changes, while the CTA has a
small bounded DOM update. That common frame authority is the main perceptual
advantage.

Gallery is not performance-proven. Current risks are:

- `DepthScroll.update()` performs journey geometry reads every frame;
- its `0.08` interpolation is frame-count based, not callback-delta based;
- `DepthTrail.addPoint()` creates a new tube geometry and disposes the previous
  geometry as the trail advances, which may create CPU/allocation pressure;
- the engine renders continuously while the Gallery is in view.

These risks require a trace even though the current result feels smooth.

## Vision/Mission — why it feels heavy or detached

- `resources/js/pages/welcome-scroll-story/controller.js` owns an independent RAF and iterates all
  prepared scenes for every rendered scroll sample.
- `resources/js/pages/welcome-scroll-story/split-text.js` splits Latin content into individual characters; Arabic uses
  larger word/space units.
- `resources/js/pages/welcome-scroll-story/motion-painters.js::paintText()` writes opacity, filter, and transform to
  every changing unit.
- `effect28()` animates blur on each unit; other effects also use 3D transforms.
- `resources/css/pages/welcome-vision-scroll.css` animates a very large blurred background pseudo
  element while sticky artwork and text remain present.

The Blade currently defines one Vision scene, one bridge, and four Mission
scenes. CSS gives every scene `722svh` at XL and `791svh` at 2XL: approximately
`4332svh` and `4746svh` for the six-scene sequence. A normal wheel movement
therefore advances narrative progress only slightly, which can feel delayed
even when no frame is dropped.

Additional source risks:

- CSS story length uses `svh` while JavaScript progress uses
  `window.innerHeight`; mobile browser-chrome changes can desynchronise them;
- ResizeObserver sets `snapNext`, so a viewport resize can intentionally snap
  the smoothed state;
- the root observer uses `100%` vertical margin, keeping this very long
  controller active around adjacent sections;
- momentum changes can still cause scene/art writes beyond the active text.

## Values — why good easing alone is insufficient

The current Values controller has stronger ownership than its old baseline:
geometry is cached, layout reads precede writes, and the critical spring is
delta-time based. The remaining smoothness gap is not simply a missing easing.

Each painted frame currently performs:

- `7 x 4 = 28` per-card CSS-variable writes in `resources/js/surfaces/home/values/paint.js::writeCardFrame()`;
- `9` root CSS-variable writes in `writeRootFrame()`;
- `2` SVG attribute writes for the trail head;
- one `getPointAtLength()` lookup while the desktop trail is active.

That is 39 DOM style/attribute mutations for a normal desktop frame. Their
visual result then passes through three transformed wrappers per card
(`pose -> float -> inner`), CSS perspective/backfaces, card shadows, SVG
drop-shadows, persistent `will-change`, and large transition blur layers.

The desktop story is `380svh`; bounce and exit are concentrated near the final
13% of progress. This can produce a perceptual slow-then-busy rhythm even when
the spring itself is mathematically continuous. CSS uses a sticky `svh` stage
while geometry still records `window.innerHeight`, retaining a mobile/short-
viewport alignment risk.

## Cross-surface scheduler conflict

Homepage motion currently has separate clocks:

- navigation RAF;
- Hero controller;
- Vision/Mission RAF with `100%` observer margin;
- Values RAF with `60%` observer margin;
- Gallery engine RAF with `35%` active margin;
- finite editorial/menu animations.

At the Vision-to-Values boundary, Vision can remain active after its root leaves
while Values has already activated before entry. Independent callbacks may then
interleave layout reads and style writes in the same browser frame. A smooth
spring inside one controller cannot guarantee a smooth page frame when another
controller consumes the same frame budget.

## Root-cause priority

| ID | Root cause | Confidence | Scope |
|---|---|---|---|
| `SMOOTH-ROOT-001` | independent surface RAFs with overlapping activation and no page read/write coordinator | source-proven architecture; runtime cost unmeasured | global homepage |
| `SMOOTH-ROOT-002` | Vision character-level style fan-out plus animated large/per-unit filters | source proven; dropped-frame impact unmeasured | Vision/Mission |
| `SMOOTH-ROOT-003` | Values has 39 DOM mutations, SVG geometry sampling, nested CSS 3D, shadows, and blur layers per frame | source proven; dropped-frame impact unmeasured | Values |
| `SMOOTH-ROOT-004` | Vision's extreme scroll length and Values' late compressed phases weaken direct input-to-motion pacing | source and math proven | Vision/Mission and Values |
| `SMOOTH-ROOT-005` | `svh` layout and `innerHeight` progress can disagree during viewport UI/resize | source proven; affected profiles unmeasured | Vision/Mission and Values |

## Decision direction — not yet implementation authority

Do not install a virtual-scroll library merely because Lusion uses one. The
smallest safe architecture is native scroll with one page motion scheduler:

1. read scroll, viewport, and active ranges once per frame;
2. calculate all subscribed surface states without DOM mutation;
3. batch DOM/canvas writes after every read;
4. activate only the current and measured adjacent surface;
5. keep Main Menu finite and event-driven;
6. retain Gallery's single frame authority, but make smoothing delta-time based
   and stop rebuilding trail geometry every frame;
7. reduce Vision to active-scene/root variables or bounded entrance timelines
   instead of continuous per-character filter writes;
8. reduce Values DOM writes and move the trail head without a per-frame SVG
   geometry query where browser proof accepts the replacement.

This requires a separate owner-accepted global-motion blueprint. It must not be
implemented as a Values-only patch or a stronger CSS override.

## Proof gap and next valid evidence

No source inspection can prove actual frame drops. Capture a comparable
Chromium Performance trace for one controlled normal and reverse scroll through
Vision -> Values at current `main`, recording frame time, long tasks,
style/layout, paint/composite, layer count, and simultaneous RAF callbacks.
Repeat later in WebKit before any global scheduler is promoted.

Until that evidence exists, the owner symptom is `FAIL_RUNTIME`, the listed
mechanisms are `PASS_SOURCE`, and the performance attribution remains
`BLOCKED_BY_MISSING_EVIDENCE`.
