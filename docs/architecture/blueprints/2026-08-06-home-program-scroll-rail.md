# Homepage Vision-to-Program Integrated Pin Blueprint

State: `OWNER_ACCEPTED / IMPLEMENTED_SOURCE`
Date: 2026-08-07
Surface: homepage Visi/Misi → Program
Source baseline: `9d5dfbe39d84982734ee45c6956a4de464c8811d`
Batch: `HOME-PROGRAM-020-INTEGRATED-DESKTOP-PIN`

## OWNER GOAL

Preserve the complete Visi/Misi horizontal motion and the complete Program
vertical motion without a section boundary interrupting the transition.
Program copy stays locally owned. Program copy changes only when the related
media frame has completely occupied the viewport.

## ROOT CAUSE

- Separating the two sticky roots removed the final `100vw` destination panel
  from the Visi/Misi track.
- Visi/Misi therefore unpinned at its own section boundary before the Program
  phase could continue inside the same visual stage.
- Program active copy used midpoint rounding, so the next title and description
  could appear while the next image was only partly visible.

## REQUIRED DESKTOP ARCHITECTURE

For enhanced widths `>= 1181px`:

1. Program remains a complete semantic section with its own title, description,
   media track, rail, link, and exit.
2. Runtime integration moves the whole Program section into the existing
   Visi/Misi horizontal track as its final `100vw` flex panel.
3. The Visi/Misi root owns one sticky viewport for both phases.
4. Story height is:
   `viewport + horizontal travel + Program vertical travel`.
5. Visi/Misi timeline progress divides only by horizontal travel.
6. Program local progress begins at:
   `story start + horizontal travel`.
7. At that point the Program white intro frame already fills the viewport.
8. Further native scrolling moves only the Program seven-frame vertical track.
9. Program HUD remains absolute inside the Program panel and never follows the
   media transform.
10. Values begins only after Program exit travel completes.

## COMPACT, REDUCED MOTION, AND FALLBACK

- At widths below `1181px`, Program returns to its original sibling location.
- Compact Visi/Misi and Program keep their existing sequential behavior.
- Reduced motion does not integrate the sticky owners.
- Without JavaScript, the original Blade order remains Visi/Misi, Program,
  Values, with all semantic Program fallback content readable.
- Resize across the wide boundary moves the complete Program root and
  immediately recomputes both story geometries.

## FULL-FRAME COPY RULE

Physical media movement remains continuous. No hold, snap, or invented pause is
added.

For visual frame position `p = current / viewport`:

- forward travel uses `floor(p)`;
- reverse travel uses `ceil(p)`;
- Program index is the completed visible frame minus the white intro frame;
- the last non-zero travel direction is retained when the user pauses between
  frames.

Therefore the next Program title and description cannot activate until its
image is fully in the viewport, in either direction.

## RAIL RULE

For program index `i`:

- local position is `(i + 1) * viewport height`;
- integrated document position is:
  `Vision story start + horizontal travel + local position`;
- document scroll and visual current synchronize in the same click transaction;
- the hash updates without browser anchor scrolling a second time.

## FORBIDDEN

- independent desktop sticky boundaries between Visi/Misi and Program
- moving only title or description nodes across owners
- duplicate white curtains
- midpoint `Math.round` active-frame selection
- invented frame dwell, scroll snapping, wheel interception, or projected
  landing
- visual changes to Values, Gallery, header, or unrelated sections

## PROOF

Available source gates:

- JavaScript syntax
- PHP focused syntax
- CSS brace balance
- source files at or below 200 lines
- complete Program-root integration and restoration
- story height includes horizontal and Program vertical distances
- horizontal progress excludes Program vertical distance
- direction-aware completed-frame copy selection
- exact rail document/visual synchronization

Browser, build, full test, performance, and owner visual proof remain
`BLOCKED_BY_MISSING_EVIDENCE` until run.
