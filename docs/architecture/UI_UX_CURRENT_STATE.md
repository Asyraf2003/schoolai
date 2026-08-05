# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-05
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-05-vision-mission-compact-rhythm.md`
Source baseline before batch: `bc6e3d1819edfc69e3f4aefa20a3d8409d86883e`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VISION-007-COMPACT-RHYTHM`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Vision/Mission `#visi-misi`
- Goal: replace compact full-screen copy stages with a close intrinsic stack and
  retain a controlled scroll lift.
- Protected: desktop horizontal composition, Blade, locale copy, media assets,
  Hero, Values, Programs source, Gallery, Articles, navigation, footer, routes,
  DB, authentication, and dependencies.

## Owner-accepted decisions

- Through `1180px`, Vision and Mission are both centered in ID, EN, and AR.
- Vision, Mission, image, and Program title use final gaps around `2-3svh`.
- Vision and Mission no longer receive `100svh` each.
- The Program section no longer reserves another full viewport in compact mode.
- Vision, Mission, and Program title begin about `10svh` lower and settle when
  their trigger title reaches viewport center.
- Motion uses transform and opacity; final document geometry never animates.
- Upward entry remains final.
- Image 2-3 keeps its bottom-to-center swap.
- Desktop from `1181px` remains unchanged.

## Implemented source

- Compact scene now uses an intrinsic grid with one bounded `2.5svh` rhythm token.
- Vision and Mission copy blocks use content height instead of `100svh`.
- The image follows Mission directly; Program follows the image directly.
- Compact Program height is intrinsic and retains a bounded bottom exit space.
- Mission alignment is centered for LTR and RTL compact layouts.
- Added one local geometry helper for range calculation and paused WAAPI control.
- Vision, Mission, and Program title use a bounded `56-104px` upward lift.
- Compact Vision and Mission typography follow their own title ranges.
- Wide entry remains a short combined play-on-entry effect.
- One controller, one observer, one scroll listener, one RAF, native WAAPI,
  dynamic import, and the six global tiers remain.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | branch checked before write |
| source scope isolation | `PASS_SOURCE` | compact Vision owners and docs only |
| JS syntax | `PASS_LOCAL_STATIC` | changed/new modules parsed with Node |
| CSS brace balance | `PASS_LOCAL_STATIC` | changed CSS owners checked |
| source line limit | `PASS_LOCAL_STATIC` | changed source files <= 200 lines |
| semantic/reduced fallback | `PASS_SOURCE` | close layout is CSS-first |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| focused/full PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | runtime review required |
| deployed performance delta | `BLOCKED_BY_MISSING_EVIDENCE` | deployment proof required |

## Out-of-scope failures retained

- `Admin/GalleryItemSoftDeleteTest`
- `HomeValuesStoryTest`

No Gallery or Values source/test owner changed.

## STATUS

The compact rhythm and scroll-lift source correction is implemented. Completion
remains blocked by checkout build/tests and responsive browser proof.

## NEXT VALID STEP

Owner/local terminal: fast-forward `main`, run the proof block, then review
compact spacing and lift at 390x844, 759x924, 1036x924, and 1180 short-height.
