# Homepage Visi/Misi to Program Blueprint

Status: `DRAFT`
Updated: 2026-08-07
Surface: homepage Visi/Misi -> Program
Current failing SHA: `ed4346f0768add58024aa88748c9a1b94655f2d2`
Incident: `INC-2026-08-07-HOME-PROGRAM-001`

## Acceptance status

```text
ACCEPTED BY: not recorded
ACCEPTED AT: not recorded
OWNER STATEMENT: no acceptance of the implemented runtime architecture
EXPLICITLY ACCEPTED VISIBLE RESULT: preserve Visi/Misi motion, Program white
canvas/media layering, stable copy layer, full-frame program states, and
accurate rail navigation
EXPLICITLY ACCEPTED ARCHITECTURE: none
```

The previous `OWNER_ACCEPTED / IMPLEMENTED_SOURCE` label was invalid and is
withdrawn. Implementation is forbidden while this blueprint remains `DRAFT`.

## Owner-visible goal

- Visi/Misi horizontal motion must remain intact.
- Program must follow without collision or broken section handoff.
- Program white canvas must behave according to the owner's frame/layer model.
- title, description, and list belong to an independent copy layer;
- only the instructed title movement may occur;
- rail selection must land on the related full media frame;
- no metadata, counter, layout, timing, dwell, snap, or architecture may be
  invented.

## Proven failure

Owner screenshots prove the current result violates the goal. They do not prove
one final technical root cause.

## Rejected or unproven approaches

- fixed curtain overlay;
- percentage anchors;
- cross-section copy reparenting;
- separate sticky-owner handoff;
- runtime Program-root reparenting;
- coupled Vision/Program layout events;
- midpoint or direction timing rules selected by the agent;
- source-token tests used as behavior proof.

None may be repeated without new evidence and explicit owner acceptance.

## Open gaps

### PROGRAM-RUNTIME-001

```text
Unknown: actual Program DOM parent and containing block when failure occurs
Blocks: ownership architecture
Evidence: live DOM parent chain, computed styles, rectangles
Falsifier: Program is proven under the expected owner with stable geometry
```

### PROGRAM-RUNTIME-002

```text
Unknown: effect of initial #program-scroll-* hash and module mount order
Blocks: rail/deep-link architecture
Evidence: reload without hash, reload with hash, click after stable mount,
scroll/current/target and module-order logs
```

### PROGRAM-RUNTIME-003

```text
Unknown: exact physical media progression required between full frames
Blocks: track mapping and copy timing
Evidence: owner-approved storyboard or annotated recording with frame milestones
```

### PROGRAM-RUNTIME-004

```text
Unknown: smallest stable relationship between Visi/Misi and Program
Blocks: separate owner versus one shared coordinator
Evidence: runtime geometry plus adversarial comparison of both designs
```

## Candidate options are not yet decisions

No option may be implemented until runtime evidence is collected. The eventual
decision must compare:

- separate semantic sections with a proven visual handoff;
- one static shared story coordinator with child semantic sections;
- another minimal architecture derived from runtime facts.

Runtime DOM reparenting is not a default option.

## Mandatory critic

The next proposal must address initial hash, reverse/interrupted scroll, resize,
short height, zoom, reduced motion, failed enhancement, locale/RTL, BFCache,
module order, duplicate ownership, rollback, and whether tests verify behavior.

## Proof and promotion

Next implementation must use a candidate branch. Promotion requires:

- automated source/build/test gates;
- runtime DOM/computed-style/geometry proof;
- Firefox/Chromium and required WebKit proof;
- rail and initial-hash proof;
- forward/reverse/interrupted motion;
- responsive/locale/accessibility matrix;
- owner visual acceptance;
- documented rollback.

## One next step

Collect the read-only runtime reproduction packet. No source changes.
