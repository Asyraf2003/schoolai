# UI/UX Engineering — Current State and Progress Ledger

Status: `FAIL / G1-RUNTIME-TUNING`
Updated: 2026-08-12
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `G1-PROGRAM-FORMATION-HANDOFF-001-RUNTIME`
Current source head before this docs sync: `5830576134ab69904800f6956dbe4be79f03c86e`
Active blueprint: `blueprints/2026-08-11-g1-program-formation-character-handoff.md`
Active checklist: `UI_UX_G1_PROGRAM_FORMATION_CHECKLIST.md`
Parent release blueprint: `blueprints/2026-08-11-release-readiness-program-values-hardening.md`
Parent release checklist: `UI_UX_RELEASE_READINESS_CHECKLIST.md`

## Current FACT

- G1 is implemented in production source and is no longer a source-audit-only task.
- Program now owns a dedicated formation concern in
  `resources/js/surfaces/home/program-journey/formation.js`.
- Program formation remains native-scroll driven; it does not add wheel hijacking,
  snap, projected landing, forced `window.scrollTo`, or RAF document-scroll writes.
- The shared Program → Values world begins white and continuously transitions to
  Character/Values blue `#2038ff`.
- The owner has visually accepted the white → blue transition behavior.
- Existing Program detail/open/back ownership was intentionally left separate
  from the formation engine.
- Existing Values heading, card flip/fan choreography, worm/line renderer, and
  spatial controller remain separate owners.
- The owner reported that the original Values card motion was restored after the
  regression recovery and should now be treated as the protected motion baseline.
- Current Values desktop entry tuning changes only the existing `hiddenPose` and
  `deckPose` Y ratios. At source head `5830576134ab...` they are:
  - `hiddenPose`: `geometry.cardHeight * 0.012`;
  - `deckPose`: `geometry.cardHeight * 0.008`.
- Those ratios are a visual tuning target for a much tighter entry deck. They are
  not proof of a literal physical 30 px gap on every display.
- The most recent full PHP-suite evidence remains the owner-reported
  `206 passed (1794 assertions)` from the earlier baseline run. A fresh full
  automated run on the current G1 head has not yet been recorded.

## G1 source audit result

The bounded source audit identified these owners:

- Program formation/visibility: `program-journey/formation.js`;
- Program existing open/detail/back: `program-journey/controller.js` + integration;
- Program responsive geometry: Program journey CSS (`compact.css`, `wide.css`,
  base/rail ownership);
- shared white → blue field: `program-values-world.js` +
  `values/story-kinetic.css`;
- Pondasi heading replay: `values/heading-state.js`;
- Values story motion/smoothing: `values/motion.js` + `values/controller.js`;
- Values desktop card choreography: `values/desktop-layout.js` +
  `values/desktop-keyframes.js`;
- Values worm/line/spatial behavior: existing Values spatial controller/renderer;
- reduced motion: existing Program/Values media-query and controller branches.

The selected architecture remains:

`Program formation owner → shared color-world owner → existing Values story owner`

No third transition section or duplicate Values controller was introduced.

## Owner-accepted G1 narrative

`WHITE EMPTY FIELD → CARD 1 → CARD 2 → CARD 3 → CARD 4 → CARD 5 → CARD 6 → FULL PROGRAM HOLD → WHITE-TO-BLUE HANDOFF → PONDASI KARAKTER → VALUES`

The Program side is now implemented around that narrative. Exact runtime rhythm
still requires proof before G1 can be marked PASS.

## Values regression and recovery record

A runtime regression occurred while tightening the initial Values deck spacing.
The failure boundary is now documented so future sessions do not repeat it.

### Last-known-good choreography baseline

Commit `649b5d8d00ca0ed27df1cc3ca41ee43d60a903f2` preserved the original
`hidden → deck → fan → preFlip → flip` chain and only changed local Y ratios.

### Regression cause

The regression began when entry spacing was coupled to heading/document geometry:

- `headingTitleBottomOffset` was introduced;
- `titleDeckOffset()` replaced the original center-relative pose formula;
- a later `centerRelease()` read raw `window.scrollY` while the Values story still
  used smoothed story progress.

That created competing coordinate/progress ownership inside the same card frame.

### Recovery

The recovery restored `desktop-layout.js` and `geometry.js` to the
last-known-good choreography behavior. After recovery, further spacing tuning is
allowed only as bounded changes to the existing center-relative pose offsets.

### Guardrail

For visual spacing requests around the Values entry deck:

- do **not** add heading DOM measurements to card choreography;
- do **not** add raw `window.scrollY` as a second card-motion driver;
- do **not** add release/capture logic merely to change entry spacing;
- do **not** change fan/flip phases, spring/momentum, or center destination unless
  the owner explicitly opens a motion-redesign scope;
- prefer one- or two-number local pose tuning when the requested change is only
  visual entry distance.

## Protected scope

Do not redesign or optimize these during the remaining G1 proof/tuning unless
new direct evidence proves a bounded defect:

- Program card geometry/layout;
- Program copy, media, detail composition, Back control, and hit targets;
- Program existing open/detail/close behavior;
- existing Program/Values sibling hit-layer protection;
- Pondasi Karakter accepted replay/directional contract;
- Values fan/flip/card choreography beyond bounded entry-spacing ratios;
- Values worm/line/spatial renderer;
- Gallery and Article;
- Hero, Vision/Mission, About, Testimonial, and unrelated navigation.

## What remains in G1

G1 is not PASS yet. Remaining evidence on the current source head:

1. fresh automated proof: `git diff --check`, structure check, build, focused
   Program/Values tests, and full PHP suite;
2. desktop runtime proof of Program 1 → 6 formation and complete white hold;
3. deterministic reverse and rapid down/up/down recovery;
4. physical Program open/detail/back around the handoff;
5. reduced-motion proof;
6. no horizontal overflow/layout jump;
7. final owner confirmation that the current tight Values entry position is
   accepted without changing the restored card choreography.

## Values worm/line status

The Values worm/line/spatial system is **not an unfinished G1 implementation**.
It is currently protected and was intentionally not redesigned by the Program →
Character handoff work.

If the owner wants to visually tune the line/worm next, open it as a separate,
bounded Values spatial-polish scope after the current G1 motion/spacing state is
frozen. Do not quietly mix line-renderer changes into G1 proof.

## Release workflow after G1

1. `G1` — finish current proof/tuning and freeze Program → Character behavior.
2. Optional owner-opened bounded Values line/spatial polish, only if still desired.
3. `G2/G3` — full responsive/browser/locale matrix:
   phone/tablet/desktop, Chromium, Safari/WebKit evidence, ID/EN LTR, AR RTL.
4. `G4` — Blade presentation boundary.
5. `G5` — Cloudflare content-media ownership.
6. `G6` — login/data security gate.
7. `G7` — final deployed regression proof.

Gallery and Article visual/refactor work remain `DEFERRED`.

## STATUS

`FAIL / G1-RUNTIME-TUNING`

This status means the bounded implementation exists and has partial owner visual
validation, but G1 has not yet completed its automated/runtime proof contract.
It is not a claim that the homepage is globally broken.

## NEXT VALID STEP

Freeze source changes temporarily and run the **current-head G1 proof bundle**.
Do not modify Values line/worm or reopen card choreography until that proof shows
which, if any, bounded defect still remains.
