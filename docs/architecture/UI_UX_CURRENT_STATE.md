# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Source main before batch: `03624783eebe794fbbc383326b25a31806b0dc8a`
Active branch: `ai/gallery-passive-end-transition-20260803`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

Blueprint: `blueprints/2026-08-03-home-depth-gallery.md`

- ID: `HOME-GALLERY-003`
- State: `IMPLEMENTING`
- Surface: homepage Gallery plus bounded transition into `/galeri`
- Protected: `/galeri` grid/lightbox trust behavior, Hero, Vision/Mission,
  Values, Programs, Articles, navigation, footer, DB schema/admin, About,
  Testimonial, and unrelated typography.

## Runtime FACT

The latest owner desktop screenshot proves the corrected Three.js bootstrap now
renders:

- a real media plane rather than a stretched `1x1` buffer;
- the atmospheric background;
- localized title and caption labels;
- the sticky Gallery viewport.

The owner accepted that result as working and requested the next bounded
refinement:

- reduce media to roughly two thirds of its current dimensions;
- make homepage media passive and photo-only;
- reserve video/embed playback for `/galeri`;
- replace dummy captions;
- move the Gallery CTA to the final depth step;
- animate plain-click navigation with enlarge, blur, and final `45deg` rotation.

The screenshot proves only one desktop Chromium-family composition. It does not
prove six tiers, WebKit, RTL, reduced motion, transition lifecycle, or
performance.

## Inspected source FACT

- Homepage Gallery previously queried published photo and video records.
- Homepage canvas and fallback media previously opened a dedicated lightbox.
- `/galeri` owns a separate card/lightbox controller that creates trusted video
  iframes and must retain that behavior.
- Plane scales before this batch were `1` desktop and `0.65` mobile.
- The CTA was a normal button after the depth component, outside the journey.
- Dummy captions were seeded in ID, EN, and AR and could remain in an existing
  database.

## Implemented source contract

- Homepage DB and static fallback queries now accept photo items only.
- `/galeri` query, card, and video iframe behavior remain unchanged.
- Homepage canvas is decorative, permanently `aria-hidden`, pointer-inert, and
  has no button role or tab stop.
- Homepage fallback media is passive article content, not an anchor or button.
- The homepage lightbox controller is removed as a losing owner.
- Desktop plane scale is `0.67`; mobile plane scale is `0.44`.
- Intrinsic aspect ratio, plane positions, camera, shader, trail, and motion
  architecture remain shared.
- One extra depth step follows the final media.
- The final plane and labels fade while the localized CTA enters.
- CTA interaction becomes available only near the end of that final step.
- Plain primary click stops the renderer, enlarges/blurs the viewport, enlarges
  the CTA, then rotates it to `45deg` and blurs before navigation.
- `/galeri` settles from a short blur/scale arrival state.
- Reduced motion, modified/middle click, no WebGL, and static fallback retain
  native link navigation.
- Known dummy captions are replaced at runtime with the localized Gallery
  section subtitle; seed data now contains meaningful localized copy.
- WebGL first-frame health accepts either a visible plane or the visible final
  CTA, preventing a false failure when the last plane intentionally fades out.

## Six-tier source contract

| Tier | Contract |
|---|---|
| XS 360–639 | scale `0.44`; bottom labels; CTA bounded to 260px |
| SM 640–767 | same mobile scene; wider safe-area spacing |
| MD 768–1023 | scale `0.67`; labels at viewport sides |
| LG 1024–1279 | same scene; larger side insets |
| XL 1280–1535 | same scene, final CTA, and transition |
| 2XL >=1536 | wider atmosphere; bounded labels and CTA |

One Blade source, renderer, scene, CTA, and route-transition module serves all
six tiers. ID/EN remain LTR. AR uses logical RTL layout without reversing camera
time, plane order, trail growth, vertical scroll, or neutral `45deg` rotation.

## Source proof status

| Gate | Status | Evidence |
|---|---|---|
| Corrected desktop bootstrap | `PASS` | owner screenshot shows plane/background/labels |
| Owner refinement decision | `PASS` | exact scale, passive media, CTA, transition accepted |
| Homepage photo-only query | `PASS_SOURCE` | DB and static source filters present |
| Full-page video preservation | `PASS_SOURCE` | `/galeri` iframe/lightbox owner retained |
| Passive homepage semantics | `PASS_SOURCE` | no media URL, video flag, anchor, canvas role, or click handler |
| Homepage lightbox removal | `PASS_SOURCE` | old controller file/import removed |
| Two-thirds scale | `PASS_SOURCE` | desktop `0.67`, mobile `0.44` |
| Final depth CTA | `PASS_SOURCE` | extra journey step and end progress owner present |
| Exit/arrival transition | `PASS_SOURCE` | WAAPI enlarge/blur/rotate plus destination settle |
| Reduced/modified navigation | `PASS_SOURCE` | native navigation remains the fallback |
| Dummy copy removal | `PASS_SOURCE` | seeder updated and runtime normalization present |
| Six-tier architecture | `PASS_SOURCE` | one scene and declared CSS adapters |
| RTL architecture | `PASS_SOURCE` | logical DOM/CSS; no locale scene fork |
| Focused source tests | `IMPLEMENTED_SOURCE` | Gallery, bootstrap, preview split updated |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot execute local command |
| `npm run check:structure` | `FAIL_PRE_EXISTING` | prior Hero checksum mismatch unrelated |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run after this batch |
| PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | not run after this batch |
| Six-tier Chromium/WebKit | `BLOCKED_BY_MISSING_EVIDENCE` | new refinement not rendered yet |
| Lighthouse/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | no comparable run |

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G01 mandatory docs and current main | `PASS` | source `03624783...` audited |
| G02 latest desktop runtime | `PASS` | owner screenshot accepted |
| G03 data/media split audit | `PASS` | homepage and `/galeri` owners identified |
| G04 photo-only passive homepage | `IMPLEMENTED_SOURCE` | query, Blade, controller changed |
| G05 plane scale refinement | `IMPLEMENTED_SOURCE` | `0.67` / `0.44` |
| G06 final depth CTA | `IMPLEMENTED_SOURCE` | extra depth and CTA state added |
| G07 route transition | `IMPLEMENTED_SOURCE` | exit and arrival modules added |
| G08 dummy copy correction | `IMPLEMENTED_SOURCE` | seed and runtime adapters updated |
| G09 focused tests/docs | `IMPLEMENTED_SOURCE` | contracts updated |
| G10 automated proof | `BLOCKED_BY_MISSING_EVIDENCE` | local execution required |
| G11 runtime matrix | `BLOCKED_BY_MISSING_EVIDENCE` | screenshots/browser proof required |

## STATUS

The requested refinement is implemented in source and prepared for a non-force
fast-forward to `main`. It is not yet runtime or six-tier `PASS`.

## NEXT VALID STEP

After publication, pull `main`, run the focused Gallery tests, then inspect one
desktop screenshot of the smaller media and final CTA transition before moving
to the remaining five tiers and WebKit.
