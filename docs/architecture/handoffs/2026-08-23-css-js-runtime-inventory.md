# CSS / JS / Runtime Ownership Inventory — 2026-08-23

Status: `D3_PASS / DURABLE`
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Scope: read-only ownership/debt inventory for H2-H4/H7 planning.

## Purpose

Freeze the current front-end ownership graph and known debt before Codex changes
runtime behavior. This document is not authorization to mass-delete, bundle,
rename, or refactor files.

## Build and structure ownership facts

`package.json` defines:

- `npm run build` -> `vite build`;
- `npm run check:structure` -> `node scripts/verify-source-structure.mjs`;
- `prebuild` runs the structure check;
- Three.js package dependency: `^0.185.1`.

`vite.config.js` declares a broad set of JS/CSS entry points across Homepage,
PPDB, auth, account/admin, and Article Canvas.

The structure checker:

- enforces a 200-line source limit;
- records local JS/CSS imports and Blade `@include`/asset references;
- treats every Vite input as an asset entry;
- reports JS/CSS files with neither a Vite-entry nor local importer;
- validates CSS aggregate import order/checksum using
  `docs/architecture/source-module-equivalence.json`.

Important consequence: a file can be considered "referenced" merely because it
is a Vite input even if no rendered template actually requests that Vite entry.
Therefore `check:structure` is necessary but not sufficient proof of runtime
ownership/dead-code status.

## Frozen structure baseline debt

The owner-terminal baseline already records these >200-line sources:

- `resources/css/pages/welcome/043-welcome-cascade-043.css` — 287 lines;
- `resources/css/surfaces/home/article-story/base.css` — 241;
- `resources/css/surfaces/home/article-story/desktop.css` — 215;
- `resources/css/surfaces/home/gallery-depth/end-cta.css` — 225;
- `resources/css/surfaces/home/values/story-heading.css` — 202;
- `resources/css/surfaces/home/values/story-kinetic.css` — 201;
- `resources/js/surfaces/home/article-story/controller.js` — 219 in the frozen
  baseline;
- `resources/js/surfaces/home/gallery-heading/desktop-continuity.js` — 252;
- `resources/js/surfaces/home/program-journey/controller.js` — 224 in the frozen
  baseline;
- `resources/js/surfaces/home/values/controller.js` — 215 in the frozen baseline;
- `resources/js/surfaces/home/values/spatial-scene.js` — 277 in the frozen
  baseline;
- `resources/views/home/sections/articles.blade.php` — 205.

Known structure-check unreferenced candidates:

- `resources/css/surfaces/home/article-story/footer-release.css`;
- `resources/js/surfaces/home/vision-story/entry.js`;
- `resources/js/surfaces/home/vision-story/typography.js`.

Known CSS source-equivalence/checksum drift:

- `resources/css/pages/welcome-hero.css`;
- `resources/css/pages/welcome.css`.

These are baseline debt, not permission to delete or rewrite. H3/H4/H5 may touch
a debt item only when actual ownership/proof makes it part of the active atomic
capability.

## CSS ownership map

### Legacy/global aggregate

`resources/css/pages/welcome.css` is an aggregate importing 47 ordered legacy/
cascade modules. This makes import order semantically significant and explains
why checksum/order proof exists.

Classification: `ACTIVE AGGREGATE / HIGH CASCADE RISK`.

Do not treat later-numbered cascade files as dead merely because an earlier rule
looks equivalent. Losing-cascade/dead-rule claims require computed-style/runtime
proof.

### Hero aggregate

`resources/css/pages/welcome-hero.css` imports nine ordered Hero modules.

Classification: `ACTIVE AGGREGATE / CHECKSUM DRIFT / HIGH CASCADE RISK`.

### Bounded surface aggregates

Examples with clearer ownership:

- `welcome-depth-gallery.css` imports Gallery base/cards/end-CTA/article-handoff/
  responsive plus the Values->Gallery handoff stylesheet;
- `welcome-article-story.css` imports Article base/desktop/responsive plus the
  intentionally retained debug ruler.

Classification: `ACTIVE SURFACE OWNERS`.

The Article debug ruler remains protected temporary work and must not be removed
as "cleanup" during hardening.

### Unreferenced CSS candidate

`article-story/footer-release.css` has no proven runtime importer/entry in the
current structure baseline. Search evidence finds only documentation/test
mentions.

Classification: `UNREFERENCED CANDIDATE`, not proven safe-to-delete.

