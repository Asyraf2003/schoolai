# Homepage Program — Kinetic Type Transition Blueprint

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Date: 2026-08-09
Owner decision: keep Codrops kinetic transition behavior while adapting the final Program detail composition to SchoolAI.
Reference: `https://github.com/codrops/KineticTypePageTransition`
Reference license: MIT.

## FACT

- The reference uses a click-driven kinetic typography transition into a strongly centered article composition.
- SchoolAI Program contains six localized paths and keeps ID/EN/AR, LTR/RTL, reduced motion and responsive tiers.
- Idle Program card geometry is already separately accepted and is not part of this detail-composition batch.

## GOAL

Opening any Program card transitions through the existing kinetic type field into a clean detail state that visually follows the Codrops composition grammar while rendering only:

- localized Back with triple-chevron mark;
- selected Program title;
- one description;
- one selected Program image.

## DETAIL COMPOSITION CONTRACT

Desktop:

- selected media is the visual anchor and is centered in the viewport;
- media target scale is `min(35vw, 36rem)` wide and `min(68svh, 43rem)` tall;
- detail media uses a portrait-like `4 / 5` frame and `object-fit: cover`;
- copy enters from the logical side and may overlap the centered media, following the Codrops article composition rather than a conventional equal two-column grid;
- Back belongs to the copy block immediately above the title and must never use viewport-fixed positioning.

Compact:

- detail becomes one column;
- Back remains immediately above the title;
- media remains centered and follows the copy;
- no desktop absolute-position coordinates are reused blindly.

RTL:

- copy side mirrors logically;
- Arabic keeps Cairo and normal Arabic casing;
- the `<<<` mark mirrors visually to the return direction;
- Back label remains locale-owned.

## CONTENT CONTRACT

Public copy remains in `lang/id/home_program.php`, `lang/en/home_program.php`, and `lang/ar/home_program.php`.

Forbidden detail content:

- number / `01 / 06`;
- eyebrow/category;
- secondary intro/summary;
- next-program code;
- unrelated article metadata.

## MOTION CONTRACT

Retain the active Codrops-derived GSAP behavior:

- type scale `2.7`;
- mirrored quarter-turn under RTL;
- line travel/stagger;
- alternating idle-card exit;
- copy vertical reveal;
- image wrapper `100% -> 0` with image `-100% -> 0`;
- reversible close timeline;
- Escape, Tab containment and focus restoration.

Each detail now owns its own Back button. Only the active detail's Back may receive focus. Reduced motion opens the same semantic detail immediately.

## OUT OF SCOPE

Do not change:

- idle cards or `2 + 4 / 3 + 3 / 2 + 2 + 2` geometry;
- eleven-step Visi/Misi -> Program handoff;
- Program Center Split heading choreography;
- Visi/Misi;
- Hero, Values, Gallery, Articles, navbar or footer.

## PROOF GATE

Static/local:

- `git diff --check`
- `php artisan test --filter=HomeProgramJourneyTest`
- full `php artisan test`
- `npm run check:structure`
- `npm run build`

Runtime:

- all six details open the matching image/title/description;
- desktop detail reads as centered media plus overlapping logical-side copy;
- Back is directly above title and cannot be hidden by the site header;
- LTR/RTL direction is correct;
- media reveal/reverse reveal remains intact;
- reduced motion remains usable;
- 360 through 1920 widths work in Chromium and WebKit.

Until runtime proof exists, status remains `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`.
