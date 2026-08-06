# Homepage Program Single Seven-Frame Track Blueprint

State: `OWNER_CORRECTED / IMPLEMENTED_SOURCE`
Date: 2026-08-06
Surface: homepage Program `#program`
Source baseline: `3865b248093da2d379d4e6b666e1a6caf13daec6`
Batch: `HOME-PROGRAM-018-SINGLE-SEVEN-FRAME-TRACK`

## Owner goal

Program must use one vertical visual track. The plain white Program opening is
media frame zero, followed by six related full-viewport images. Only the track
moves. The section title, description, action, and clickable six-item rail stay
independent from that media movement.

The original section title moves smoothly to the Program corner during the first
white-to-image transition. After reaching the corner, only title text changes.
The description never changes coordinate; only its text changes.

No category/eyebrow text or `00 / 00` counter is allowed above the title.

## Source facts and rejected implementation

The rejected source used three independent visual owners:

- a six-image track;
- a fixed white curtain with separate `entryProgress`;
- a fixed HUD activated by threshold.

It also placed rail anchors by percentage of total section height. Consequently,
white was not a media frame, description coordinates were captured while the
previous section was moving, text became white while the curtain was still
white, and rail item 2 could resolve to an intermediate or unrelated frame.

That model is removed rather than patched with more thresholds.

## Implemented ownership

| Concern | Owner |
|---|---|
| white frame plus six semantic media frames | Program Blade partial |
| exact viewport distances and active frame | `geometry.js` |
| handoff, copy swapping, track and exit render | `controller.js` |
| native-target `0.08` visual smoothing | existing `motion.js` |
| sticky viewport, full-screen frames, fixed HUD | Program CSS modules |
| durable source contracts | focused Program feature test |

## Track geometry

```text
frame 0 = plain white Program opening
frame 1 = Program image 1
frame 2 = Program image 2
frame 3 = Program image 3
frame 4 = Program image 4
frame 5 = Program image 5
frame 6 = Program image 6
```

Each frame is exactly one measured `window.innerHeight`. The complete media track
height is `(programCount + 1) * viewportHeight`. Its maximum travel is
`programCount * viewportHeight`, leaving Program image 6 full-screen before the
existing Values exit begins.

Rail item `n` targets `n * viewportHeight` from Program start. There is no
percentage approximation and no separate entry distance outside the media track.

## Copy behavior

Before Program starts, the controller records the original title and description
viewport rectangles while they are still visible in Visi/Misi. At Program start:

- the same title node moves into the final title slot;
- inverse geometry keeps it visually at its recorded origin;
- first-frame progress removes that inverse transform, moving only the title to
  its final corner;
- the same description node is placed at its recorded top, left, and width;
- description position receives no scroll-linked transform;
- after the first image is effectively full-screen, title and description text
  swap to Program 1, then follow later active frames.

## Rail and scroll behavior

The rail remains ordinary semantic anchor navigation. Each link points to a real
absolute anchor at the corresponding exact viewport step. Native scroll remains
the document owner. Program does not add snap settling, projected landing,
wheel interception, or `window.scrollTo`.

The visual track follows native target with the existing Gallery-style `0.08`
lerp. Forward, reverse, interrupted input, and rail navigation use the same
single current value.

## Explicit non-goals

This correction does not redesign typography, invent new metadata, add counters,
add category labels, change Program content, modify Visi/Misi, replace images,
change Values, or introduce a new animation style.

## Proof gates

Source checks available before publication:

- JS syntax for controller and geometry
- PHP syntax for focused test
- balanced CSS braces
- changed source files at or below 200 lines
- absence of rejected curtain/metadata/percentage geometry tokens
- atomic fast-forward publication and changed-path verification

Browser and command gates remain `BLOCKED_BY_MISSING_EVIDENCE` until run from a
checkout with the application and browser matrix.