## Homepage JavaScript entry/ownership map

### Main synchronous entry

`resources/js/pages/welcome.js` synchronously imports:

- navigation;
- Program/Values world;
- Values controller;
- Article Story controller;
- Program cards/journey;
- public content;
- gallery wall;
- lazy media.

Classification: `ACTIVE PAGE AGGREGATE`.

Consequence: Values/Article/Program control code enters the page graph from the
main Homepage entry even when their section is far below the initial viewport.
This is not automatically wrong because the accepted product direction allows
aggressive preparation, but H4 must measure whether synchronous ownership delays
critical initial readiness.

### Vision enhancement

`welcome-vision-story.js` waits for Hero presentation, then schedules a dynamic
import of the Vision controller using `requestIdleCallback` or a timeout.

Classification: `ACTIVE DEFERRED ENHANCEMENT`.

This already resembles the accepted background-preparation direction, although
D5/H7 must still prove delayed/failed/reduced-motion behavior.

### Gallery enhancement

`welcome-depth-gallery.js` waits for an IntersectionObserver hit with a `100%`
vertical root margin, then dynamically imports the Gallery controller. The
controller has a second proximity observer (`35%` root margin) and only then
initializes the engine/Three runtime.

Classification: `ACTIVE PROXIMITY-LOADED ENHANCEMENT`.

This is a known architectural mismatch with the owner-accepted future loading
strategy: Gallery preparation should eventually participate in persistent
sequential background warming rather than being only proximity-triggered.
Do not change it before the relevant H2-H4 execution packet.

### Program journey

Program cards synchronously import Program formation/controller from the main
Homepage graph. `motion.js` loads GSAP from jsDelivr at runtime:

`gsap@3.7.1/dist/gsap.min.js`.

The Program controller has a reduced/failure fallback and its internal mount
returns cleanup functions. The current page bootstrap calls
`mountProgramFormation(root)` and `mountProgramJourney(root)` without retaining
returned cleanup handles.

Classification:

- Program controller: `ACTIVE OWNER`;
- GSAP CDN: `EXTERNAL RUNTIME DEPENDENCY`;
- discarded mount cleanup handle: `LIFECYCLE DEBT / PROOF REQUIRED`.

H4 must decide whether external GSAP remains intentional or is consolidated;
D7/H7 must prove repeated/BFCache lifecycle behavior before release.

## Graphics runtime ownership

### Gallery Three.js

Gallery owns a runtime loader with:

`https://cdn.jsdelivr.net/npm/three@0.183.0/build/three.module.min.js`

using `import(/* @vite-ignore */ ...)`.

Classification: `ACTIVE EXTERNAL THREE RUNTIME`.

### Values Three.js

`values/spatial-scene.js` statically imports package Three.js and addons. The
package graph currently declares `three ^0.185.1`.

`values/controller.js` currently sets:

`VALUES_SPATIAL_ENABLED = false`.

`values/spatial-controller.js` nevertheless contains a dynamic import of
`./spatial-scene.js`, so Vite still emits the spatial/Three chunk even though the
feature is currently disabled at runtime.

Classification:

- Values spatial feature: `RUNTIME DISABLED`;
- Values spatial chunk: `BUILD-EMITTED / CURRENTLY NOT ACTIVATED`;
- package Three runtime: `0.185.x graph`;
- Gallery Three runtime: `external 0.183.0`.

This explains the known successful-build warning around the approximately
549 kB `spatial-scene` chunk and proves that simply increasing Vite's warning
threshold would hide a real ownership problem.

H4 must converge on one deliberate Three/runtime/bundle strategy and decide
whether disabled Values spatial code should remain built, be conditionally
owned, or be removed only after product ownership is confirmed.

## RAF / observer / lifecycle map

Source audit finds multiple legitimate independent animation owners rather than
one accidental global RAF loop:

- Gallery controller/engine;
- Values story;
- Vision story;
- Program heading/formation;
- Article story;
- editorial headings/navigation and some PPDB behavior.

### Gallery

Current Gallery controller explicitly manages:

- `IntersectionObserver`;
- visibility change;
- reduced-motion change;
- `pagehide`/`pageshow`;
- activation RAF cancellation;
- engine stop/dispose;
- route-transition cleanup.

Classification: `ACTIVE OWNER / EXPLICIT LIFECYCLE`, with H2/H3 state-clock
hardening still pending.

### Values

