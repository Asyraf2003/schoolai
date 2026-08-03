# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Owner: Asyraf Mubarak
Updated: 2026-08-04
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Source baseline: `087f2afac7b7b77dfcb62a27bb22c13b3cdc4da2`
Source implementation head: `fbbc83b6672053652ac4551aea3970b825df0fcc`
Active route/surface: homepage `#nilai`
Raw reference: `../measurements/2026-08-03-home-values-reference-motion-raw.md`
Execution channel: Web AI with explicit direct-`main` authorization

## Owner goal and accepted direction

Rebuild the Values section as an Al Mustaqbal editorial card story informed by
the supplied Lusion motion evidence, without copying Lusion code, assets,
branding, card art, shaders, or type files.

The accepted result is:

```text
green mission fade/blur -> blue Values field
-> immediate two-line masked heading entrance from the center
-> centered desktop deck -> stable fan -> overlapping perspective flip
-> upright CSS Grid row -> completed continuous trail
-> blue fade/blur -> white -> following Programs section
```

Desktop uses semantic DOM, CSS 3D, and one JavaScript orchestrator. Three.js,
WebGL, canvas text, new dependencies, and parallel locale/tier DOMs are out of
scope. XS uses a natural one-column flow; SM/MD use a natural 2x2 grid; LG/XL/
2XL use the cinematic four-column story. The exact owner-specified headings are
`PONDASI / KARAKTER`, `VALUES / STUDENTS`, and
`أَسَاسُ الْمَدْرَسَةِ`.

## FACT — inspected source baseline

- `welcome.blade.php` renders Vision/Mission, Values, then Programs and loads
  the Values CSS and `welcome.js` entry explicitly through Vite.
- `welcome.js` imports one Values controller.
- `school-values.blade.php` renders one semantic `h2` and four `article` cards
  from `nilai_sekolah` lang data.
- The current Blade hard-codes a different heading per locale instead of using
  the accepted heading copy from lang files.
- `welcome-values-story.css` is the Values CSS owner entry and imports six
  bounded surface modules in a stable order.
- In enhanced mode, `.values-story__cards` changes from CSS Grid to
  `display:block`; every `.values-card` becomes `position:absolute` at
  `left:50%`.
- `desktop-keyframes.js` calculates manual X slots, while `desktop-layout.js`
  calculates Y from `geometry.viewportHeight` ratios.
- The CSS sticky stage height is `calc(100svh - var(--nav-h))`, while the
  controller measures `window.innerHeight` and calls it `viewportHeight`.
- The current sticky stage owns both `overflow:hidden/clip` and `perspective`.
- Card translation/fan/scale live on the article; flip lives on the inner
  wrapper; float is separate. The final layout slot itself is not preserved
  during enhanced motion.
- JavaScript writes a fixed `20 - index` z-index every frame. The current source
  does not dynamically swap z-order; the repeated write is redundant.
- The trail is three copies of one SVG path. Its apparent head is a dashed path,
  not a point measured on the continuous route.
- The current source has an entry overlay but no owned Values-to-Programs exit
  layer.
- Current Values translations contain abbreviated or non-accepted Rasulullah
  honorific forms.
- ID/EN use the loaded variable Inter family; AR uses locally bundled Cairo.
- `text-system.css` and locale typography entries load after the Values entry;
  equal-specificity component type rules can lose to semantic-role rules.
- Reduced motion and unsupported CSS 3D retain semantic content, but the current
  reduced-motion rules do not explicitly restore every enhanced absolute owner.
- The legacy `.nilai-*` selectors in `welcome.css` no longer match the active
  Values DOM. They are an inactive fossil protected by the source-equivalence
  manifest, not an active runtime owner for this batch.
- Programs begins with a very light green background. The Values exit must pass
  through white and meet that color without a horizontal seam.

## GAP — evidence not available in this execution channel

- `GAP-VALUES-RUNTIME-001`: no corrected Chromium capture for the resulting
  source.
