# Homepage Hero Demo 1 WebGL Transition

BLUEPRINT ID: `HOME-HERO-WEBGL-DEMO1-001`
STATUS: `COMPLETE_WITH_EXTERNAL_BROWSER_PERFORMANCE_DEFERRED`
OWNER: Asyraf Mubarak
DATE: 2026-08-01
SOURCE MAIN SHA: `f77545362801059ef801d480d660ca1ce638f464`
ACTIVE ROUTE/SURFACE: homepage Hero media carousel
TARGET EXECUTION CHANNEL: Web AI / GitHub
REFERENCE: `https://github.com/akella/webGLImageTransitions`, Demo 1

## Owner goal

Use the visual behavior of Demo 1 when Hero media changes. The effect is a
noisy horizontal WebGL wipe between the outgoing and incoming Hero media.

Direction policy:

- physical right arrow/key/swipe-left: right to left;
- physical left arrow/key/swipe-right: left to right;
- automatic timer and video-ended transition: ID/EN right to left, AR left to right;
- dot navigation follows the logical target direction and current locale.

The incoming video starts immediately when the slide becomes active. It does
not wait for the WebGL transition to finish.

## FACT

- Hero has one semantic Blade DOM and one JS state/controller owner.
- Media may be an image or native muted inline video with poster/fallback.
- Existing CSS motion remains the reduced-motion, no-WebGL, and failure path.
- Demo 1 uses a horizontal noise mask between two textures and only implements
  a forward transition in its original code.
- Production now owns one bounded raw WebGL1 transition, not a general 3D engine.
- No package or lockfile changed in this batch.

## Scope

SCOPE IN:

- Hero transition direction orchestration;
- a deferred, project-owned minimal WebGL renderer and shader;
- image, poster, and available video-frame texture selection;
- lifecycle, context-loss, reduced-motion, and CSS fallback;
- focused tests, Chromium runtime proof, docs, and bundle report.

SCOPE OUT:

- page-to-page transitions;
- navigation, other homepage sections, About, and Testimonial;
- DB/schema/content changes;
- a site-wide renderer or third-party 3D engine;
- changing the established Hero layout or copy animation.

## Semantic and fallback contract

- Content, headings, CTA, controls, reading order, and focus remain in Blade.
- Canvas is decorative and `aria-hidden`.
- No JS: first slide remains usable.
- No WebGL/context loss/texture failure: existing CSS transition runs.
- Reduced motion: no autoplay and no WebGL transition.
- Video begins when its slide becomes active; a poster may be used as its
  transition texture until a drawable frame exists.

## Ownership

| Concern | Owner |
|---|---|
| semantic content and media elements | Hero Blade |
| active index, autoplay, controls | Hero controller |
| physical and locale direction policy | Hero direction module/events |
| video hydration/playback/fallback | Hero media controller |
| WebGL program, textures, RAF, disposal | Hero WebGL transition module |
| canvas layering and fallback state | Hero WebGL CSS |
| production entry | deferred dynamic import from Hero controller |

No external engine is installed. A minimal WebGL1 fullscreen quad is sufficient
for this single shader and reduces bundle/API surface compared with Three.js.

## Implemented storyboard

1. Resolve a drawable outgoing image, poster, or video frame.
2. Activate the incoming slide and begin its native video immediately.
3. Place one decorative canvas inside the incoming media layer.
4. Keep the outgoing frame visible while the incoming texture becomes drawable,
   bounded by a 1.2-second staging window.
5. Animate the Demo 1-inspired noisy horizontal mask for 1.35 seconds.
6. Remove the canvas, cancel RAF, and release transition textures.
7. A newer request cancels and replaces stale transition work deterministically.

## Responsive and locale

The same canvas follows the owned media container at every tier from 360px.
DPR is capped at `1.5`. Object-cover UV fitting is calculated per source.
There is no alternate DOM, shader, renderer, or controller per tier/locale.

ID/EN use LTR automatic direction. AR uses RTL automatic direction. Physical
arrow/key/swipe direction always follows the physical input, regardless of
locale.

## Capability and lifecycle

- WebGL is dynamically imported after Hero setup and warmed during idle time.
- The renderer runs RAF only during a transition.
- Hidden/offscreen/pagehide/reduced-motion suspends or cancels WebGL work.
- Context loss cancels the effect and exposes native media/CSS fallback.
- Resize updates canvas geometry without reinitializing Hero state.
- Remote image textures use an isolated CORS-safe clone; native media is never
  made dependent on WebGL texture permission.
- Dispose removes canvas, handlers, GL resources, transition textures, and RAF.

## Proof result

Exact proven branch head before documentation closeout:
`321aeeca9cc07cc293651e6a93542ae5650e7778`.

Automated proof:

- clean diff/checkout hygiene: PASS;
- Vite 8.1.3 production build: PASS, 90 modules transformed;
- focused Hero/navigation: 9 passed, 205 assertions;
- Chromium responsive/locale matrix: 33 cases PASS;
- widths: 360, 390, 640, 768, 1024, 1180, 1181, 1279, 1280, 1536, 1920;
- locales: ID/LTR, EN/LTR, AR/RTL;
- physical right-to-left and left-to-right controls: PASS;
- video-ended automatic direction for ID and AR: PASS;
- image timer automatic direction for ID and AR: PASS;
- repeated transitions and one-active-slide invariant: PASS;
- no stale canvas or transient class after settlement: PASS;
- hidden media pause, video/image failure, BFCache restoration: PASS;
- reduced motion and no-JavaScript fallback: PASS;
- all Hero CSS/JS source modules at or below 200 lines: PASS;
- Hero CSS equivalence/import order: PASS.

Recorded production bundle:

- Hero CSS: 12,953 raw / 3,062 gzip bytes;
- Hero entry JS: 9,751 raw / 3,345 gzip bytes;
- deferred WebGL renderer chunk: 7,126 raw / 2,890 gzip bytes;
- navigation CSS remains separately owned at 13,915 raw / 3,275 gzip bytes.

The full Laravel suite still has the known stale `HomeAboutReelTest` failure
against the intentionally disabled About surface. Structure recording still
contains only the three pre-existing oversized Vision/Mission source files.
Neither is caused by this batch.

## Deferred evidence

The following are not claimed as PASS:

- real macOS Safari/WebKit visual acceptance;
- Lighthouse/PageSpeed/Core Web Vitals;
- measured GPU frame timing on representative low-end/mobile hardware;
- manually forced WebGL context-loss visual acceptance in real Safari.

The repository `npm audit` still reports one high-severity dependency issue.
This batch did not change `package.json` or lockfiles; remediation remains a
separate dependency task rather than an unreviewed shader upgrade.

## Rollback

Remove the WebGL CSS import, transition/direction/WebGL modules, and controller
hook. The existing CSS transition and semantic Hero remain the complete fallback.
