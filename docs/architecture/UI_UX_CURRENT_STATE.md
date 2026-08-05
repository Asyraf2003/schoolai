# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-05
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-05-vision-mission-static-copy-image-rollback.md`
Source baseline before batch: `278b8a9a298d00a30a6fcc36fed13c7c0e346eaf`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, or performance completion.

## Active production batch

- ID: `HOME-VISION-010-STATIC-COPY-IMAGE-ROLLBACK`
- State: `IMPLEMENTED_SOURCE`
- Surface: homepage Vision/Mission `#visi-misi`
- Goal: preserve static critical copy while restoring the former image journey.
- Protected: locale copy, Hero, Values, Gallery, Articles, navigation, footer,
  routes, DB, authentication, dependencies, and media assets.

## Owner-accepted decisions

- Static applies only to Vision and Mission text.
- Copy uses three responsive layouts and remains centered in ID, EN, and AR.
- Image geometry, pinned track, image 2-3 swap, and Program journey are restored.
- Copy is structurally outside the animated track.
- Motion code does not query or mutate copy or typography nodes.

## Implemented source

- Added a static copy stage before the motion wrapper.
- Restored the original vertical two-image stack and frame geometry.
- Restored compact vertical and desktop horizontal image/Program travel.
- Replaced the temporary image-only viewport controller with the shared track
  controller stripped of typography ownership.
- Removed typography preparation, entry synchronization, lift, opacity, and text
  animation from the active controller and timeline.
- One observer, one passive scroll listener, one resize listener, and one RAF
  remain for image/Program motion only.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| current `main` validation | `PASS_SOURCE` | branch checked before write |
| screenshot/root-cause audit | `PASS_SOURCE` | horizontal stack conflicted with vertical transform |
| source scope isolation | `PASS_SOURCE` | Vision copy and image journey owners only |
| JS syntax | `PASS_LOCAL_STATIC` | active JS modules parsed with Node |
| CSS brace balance | `PASS_LOCAL_STATIC` | active CSS owners checked |
| source line limit | `PASS_LOCAL_STATIC` | changed source files <= 200 lines |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector has no checkout |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| focused/full PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | owner checkout required |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | runtime review required |

## STATUS

Static copy and restored image journey are implemented in source. Completion is
blocked by checkout build/tests and responsive browser proof.
