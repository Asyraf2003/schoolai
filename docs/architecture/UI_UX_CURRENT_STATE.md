# UI/UX Engineering — Current State and Progress Ledger

Status: `BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-03-home-values-card-story.md`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VALUES-001`
- State: `IMPLEMENTING`
- Surface: homepage Values section `#nilai`
- Owner goal: Lusion-informed four-card scroll story using Al Mustaqbal content
- Protected: Hero, Vision/Mission, Programs, Gallery, Articles, navigation,
  footer, DB/admin/routes, disabled About, and disabled Testimonial

## Owner-accepted reference contract

The owner accepted this SchoolAI translation:

- blue full-viewport field with oversized localized Values heading;
- four readable white cards using current school value content;
- sequential right-to-left Y-axis flips;
- school-owned geometric line backs, not copied reference art;
- fan, centered stack, and final departure;
- six responsive tiers from 360px;
- one shared UI/motion meaning for ID/EN/AR and LTR/RTL.

## Runtime feedback FACT

Owner screenshots from local `127.0.0.1:8000` on a 1920x1080
Brave/Chromium desktop prove the first published implementation failed its
intended sticky composition:

- the card stage moved upward beneath the fixed navbar;
- cards were small and partially transparent;
- the heading did not enter during the captured journey;
- a long empty blue area remained after the stage moved away.

This is a runtime `FAIL` for the first published motion result. It does not prove
the corrected patch.

## Root-cause FACT

- `.values-story` used `overflow: hidden`.
- Its child `.values-story__stage` used `position: sticky`.
- The overflow ancestor became the sticky containing scroll context, so the
  stage travelled with the page instead of remaining pinned.
- Card entry opacity lasted through 9% of a `900svh` desktop timeline, creating
  almost one viewport of weak/empty composition.
- Sticky release and the card exit both completed at progress `1`, allowing the
  stage to unpin before inertial exit painting had visibly settled.

## Corrected source contract

Correction source head before this ledger update:
`a66a04c91a4f40ff57430734b5d2ab9ed6e07e2c`.

- Values root now uses `overflow: clip`; static fallback retains hidden overflow.
- Motion activation now requires `overflow: clip`, sticky, and preserve-3d
  support. Unsupported engines retain the semantic static grid.
- Sticky stage starts below `--nav-h` and fills the remaining viewport.
- Desktop travel is reduced from `820–900svh` to `500–520svh`.
- Card targets are enlarged across all six tiers.
- Initial cards begin at `0.88` opacity and settle within 2.5% progress.
- Sequential flip starts earlier; fan, stack, heading, and exit phases are
  compressed into a continuous journey.
- Progress reaches `1` with a `0.75` viewport release hold so inertial exit can
  settle before the sticky stage unpins.
- No DOM, locale data, routes, DB, WebGL, media, or unrelated section changed.

## Six-tier source contract

| Tier | Source composition |
|---|---|
| XS 360–639 | large readable stack, compact fan, `600svh` |
| SM 640–767 | 2x2 fronts/fallback, `580svh` |
| MD 768–1023 | larger 2x2 field, `560svh` |
| LG 1024–1279 | four cards in one row, `520svh` |
| XL 1280–1535 | enlarged cinematic row, `500svh` |
| 2XL >=1536 | bounded cards on expanded field, `520svh` |

Navigation source is untouched, so 1180/1181 remains outside the changed
surface proof.

## Locale/direction source contract

- One DOM, controller, timeline, and physical card order serves ID, EN, and AR.
- Locale changes copy, `lang`, `dir`, family, and natural text alignment only.
- Neutral Y-axis flips, vertical time, fan order, and stack order stay shared.
- No mixed-language duplicate DOM or locale-specific component fork exists.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Mandatory docs/source audit | `PASS_SOURCE` | current owners inspected |
| Owner art direction | `PASS` | prompt and reference screenshots |
| Initial desktop runtime | `FAIL` | owner screenshots show broken sticky journey |
| Sticky root cause | `PASS_SOURCE` | overflow/sticky ownership identified |
| Corrected sticky/fallback contract | `IMPLEMENTED_SOURCE` | bounded CSS/JS patch |
| Corrected pacing/scale contract | `IMPLEMENTED_SOURCE` | layout/tier modules |
| JavaScript syntax | `PASS_LOCAL_PATCH` | `node --check` on all three modules |
| Correction diff scope | `PASS_SOURCE` | five Values-owned files only |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repo command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run after correction |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run after correction |
| PHP/focused tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run after correction |
| Corrected Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | fresh recording absent |
| WebKit/six-tier/RTL runtime | `BLOCKED_BY_MISSING_EVIDENCE` | matrix absent |
| Accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | comparable runs absent |

## Progress ledger

| Stage | Status |
|---|---|
| V01 docs/current source | `PASS_SOURCE` |
| V02 owner storyboard | `PASS` |
| V03 initial implementation | `IMPLEMENTED_SOURCE` |
| V04 initial XL runtime | `FAIL` |
| V05 root-cause correction | `IMPLEMENTED_SOURCE` |
| V06 corrected XL runtime | `BLOCKED_BY_MISSING_EVIDENCE` |
| V07 automated proof | `BLOCKED_BY_MISSING_EVIDENCE` |
| V08 full release matrix | `BLOCKED_BY_MISSING_EVIDENCE` |

## STATUS

The first runtime result failed. The sticky containment, scale, pacing, and
release defects are corrected in source and published to `main`. The corrected
runtime remains unproven.

## NEXT VALID STEP

Pull current `main` and capture one fresh 1920x1080 Brave/Chromium scroll
sequence showing front cards, sequential flips, fan, stack, heading, and exit.
