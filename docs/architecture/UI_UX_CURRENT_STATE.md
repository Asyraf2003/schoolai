# UI/UX Engineering Current State

Status: `FAIL`
Updated: 2026-08-07
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Current source SHA: `ed4346f0768add58024aa88748c9a1b94655f2d2`
Active governance batch: `HOME-PROGRAM-021-GOVERNANCE-HARDENING`
Active incident: `INC-2026-08-07-HOME-PROGRAM-001`
Active blueprint: `blueprints/2026-08-06-home-program-scroll-rail.md`

## Owner evidence

Owner screenshots and feedback on 2026-08-07 prove:

- Visi/Misi horizontal choreography is not preserved as requested;
- Program white canvas, media, title, and description produce unintended
  compositions;
- Program transitions and rail/full-frame behavior remain incorrect;
- implementation introduced unrequested architecture and visual decisions.

This is runtime and owner-visible `FAIL`.

## Evidence status

```text
SOURCE_SYNTAX: previously PASS for inspected files
SOURCE_STRUCTURE: previously PASS for limited contracts
BUILD_TEST: BLOCKED_BY_MISSING_EVIDENCE
RUNTIME_GEOMETRY: FAIL
RUNTIME_CHOREOGRAPHY: FAIL
RESPONSIVE_LOCALE: BLOCKED_BY_MISSING_EVIDENCE
BROWSER_ENGINE: BLOCKED_BY_MISSING_EVIDENCE
ACCESSIBILITY: BLOCKED_BY_MISSING_EVIDENCE
PERFORMANCE: BLOCKED_BY_MISSING_EVIDENCE
OWNER_VISUAL: FAIL
PUBLICATION: PASS
FINAL_STATUS: FAIL
ROOT_CAUSE: BLOCKED_BY_MISSING_EVIDENCE
```

Source checks and publication do not offset runtime failure.

## Invalidated decisions

The following are not owner-accepted facts and must not be reused without fresh
evidence and acceptance:

- separate white curtain;
- percentage rail mapping;
- moving Program copy between sections;
- independent sticky owners as the final architecture;
- runtime movement of the entire Program section into Visi/Misi;
- coupled `program:layout` and `vision:layout` ownership;
- direction-based `floor/ceil` copy timing;
- any claim that no visual/interaction decisions were added.

## Source freeze

No further Program or Visi/Misi production source mutation is allowed until:

1. the runtime reproduction packet is collected;
2. competing root-cause hypotheses are tested;
3. one architecture survives adversarial review;
4. blueprint acceptance evidence exists;
5. a candidate-branch proof plan and rollback are defined.

## Required runtime packet

```text
current SHA
Firefox/Chromium version, OS, hardware
viewport width x height, zoom
locale/direction
URL and initial hash
reload versus in-page navigation
input and exact steps
recording from end of Visi/Misi through Program 2
DOM parent chain
computed sticky/position/overflow/transform values
bounding rectangles
scroll/current/target state
module mount order
```

## Progress

Governance hardening is the only active capability. Visual/runtime progress does
not increase from this docs batch.

## One next valid step

`owner/local terminal`: collect the read-only runtime reproduction packet for
SHA `ed4346f0768add58024aa88748c9a1b94655f2d2`. Do not edit source.
