# Desktop Program and Values Continuity

Status: `IMPLEMENTING`
Date: 2026-08-21
Source main SHA: `f8ae8150b5932c3d2a86d6607da990650de87028`
Route: `/` — Program and Values
Channel: Terminal Codex

## Goal and scope

Raise the desktop Program heading and formation field, shorten the final
Program-to-Values breathing interval, make the shared handoff and Values rest
on one exact blue token, and bring the desktop Values deck close to its heading.

Editable owners are Program wide geometry, Program formation constants, the
shared `program-values-world` compositor, and desktop Values pose geometry.
Program formation order, dialog/open/back/media behavior, Values keyframe
sequence, Values spatial/worm renderer, content, and semantic DOM are protected.
Tablet, phone, and Arabic/RTL visual tuning are deferred.

## FACT, GAP, and decision

- Program heading/cards are laid out by `program-journey` CSS and revealed by
  its local formation sampler.
- Program and Values share one visual world, but `#2038ff` is duplicated across
  separate CSS owners.
- Desktop Values cards are center-relative in `desktop-layout.js`; the accepted
  `hidden -> deck -> fan -> preFlip -> flip` phases live in keyframes.
- Owner decision: desktop-only normalized geometry may change; protected motion
  meaning and interaction must not.

## Contracts

- XS/SM/MD/LG: preserve current composition and motion; regression only.
- XL/2XL: use fluid `svh`/`vw`/`clamp()` geometry, one shared final-color token,
  and a center-relative deck offset only.
- ID/EN/AR keep one DOM and current direction adapters; no locale tuning claim.
- Reduced motion remains static and usable. Unsupported motion preserves cards,
  headings, and Program controls.
- No new controller, raw-scroll driver, dependency, media, or renderer.

## Active execution and proof

1. `PASS`: bounded XL/2XL geometry/token patch implemented.
2. `PASS`: 1440 × 900 Chromium proof records heading-to-field `-28.81px`,
   cards-to-Program-end `225.59px`, Values deck gap `67.91px`, exact
   `#2038ff`, stable reverse state, no overflow, and working Program open/back.
3. `PASS`: affected Program/Values suite passes inside the 26-test focused run.
4. `BLOCKED_BY_MISSING_EVIDENCE`: the repository-wide structure gate already
   fails at source HEAD, while the full PHP gate also needs unavailable GD.

Acceptance requires closer heading/card geometry, approximately quarter-viewport
handoff breathing space, exact shared final blue, intact Program controls, no
stale reverse state, and no horizontal document overflow.
