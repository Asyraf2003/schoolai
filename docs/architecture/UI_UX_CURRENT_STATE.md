# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-05
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-05-vision-mission-mature-motion-static-copy.md`
Mature source baseline: `a1287ed901f983d7b6cffda03db723b511bc1832`

## Active production batch

- ID: `HOME-VISION-011-MATURE-MOTION-STATIC-COPY`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Vision/Mission `#visi-misi`
- Goal: restore mature responsive geometry and story motion, while making
  Vision/Mission copy immediately visible and non-animated.

## Owner correction

- Desktop `>=1181px` keeps the horizontal journey.
- Large tablet `1024-1180px` keeps Vision and Mission side by side.
- Narrow tablet and phone `<=1023px` keep the close vertical stack.
- Image 2-3 swap and Program travel remain active.
- Only Vision/Mission text entrance motion is removed.

## Implemented source

- Restored Blade and four Vision/Mission CSS owners from mature commit
  `a1287ed9`.
- Restored the delayed production loader from the same baseline.
- Track and image-stack WAAPI progress remain active.
- Removed typography preparation and typography-entry ownership.
- Removed compact Vision/Mission lift, opacity, and split-text progress.
- Active timeline now owns only track travel and image 2-3 swap.
- Copy remains semantic and visible before JavaScript enhancement.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | checked before publication |
| mature geometry restoration | `PASS_SOURCE` | exact baseline blobs restored |
| copy motion isolation | `PASS_SOURCE` | active controller/timeline have no typography import |
| JS syntax | `PASS_LOCAL_STATIC` | controller, preparation, timeline parsed by Node |
| source line limit | `PASS_LOCAL_STATIC` | active JS files remain <=200 lines |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| focused/full PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | runtime review required |

## Out-of-scope failures retained

- `Admin/GalleryItemSoftDeleteTest`
- `HomeValuesStoryTest`

## STATUS

The mature horizontal/compact journey is restored. Vision and Mission copy now
loads directly in its final readable state. Completion remains blocked by local
build, tests, and browser proof.

## NEXT VALID STEP

Fast-forward `main`, build assets, then verify that copy is immediately visible
and that PC horizontal travel plus compact image/program motion still behaves as
it did at the mature baseline.
