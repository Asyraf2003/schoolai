# Homepage Values Spatial Lines Blueprint

Blueprint ID: `HOME-VALUES-002-SPATIAL-LINES`
Status: `IMPLEMENTING`
Owner: Asyraf Mubarak
Date: 2026-08-09
Source main SHA: `196a2c02b606dba22db8e6bfbd838f00c6b7f152`
Active route/surface: homepage Program -> `#nilai`
Execution channel: Terminal Codex

## Goal and reference

Translate the large spatial-stroke character of Lusion's About / Area of
Expertise sequence into an Al Mustaqbal Values background without copying its
source, geometry, assets, branding, or exact composition.

The accepted result is:

```text
Program light-blue kinetic field
-> Program type sinks while #2038ff takes over
-> first spatial stroke arrives at the boundary
-> Values cards remain semantic DOM above three solid white Line2 strokes
-> the three compositions travel substantially and reversibly with scroll
```

## FACT and GAP

- `main` and `origin/main` are both `196a2c02...` after fetch.
- `three` is absent from `package.json` and `package-lock.json`.
- Values already owns one IntersectionObserver, ResizeObserver, scroll target,
  critically damped RAF scheduler, reduced-motion gate, and BFCache listeners.
- The current single SVG trail is decorative and is the losing visual owner.
- Program precedes Values in the rendered semantic DOM; its cards, detail,
  heading, and content remain protected.
- Laravel runtime responds locally and WebKitGTK is installed. Chromium
  Playwright proof remains required after implementation.
- `GAP-VALUES-SPATIAL-RUNTIME-001`: visual composition, Line2 sufficiency,
  reverse behavior, and lifecycle are unproven until browser execution.

## Scope and ownership

Editable:

- `package.json`, `package-lock.json`;
- this blueprint and `UI_UX_CURRENT_STATE.md`;
- `school-values.blade.php` and `HomeValuesStoryTest.php`;
- the Values CSS entry/surface modules;
- the Values controller, motion, geometry, paint, and new spatial helpers.

Read-only/protected:

- Program Blade/cards/detail/title and all visible copy;
- Vision/Mission, Gallery, Articles, Navbar, About, and Testimonial;
- locale copy and typography owners;
- unrelated existing user changes.

Owner map:

| Concern | Target owner |
|---|---|
| semantic Values cards/copy | existing Blade DOM |
| Program -> Values takeover | Values handoff CSS + Values controller variable |
| scroll target/smoothing/RAF | existing Values controller/motion |
| spatial renderer/geometry | lazy Values spatial scene module |
| canvas sizing/fallback | Values spatial CSS + ResizeObserver |
| Vite graph | existing `welcome.js` entry and dynamic scene chunk |

There is no second scroll listener, animation scheduler, renderer, scene per
tier, or global homepage graphics runtime.

## Semantic and fallback contract

- One `h2`, one list, and four card articles remain the readable product.
- Canvas is `aria-hidden`, pointer-inert, and always behind the cards.
- No JS/WebGL/context-loss uses three large CSS strokes on the blue field.
- Reduced motion never loads or continuously choreographs Three.js; it keeps a
  subtle static composition and the natural readable card grid.
- Renderer failure cannot hide, move, or block Values content.

## Storyboard and lifecycle

| State | Trigger/result |
|---|---|
| idle | static fallback; no Three.js bytes or context |
| eligible | Values intersects the existing near-active root margin |
| loading | dynamic import of Three.js scene chunk |
| ready | one transparent WebGLRenderer, one camera, three Line2 objects |
| active | existing smoothed Values progress renders only requested frames |
| settled | no RAF; last deterministic composition remains |
| suspended | far offscreen, hidden tab, or pagehide; no render work |
| failed | CSS fallback restored; semantic DOM unaffected |
| disposed | geometries, materials, renderer, context, listeners released |

Each line has its own Catmull-Rom path, phase, Z depth, progress ratio, and X/Y
travel. No `Math.random`, wheel interception, forced scrolling, snapping,
texture, light, shadow, postprocessing, or bloom is permitted. TubeGeometry is
deferred unless runtime proves Line2 insufficient.

## Responsive, locale, and browser contract

| Tier | Scene contract |
|---|---|
| XS 360-639 | same three paths, shorter camera field, DPR <= 1.25 |
| SM 640-767 | same scene, fluid camera/aspect fit |
| MD 768-1023 | same scene behind natural 2x2 cards |
| LG 1024-1279 | same scene; no navigation ownership change |
| XL 1280-1535 | full sticky Values choreography, DPR <= 1.5 |
| 2XL >=1536 | bounded DPR and expanded off-frame curves |

ID/EN use the shared neutral composition. AR changes DOM direction/type only;
the non-directional scene is not mirrored without runtime collision evidence.
Resize/orientation preserves logical progress and recomputes camera/geometry.
Chromium and WebKit must prove WebGL sizing, context lifecycle, and fallback.

## Performance and accessibility

- npm dependency and official `three/addons/lines/*` imports only; no CDN.
- Engine module stays outside the initial route chunk through dynamic import.
- One renderer, three geometries, three materials, three draw calls; no assets.
- Drawing buffer follows CSS size with capped DPR and no unnecessary resize.
- No continuous RAF while settled, hidden, or outside the near-active region.
- Canvas reserves the owned layer, is decorative, and does not alter focus,
  keyboard, touch, reading order, zoom, or locale switching.

## Execution and proof

1. `COMPLETE`: implement the bounded Values scene/handoff and focused DOM/source
   contract test.
2. `COMPLETE`: run diff, structure, build, focused test, and full PHP suite.
3. `COMPLETE`: prove Chromium desktop states A-F plus offscreen RAF/context.
4. `COMPLETE`: prove all width tiers, AR RTL, reduced motion, and WebKit.
5. `BLOCKED`: commit/push is prohibited because the clean baseline structure
   gate fails and the full PHP suite has two unrelated failures.

Required runtime captures: Program end, blue takeover, first line, frontal
cards, materially changed mid-scroll composition, and reverse restoration.
Runtime evidence is complete. Repository-wide publication status remains
`FAIL`; repairing baseline Vision/Hero/Program/analytics owners is outside this
blueprint and requires a separate owner-authorized batch.
