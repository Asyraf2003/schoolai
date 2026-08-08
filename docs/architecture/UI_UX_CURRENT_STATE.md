# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-09
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-003-CODROPS-DIRECT-GSAP`
Source baseline: `badd513c0a40d01a3f665c882d5756a1f46bbd3f`
Source implementation head: `24cb355434b02b5d6fcb915b3f12890b3e3df80f`
Active blueprint: `blueprints/2026-08-08-home-program-kinetic-type-transition.md`

## Latest owner decision

- The WAAPI translation of the Program interaction is no longer the target.
- Program should follow the Codrops `KineticTypePageTransition` template behavior as directly as practical.
- Keep the six SchoolAI programs PG, TK, SD, TQ, MB and LT.
- Final photography will later be served as optimized WebP/AVIF variants from Cloudflare.
- Do not change About/Visi/Misi or other homepage sections in this batch.

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
- Desktop card composition now follows the reference flex/stagger pattern, adapted from four to six cards.
- The Program enhancer lazily loads GSAP 3.7.1 from jsDelivr instead of adding it to the main package/lock dependency graph.
- The GSAP timelines use the reference durations, easing roles, scale, rotation, line travel, stagger, card exit and article reveal behavior.
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
- ten kinetic type lines;
- representative ID/EN/AR content;
- no obsolete Program sticky/rail/frame markup;
- GSAP 3.7.1 transition markers;
- Codrops timing/easing markers;
- no wheel handling or `scrollTo`;
- presence of the Codrops MIT notice.

## Existing proof before this direct-template correction

Owner-local proof supplied before this correction:

- Laravel suite: `197 passed (1659 assertions)` in `13.87s`.
- `npm run check:structure` already failed because of three unrelated pre-existing items:
  - `resources/js/surfaces/home/vision-story/entry.js` orphan reference;
  - `resources/js/surfaces/home/vision-story/typography.js` orphan reference;
  - `resources/css/pages/welcome-hero.css` checksum drift.

Those are outside Program scope and remain untouched. The Laravel pass is a baseline, not post-change proof for the direct GSAP correction.

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

- six-card desktop composition against the Codrops reference;
- each card opening its correct detail;
- kinetic type scale/rotation/stagger timing;
- back/Escape and focus restoration;
- repeated open/close without stale body lock;
- ID/EN/AR and mirrored RTL motion;
- reduced motion;
- 360, 390, 640, 768, 1024, 1180/1181, 1280, 1440, 1536 and 1920 widths;
- Chromium and WebKit;
- final Cloudflare media weight only after real Cloudflare asset variants exist.

STATUS: `BLOCKED_BY_MISSING_EVIDENCE`

NEXT EXECUTION CHANNEL: `owner/local terminal`

NEXT VALID STEP: pull current `main`, run the focused Program test and inspect one desktop open/close cycle against the Codrops demo before any further visual tuning.
