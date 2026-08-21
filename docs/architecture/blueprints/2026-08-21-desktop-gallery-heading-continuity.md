# Desktop Gallery Heading Continuity

Status: `IMPLEMENTING`
Date: 2026-08-21
Source main SHA: `f8ae8150b5932c3d2a86d6607da990650de87028`
Route: `/` — Gallery heading
Channel: Terminal Codex

## Goal and scope

Make the desktop Gallery heading and description enter from the preceding
section and exit into the existing depth-gallery journey as one continuous
scroll composition. Preserve Gallery data, fallback, WebGL renderer, CTA, and
route transition.

Editable owners are `welcome-gallery-heading.css` and its local heading
controller. `gallery-depth` files, gallery queries, media, shaders, trail, and
gateway art direction are protected.

## FACT, GAP, and decision

- Gallery heading currently replays a time-based animation when it crosses one
  trigger line; it has no scroll-linked exit state.
- `gallery-depth` begins after a second large margin, producing a detached-page
  feeling even though both live in one section.
- Owner decision: tune only desktop position and entry/exit continuity; do not
  rebuild Gallery or choose a final 3D gateway.

## Contracts

- XS/SM/MD/LG: retain current static/replay behavior without tuning.
- XL/2XL: local normalized section progress drives heading enter/rest/exit; no
  document horizontal scrolling and no Gallery engine ownership changes.
- ID/EN/AR keep one DOM and current direction rules; no RTL tuning claim.
- Reduced motion is fully visible and static. Interruption, reverse, resize,
  page show, and hidden-page cleanup must not leave stale inline state.

## Active execution and proof

1. `PASS`: XL/2XL heading entry/rest/exit is a bounded local scroll state.
2. `PASS`: focused Gallery contracts pass; Chromium entry → rest → reverse
   returns `.6953 → 1 → .6953` with no document overflow or runtime error.
3. `DEFERRED`: final Gallery gateway/3D object remains outside this blueprint.
4. `BLOCKED_BY_MISSING_EVIDENCE`: repository-wide gates retain the baseline
   structure/GD blockers recorded in the current-state ledger.

Acceptance requires continuous entry/exit, preserved depth-gallery behavior,
and no horizontal document overflow.
