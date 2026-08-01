# Homepage Hero Demo 1 WebGL Transition

BLUEPRINT ID: `HOME-HERO-WEBGL-DEMO1-001`
STATUS: `OWNER_ACCEPTED`
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

- Hero already has one semantic Blade DOM and one JS controller.
- Media may be an image or native muted inline video with poster/fallback.
- Existing CSS motion is the proven reduced-motion, no-WebGL, and failure path.
- Demo 1 uses a horizontal noise mask between two textures and only implements
  a forward transition in its original code.
- No production WebGL dependency currently exists.

## GAP

Real Safari/WebKit, PageSpeed, GPU timing, and context-loss runtime proof are
not available in this execution channel. They remain missing evidence rather
than assumed PASS.

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

## Storyboard

1. Capture a drawable outgoing image/video/poster frame.
2. Activate the incoming slide immediately and start its video when allowed.
3. Place one canvas inside the incoming media layer.
4. Animate the Demo 1-inspired noisy horizontal mask for 1.35 seconds.
5. Remove the canvas from active rendering and release transition textures.
6. On interruption, finish current state deterministically before starting the
   latest requested transition.

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
- Dispose removes canvas, handlers, GL resources, and pending RAF.

## Proof gates

- `git diff --check`;
- source structure and 200-line ownership;
- `npx vite build` and bundle delta;
- focused Hero/navigation tests;
- Chromium ID/EN/AR direction assertions for physical and automatic paths;
- repeated transitions keep one active slide and no stale canvas/RAF state;
- reduced-motion, no-JS, image/video failure, visibility, and BFCache fallback;
- real Safari/WebKit, PageSpeed, render-frame timing, and context-loss visual
  acceptance remain explicitly deferred when unavailable.

## Rollback

Remove the WebGL CSS import, transition/direction/WebGL modules, and controller
hook. The existing CSS transition and semantic Hero remain the complete fallback.
