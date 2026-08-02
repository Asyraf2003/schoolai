# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Source main before batch: `3e0b6ea36b5989a88202c51ee4d063a1d3c3259b`
Published implementation main: `50f639782761249211fcbef77695cdac7a7a9c53`

Commit publication proves source state only. It does not prove rendering,
responsive parity, accessibility, performance, or browser lifecycle.

## Active production batch

Blueprint: `blueprints/2026-08-03-vision-effect25-scroll.md`

- ID: `HOME-VISION-002`
- State: `IMPLEMENTING`
- Surface: homepage Vision/Mission only
- Reference: Codrops On-Scroll Typography Animations, Set 2, effect 25
- Protected: Hero, Testimonial, School Values, Programs, Gallery, Articles,
  navigation, translations, authentication, database, dependencies, and assets

## Current FACT and DECISION

- Owner feedback removes the complete About surface from homepage composition.
- About partial, dedicated CSS, mixed responsive CSS, Vite entry, and focused
  About test are removed; assets `9.png` through `12.png` remain untouched but
  are no longer loaded by the homepage.
- Vision/Mission is the sole scroll-typography story.
- Opening, vision, bridge, and all four mission scenes use one effect grammar:
  glyphs grow vertically from `scaleY(0)` to `scaleY(1)` while their scene is
  held in a sticky viewport.
- Existing semantic key phrases preserve blue, orange, green, and purple color
  emphasis.
- ID and EN animate Unicode characters sequentially.
- Arabic animates complete words sequentially; isolated Arabic letters are
  never generated, so joining and shaping remain intact.
- One native requestAnimationFrame controller owns scroll, resize, reduced
  motion, pagehide, and BFCache lifecycle. It updates only scenes whose progress
  changes and restores static text when disposed.
- No JS/failure state remains complete server-rendered text. Reduced motion
  removes long scene travel, sticky positioning, and transforms.
- One semantic DOM serves the six width tiers. Scene travel and page padding
  adapt at 640, 768, 1024, 1280, and 1536px.
- No GSAP, ScrollTrigger, Lenis, Splitting, WebGL, external font, package, or
  translation change is introduced.

## Source proof status

| Gate | Status | Evidence |
|---|---|---|
| Scope diff | `PASS` | connector compare contains only Vision/Mission, About retirement, entries, focused test, and architecture docs |
| About runtime owners | `PASS_SOURCE` | partial and active CSS/Vite/test owners removed from published diff |
| One motion grammar | `PASS_SOURCE` | every rendered story text declares `data-story-effect="stretch"`; rise/fan/focus removed |
| Arabic shaping guard | `PASS_SOURCE` | locale adapter segments Arabic on whitespace into complete words |
| Six-tier source contract | `PASS_SOURCE` | base XS plus 640/768/1024/1280/1536 boundaries in dedicated CSS |
| No-JS/disposal fallback | `PASS_SOURCE` | transform activation is gated by `is-story-ready`; destroy removes inline transforms and progress |
| Source file line limit | `PASS_SOURCE` | every added/replaced active source file remains below 200 lines by inspection |
| Publication | `PASS` | `main` fast-forwarded without force to `50f639782761249211fcbef77695cdac7a7a9c53` |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector channel cannot run repository command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not executed in connector channel |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not executed in connector channel |
| Focused PHP test | `BLOCKED_BY_MISSING_EVIDENCE` | contract updated but not executed in connector channel |
| Full PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | not executed in connector channel |
| Chromium/WebKit runtime | `BLOCKED_BY_MISSING_EVIDENCE` | no browser run yet |
| Lighthouse/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | no comparable runtime run yet |

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| V01 owner correction | `PASS` | explicit request removes About and selects effect 25 for all Vision/Mission scenes |
| V02 blueprint | `PASS` | `HOME-VISION-002` records one surface and one motion grammar |
| V03 About retirement | `PASS_SOURCE` | render/include/CSS/Vite/test owners removed |
| V04 semantic Vision/Mission rebuild | `IMPLEMENTED_SOURCE` | seven sticky editorial beats with marked phrase colors |
| V05 locale adapter | `IMPLEMENTED_SOURCE` | ID/EN characters and Arabic complete words |
| V06 six-tier layout | `IMPLEMENTED_SOURCE` | fluid type plus five declared width boundaries |
| V07 lifecycle/performance bound | `IMPLEMENTED_SOURCE` | one RAF, progress cache, static disposal, reduced-motion result |
| V08 automated proof | `BLOCKED_BY_MISSING_EVIDENCE` | repository commands have not run |
| V09 runtime matrix | `BLOCKED_BY_MISSING_EVIDENCE` | tiers/locales/engines/reduced motion untested |
| V10 publication | `PASS` | direct non-force fast-forward to published implementation SHA |

## STATUS

The corrected production source is published on `main`. About is removed and
Vision/Mission is now an effect25-only scroll story. Build, test, browser,
accessibility, and PageSpeed status remain blocked until actual gates run.

## NEXT VALID STEP

Pull current `main`, then run `git diff --check`, `npm run check:structure`,
`npm run build`, and `php artisan test`. Report the first failing gate verbatim;
do not begin visual tuning until automated source/build status is known.
