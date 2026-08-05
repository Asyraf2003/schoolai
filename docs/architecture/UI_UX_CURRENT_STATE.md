# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-05
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-05-vision-mission-deterministic-geometry.md`
Source baseline before batch: `c9bc2fc22780b950b548f1f0ac491f96d254eb61`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VISION-006-DETERMINISTIC`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Vision/Mission `#visi-misi`
- Goal: remove reload-dependent composition, duplicate Mission reveal,
  viewport-coordinate timing, Arabic Vision drift, and locale-unsafe home data.
- Protected: Hero, Values, Programs, Gallery, Articles, navigation, footer,
  About, Testimonial, locale copy, routes, DB, authentication, dependencies, and
  Vision/Mission media assets.

## Runtime evidence received

Owner screenshots at 759x924, 1036x924, and 1081x924 showed:

- the same viewport could render either static copy flow or enhanced pinned flow;
- compact Mission remained faded until its title had passed viewport center;
- image swap timing varied between reloads;
- Arabic Vision centered text but not always its container.

Local test report remained 191 passed and 3 failed:

- Gallery soft-delete homepage assertion;
- page-wide Values `aria-pressed` assertion;
- English Vision/Mission copy assertion.

## Verified root causes

- Preparation could permanently choose static or enhanced composition based on
  section position after font and image waits.
- Static and enhanced compact CSS had different geometry and conflicting legacy
  responsive owners.
- Mission had both typography progress and a separate parent opacity/translate.
- Mission and frame offsets mixed viewport coordinates with local track distance.
- Scroll-event geometry reads lagged the smoothed rendered track.
- Arabic Vision centered text without consistently centering its article box.
- `HomeController::$homeDataCache` stored translation data without a locale key.

## Owner-accepted decisions

- Use shared semantic stage geometry for static and enhanced states.
- Gate sticky motion with one capability marker.
- Initialize enhancement atomically after bounded preparation.
- Remove compact parent Mission reveal; effect25 is the sole Mission reveal.
- Derive Mission and frame progress from local track geometry.
- Complete both when their center reaches viewport center.
- Keep upward Mission entry final.
- Center the Vision article and text in every locale.
- Remove the locale-unsafe homepage translation cache.
- Do not address Gallery or Values failures in this batch.

## Implemented source

- Compact stage geometry no longer depends on `.is-enhanced`.
- Legacy responsive layout rules that produced a different static flow were
  removed.
- Desktop fallback shares the enhanced first-frame composition.
- A motion capability marker gates pinning.
- The late-preparation skip was removed.
- Enhanced class, measurement, timeline, and current progress initialize in one
  synchronous completion step.
- Compact Mission parent opacity and translate animation were removed.
- Mission and frame ranges now use track-local geometry.
- Compact Mission progress now follows rendered master progress.
- Upward entry remains final until a fresh downward pass begins above it.
- Vision article measure is centered in ID, EN, and AR.
- The locale-unsafe `homeDataCache` was removed.
- One controller, one observer, one scroll listener, one RAF owner, native WAAPI,
  dynamic import, lazy media, and all six global tiers remain.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | branch checked before write |
| screenshot/root-cause audit | `PASS_SOURCE` | runtime symptoms matched owners |
| source scope isolation | `PASS_SOURCE` | active owners and locale cache only |
| JS syntax | `PASS_LOCAL_STATIC` | changed JS parsed with Node |
| PHP syntax | `PASS_LOCAL_STATIC` | changed PHP parsed with PHP lint |
| CSS brace balance | `PASS_LOCAL_STATIC` | four CSS owners checked |
| source line limit | `PASS_LOCAL_STATIC` | changed source files <= 200 lines |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| focused PHP test | `BLOCKED_BY_MISSING_EVIDENCE` | cache fix not run here |
| full PHP tests | `FAIL_REPORTED_LOCAL` | prior 191 passed, 3 failed |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | runtime review required |
| deployed performance delta | `BLOCKED_BY_MISSING_EVIDENCE` | deployment proof required |

## Out-of-scope failures retained

- `Admin/GalleryItemSoftDeleteTest`
- `HomeValuesStoryTest`

No Gallery or Values owner or assertion changed.

## STATUS

The deterministic Vision/Mission source correction is implemented. Completion
remains blocked by checkout build/tests and the browser/runtime matrix.

## NEXT VALID STEP

Owner/local terminal: fast-forward `main`, run the proof block, then reproduce
the supplied 759x924, 1036x924, and 1081x924 cases across reload states before
additional visual tuning.
