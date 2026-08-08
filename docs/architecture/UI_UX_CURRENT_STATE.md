# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-09
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-004-SINGLE-KINETIC-HANDOFF-RESET`
Source baseline: `710fbb36b653f357a48db6cfceb8fa9dab7caa1c`
Source implementation head: `14e64cd6f6c32c9edbc8ca3b5c6d56929456d747`
Active blueprint: `blueprints/2026-08-08-home-program-kinetic-type-transition.md`

## Latest owner decision

- Program should continue to follow the Codrops `KineticTypePageTransition` behavior.
- The Visi/Misi -> Program handoff must read as one Program field, not a separate transition banner.
- Remove the duplicate handoff typography layer; one `data-program-type` field must span the handoff and Program body.
- The kinetic type must restore its CSS baseline after every GSAP close cycle; no stale `0.05` inline opacity may remain.
- Keep the eleven-step sharp beige -> light-blue handoff.
- Keep the six SchoolAI programs PG, TK, SD, TQ, MB and LT.
- Final photography will later be served as optimized WebP/AVIF variants from Cloudflare.
- Do not change card layout, About/Visi/Misi, or other homepage sections in this batch.

## Reference facts

Reference: `https://github.com/codrops/KineticTypePageTransition`.

The source is MIT licensed and uses GSAP `^3.7.1`. Its main transition uses:

- item fade/alternating `25%` vertical exit;
- kinetic type scale `2.7` and `-90deg` rotation;
- type-line `20% -> -200%` travel with `0.04` stagger;
- `power*`, `expo` and `back` easing roles;
- staggered article copy entrance;
- image-wrapper `100% -> 0` and image `-100% -> 0` reveal;
- reversed close timeline.

The Adobe/Typekit font referenced by the demo is not treated as MIT-licensed source and is not copied.

## Implemented source contract

- Program remains a six-card click-driven showcase, not a scroll journey.
- Desktop/tablet/mobile card composition is unchanged by this batch.
- The Program enhancer lazily loads GSAP 3.7.1 from jsDelivr instead of adding it to the main package/lock dependency graph.
- The GSAP timelines retain the reference durations, easing roles, scale, rotation, line travel, stagger, card exit and article reveal behavior.
- Exactly one Program kinetic type field now exists in Blade and spans from the handoff into the Program body.
- The former `.program-kinetic__handoff-type` duplicate layer has been removed.
- The eleven-step handoff is background-only: `#f4f1e9` -> `#e7f5ff` using sharp `repeating-linear-gradient` steps.
- Shared type size, weight, leading, tracking, color and opacity come from Program-level CSS tokens.
- Static kinetic type starts inside the handoff after the first solid beige step and continues behind `Program Kami`.
- During open/close, that same field becomes the fixed Codrops transition layer.
- `TypeTransition` captures the computed CSS resting opacity before animation and returns to it on close.
- GSAP inline opacity/transform state is cleared at the end of close so repeated cycles return to CSS baseline rather than accumulating stale state.
- RTL mirrors the quarter-turn and kinetic line travel while preserving the PG -> TK -> SD -> TQ -> MB -> LT logical order.
- Back fades in with the detail and fades out on close; Escape and Tab containment remain supported.
- Body overflow is locked only while a Program detail dialog is active.
- Reduced-motion bypasses the kinetic transition and keeps immediate semantic detail access.
- If GSAP fails to load, the full non-enhanced Program descriptions remain visible.
- No wheel hijacking, document `scrollTo`, WebGL or continuous RAF loop was introduced.

## Locale contract

Public Program content remains in:

- `lang/id/home_program.php`
- `lang/en/home_program.php`
- `lang/ar/home_program.php`

Blade contains no locale `match` block for public Program copy.

## Third-party license contract

The required Codrops MIT notice is stored at:

`docs/licenses/CODROPS_KINETIC_TYPE_PAGE_TRANSITION_MIT.md`

## Media contract

Development still uses six Unsplash URLs. Final media is expected to migrate to Cloudflare.

The target Cloudflare delivery pattern is responsive variants, not one oversized source per device:

- small/mobile width variants;
- medium/tablet variants;
- desktop variants;
- WebP/AVIF negotiation;
- `srcset`/`sizes` once final asset IDs exist;
- lazy non-critical media;
- CDN caching.

With that contract, image cost should be dominated by the selected viewport variant rather than the original master file size.

## Focused source tests

`HomeProgramJourneyTest.php` now expects:

- six localized cards and six details;
- exactly one Program kinetic type field and ten kinetic type lines;
- no `.program-kinetic__handoff-type` duplicate;
- eleven sharp handoff steps;
- shared Program kinetic type tokens;
- computed resting opacity restoration plus GSAP `clearProps` cleanup;
- representative ID/EN/AR content;
- no obsolete Program sticky/rail/frame markup;
- GSAP 3.7.1 transition markers;
- Codrops timing/easing markers;
- no wheel handling or `scrollTo`;
- presence of the Codrops MIT notice.

## Existing proof before this correction

Owner-local proof supplied before this correction:

- Laravel suite: `197 passed (1659 assertions)` in `13.87s`.
- `npm run check:structure` already failed because of three unrelated pre-existing items:
  - `resources/js/surfaces/home/vision-story/entry.js` orphan reference;
  - `resources/js/surfaces/home/vision-story/typography.js` orphan reference;
  - `resources/css/pages/welcome-hero.css` checksum drift.

Those are outside Program scope and remain untouched. The Laravel pass is a baseline, not post-change proof for this Program correction.

## Blocked proof

Run against current `main` locally:

```bash
git diff --check
git status --short
php artisan test --filter=HomeProgramJourneyTest
php artisan test
npm run check:structure
npm run build
```

`check:structure`/`build` may remain blocked by the three unrelated pre-existing structure failures above; report exact output rather than repairing them inside Program scope.

Runtime proof still required:

- handoff reads as one continuous beige -> light-blue Program field;
- the same kinetic typography size/color/opacity is visible through handoff and Program body;
- no separate mini-type banner remains;
- open one Program detail and close it repeatedly without kinetic text becoming dimmer;
- card layout remains unchanged;
- each card opens its correct detail;
- back/Escape and focus restoration;
- ID/EN/AR and mirrored RTL motion;
- reduced motion;
- 360, 390, 640, 768, 1024, 1180/1181, 1280, 1440, 1536 and 1920 widths;
- Chromium and WebKit;
- final Cloudflare media weight only after real Cloudflare asset variants exist.

STATUS: `BLOCKED_BY_MISSING_EVIDENCE`

NEXT EXECUTION CHANNEL: `owner/local terminal`

NEXT VALID STEP: pull current `main`, run the focused Program test, then inspect one desktop handoff plus two consecutive open/close cycles before any further visual tuning.
