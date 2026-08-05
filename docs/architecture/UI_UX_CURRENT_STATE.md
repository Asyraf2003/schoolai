# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-05
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-05-vision-mission-static-copy-image-motion.md`
Source baseline before batch: `91719bd35cfbe11b16c8047a3f1ac74c27d32ce4`

## Active production batch

- ID: `HOME-VISION-009-STATIC-COPY-IMAGE-MOTION`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Vision/Mission `#visi-misi`
- Goal: retain the accepted three static copy layouts while restoring only the
  image 2-3 scroll swap.
- Protected: Vision/Mission copy, locale strings, Program visibility, Hero,
  Values, Programs source, Gallery, Articles, navigation, footer, routes, DB,
  authentication, and dependencies.

## Owner correction

The previous batch interpreted “static” as the whole section. The accepted
meaning is narrower:

- Vision and Mission copy are static and always readable;
- image 2-3 retains scroll motion;
- Program remains static and follows the image stage.

## Implemented source

- Restored the Vite and homepage entry for `welcome-vision-story.js`.
- Replaced the old entry with one image-only controller.
- The production entry does not import the legacy typography/story controller.
- Copy layout and visibility remain CSS-only in three responsive compositions.
- Image 2-3 uses a vertical stack only when image motion is active.
- The swap starts at frame entry and finishes at frame center.
- A bounded sticky image stage holds the final image before static Program copy.
- Reduced motion and failed JavaScript retain the static two-image fallback.
- One passive scroll listener, one observer, one RAF, resize rebuild, and BFCache
  restoration are used.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | branch checked before write |
| source scope isolation | `PASS_SOURCE` | image entry, image CSS, entry wiring, docs |
| copy ownership isolation | `PASS_SOURCE` | controller has no copy/typography query |
| JS syntax | `PASS_LOCAL_STATIC` | entry parsed with Node |
| CSS brace balance | `PASS_LOCAL_STATIC` | enhanced owner checked |
| source line limit | `PASS_LOCAL_STATIC` | changed source files <= 200 lines |
| static fallback | `PASS_SOURCE` | class-free base layout remains readable |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| focused/full PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | runtime review required |

## Out-of-scope failures retained

- `Admin/GalleryItemSoftDeleteTest`
- `HomeValuesStoryTest`

## STATUS

The source now keeps critical copy static while restoring image-only motion.
Completion remains blocked by checkout build/tests and browser proof.

## NEXT VALID STEP

Fast-forward `main`, run the proof block, then verify image progress and static
copy at 390x844, 759x924, 1036x924, 1180px, and desktop in ID, EN, and AR.
