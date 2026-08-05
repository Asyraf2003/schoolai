# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-05
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-05-vision-mission-static-three-layout.md`
Source baseline before batch: `a1287ed901f983d7b6cffda03db723b511bc1832`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, or performance completion.

## Active production batch

- ID: `HOME-VISION-008-STATIC`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Vision/Mission `#visi-misi`
- Goal: make critical Vision/Mission information permanently visible through
  three static responsive layouts.
- Protected: semantic copy, media assets, Hero, Values, Programs source,
  Gallery, Articles, navigation, footer, About, Testimonial, routes, DB,
  authentication, and dependencies.

## Owner-accepted decisions

- All Vision/Mission motion is removed from production at every width.
- Desktop `>=1181px` retains Vision left, Mission right, and Mission lower.
- Large tablet `1024-1180px` uses two centered columns.
- Narrow tablet and phone `<=1023px` use a close centered stack.
- Image and Program follow with bounded `2-3svh` rhythm.
- ID, EN, and AR use the same semantic layout and centered copy.

## Implemented source

- CSS is the only runtime owner of Vision/Mission layout.
- The Vision/Mission JavaScript entry was removed from Blade and Vite inputs.
- Sticky pinning, long scroll height, horizontal travel, typography reveal,
  image swap, lift, opacity gates, RAF, observer, and WAAPI are no longer loaded.
- Desktop preserves the accepted copy hierarchy without absolute off-screen
  travel.
- Large tablet uses two columns; smaller widths stack.
- The static media composition shows the main image plus images two and three.
- Program content follows the image without reserving a full viewport.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | branch checked before write |
| latest user change preservation | `PASS_SOURCE` | patch built above `a1287ed9` |
| source scope isolation | `PASS_SOURCE` | Vision owners, Vite entry, docs only |
| CSS brace balance | `PASS_LOCAL_STATIC` | four CSS owners checked |
| source line limit | `PASS_LOCAL_STATIC` | changed source files <= 200 lines |
| JS-disabled semantic result | `PASS_SOURCE` | production layout is CSS-only |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| focused/full PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | runtime review required |

## Out-of-scope failures retained

- `Admin/GalleryItemSoftDeleteTest`
- `HomeValuesStoryTest`

No Gallery or Values source/test owner changed.

## STATUS

The static three-layout Vision/Mission source is implemented. Completion remains
blocked by checkout build/tests and responsive browser proof.

## NEXT VALID STEP

Owner/local terminal: fast-forward `main`, run the proof block, then review ID,
EN, and AR at 1023/1024 and 1180/1181 boundaries.