- `GAP-VALUES-WEBKIT-001`: no Safari/WebKit runtime is available here.
- `GAP-VALUES-MATRIX-001`: six-tier ID/EN/AR, reverse/fast scroll, resize,
  reload-near-section, reduced-motion, zoom, and accessibility runtime proof is
  absent.
- `GAP-VALUES-BUILD-001`: the connector does not provide a repository checkout,
  so full structure/build/PHP commands cannot run in this channel.

These gaps do not block the owner-accepted source rebuild. They do block
`PROVEN` and any runtime `PASS` claim.

## Source ownership map

| Concern | Baseline owner | Target owner |
|---|---|---|
| semantic heading/cards | `school-values.blade.php` | same bounded partial |
| localized copy | `lang/{id,en,ar}/home.php` | same `nilai_sekolah` arrays |
| entry/timeline/clip/exit | `story-shell.css` | same module, separated layers |
| heading masks/composition | `story-heading.css` | same module |
| final layout/card treatment | `story-cards.css` | CSS Grid remains source of truth |
| card-back art | `story-card-back.css` | same decorative-only module |
| route/tier/reduced styles | `story-responsive.css` | same module |
| one continuous trail | `story-trail.css` + Blade paths | one path + one measured head |
| lifecycle/RAF | `controller.js` | same single controller |
| stage/slot measurement | controller-local reads | `geometry.js` |
| motion integration | `motion.js` | delta-time critically damped motion |
| desktop pose | `desktop-layout.js` | transforms relative to real Grid slots |
| flip/fan curve | `desktop-keyframes.js` | named semantic curve, no device slots |
| responsive pose | `layout.js` | natural one-column/2x2 entrance/flip |
| CSS variable writes | `paint.js` | pose/flip/trail owners only |
| heading entrance state | `heading-state.js` | time-based, direction-aware mask state |
| Vite/import graph | existing entries | unchanged |

## Current transform hierarchy and root causes

```text
sticky stage [clip + perspective]
-> cards container [Grid disabled when ready]
-> absolute article [manual X/Y/Z + rotateX/Z + scale]
-> float wrapper [CSS keyframes]
-> inner [rotateY]
-> front/back
```

| Reported issue | Audit result and root cause |
|---|---|
| stage/JS center mismatch | confirmed: `100svh - nav` versus `innerHeight` |
| guessed vertical center | confirmed: `viewportHeight * ratio` |
| absolute final layout | confirmed: enhanced Grid is replaced by block/absolute slots |
| clip/perspective coupling | confirmed on the same sticky owner |
| transform ownership mixing | confirmed at layout/pose boundary; final slot is transformed away |
| width-compression appearance | prior runtime failed; current source correction is still unproven |
| natural near/far projection | unproven; shared camera exists but remains inside clip owner |
| fan remains during front face | not structurally explicit; raw sampled curves implicitly drive both |
| z-order jump | not confirmed in current source; order is fixed but redundantly repainted |
| cards start too low | still possible because anchors use viewport ratios, not stage geometry |
| late/overlapping heading spawn | confirmed: old copy and vertical masks share one horizontal origin |
| trail ends early | runtime unproven; source reaches completion only at the final fade window |
| hard exit seam | confirmed: no Values-owned exit layer exists |
| weak card hierarchy | source has parts, but footer is inverted and body reads as a text block |
| reverse/fast/resize stability | unproven; resize currently snaps scroll state |

## Legacy removal/isolation decision

Replace, do not layer over:

- enhanced `display:block`/absolute card layout;
- `left:50%` and manual `rowSlot()` final positions;
- viewport-ratio card centers;
- perspective on the clipping shell;
- repeated per-frame z-index writes;
- three duplicate trail paths and the dashed fake head;
- hard-coded locale heading match in Blade;
- implicit sampled fan/flip coupling and resize snap;
- external entry overlay without an owned exit counterpart.

Preserve the raw measurement document. Preserve inactive `.nilai-*` fossils in
the equivalence-managed global cascade because deleting them is a separate
global migration and they do not match the active DOM.

