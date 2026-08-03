# UI/UX Engineering — Current State and Progress Ledger

Status: `BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-04
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-03-home-values-card-story.md`
Raw evidence: `measurements/2026-08-03-home-values-reference-motion-raw.md`
Source implementation head: `fbbc83b6672053652ac4551aea3970b825df0fcc`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VALUES-001`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Values section `#nilai`
- Accepted blueprint commit: `48830e93645dc0d5a681a89546f4dc8bc1941dab`
- Implementation commit: `fbbc83b6672053652ac4551aea3970b825df0fcc`
- Scope: 19 Values-owned source and focused-test files
- Protected: Hero, Vision/Mission, Programs, Gallery, Articles, navigation,
  footer, DB/admin/routes, About, and Testimonial

## Implemented result

The Values section was rebuilt as one semantic, localized card story:

```text
mission green fade/blur -> blue Values field
-> center-origin two-line heading reveal
-> centered desktop deck -> fan -> overlapping perspective flips
-> upright CSS Grid row -> completed continuous trail
-> blue fade/blur -> white -> Programs
```

- ID/EN/AR headings and the required honorific forms are owned by translations.
- XS keeps a natural one-column layout.
- SM and MD keep a natural 2x2 Grid.
- LG, XL, and 2XL share the cinematic four-column sequence.
- The semantic card articles remain the final CSS Grid slots.
- Motion transforms only child pose wrappers relative to measured Grid slots.
- The clipping shell and perspective stage are separate owners.
- Desktop centering derives from `getBoundingClientRect()` stage/card geometry,
  not viewport-height ratios.
- One SVG path plus one measured circle head owns the route.
- Reduced motion and unsupported 3D resolve to readable front cards.
- No dependency, asset, WebGL, canvas, font, or parallel locale DOM was added.

## Root causes removed or isolated

- Enhanced `display:block` plus absolute final card slots was replaced by Grid.
- Manual X slots and viewport-ratio Y anchors were removed from final layout.
- Perspective was moved off the clipping owner.
- Fixed z-order is assigned structurally instead of repainted every frame.
- Three duplicate trail paths and the dashed fake head were replaced.
- Hard-coded Blade locale headings were replaced by translation-owned copy.
- Values now owns both entry and exit transition layers.
- The inactive equivalence-managed `.nilai-*` fossil remains isolated; deleting
  it is a separate global cascade migration.

## Source ownership

| Concern | Owner |
|---|---|
| semantics and story DOM | `resources/views/home/sections/school-values.blade.php` |
| localized heading/content | `lang/{id,en,ar}/home.php` |
| entry/timeline/clip/exit | `resources/css/surfaces/home/values/story-shell.css` |
| heading masks | `resources/css/surfaces/home/values/story-heading.css` |
| final Grid and card faces | `resources/css/surfaces/home/values/story-cards.css` |
| continuous route | `resources/css/surfaces/home/values/story-trail.css` |
| lifecycle and RAF | `resources/js/surfaces/home/values/controller.js` |
| stage/Grid measurement | `resources/js/surfaces/home/values/geometry.js` |
| critically damped motion | `resources/js/surfaces/home/values/motion.js` |
| desktop fan/flip pose | `desktop-keyframes.js` and `desktop-layout.js` |
| responsive pose | `resources/js/surfaces/home/values/layout.js` |
| CSS variable writes | `resources/js/surfaces/home/values/paint.js` |
| focused regression contract | `tests/Feature/HomeValuesStoryTest.php` |

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Atomic source publication | `PASS_SOURCE` | `main` verified at implementation commit |
| Commit scope | `PASS_SOURCE` | exactly 19 expected files in GitHub commit |
| Exact headings/honorifics | `PASS_SOURCE` | translation and Blade source inspected |
| Semantic DOM/Grid fallback | `PASS_SOURCE` | one `h2`, four articles, Grid final slots |
| JS syntax | `PASS_LOCAL_PATCH` | `node --check` passed for every changed module |
| CSS syntax | `PASS_LOCAL_PATCH` | all six modules parsed with Lightning CSS |
| Enforced source line limit | `PASS_LOCAL_PATCH` | changed `resources/` files are <=200 lines; `lang/` is outside the checker roots |
| Focused feature test | `IMPLEMENTED_SOURCE` | test added; PHP runtime unavailable here |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | no repository checkout in this channel |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | no complete checkout/package root |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | no complete checkout/package root |
| PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | `php` is unavailable in this channel |
| Desktop center tolerance | `BLOCKED_BY_MISSING_EVIDENCE` | browser rectangles not captured |
| Six-tier ID/EN/AR runtime | `BLOCKED_BY_MISSING_EVIDENCE` | responsive matrix not captured |
| Reverse/fast/resize/BFCache | `BLOCKED_BY_MISSING_EVIDENCE` | lifecycle capture absent |
| Reduced motion/zoom/a11y | `BLOCKED_BY_MISSING_EVIDENCE` | runtime audit absent |
| Chromium/WebKit/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | target runtimes unavailable here |

## STATUS

The accepted source rebuild is published on `main`. Release/runtime status
remains `BLOCKED_BY_MISSING_EVIDENCE`; no browser or build gate is recorded as
passing without evidence.

## NEXT VALID STEP

Pull `main` with `git pull --ff-only origin main`, then run the repository proof
commands and capture the required browser matrix from the resulting checkout.
