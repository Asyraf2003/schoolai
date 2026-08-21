# Desktop Article Journey and Footer Release

Status: `IMPLEMENTING`
Date: 2026-08-21
Source main SHA: `f8ae8150b5932c3d2a86d6607da990650de87028`
Route: `/` — Article and Footer
Channel: Terminal Codex

## Goal and scope

Replace the homepage Article presentation while reusing current database article
data: centered small media grows to a fluid 16:9 feature; title and description
meet its exact corner/edge geometry; vertical input drives a pinned horizontal
story, then a vertical media roll and final Article CTA; the scene releases to a
roughly full-viewport desktop Footer without a jump.

Editable owners are the Article Blade partial, a new modular Article CSS/JS
surface, homepage entry imports, focused tests, and a home-only Footer modifier.
Article detail/list pages, article CRUD/security, Gallery, and unrelated Footer
content are protected.

## FACT, GAP, and decision

- Homepage data is database-only, newest-first, capped at four items, with CTA
  already resolved to the named `artikel` route.
- Legacy presentation selectors are tied to `artikel-digest`; a new class
  namespace can replace that owner without a cascade fight.
- Exact Cloudflare origin/config is absent. `MEDIA-CLOUDFLARE-001` blocks only
  migration of hotlinked content media, not this presentation work.
- Owner decision: native vertical input, deterministic sticky scene, no browser
  horizontal-scroll interaction, and four media in the roll when four exist.

## Contracts

- XS/SM/MD/LG: retain one semantic featured-article link and final listing CTA
  in native vertical flow; no new visual tuning or pinned scene.
- XL/2XL: fluid opening around 42vw, 7/8-media horizontal panels with alternating
  top/bottom copy, 2/5-width vertical roll plus 1/8 gap and final full-height
  media, then clean native-flow release.
- ID/EN/AR share one DOM/data source; no RTL choreography tuning claim.
- Reduced motion and unsupported sticky capability use readable vertical cards.
- Images keep intrinsic ratio/dimensions and lazy decode; no new remote origin,
  dependency, canvas, WebGL, drag carousel, or continuous offscreen loop.

## Active execution and proof

1. `PASS`: semantic Article markup and modular desktop scene are implemented.
2. `PASS`: Article source/empty/database/CTA tests pass in the focused run.
3. `PASS`: 1440 × 900 Chromium proves 188.05 → 604.8px opening growth,
   16:9 ratio, exact 0px corner/edge alignment, horizontal and roll travel,
   active `/artikel` CTA, no overflow, and no runtime exception.
4. `PASS`: sticky release is contiguous with the Footer at the same document
   coordinate; the home Footer resolves to exactly 900px at this viewport.
5. `BLOCKED_BY_MISSING_EVIDENCE`: Cloudflare origin is unknown and global
   structure/GD gates are blocked independently of this scene.

Acceptance requires every requested phase in order, deterministic reverse/rapid
scroll, usable links and CTA, no document horizontal overflow, and no Footer jump.
