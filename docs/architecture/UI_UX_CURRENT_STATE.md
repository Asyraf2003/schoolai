# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-09
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-006-MINIMAL-DETAIL`
Source baseline: `9626247838b4155a8590bf0451cb025aaf2fcc94`
Source implementation head: `9d3d9c60d7f3c7514add92c16470f8be37e1524c`
Active blueprint: `blueprints/2026-08-08-home-program-kinetic-type-transition.md`

## Latest owner decision

- Keep the Codrops kinetic transition when a Program card is opened.
- Simplify the final Program detail screen to only localized Back, the selected Program title and one description.
- Back labels are `Kembali`, `Back`, and `العودة`.
- Remove detail image, number, eyebrow/category, secondary intro/summary, next-program code and all related layout slots.
- Detail title should use the same editorial font grammar as the Program section heading.
- Keep idle cards, the eleven-step Visi/Misi -> Program handoff, Visi/Misi, card geometry and other homepage sections unchanged.

## Implemented minimal detail contract

- Blade renders six detail articles, each with only `h3` title + `.program-kinetic__detail-description`.
- Detail media markup has been removed; Program now renders only the six idle-card Unsplash images rather than duplicating them inside details.
- `detailParts()` now collects only title and description.
- GSAP detail open/close no longer targets image wrapper/image, number, eyebrow, intro or next-code elements.
- The existing kinetic type transition, card exit, Back fade, Escape handling, Tab containment and focus restoration remain in place.
- Detail layout is a clean full-viewport field using the existing Program background tokens.
- ID/EN detail title uses the Program editorial display grammar: light variable weight `360`, tight `-.055em` tracking, large scale and uppercase presentation.
- Arabic detail title uses Cairo, weight `500`, normal Arabic casing/tracking and Arabic-appropriate line-height.
- Description is limited to a readable `42rem` measure.
- Back uses logical `inset-inline-start`; the arrow mirrors under RTL.

## Existing Program contract retained

- Program idle cards remain desktop `2 + 4`, tablet `3 + 3`, mobile `2 + 2 + 2`.
- Idle media remains landscape `4 / 3` with the Codrops-inspired stagger geometry.
- Idle card copy remains title + description only.
- `Program Kami` / `Our Programs` remains visually uppercase in LTR and uses the center-split vertical entrance only.
- Arabic Program heading remains Cairo without Latin uppercase behavior.
- One Program kinetic type field spans the content-driven Program height.
- The field renders 20 lines from locale-owned Islamic vocabulary.
- Program background remains `#e7f5ff`; kinetic type remains `#397aa6` at resting opacity `.16`.
- The eleven-step handoff remains unchanged.
- GSAP 3.7.1 remains lazy-loaded from jsDelivr.
- Open/close restores kinetic resting opacity and clears inline transform/opacity state.
- No wheel hijacking, document `scrollTo`, WebGL or continuous page RAF was introduced.

## Locale contract

Public Program content remains locale-owned in:

- `lang/id/home_program.php`
- `lang/en/home_program.php`
- `lang/ar/home_program.php`

Only the detail Back labels changed in this batch:

- ID: `Kembali`
- EN: `Back`
- AR: `العودة`

Existing item metadata remains in locale data but is not rendered in the minimal detail state.

## Scope proof

Changed implementation surfaces are limited to:

- Program locale Back labels;
- Program detail Blade markup;
- Program detail DOM collection and GSAP targets;
- Program detail CSS presentation;
- removal of obsolete wide/compact detail-media rules;
- focused Program regression test;
- Program blueprint/current-state docs.

Idle card geometry, handoff source, Visi/Misi source and unrelated homepage sections were not changed in this batch.

## Focused source tests

`HomeProgramJourneyTest.php` now locks:

- six localized cards/details;
- one Program kinetic type field with 20 lines;
- six idle-card images only;
- localized short Back labels;
- exactly six detail descriptions;
- absence of detail image/number/eyebrow/intro/next markup;
- absence of obsolete detail-image JS/CSS targets;
- section-style detail-title typography;
- existing kinetic transition/state restoration;
- existing heading, handoff and responsive card geometry contracts.

## Proof state

Owner-local proof from before this batch remains only a baseline:

- Laravel suite: `197 passed (1659 assertions)` in `13.87s`.

No post-batch local build/browser proof is available through the GitHub-only execution channel.

Required local proof against current `main`:

```bash
git diff --check
git status --short
php artisan test --filter=HomeProgramJourneyTest
php artisan test
npm run check:structure
npm run build
```

Runtime proof required:

- each of the six cards opens the matching title/description;
- detail visibly contains only Back, title and one description;
- ID shows `< Kembali`, EN `< Back`, Arabic mirrors the arrow with `العودة`;
- long ID/EN/AR titles remain readable without clipping;
- repeated open -> Back cycles restore the same kinetic resting state;
- Escape, Tab containment and focus restoration still work;
- reduced motion remains usable;
- 360, 390, 640, 768, 1024, 1180/1181, 1280, 1440, 1536 and 1920 widths;
- Chromium and WebKit.

STATUS: `BLOCKED_BY_MISSING_EVIDENCE`

NEXT EXECUTION CHANNEL: `owner/local terminal`

NEXT VALID STEP: pull current `main`, run `HomeProgramJourneyTest`, then open one Program detail in ID and AR to verify the minimal detail hierarchy and Back direction before further visual tuning.
