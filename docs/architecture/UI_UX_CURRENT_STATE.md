# UI/UX Engineering — Current State and Progress Ledger

Status: `FAIL / G1-OPEN`
Updated: 2026-08-11
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `G1-PROGRAM-FORMATION-HANDOFF-001-AUDIT`
Source implementation baseline: `ce1aa637c08cc70f13cbe254ed4da32e8bafd254`
Active blueprint: `blueprints/2026-08-11-g1-program-formation-character-handoff.md`
Active checklist: `UI_UX_G1_PROGRAM_FORMATION_CHECKLIST.md`
Parent release blueprint: `blueprints/2026-08-11-release-readiness-program-values-hardening.md`
Parent release checklist: `UI_UX_RELEASE_READINESS_CHECKLIST.md`

## Current FACT

- The bounded Program/Values baseline repair and production-only GA4/CSP change
  are in source baseline `ce1aa637...`.
- The owner synchronized the earlier stale local checkout to remote main.
- The owner has now reported a full PHP suite result of `206 passed` with `1794
  assertions` and no failed PHP test in that run.
- The exact `git diff --check`, `npm run check:structure`, `npm run build`, and
  source SHA from that same owner run were not included in the latest evidence
  message, therefore this ledger records the PHP-suite proof but does not invent
  the rest of G0 proof.
- No production source mutation for the new G1 choreography has been executed.
- G1 choreography and proof requirements are now owner-accepted and documented.

## Owner-accepted G1 goal

Program should feel longer and more deliberate before Character/Values takes
over.

Accepted narrative:

`WHITE EMPTY FIELD → CARD 1 → CARD 2 → CARD 3 → CARD 4 → CARD 5 → CARD 6 → FULL PROGRAM HOLD → WHITE-TO-BLUE HANDOFF → PONDASI KARAKTER → VALUES`

Required behavior:

- Program begins/settles on a white visual field.
- Program cards are initially visually absent but remain under the same semantic
  Program ownership.
- Cards reveal one by one from a subtle below-position into their existing final
  layout positions.
- Downward formation accumulates `0 → 1 → 2 → 3 → 4 → 5 → 6`.
- Reverse scroll unwinds deterministically `6 → 5 → 4 → 3 → 2 → 1 → 0`.
- After card 6, all cards receive a deliberate white breathing/hold interval.
- Only after that hold does the shared visual world continuously transition from
  white toward Character/Values blue `#2038ff`.
- Pondasi Karakter receives the stage after blue ownership is established.
- Existing Program layout, detail/open/back behavior, Values cards/worm, Gallery,
  and Article remain protected.

## G1 motion decisions

- Native scrolling remains the input/target.
- Do not implement one-wheel-notch-per-card behavior.
- Do not add wheel hijacking, automatic snap, projected landing, anchor settling,
  forced `window.scrollTo`, or click-to-frame behavior.
- Card reveal is progress/state driven rather than six long independent timed
  animations that continue against reverse scroll.
- Exact translate offset, reveal span, and white-hold span are **not** guessed in
  docs. They must be selected from source geometry and representative runtime
  evidence.
- Existing responsive Program grid topology remains unchanged.
- Neutral card formation is block-axis motion and is not mechanically mirrored
  for Arabic RTL.
- Reduced motion must preserve readable Program formation/state ownership without
  requiring pronounced translation.

## Protected scope

Do not redesign or optimize these during G1 unless direct evidence proves a
bounded compatibility defect:

- Program card geometry/layout;
- Program copy, media, detail composition, Back control, and hit targets;
- Program existing open/detail/close behavior;
- existing Program/Values sibling hit-layer protection;
- Pondasi Karakter accepted replay/directional contract;
- Values cards and worm/line renderer;
- Gallery and Article;
- Hero, Vision/Mission, About, Testimonial, and unrelated navigation.

## Release workflow remains

1. `G0` — clean automated baseline evidence.
2. `G1` — Program formation → Character color handoff.
3. `G2/G3` — phone/tablet/desktop + Chromium/Safari-WebKit + ID/EN LTR + AR RTL.
4. `G4` — Blade presentation boundary.
5. `G5` — Cloudflare content-media ownership.
6. `G6` — login/data security gate.
7. `G7` — final deployed regression proof.

Gallery and Article visual/refactor work remain `DEFERRED`.

## G1 proof contract

G1 cannot be labeled PASS from one screenshot. The bounded implementation must
prove at minimum:

- initial white Program field;
- card 1 through card 6 sequential formation;
- complete six-card white hold;
- smooth white → blue handoff;
- Pondasi Karakter takeover;
- deterministic reverse;
- rapid forward/reverse recovery;
- Program physical open/back click/tap around the handoff;
- reduced-motion readable fallback;
- no horizontal overflow/layout jump;
- relevant focused tests/build and full PHP suite remain green.

The complete checkbox contract lives in
`UI_UX_G1_PROGRAM_FORMATION_CHECKLIST.md`.

## STATUS

`FAIL / G1-OPEN`

This means G1 is owner-approved but not implemented/proven. It is not a claim
that the existing homepage is globally broken.

## NEXT VALID STEP

Perform a **read-only source audit** of the current Program → Values owners:
background, card motion/visibility, scroll progress, responsive geometry,
shared-world color handoff, Pondasi trigger/replay, reduced-motion fallback, and
existing protection tests.

Do not edit production source until that audit identifies the smallest existing
owner capable of implementing sequential formation plus the white-to-blue
handoff.
