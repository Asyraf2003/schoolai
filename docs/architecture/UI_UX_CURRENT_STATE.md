# UI/UX Engineering — Current State and Progress Ledger

Status: `FAIL / G2-VALUES-MOTION-STUDY`
Updated: 2026-08-12
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Current UI source head before this docs-only state change: `f8306496575876cfcfd680270871fa0e50b893ed`
Active G1 blueprint: `blueprints/2026-08-11-g1-program-formation-character-handoff.md`
Active G1 checklist: `UI_UX_G1_PROGRAM_FORMATION_CHECKLIST.md`
Active reference study: `references/2026-08-12-lusion-area-of-expertise-motion-study.md`
Parent release checklist: `UI_UX_RELEASE_READINESS_CHECKLIST.md`

## Current FACT

- G1 Program formation is implemented.
- Program rests in a white visual field, forms six cards sequentially, receives a
  breathing interval, then hands the shared field continuously to Values blue
  `#2038ff`.
- The owner has visually accepted the Program → Values color transition.
- Program open/detail/back behavior remains a separate protected owner.
- Desktop Values card motion was recovered after an earlier regression and the
  owner reported that the restored desktop behavior is visually good.
- The current desktop entry spacing uses only the existing center-relative pose
  ratios and does not add a second raw-scroll driver.
- Tablet/compact Values card motion is **not accepted yet**.
- A mode-3 responsive experiment currently exists at source head
  `f8306496575876cfcfd680270871fa0e50b893ed`.
- Runtime screenshots and owner review show that the responsive experiment still
  does not behave as one coherent spatial system: the projected top edge can be
  excessively high, rotation and body travel do not feel phase-locked, and fast
  scroll can feel like state swapping rather than an object constrained to a
  continuous rail.
- The current mode-3 experiment includes `tabletRailY(...)`, raw target progress
  passed into card paint, and a staged `180° → 100° → 10° → 0°` angle sequence.
  These are now treated as experimental evidence, **not final architecture**.
- Values line/worm/spatial rendering is not the active defect and remains frozen.
- The most recent full PHP-suite evidence remains the earlier owner-reported
  `206 passed (1794 assertions)`; a fresh complete current-head proof still has
  to be recorded later.

## Why source is frozen now

Further screenshot-driven angle or Y-offset tuning would accumulate patches on a
responsive model whose coordinate ownership is not yet proven.

The active question is no longer:

`What angle/offset makes this screenshot look closer?`

It is:

`What trajectory and transform ownership make the card one coherent object across slow, fast, and reverse scroll?`

Until that is measured, do not add another responsive Values card patch.

## Protected baseline

### Desktop

Freeze the accepted desktop Values behavior:

- `desktop-layout.js`;
- `desktop-keyframes.js`;
- restored `hidden → deck → fan → preFlip → flip` choreography;
- accepted entry-spacing tuning;
- desktop Program → Values handoff.

### Unrelated Values owners

Freeze:

- Pondasi heading choreography;
- Values line/worm/spatial renderer;
- card content and visual styling;
- reduced-motion behavior unless the later rail blueprint explicitly requires a
  bounded adapter.

### Other homepage surfaces

Freeze:

- Program geometry and detail interaction;
- Hero;
- Vision/Mission;
- Gallery;
- Article;
- About;
- Testimonial;
- unrelated navigation.

## Active reference study

The current authoritative work item is:

`docs/architecture/references/2026-08-12-lusion-area-of-expertise-motion-study.md`

Its purpose is to measure the Lusion Area of Expertise card motion before a new
SchoolAI responsive rail architecture is chosen.

Required study evidence includes:

- one card first, not all four at once;
- primary viewport `841 × 878` CSS px;
- at least 12 ordered checkpoints P00–P11;
- `getBoundingClientRect()` geometry;
- computed `transform` / `matrix` / `matrix3d`;
- transformed parent ownership;
- `transform-origin`;
- `perspective` and `perspective-origin`;
- top-edge direction/slope behavior;
- slow scroll;
- normal scroll;
- fast scroll;
- rapid reverse;
- interruption/recovery.

Observed facts, architecture inference, and SchoolAI design decisions must remain
separate.

## Working hypothesis

The hypothesis to prove or reject is:

```text
native scroll
    ↓
scene target progress
    ↓
critically damped visual progress
    ↓
card-local progress + card offset
    ↓
one spatial rail sampler
    ↓
{x, y, z, rx, ry, rz, scale}
    ↓
one coherent card pose
```

This is a proposed SchoolAI direction, not a claim about Lusion's private source.

The critical invariant is that scroll speed may change how quickly the target
moves, but must not change the geometric path through which the visual card is
sampled.

## Guardrails from previous regressions

Do not repeat these patterns without new measured evidence:

- heading/document geometry used as a second card-motion coordinate system;
- direct raw `window.scrollY` competing with smoothed Values story progress;
- release/capture patches added solely to fix visual spacing;
- separate rotation and body-motion drivers that can lose phase lock;
- checkpoint-angle tuning presented as a final motion model;
- modifying desktop because tablet is defective.

## G1 status

G1 is still not formally PASS because the complete automated/runtime proof bundle
has not been recorded on the latest accepted source state.

However, the active visual design problem has moved into G2 responsive motion
study because:

- desktop is owner-accepted as the protected visual baseline;
- tablet/compact is the unresolved quality gap;
- Safari/macOS has not yet received final proof;
- phone tuning remains later work after the responsive motion model is understood.

## Release sequence from here

1. `G2 Motion Study` — measure Lusion Area of Expertise at tablet width.
2. `G2 Blueprint` — only after evidence threshold, define SchoolAI responsive
   card rail architecture.
3. `G2 Implementation` — replace the disproven mode-3 experiment rather than
   stacking another patch on it.
4. `G2 Runtime Proof` — tablet first; then phone adapter; desktop regression check.
5. `G2 Browser Proof` — Chromium, then Safari/WebKit evidence.
6. `G3 Locale Proof` — ID/EN LTR and AR RTL.
7. close remaining G1 automated proof bookkeeping against the accepted source.
8. `G4` Blade presentation boundary.
9. `G5` Cloudflare content-media ownership.
10. `G6` login/data security gate.
11. `G7` final deployed regression proof.

Gallery and Article visual/refactor work remain `DEFERRED`.

## STATUS

`FAIL / G2-VALUES-MOTION-STUDY`

This means the responsive Values card model is intentionally frozen for
measurement. It does not mean the homepage is globally broken.

## NEXT VALID STEP

Capture Lusion Area of Expertise checkpoints P00–P11 at `841 × 878` for one card
using the active motion-study protocol. Do not push another responsive Values card
motion change before those measurements are available.
