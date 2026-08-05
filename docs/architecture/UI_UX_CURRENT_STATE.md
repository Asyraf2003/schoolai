# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-05
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-05-vision-mission-fast-entrance.md`
Source baseline before batch: `3387885eaf0d98d5accc8a7cc0f2f0afce0ffd2e`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VISION-004-ENTRANCE`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Vision/Mission `#visi-misi`
- Owner decision date: 2026-08-05
- Execution channel: Web AI direct GitHub `main`
- Goal: keep the current paper story and desktop composition while replacing
  typography scrub with a short, downward-entry effect.
- Protected: Hero, Values, Programs, Gallery, Articles, navigation, footer,
  About, Testimonial, locale data, routes, DB, authentication, dependencies, and
  Vision/Mission media assets.

## Verified source facts before execution

- `main` was `3387885eaf0d98d5accc8a7cc0f2f0afce0ffd2e` before the batch.
- Homepage renders one Vision/Mission semantic partial with one Vision article,
  one Mission article, three lazy images, and one program panel.
- `welcome-vision-story.js` defers the controller with Hero readiness,
  `requestIdleCallback`, and dynamic import.
- One controller owns IntersectionObserver, passive scroll/resize listeners,
  one bounded RAF smoother, measurement, and cleanup.
- The track changes from vertical through `1180px` to horizontal from `1181px`.
- Before this batch, Vision and Mission both used the same effect25-like
  `scaleY` grammar.
- Before this batch, typography was scrubbed by scroll progress; mobile Mission
  typography was tied to its later vertical-story progress.
- Before this batch, compact and responsive CSS both owned physical Mission
  alignment, producing right-aligned ID/EN and left-aligned AR at `<=1023px`.
- The previous ledger still described the earlier clean-slate state and was
  stale relative to production source.

## Owner-accepted decisions

- Vision uses deterministic word-level Set 2 model 12 (`effect27`) motion.
- Mission uses Set 2 model 10 (`effect25`) baseline reveal.
- Both entrances start together only when scrolling down from above and the
  section top crosses into the viewport.
- Upward entry shows final static copy without replay.
- The entrance is deliberately short; content never waits on scroll progress.
- Six global tiers remain unchanged; `1180/1181` stays a sub-boundary inside LG.
- Through `1180px`, Vision is centered and Mission uses logical start.
- Desktop composition from `1181px` is retained.
- No new dependency, controller, listener owner, breakpoint tier, or parallel
  locale implementation is introduced.

## Implemented source

- Replaced the shared typography progress API with explicit `reset`, `play`,
  and `finish` entrance states.
- Vision now uses deterministic bounded opacity/X/Y/Z/rotateX motion per word.
- Mission uses grouped grapheme reveal for ID/EN and complete-word reveal for AR.
- Added stable word wrappers so line composition remains structured.
- Removed typography from scroll-progress scrubbing while retaining the existing
  track, image swap, and compact Mission scene motion.
- Added downward crossing detection to the existing controller; upward movement
  immediately forces final typography state.
- Preserved dynamic import, image decoding/lazy attributes, one observer, one
  scroll listener, and one RAF owner.
- Consolidated vertical alignment into logical CSS through `1180px`; removed
  the conflicting physical alignment owner from compact CSS.
- Kept all changed source files within the enforced 200-line limit.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | branch checked before write |
| source scope isolation | `PASS_SOURCE` | only Vision/Mission owners and docs changed |
| JS syntax | `PASS_LOCAL_STATIC` | `node --check` on three changed JS modules |
| source line limit | `PASS_LOCAL_STATIC` | every changed source file <= 200 lines |
| semantic/no-JS fallback | `PASS_SOURCE` | Blade and locale content unchanged |
| lazy/controller ownership | `PASS_SOURCE` | existing loader and one controller retained |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| focused/full PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | deployed/runtime review required |
| deployed performance delta | `BLOCKED_BY_MISSING_EVIDENCE` | run after deployment |

## Prior Values state retained

The published Values implementation and its prior proof status remain unchanged.
This batch does not alter Values source, tests, layout, or progress.

## STATUS

The requested C+B+B Vision/Mission correction is implemented in production
source. Completion remains blocked until local structure/build/tests, full
responsive/locale browser review, and deployed performance comparison run.

## NEXT VALID STEP

Owner/local terminal: fast-forward pull the resulting `main` and run the exact
source proof block from the active blueprint without discarding unrelated local
work.