`values/lifecycle.js` owns scroll/resize/pageshow/pagehide/visibility/
reduced-motion listeners plus IntersectionObserver and ResizeObserver, and
returns a cleanup that removes/disconnects all of them.

The Values controller suspends spatial work when hidden/offscreen and only
requests RAF while enabled, active, visible, and unsettled.

Classification: `ACTIVE OWNER / EXPLICIT LIFECYCLE`.

### Article

Article Story uses an IntersectionObserver plus scroll/resize/pageshow/
visibility listeners and RAF-on-demand rather than a permanent frame loop. It
returns complete listener/observer cleanup, but the module-level bootstrap calls
`mountArticleStory(articleStory)` without retaining the returned disposer.

Classification: `ACTIVE OWNER / CLEANUP AVAILABLE BUT NOT PAGE-OWNED`.

### Program

Program Journey also returns cleanup internally, while current page bootstrap
does not retain it.

Classification: `ACTIVE OWNER / CLEANUP AVAILABLE BUT NOT PAGE-OWNED`.

These discarded disposer handles do not prove a current leak on traditional
full-page navigation. They are release-risk evidence for repeated mount,
BFCache, future partial navigation, and lifecycle certification. H2/H7 should
prove behavior before deciding whether a page-level lifecycle registry is
necessary.

## Vite-entry caveat / dead-code classification

The Vite configuration includes several historical/specialized Homepage entries
that are not requested by `welcome.blade.php` itself. For example, code search
for `welcome-testimonial-story.js` finds the Vite config as its live build
reference but no current Homepage template request.

`resources/js/app.js` dynamically imports
`welcome-testimonial-extra-nodes.js`, but `app.js` itself is used by admin/
locked-account surfaces rather than the Homepage template.

Therefore D3 uses these categories:

- `ACTIVE TEMPLATE ENTRY`: explicitly requested by a rendered Blade surface;
- `ACTIVE IMPORTED MODULE`: reached from an active entry;
- `BUILD ENTRY ONLY`: declared in Vite but not yet proven requested by the
  current surface;
- `UNREFERENCED CANDIDATE`: neither entry nor importer according to structure
  proof;
- `RUNTIME DISABLED BUT BUILT`: build graph exists while feature flag prevents
  activation;
- `LEGACY/UNKNOWN`: deletion forbidden until runtime/route proof exists.

Codex must not equate `BUILD ENTRY ONLY` with safe deletion. It must first prove
whether another route/layout/admin surface requests it.

## D3 decisions for H2-H4/H7

- Do not perform broad CSS cleanup during H2/H3.
- Preserve aggregate CSS import order until source-equivalence ownership is
  intentionally repaired.
- Do not delete the three structure-reported unreferenced candidates without
  route/runtime proof.
- H4 must address the split Three ownership and the disabled-but-built Values
  spatial chunk with measurement, not warning suppression.
- H4 should also record the GSAP CDN runtime as an explicit dependency decision.
- The future accepted preparation scheduler may replace proximity-only Gallery
  preparation, but continuous offscreen rendering remains forbidden.
- Do not invent one giant global RAF scheduler merely because multiple animation
  owners exist. Consolidate only where measurement/lifecycle proof shows real
  duplicate work.
- Page-level disposer ownership is a targeted lifecycle question for Program and
  Article, not a mandate for framework-wide navigation infrastructure.

## D3 acceptance proof for later execution

Relevant capabilities must prove, as applicable:

- bundle/chunk composition before and after changes;
- no duplicate Three runtime/version on the Homepage;
- no invisible continuous RAF/WebGL/video work;
- listeners/observers/disposers do not accumulate across repeated lifecycle
  scenarios;
- hidden tab and BFCache suspend/resume behavior;
- reduced-motion fallback;
- fast/reverse scrolling without contradictory state;
- CSS import order and rendered semantics remain stable where aggregates are
  touched;
- `git diff --check`, `npm run check:structure`, `npm run build`, and tests are
  reported against the frozen baseline without hiding unrelated debt.

## D3 conclusion

The major runtime risk is not simply "too many files". It is mixed ownership:
legacy CSS aggregates plus newer bounded surface CSS, multiple independent but
mostly bounded lifecycle controllers, external GSAP, and two different Three.js
runtime strategies. The most concrete H4 hotspot is Values spatial code being
runtime-disabled while still producing the large package-Three build chunk.

No runtime source was changed during this inventory.
