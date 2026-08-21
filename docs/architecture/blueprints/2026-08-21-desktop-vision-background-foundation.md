# Desktop Vision Background Foundation

Status: `IMPLEMENTING`
Date: 2026-08-21
Source main SHA: `f8ae8150b5932c3d2a86d6607da990650de87028`
Route: `/` — Vision/Mission
Channel: Terminal Codex

## Goal and scope

Add one desktop background compositor that can later accept color and motif per
Vision state and blends states with gradual diffusion/crossfade. No motif or
palette mapping is selected in this batch.

The existing semantic panels, localized copy, media, pinned mask timeline, and
image lifecycle remain unchanged. Editable owners are the Vision Blade
compositor hook, Vision CSS, and a small module integrated into the existing
Vision timeline. Gallery and WebGL owners are forbidden here.

## FACT, GAP, and decision

- The existing Vision timeline already owns one normalized progress value and
  active panel state.
- The section has a fixed background only; no state compositor exists.
- `VISION-MOTIF-MAPPING-001`: exact motif and palette per state are unknown and
  explicitly deferred by the owner.
- Decision: expose configurable state slots whose default values preserve the
  current visual background and use no motif asset.

## Contracts

- XS/SM/MD/LG: preserve current static section background and layout.
- XL/2XL: two compositor layers crossfade from the existing timeline; layers
  accept color, pattern image, size, position, opacity, and diffusion variables.
- ID/EN/AR share the same compositor; no direction-specific motif assumption.
- Reduced motion resolves immediately to a stable state without blur animation.
- No new media request, dependency, canvas, renderer, or initial-path payload.

## Active execution and proof

1. `PASS`: configurable two-layer compositor is integrated with the existing
   Vision timeline; no second progress owner was added.
2. `PASS`: focused source/DOM tests and 1440 × 900 runtime prove an in-between
   layer at opacity `.8548` with `blur(1.16px)` and no runtime exception.
3. `DEFERRED`: motif and final palette mapping remain owner decisions; all
   placeholder states intentionally resolve to the current color and `none`.
4. `BLOCKED_BY_MISSING_EVIDENCE`: repository-wide gates retain the baseline
   structure/GD blockers recorded in the current-state ledger.

Acceptance requires no hard cut, no duplicate state controller, unchanged copy
and images, and defaults visually equivalent to the current background.
