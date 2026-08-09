# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-09
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-009-EXACT-CODROPS-DETAIL-GEOMETRY`
Source baseline: `359f88bfe37cdc98bea3370d9cbedc6b79f4fef3`
Source implementation head: `42ff4ee0eecf5f00e5eb2c259d351a0012f8d052`
Active blueprint: `blueprints/2026-08-08-home-program-kinetic-type-transition.md`

## Latest owner decision

- Keep the existing Program detail content contract: local `<<< Back`, title, one description and one image only.
- Stop approximating the Codrops desktop composition and use its desktop article geometry directly where compatible with SchoolAI.
- The only intentional visual deviation is the SchoolAI typeface stack plus the owner-required local Back control directly above the title.
- Keep idle cards, handoff, Program Center Split, Visi/Misi and unrelated sections unchanged.

## Verified reference geometry

The Codrops source defines desktop article geometry as:

- article wrapper begins at `20vh` and occupies `80vh`;
- article width is `calc(38vw + 280px)`;
- article grid rows are `10vw 2rem 12vw auto auto`;
- article grid columns are `1.5rem 30% 1fr 1.5rem`;
- media owns column 3 and spans the full article height;
- title spans from column 2 through column 3 and uses `8vw`, line-height `.85`, uppercase and bold weight;
- body copy inherits the base `1rem` size;
- image frame uses `17px 17px 0 0` radius.

At a 1920-wide viewport this yields roughly a 34vw media column instead of the previous capped `36rem` media width. The previous SchoolAI implementation therefore rendered materially smaller than the reference.

## Implemented correction

- Removed the desktop `min(35vw, 36rem)` / `min(68svh, 43rem)` caps.
- Desktop detail now uses `top: 20svh`, `height: 80svh`, `width: calc(38vw + 280px)` and the reference grid rows/columns.
- `.program-kinetic__detail-copy` becomes `display: contents` at desktop so Back, title and description can occupy the reference grid directly.
- Back occupies column 2 / row 2 directly above the title.
- Title occupies columns `2 / 4`, row 3, and therefore deliberately crosses over the media just like the reference.
- Description occupies column 2 / row 4 with the reference-like `1rem` body scale.
- Media occupies column 3, rows `1 / 6`, fills the entire 80svh article height, and retains the existing Codrops reveal animation.
- Detail title now uses SchoolAI's display font family at `8vw`, bold weight and `.85` line-height. Arabic keeps Cairo while matching the reference scale/weight intent.
- RTL mirrors media/copy ownership logically.

## Existing Program contract retained

- Detail content remains only Back + title + one description + one media item.
- Idle cards remain desktop `2 + 4`, tablet `3 + 3`, mobile `2 + 2 + 2`.
- Idle card media remains landscape `4 / 3`.
- Program kinetic background, Islamic word field, eleven-step handoff and Center Split are unchanged.
- GSAP type transition and detail-media reveal/reverse reveal are unchanged.
- No Visi/Misi source changed in this batch.

## Scope proof

Implementation changes in this batch are limited to:

- `resources/css/pages/welcome/program-journey/hud.css`
- `resources/css/pages/welcome/program-journey/wide.css`
- `tests/Feature/HomeProgramJourneyTest.php`
- Program blueprint/current-state docs.

## Proof state

No post-batch browser/build proof is available through the GitHub connector.

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

- on desktop, media begins around 20% viewport height and reaches the viewport bottom like the Codrops reference;
- media width, article width and title overlap visually match the reference composition;
- title uses reference-like 8vw scale and `.85` line-height while retaining SchoolAI font family;
- description uses reference-like 1rem body scale;
- `<<< Kembali / Back / RTL equivalent` remains immediately above the title and reachable below the site header;
- ID/EN/AR and LTR/RTL remain readable;
- compact layouts remain usable;
- Chromium and WebKit.

STATUS: `BLOCKED_BY_MISSING_EVIDENCE`

NEXT EXECUTION CHANNEL: `owner/local terminal`

NEXT VALID STEP: pull current `main`, run `HomeProgramJourneyTest`, then compare one 1920px desktop Program detail side-by-side with the Codrops reference before any further tuning.