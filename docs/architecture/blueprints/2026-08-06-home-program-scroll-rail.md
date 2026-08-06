# Homepage Program Independent Sticky Track Blueprint

State: `OWNER_ACCEPTED / IMPLEMENTED_SOURCE`
Date: 2026-08-07
Surface: homepage Visi/Misi handoff and Program `#program`
Source baseline: `719a3b5114660767dc7268a325b9465622754132`
Batch: `HOME-PROGRAM-019-SEPARATE-OWNERS`

## OWNER GOAL

Keep every intended animation while separating Program from Visi/Misi. Program
must own its title, description, white opening canvas, six media panels, link,
and clickable rail. The title moves once toward the logical corner. The
description remains fixed. Only the white/media track moves vertically.

## FACT AND ROOT CAUSE

- The previous source placed Program intro copy inside the Visi/Misi horizontal
  track and then moved those nodes into a fixed Program HUD at runtime.
- Program also contained a second white intro frame, creating two Program intro
  canvases under different owners.
- The HUD used `position: fixed`, so copy was attached to the browser viewport
  rather than the Program sticky viewport.
- Fragment links moved native document scroll while the visual lerp continued
  from its old current value, leaving the viewport between media frames.
- Removing the Program panel from Visi/Misi changes its horizontal track width;
  the former fixed `430svh` height therefore cannot preserve motion proportion.

## REQUIRED ARCHITECTURE

### Visi/Misi owner

- Contains only vision copy, mission copy, and its image composition.
- Retains horizontal WAAPI track and image-stack animation.
- Calculates vertical scroll distance from actual horizontal overflow:
  `viewport height + (track width - viewport width)`.
- Contains no Program title, description, selectors, or data attributes.

### Program owner

- Contains one sticky viewport.
- Contains one vertical media track with exactly seven full-screen frames:
  white intro plus six program images.
- Contains one separate absolute copy layer inside the sticky viewport.
- Owns localized Program intro title and description directly in its Blade.
- Uses an invisible intro guide only to preserve the existing intro geometry
  across viewport sizes; the guide adds no visible content or animation.
- Moves the title from the intro guide position to the corner during frame zero.
- Keeps the description at the intro guide position for all Program frames.
- Changes title and description text with opacity/light blur only.
- Keeps the link and rail hidden during the white intro, then reveals them on
  the first full media frame.

### Rail transaction

For program index `i`:

- local visual position is `(i + 1) * viewport height`;
- document position is `Program start + local visual position`;
- click sets document scroll and visual current to the same value;
- URL hash is updated without triggering a second browser anchor scroll;
- no snap timer, wheel interception, projected landing, or automatic snapping
  is introduced.

## RESPONSIVE, LOCALE, AND FALLBACK

- ID/EN remain LTR and AR uses logical mirrored title/rail placement.
- Intro guide and visible intro copy share the same responsive typography and
  layout rules.
- Compact tiers retain their existing centered title behavior and rail
  visibility rules.
- Without JS, Program copy overlays only the white intro frame and each media
  article retains its semantic fallback title, description, and link.
- Reduced motion maps visual current directly to native target.

## FORBIDDEN

- Program DOM inside `vision-mission.blade.php`
- `data-program-origin`
- moving Program nodes between sections
- browser-viewport fixed Program HUD
- a separate white curtain
- category metadata or `00 / 00` counters
- percentage-based rail target positions
- visual changes outside Visi/Misi ownership cleanup and Program correction

## PROOF

Available source gates:

- JavaScript syntax
- focused PHP syntax
- CSS brace balance
- changed source files at or below 200 lines
- absence of cross-section Program ownership and fixed HUD
- one intro plus six media frames
- exact document/visual rail targets
- Visi/Misi height based on real horizontal travel

Runtime/browser/build proof remains `BLOCKED_BY_MISSING_EVIDENCE` until run.
