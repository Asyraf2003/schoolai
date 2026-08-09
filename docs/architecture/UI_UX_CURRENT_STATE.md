# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-09
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-008-CODROPS-DETAIL-COMPOSITION`
Source baseline: `155c5bfd4acca6f59d6d2a75a5dfee8b3ab8b4f1`
Active blueprint: `blueprints/2026-08-08-home-program-kinetic-type-transition.md`

## Latest owner decision

- Keep the existing Codrops kinetic transition when a Program card is opened.
- Detail content remains intentionally minimal: Back, selected Program title, one description and one selected Program image.
- Make the final detail composition visually follow the Codrops reference more closely: one large media anchor centered in the viewport, with the copy block overlapping from the logical side.
- Move `<<< Kembali / Back / العودة` into each detail copy block directly above the title; it must not be fixed to the viewport because the site header can cover a fixed top control.
- Keep the current reduced detail-title scale.
- Keep the Codrops image wrapper/image reveal already implemented.
- Do not change idle card geometry, the eleven-step handoff, Program Center Split, Visi/Misi or unrelated homepage sections.

## Implemented detail contract

- Each of the six detail articles owns its own localized Back button.
- `collectProgramDom()` now owns six `backs`; hidden detail ancestors keep inactive controls out of the focus order.
- Reduced-motion and GSAP controllers bind all six Back controls and focus only the active detail's Back button.
- `detailParts()` includes Back + title + description as the copy reveal group and keeps the existing image wrapper/image targets.
- Back is normal-flow content above the title, not `position: fixed`.
- Desktop detail uses a centered media anchor at `50% / 50%`, width `min(35vw, 36rem)`, height `min(68svh, 43rem)`.
- Desktop copy is positioned from the logical side and may overlap the centered media, matching the reference composition grammar rather than a conventional two-column landing-page grid.
- The detail image uses a portrait-like `4 / 5` frame in the shared rule while idle cards remain landscape `4 / 3`.
- Tablet/mobile use one-column detail flow with copy first and centered media below it, keeping Back immediately available above the title.
- RTL mirrors the copy side and the triple-chevron mark while preserving Arabic text direction.

## Existing Program contract retained

- Idle cards remain desktop `2 + 4`, tablet `3 + 3`, mobile `2 + 2 + 2`.
- Idle card media remains landscape `4 / 3`.
- Idle card copy remains title + description only.
- Program kinetic background, Islamic word field, colors and eleven-step Visi/Misi handoff are unchanged.
- Program Center Split replay/gap implementation is unchanged.
- GSAP 3.7.1 remains lazy-loaded and the existing media reveal remains `imageWrap 100% -> 0`, image `-100% -> 0`.
- No wheel hijacking, document `scrollTo`, WebGL or continuous page RAF was introduced.

## Scope proof

Changed implementation surfaces in this batch are limited to:

- `resources/views/home/sections/featured-programs.blade.php`
- `resources/js/surfaces/home/program-journey/geometry.js`
- `resources/js/surfaces/home/program-journey/controller.js`
- `resources/css/pages/welcome/program-journey/hud.css`
- `resources/css/pages/welcome/program-journey/wide.css`
- `resources/css/pages/welcome/program-journey/compact.css`
- `tests/Feature/HomeProgramJourneyTest.php`
- Program blueprint/current-state docs.

No Visi/Misi, handoff, Program heading/Center Split or idle-card ordering source is intentionally changed.

## Proof state

No post-batch local runtime/build proof is available through the GitHub connector.

Required local proof:

```bash
git diff --check
git status --short
php artisan test --filter=HomeProgramJourneyTest
php artisan test
npm run check:structure
npm run build
```

Runtime proof required:

- desktop detail visually reads like the Codrops article composition: centered large media plus side/overlapping copy;
- `<<< Kembali / Back / RTL equivalent` sits immediately above the title and remains reachable with the site header visible;
- Back/Escape/Tab containment and focus restoration work for all six details;
- media reveal and reverse reveal remain intact;
- compact layouts keep Back above title and media centered;
- ID/EN/AR and LTR/RTL remain readable;
- 360 through 1920 widths in Chromium and WebKit.

STATUS: `BLOCKED_BY_MISSING_EVIDENCE`

NEXT EXECUTION CHANNEL: `owner/local terminal`

NEXT VALID STEP: pull current `main`, run `HomeProgramJourneyTest`, then inspect one desktop ID detail with the header visible before further visual tuning.
