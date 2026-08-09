# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-09
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-005-HEADING-FULL-TYPE-RTL-MISSION`
Source baseline: `1e7b38989ea48df76a62b7aa5d98b71ca7f20bd1`
Source implementation head: `43c807e2de2fae605fc8fad5119f52748b7f05e1`
Active blueprint: `blueprints/2026-08-08-home-program-kinetic-type-transition.md`

## Latest owner decision

- Keep the Codrops `KineticTypePageTransition` behavior for Program detail transitions.
- Keep the six-card SchoolAI composition: desktop `2 + 4`, tablet `3 + 3`, mobile `2 + 2 + 2`.
- Program idle media remains large landscape/staggered, following the Codrops card geometry grammar.
- `Program Kami` / `Our Programs` is visually uppercase in LTR.
- Program heading entrance uses only the Values-style center split: first line rises from the center while the second line drops from the center; no horizontal heading shift is allowed.
- Arabic heading remains Arabic without uppercase transformation.
- Program kinetic background must cover the complete content-driven Program height, not only one viewport.
- The kinetic wall may use localized Islamic vocabulary such as Islam, Iman, Ihsan, Akhlakul Karimah, Qur'an, Adab, Ilmu, Amanah, Hikmah and Taqwa.
- Program background/type colors remain the existing source tokens; do not invent a new color family.
- Fix the Arabic Mission list layout so its copy owns the flexible track instead of collapsing into the narrow counter track.
- Do not change the eleven-step Visi/Misi -> Program handoff, card detail choreography, Hero, Values, Gallery, Articles, navbar or footer in this batch.

## Implemented Program source contract

- Public Program copy and kinetic word banks are locale-owned in `lang/id/home_program.php`, `lang/en/home_program.php`, and `lang/ar/home_program.php`.
- Blade renders 20 kinetic type lines from the locale word bank and repeats each phrase horizontally to overscan the viewport.
- The kinetic field is a single Program-owned layer; the removed `.program-kinetic__handoff-type` duplicate is not reintroduced.
- The type field now spans the actual Program content height using an absolute `top: 0; bottom: 0; height: auto` contract.
- During Codrops open/close choreography, that same field becomes the fixed transition layer.
- Program background remains `#e7f5ff`.
- Program kinetic text remains `#397aa6` at the existing `.16` resting opacity.
- The eleven-step handoff remains sharp beige `#f4f1e9` -> real Program field reveal with white separator lines.
- Desktop card geometry remains `2 + 4` with the Codrops-inspired `7svh` base and `9svh` interval.
- Tablet remains `3 + 3`; mobile remains `2 + 2 + 2`.
- Card media remains landscape `4 / 3` in active responsive tiers.
- Card idle copy remains title + description only.

## Heading contract

- Heading presentation is isolated in `resources/css/pages/welcome/program-journey/heading.css`.
- Heading runtime trigger is isolated in `resources/js/surfaces/home/program-journey/heading.js`.
- The Program controller imports and owns the heading lifecycle.
- IntersectionObserver triggers the reveal when the heading enters the viewport.
- LTR line one starts at `translateY(108%)` and resolves upward to zero.
- LTR line two starts at `translateY(-108%)` and resolves downward to zero.
- There is no horizontal heading translation.
- Reduced motion resolves the heading immediately without transition.
- Arabic single-line heading uses the same vertical reveal contract while retaining Cairo and normal Arabic casing.

## Mission RTL correction

FACT from owner runtime screenshot: Arabic Mission copy collapsed into a very narrow vertical column while ID rendered normally.

Source cause:

- mission rows used a narrow counter track plus a flexible copy track;
- `direction: rtl` changed placement behavior;
- copy/counter grid columns were not explicitly owned;
- desktop enhanced CSS also redefined the mission tracks with higher specificity.

Implemented correction:

- LTR base row: `2.5rem minmax(0, 1fr)`;
- RTL base row: `minmax(0, 1fr) 2.5rem`;
- counter and copy receive explicit grid columns;
- desktop enhanced LTR row: `2.2rem minmax(0, 1fr)`;
- desktop enhanced RTL row: `minmax(0, 1fr) 2.2rem`;
- Arabic copy keeps `text-align: start` and Cairo ownership.

No Mission text, media, scroll choreography or locale content changed.

## Existing Codrops transition contract

- GSAP 3.7.1 remains lazily loaded from jsDelivr.
- type scale remains `2.7`;
- LTR quarter-turn remains `-90deg`, mirrored to `+90deg` in RTL;
- line travel/stagger, alternating card exit, detail copy reveal and image reveal remain unchanged;
- close restores the CSS resting opacity and clears inline transform/opacity state;
- Back, Escape, Tab containment and focus restoration remain owned by Program integration;
- no wheel hijacking, document `scrollTo`, WebGL or continuous page RAF was introduced.

## Focused source tests

`HomeProgramJourneyTest.php` now locks:

- six localized cards/details;
- one Program type field with 20 kinetic lines;
- localized Islamic word-bank presence for ID/EN/AR;
- center-split heading entrance with no horizontal shift;
- isolated heading CSS/JS imports;
- content-height kinetic wall;
- eleven-step handoff invariants;
- Codrops GSAP timing/state restoration;
- desktop/tablet/mobile card geometry.

`HomeVisionMissionHeadingTest.php` now locks:

- localized About/Visi/Misi rendering;
- existing native pinned mask reveal behavior;
- explicit LTR/RTL mission counter/copy tracks in base and enhanced desktop CSS.

## Proof state

Owner-local proof from before this batch remains only a baseline:

- Laravel suite: `197 passed (1659 assertions)` in `13.87s`.

No post-batch local build/browser proof is available through the GitHub-only execution channel.

Required local proof against current `main`:

```bash
git diff --check
git status --short
php artisan test --filter=HomeProgramJourneyTest
php artisan test --filter=HomeVisionMissionHeadingTest
php artisan test
npm run check:structure
npm run build
```

Runtime proof required:

- LTR heading visually reads `PROGRAM KAMI` / `OUR PROGRAMS` and enters from the center split only;
- Arabic heading remains readable and does not receive Latin uppercase behavior;
- kinetic background text fills the entire Program section through the final card;
- 20-line wall does not create unacceptable Codrops open/close delay;
- Program open -> Back repeated twice restores the same resting typography state;
- Arabic Mission copy uses normal readable line widths on desktop and compact layouts;
- 360, 390, 640, 768, 1024, 1180/1181, 1280, 1440, 1536 and 1920 widths;
- Chromium and WebKit.

STATUS: `BLOCKED_BY_MISSING_EVIDENCE`

NEXT EXECUTION CHANNEL: `owner/local terminal`

NEXT VALID STEP: pull current `main`, run the two focused tests, then inspect one LTR Program entrance plus Arabic Mission at desktop width before further visual tuning.
