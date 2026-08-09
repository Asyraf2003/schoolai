# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-09
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-010-ADAPTIVE-DETAIL-TITLE`
Source baseline: `9ab19955e9a06c90f076dabee58a00bac5a976b5`
Source implementation head: `1f5f4883c33ba1ef037629134225e2602b7c62a6`
Active blueprint: `blueprints/2026-08-08-home-program-kinetic-type-transition.md`

## Latest owner correction

Owner runtime proof showed that exact Codrops `8vw` title sizing plus a fixed `12vw` title row fails for longer localized SchoolAI titles such as `Taman Kanak-kanak`: the title wraps to multiple lines, overflows its fixed grid row and collides with the description.

The media geometry itself is accepted for this correction and must not move.

## Implemented correction

- Keep desktop article geometry at `top: 20svh`, `height: 80svh`, `width: calc(38vw + 280px)`.
- Keep media in column 3 spanning the full article height. No media position/size rule changed in this batch.
- Replace the fixed desktop title row `12vw` with content-driven `auto`:
  - `grid-template-rows: 10vw 2rem auto auto 1fr`.
- The description remains row 4, so it now starts after the rendered title height instead of occupying a fixed coordinate that can collide with wrapped glyphs.
- Blade derives a locale-aware title scale from the rendered localized title length using `mb_strlen`:
  - `short` <= 12 characters;
  - `medium` <= 20 characters;
  - `long` > 20 characters.
- Desktop title scale:
  - short: `8vw`, preserving the reference scale for genuinely short titles;
  - medium: `5.75vw`;
  - long: `4.75vw`.
- The same length tiers apply under RTL; Arabic keeps Cairo and its Arabic line-height/casing contract.
- `text-wrap: balance` is applied to the desktop detail title.
- Back remains directly above the title.
- Description remains reference-like `1rem` copy.

## Existing Program contract retained

- Detail content remains only Back + title + one description + one media item.
- Detail media and its Codrops reveal animation are unchanged in this batch.
- Idle cards remain desktop `2 + 4`, tablet `3 + 3`, mobile `2 + 2 + 2`.
- Program handoff, kinetic background and Center Split heading are unchanged.
- Visi/Misi and unrelated homepage sections are unchanged.

## Scope proof

Implementation changes in this batch are limited to:

- `resources/views/home/sections/featured-programs.blade.php`
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

- `Taman Kanak-kanak` no longer collides with its description;
- short titles retain the large Codrops-like display scale;
- medium/long titles remain visually dominant but fit in roughly one or two readable lines;
- description always begins after title content;
- media position and scale remain identical to the accepted previous screenshot;
- ID/EN/AR and RTL/LTR remain readable;
- compact layouts remain unchanged;
- Chromium and WebKit.

STATUS: `BLOCKED_BY_MISSING_EVIDENCE`

NEXT EXECUTION CHANNEL: `owner/local terminal`

NEXT VALID STEP: pull current `main`, run `HomeProgramJourneyTest`, then recheck the same desktop `Taman Kanak-kanak` detail screenshot before changing media geometry again.
