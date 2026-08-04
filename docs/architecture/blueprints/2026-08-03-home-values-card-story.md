# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Owner: Asyraf Mubarak
Updated: 2026-08-04
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Source baseline: `087f2afac7b7b77dfcb62a27bb22c13b3cdc4da2`
Source implementation head: `fbbc83b6672053652ac4551aea3970b825df0fcc`
Revision baseline: `c24e4d73fd57659df9f18eeb732d3ec755031743`
Revision blueprint checkpoint: `e56b00a455848772905def3c3ab63016dd24c303`
Revision implementation head: `c1c45381d7b1824ab72ca7d6d85fed77889e58ad`
Focused correction baseline: `2e052a87a71d8c429358277d96c056998d4595b1`
Focused correction blueprint: `0521c9aa1641c8d547099f8a7c758f943a32a0ae`
Focused correction source head: `95b3b78f91d42d536881324eda6d453c7139a015`
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
scope. XS/SM use a natural one-column flow; MD/LG use a natural 2x2 grid; XL/
2XL use the cinematic four-column story. The exact owner-specified headings are
`PONDASI / KARAKTER`, `VALUES / STUDENTS`, and
`أَسَاسُ الْمَدْرَسَةِ`.

## Owner-accepted focused correction R3 — 2026-08-04

The owner's latest screenshots and correction are accepted as a narrow third
iteration inside the existing Values surface:

- keep both desktop title lines at the same logical inline start during reveal;
  line one rises from below, line two drops from above, then line two shifts
  toward inline-end; Arabic mirrors the inline shift only;
- remove the desktop heading dwell: it follows scroll upward from the first
  story progress while the cards remain the only held composition;
- simplify every front to index, summary, title, and body copy; remove the code
  tile, duplicate footer, top accent strip, and internal divider;
- replace the decorative back logo/orbit with a repeated Islamic geometric
  hexagonal field;
- after the desktop flips settle, give each card exactly three reversible
  vertical bounce cycles, then move the complete row upward; remove perpetual
  float animation;
- keep heading reveal on XS/SM/MD/LG and make responsive card motion a single
  smooth back-to-front `180deg -> 0deg` turn with no overshoot or return;
- scale card padding and typography from the card's own inline size so content
  remains readable on phone, tablet, and desktop; remove the small XL override;
- keep card chronology/direction neutral across LTR/RTL; only content direction
  changes.

R3 is published at `95b3b78f91d42d536881324eda6d453c7139a015` as an
11-file Values/test commit. Available proof passed JavaScript syntax, CSS parse,
PHP test-source parse, <=200-line owners, pure responsive `180/90/0deg` flip,
three desktop bounce cycles, immediate heading travel, intact desktop overlap,
and explicit upward exit.

The smoothness investigation is read-only in this batch. Live Lusion evidence
shows a fixed, native-scroll-locked UI and canvas under one virtual-scroll render
pipeline. SchoolAI retains native document scrolling plus surface-local RAF
controllers. No global scroll owner, other surface, dependency, canvas, or
WebGL pipeline may change in R3.

## Owner-accepted runtime correction — 2026-08-04

The owner-provided SchoolAI captures are runtime `FAIL` evidence for
`c24e4d73`. The prior source rebuild preserved the intended wrapper ownership
but normalized away several measured reference relationships and implemented
the wrong heading axis.

The accepted correction is atomic to Values and its owned transition layers:

- remove the visible eyebrow “Nilai yang Menjadi Arah Tumbuh Anak” and its EN/AR
  equivalents;
- reveal line one vertically from below and line two vertically from above;
  after reveal, shift line two toward the visual center on MD through 2XL only;
- hide description on XS/SM, place it below the title on MD and portrait LG,
  and at the logical side on landscape LG and XL/2XL; RTL mirrors only header
  composition;
- keep card order, geometry, flip direction, and chronology identical for LTR
  and RTL; only card content direction/language changes;
- use exact horizontal ratios: XS/SM `8/84/8%`; MD/LG
  `4.1667/43.75/4.1667/43.75/4.1667%`; XL+
  `5/21/2/21/2/21/2/21/5%`;
- use a card aspect of approximately `.717`, allowing height to follow width
  fluidly instead of hard-capping the card to one screenshot;
- use a height-driven perspective camera matching the revalidated
  `perspective = 100vh` relationship, with a safe short-height floor;
- make all four backs present from the first deck frame; overlap may make them
  read as one card, but no card may fade in from nothing;
- shorten the desktop timeline from `600-640vh` toward the revalidated
  approximately `376vh` story scale;
- start each desktop flip while the previous card is about `25-35%` through
  its turn, retain fan tilt through the edge-on phase, overshoot the front by
  about `18deg`, then settle at zero;
- on XS/SM/MD/LG, derive flip progress from the visible fraction of the card:
  begin when half the body is visible and settle when the full body is visible;
  paired cards use the same rotation direction and timing;
- increase bounded floating enough to read as suspension while keeping it on
  the dedicated float wrapper and out of layout measurement;
- never fade the final card row. It remains complete through sticky release and
  exits upward with normal section movement; reverse scroll restores it intact;
- complete the single background path before the exit transition and retain
  reversible geometry.

No card art, source code, asset, font, shader, or exact component structure from
Lusion is copied.

## Revision FACT and root-cause map

Current `c24e4d73` ownership remains correctly isolated: Blade owns one
localized semantic tree; the six Values CSS modules own treatment; one
controller plus the eight Values helpers own measurement and motion; the Vite
entry/import graph remains unchanged. The failure is inside those owners:

| Owner-visible failure | Current source fact | Root cause / replacement |
|---|---|---|
| cards appear late | ready state sets card opacity to zero; hidden poses also use zero opacity | keep every back opaque and reveal the deck through overlap/translation |
| cards disappear at the end | Grid opacity falls during `0.94-1` | remove group/card fading; let sticky release move the complete Grid |
| PC cards too narrow | field caps at `92rem`; card caps at `21rem`; gap caps at `1.6rem` | preserve the measured viewport ratios with real Grid columns |
| flip looks flat | perspective is width-driven `54-66rem` | use the measured height-driven camera and retain the preserve-3d chain |
| weak float | keyframes travel only `-3px..3px` | bounded `6-10px` compositor float on the dedicated wrapper |
| insufficient front overshoot | `FLIP_OVERSHOOT = -8` | use the observed approximately `-18deg` settle path |
| flip reads one-by-one | slow early cubic turn hides the nominal overlap | continuous curve reaches about 30–36deg when the next flip begins |
| phone/tablet flip timing drifts | range derives from whole-root progress and viewport offsets | calculate actual card visible fraction from cached slot geometry |
| heading enters from the sides | root writes line X variables and masks use `translateX` | line one uses positive Y travel; line two uses negative Y travel |
| description collides with title | side layout begins at 640px; desktop box is `29vw` at `19svh` | hide through SM, below-title MD, measured side box LG+ |
| unwanted small label | Blade still renders `.values-story__eyebrow` | remove its DOM and unused locale key |
| pacing feels late | desktop story is `600-640svh` | reduce to the measured approximately `376svh` narrative scale |

The final CSS Grid, semantic card articles, pose/float/flip wrapper separation,
fixed structural z-order, one SVG route, and single RAF controller are retained.
The opacity reveal/fade, capped viewport geometry, horizontal heading masks,
root-progress responsive flip, and shallow overshoot are replaced rather than
overridden.

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
| translate X/Y/Z, rotateZ, scale | pose wrapper variables |
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
| section enters | independent 900ms vertical heading reveal starts immediately |
| `0.00–0.16` | all four opaque backs rise as one legible centered deck |
| `0.12–0.36` | deck opens into a fixed-order fan around stage center |
| `0.30–0.42` | cards approach real Grid slots with fan tilt retained |
| `0.38` | card 1 flip begins |
| `0.45` | card 2 begins while card 1 has turned about 30–36deg |
| `0.52` | card 3 begins with the same overlap |
| `0.59` | card 4 begins with the same overlap |
| each flip | `rotateY 180 -> about -18 -> 0`; `rotateZ` holds through edge-on, then settles before the front pass |
| `0.86+` | upright Grid holds; trail continues toward completion |
| `0.96` | trail is complete before its exit fade |
| sticky release | complete opaque Grid and section move upward; no card fade |

Scroll motion uses callback delta time and a critically damped state. It
supports reverse and fast input without per-sample stops. Resize invalidates
geometry without blindly snapping an active animation; a tier-mode change
resolves to the current logical progress.

## Six-tier responsive contract

| Tier | Layout | Header/copy | Motion | Trail |
|---|---|---|---|---|
| XS `<640` | one column; `84vw` cards field | heading; description hidden | flip from 1/2 to full visibility | hidden |
| SM `640–767` | one column; `84vw` cards field | heading; description hidden | same-direction sequential flip | hidden |
| MD `768–1023` | 2x2; exact 1/10.5/1/10.5/1 ratio | description below heading; line two shifts modestly | same-direction paired flip | hidden |
| LG `1024–1279` | same centered 2x2 Grid | portrait copy below; landscape copy at logical side | same-direction paired flip; no deck/fan | hidden |
| XL `1280–1535` | centered four-column Grid | full side-copy sequence | stack/fan/flip/settle | visible |
| 2XL `>=1536` | bounded four-column Grid | bounded editorial scale | shared cinematic sequence | visible |

XL+ uses the exact measured 5/21/2/21/2/21/2/21/5 viewport ratio. All card
heights follow the shared aspect and available width. XS/SM/MD/LG stay in
natural document flow rather than using a desktop pinning model.

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

- The failed baseline is `c24e4d73fd57659df9f18eeb732d3ec755031743`.
- The revalidated correction blueprint was published at `7b625935c555d379cb0a455bcb1164c682123856`;
  its tablet/desktop tier clarification was published at
  `e56b00a455848772905def3c3ab63016dd24c303`.
- The bounded 16-file revision was published atomically at
  `c1c45381d7b1824ab72ca7d6d85fed77889e58ad` by non-force fast-forward.
- Every Values JavaScript module passed `node --check`; the Values CSS entry
  bundled with Lightning CSS; ID/EN/AR lang and the focused Pest source parsed
  with an independent PHP parser; every Values source owner remains <=200 lines.
- Source-model proof records exact six-tier Grid ratios, natural near/far 3D
  projection, 34.6deg inter-card overlap, -18deg front overshoot, opposite Y
  heading masks, 50-100% visible-fraction responsive flips, and no card-opacity
  exit property.
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


## Focused correction R3 execution result

- The owner-accepted checkpoint is
  `0521c9aa1641c8d547099f8a7c758f943a32a0ae`.
- The bounded source commit is
  `95b3b78f91d42d536881324eda6d453c7139a015`, advanced by non-force
  fast-forward.
- Exactly 11 files changed: one Blade partial, four Values CSS owners, five
  Values JavaScript owners, and one focused feature test.
- No locale copy, route, dependency, Vite entry, protected surface, or global
  scroll controller changed.
- Full checkout build/PHP/browser proof remains
  `BLOCKED_BY_MISSING_EVIDENCE`.