## Target hierarchy and transform ownership

```text
section shell
├── entry transition layer
├── timeline
│   └── sticky clip shell
│       ├── heading layer
│       ├── one-path trail layer
│       └── perspective stage
│           └── final CSS Grid
│               └── semantic card slot (never transformed)
│                   └── pose wrapper (X/Y/Z + rotateZ + scale)
│                       └── float wrapper (tiny vertical float)
│                           └── flip wrapper (rotateY only)
│                               ├── front face
│                               └── decorative back face
└── exit transition layer
```

| Transform/property | Sole owner |
|---|---|
| final columns, gaps, card slot | CSS Grid/card article |
| clipping/sticky | sticky clip shell |
| perspective/origin | perspective stage |
| translate X/Y/Z, rotateZ, scale, opacity | pose wrapper variables |
| float Y | float wrapper keyframes |
| rotateY | flip wrapper variable |
| face orientation/visibility | front/back faces |
| heading mask travel | title-text wrappers |
| heading scroll exit | heading block |
| line drawing/head | one SVG path + circle |
| entry/exit blur/fade | sibling transition layers |

The controller measures the sticky stage and untransformed semantic card slots
with `getBoundingClientRect()`. Since transforms move a child pose wrapper, the
article rectangles remain the true CSS Grid targets. Final pose values are zero;
JavaScript cannot become the final layout source.

## Centering contract

```text
cardsCenterX = cardsUnionRect.left + cardsUnionRect.width / 2
cardsCenterY = cardsUnionRect.top + cardsUnionRect.height / 2
stageCenterX = stageRect.left + stageRect.width / 2
stageCenterY = stageRect.top + stageRect.height / 2
```

Desktop target: horizontal delta <=8px and vertical delta <=12px, or <=2% of
the corresponding stage dimension. Stack and fan offsets derive from the real
stage center, card slot centers, and card dimensions. No viewport percentage is
the visual center.

## Storyboard and measured timeline

| Progress/state | Result |
|---:|---|
| section enters | independent 900ms heading reveal starts immediately |
| `0.05–0.18` | all four backs form one legible centered deck |
| `0.18–0.38` | deck opens into a fixed-order fan around stage center |
| `0.34–0.46` | cards approach real Grid slots with fan tilt retained |
| `0.44` | card 1 flip begins |
| `0.515` | card 2 begins at about 27% of card 1 duration |
| `0.590` | card 3 begins at about 27% of card 2 duration |
| `0.665` | card 4 begins at about 27% of card 3 duration |
| each flip | `rotateY 180 -> slight front overshoot -> 0`; `rotateZ -> 0` |
| `0.90+` | upright Grid holds; trail is complete before fading |
| `0.94–1` | cards/header/trail leave before the white exit layer |

Scroll motion uses callback delta time and a critically damped state. It
supports reverse and fast input without per-sample stops. Resize invalidates
geometry without blindly snapping an active animation; a tier-mode change
resolves to the current logical progress.

## Six-tier responsive contract

| Tier | Layout | Header/copy | Motion | Trail |
|---|---|---|---|---|
| XS `<640` | natural one-column 1-1-1-1 | heading; description hidden | simple per-card entrance/flip | hidden |
| SM `640–767` | natural 2x2 Grid | heading + copy when space allows | row/pair overlap, no stack/fan | hidden |
| MD `768–1023` | natural 2x2 Grid | heading + copy | row/pair overlap, no stack/fan | hidden |
| LG `1024–1279` | centered four-column Grid | full sequence | stack/fan/flip/settle | visible |
| XL `1280–1535` | centered four-column Grid | full sequence | full cinematic sequence | visible |
| 2XL `>=1536` | bounded four-column Grid | bounded editorial scale | shared cinematic sequence | visible |

All gaps, padding, card widths, and heading sizes remain fluid with intrinsic
Grid and `clamp()`. XS/SM/MD stay in natural document flow rather than using a
desktop pinning model.

## Locale/direction contract

