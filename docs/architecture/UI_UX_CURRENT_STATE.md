# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-04
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-04-vision-mission-clean-slate.md`
Source baseline: `e42308fad7f6d7e48aa1e109eacec1d49947d75f`
Cross-surface audit: `measurements/2026-08-04-home-motion-smoothness-source-audit.md`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VISION-003-CLEAN`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Vision/Mission `#visi-misi`
- Owner decision date: 2026-08-04
- Execution channel: Web AI direct GitHub `main`
- Goal: remove the complete old Vision/Mission motion ownership before a new
  scroll-linked WAAPI surface is designed.
- Protected: Hero behavior/content, Values, Programs, Gallery, Articles,
  navigation, footer, DB/admin/routes, About, Testimonial, locale data, and
  `public/media/home/9.png` through `12.png`.

## Owner-provided audit FACT

The owner ran a read-only local audit at source baseline `e42308fad`.

- local `main` and `origin/main` matched the baseline before publication;
- two unrelated local seeder files were modified and remain outside this batch;
- the old Vision system had one dedicated CSS entry and one JavaScript entry;
- the JavaScript entry initialized at DOM ready;
- the controller owned an RAF, IntersectionObserver, ResizeObserver, smoothing,
  scene measurement, text splitting, and per-frame painting;
- Latin content was split into characters; Arabic content was split into words;
- the Blade rendered one Vision scene, one bridge, four Mission scenes, and
  eight artwork nodes;
- stale legacy Vision selectors also remained in mechanically split homepage
  CSS, the Arabic typography adapter, and the generic reveal controller;
- the four retained PNG assets are tracked, valid `1600x2000` RGBA images.

The uploaded raw CLI report is conversation evidence. Durable product decisions
and resulting source state are recorded in the active blueprint and this ledger.

## Execution

The cleanup publication:

- replaced the animated Blade scene graph with one server-rendered semantic
  section containing an H2, one Vision article, and four ordered Mission items;
- preserved ID, EN, and AR content from the existing locale source;
- preserved semantic emphasis and the full Arabic honorific;
- removed the old Vision CSS/JS entries from homepage Blade and Vite;
- deleted the dedicated scroll stylesheet, entry, controller, scroll progress,
  scene renderer, motion painters, and text splitter;
- removed stale Vision rules from the generic homepage reveal controller;
- removed dormant legacy Vision/Mission selectors from the mechanically split
  homepage CSS and Arabic typography selector lists;
- replaced the focused feature test with assertions for static semantics,
  four Mission items, Arabic honorific output, and absence of old story hooks;
- left two mechanically protected CSS import slots as harmless comments instead
  of changing the 47-file import structure without structure proof.

No WAAPI, smooth-scroll package, virtual scroll, WebGL, new image load, section
reordering, or page-wide scheduler was added.

## Deferred WAAPI contract

The next Vision blueprint must use progressive enhancement:

1. Hero and semantic content remain on the initial critical path.
2. Vision enhancement code is dynamically imported only after Hero presentation.
3. Preparation should use an idle window with a bounded fallback timeout.
4. Media decoding and geometry preparation happen before proximity activation.
5. One paused WAAPI master timeline follows native scroll progress.
6. Frequent motion is limited to wrapper `transform` and `opacity`.
7. Reduced motion and failure retain the complete static section.
8. No deleted story controller or parallel Vision RAF may return.

The exact Hero-ready signal is still a blueprint decision. Current source exposes
`hero:slide-active`; it does not yet expose a dedicated first-presentation-ready
event.

## Prior Values state retained

The prior focused Values implementation remains published in ancestry:

- accepted revision blueprint checkpoint: `e56b00a455848772905def3c3ab63016dd24c303`;
- focused correction blueprint: `0521c9aa1641c8d547099f8a7c758f943a32a0ae`;
- focused Values source: `95b3b78f91d42d536881324eda6d453c7139a015`;
- source ancestry clarification: `7eddaeb3259cfb1ae3d11eabee69487fb599d819`.

Values runtime, browser, performance, and accessibility status remain unchanged
and unpromoted by this Vision cleanup.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| owner baseline audit | `PASS_SOURCE` | local report at `e42308fad`; remote/local matched before write |
| scope isolation | `PASS_SOURCE` | no seeder, DB, route, locale, Hero, Values, Program, Gallery, or asset edit |
| semantic fallback source | `PASS_SOURCE` | one section, one Vision article, four Mission items |
| old entry removal | `PASS_SOURCE` | homepage and Vite no longer load old CSS/JS entries |
| old dedicated owners | `PASS_SOURCE` | dedicated stylesheet and story modules deleted |
| stale reveal/legacy selectors | `PASS_SOURCE` | known active and dormant selectors removed from inspected owners |
| focused test source | `PASS_SOURCE` | static/locale/Arabic/no-legacy assertions published |
| assets and locale data | `PASS_SOURCE` | retained without mutation |
| source line limit | `PASS_SOURCE` | new Blade, test, and blueprint remain below 200 lines |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run checkout command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| focused/full PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | static source not rendered in this channel |
| accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | not run |

## STATUS

The old Vision/Mission runtime is removed from production source and replaced by
a complete static semantic fallback. The clean-slate source goal is implemented;
completion remains blocked until local structure/build/tests and browser review
run against the resulting `main`.

## NEXT VALID STEP

Owner/local terminal: pull current `main` without discarding the two unrelated
modified seeder files, then run the exact proof block from the active blueprint.
Do not begin the WAAPI build until the cleanup structure, build, and focused/full
tests pass.