| Locale | Heading | Direction/composition | Honorific form |
|---|---|---|---|
| ID | `PONDASI / KARAKTER` | Inter/LTR; copy right/right-aligned | `Rasulullah shallallahu ‘alaihi wasallam` |
| EN | `VALUES / STUDENTS` | Inter/LTR; copy right/right-aligned | `the Messenger of Allah, peace and blessings be upon him` |
| AR | `أَسَاسُ / الْمَدْرَسَةِ` | Cairo/RTL; copy left/left-aligned | `رَسُولُ اللهِ صَلَّى اللهُ عَلَيْهِ وَسَلَّمَ` |

Arabic is split only at a word boundary for the two line masks; no character
splitting is allowed. The semantic `aria-label` preserves the exact complete
Arabic heading. Neutral vertical scroll and card flip time do not reverse for
RTL; center-origin heading directions and logical line-two settlement mirror.

## Semantic, accessibility, browser, and fallback contract

- One `h2`, one list, and four `article` values remain server rendered.
- Front content remains text DOM; backs and trail are decorative.
- No-JS, unsupported 3D, and reduced-motion states show the readable front Grid.
- Reduced motion removes sticky storytelling, line motion, float, fan, and
  complex flip.
- CSS feature detection gates the enhanced path; no user-agent fork exists.
- Chromium and WebKit must prove sticky/clip, CSS 3D/backface, SVG geometry,
  variable Inter/Cairo shaping, RTL, and BFCache behavior.
- No keyboard target or focus order changes. Cards remain non-interactive
  articles.

## Performance contract

- No dependency, asset, font, renderer, canvas, or additional animation loop.
- One RAF scheduler; layout is read in one measure pass and written afterward.
- Final slot geometry is cached until resize/re-entry; no per-card layout read
  occurs inside the animation loop.
- Motion uses transform/opacity. Entry/exit blur is bounded to transition
  layers and removed for reduced motion.
- The SVG route length is cached; only one point lookup is used for its head on
  desktop frames.

## Proof plan

Static/source:

```text
node --check on every changed Values JS module
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeValuesStoryTest
php artisan test
```

Runtime: Chromium and WebKit; ID/EN/AR; 360, 390, 640, 768, 1024, 1280,
1440, 1536, and 1920; boundary pairs; normal/reverse/fast scroll; active resize;
reload near section; reduced motion; 200% zoom; short height; keyboard and touch.

Capture: pre-entry, completed heading, deck, early/late fan, first 90-degree
flip, card 1/2 overlap, front face, final Grid, completed trail, and white exit.
Measure stage/card-union center in every tier. An unavailable gate remains
`BLOCKED_BY_MISSING_EVIDENCE`.

## Rollback plan

Rollback is one revert of the resulting Values rebuild commit(s). The Vite
entry, route composition, controller import, translation keys, and raw evidence
paths remain stable, so rollback does not require reconstructing deleted global
source. Protected sections are outside the patch.

## Execution result

- The accepted audit/blueprint was published at `48830e93645dc0d5a681a89546f4dc8bc1941dab`.
- The bounded 19-file source rebuild was published atomically at
  `fbbc83b6672053652ac4551aea3970b825df0fcc`.
- Changed JavaScript passed `node --check`; all six changed CSS modules parsed
  with Lightning CSS; changed enforced-root files remained at or below 200
  lines (`lang/` is outside the checker roots).
- Full checkout, build, PHP, browser, responsive, lifecycle, accessibility, and
  performance proof remains `BLOCKED_BY_MISSING_EVIDENCE`.

## Execution plan

1. `COMPLETE`: publish the inspected owner-accepted audit/blueprint.
2. `COMPLETE`: rebuild only the mapped Values owners and focused test.
3. `COMPLETE`: run available local syntax/source proof and record unavailable
   checkout/browser gates honestly.
4. `COMPLETE`: fast-forward `main`, verify the source SHA, and update the durable
   progress ledger/handoff.
5. `PENDING`: run checkout/browser proof and promote only when every acceptance
   gate has recorded evidence.
