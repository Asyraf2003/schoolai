# Homepage Values Reference Motion — Raw Evidence

Status: `RAW_EVIDENCE`
Date: 2026-08-03
Active blueprint: `HOME-VALUES-001`
Surface: homepage Values section `#nilai`
Reference: owner recording of `lusion.co/about`
Reference viewport: `1280x720` recording; computed-style capture at
`1919x964`

This file preserves owner-supplied measurements before production
normalization. It is evidence, not copied source code and not a claim that the
SchoolAI runtime already matches the reference.

## Recording inventory

| Recording | Owner description | Resolution | Nominal fps | Duration |
|---|---|---:|---:|---:|
| `202608032228.mp4` | normal downward scroll | 1280x720 | 60 | 4.156 s |
| `202608032228 (1).mp4` | normal upward scroll | 1280x720 | 60 | 1.648 s |
| `202608032228 (2).mp4` | fast downward scroll | 1280x720 | 60 | 0.720 s |

Observed forward chronology:

```text
previous section
-> title reveal
-> one low back card
-> four-card deck becomes legible
-> deck rises and opens into a tilted fan
-> overlapping first-to-fourth flips
-> information fronts pass through edge-on states
-> small front-side overshoot
-> stable upright information row
-> following section
```

Observed reverse chronology is the same spatial path in reverse. Fast scroll
compresses the chronology but preserves continuity; cards do not teleport to
independent layouts.

## Heading computed style

Selected reference word at rest:

```text
tag: DIV
class: word
text: OF
transform: matrix(1, 0, 0, 1, 0, 0)
transform-origin: 147.352px 115.141px
display: inline-block
font-family: Aeonik
font-size: 230.28px
font-weight: 400
line-height: 230.28px
width: 294.703125px
height: 230.28125px
x: 727.9375px
y: 229.302734375px
```

Parent line evidence:

```text
text: AREA OF
position: relative
font-size: 230.28px
font-weight: 400
line-height: 230.28px
width: 926.703125px
height: 230.28125px
```

Owner-captured word travel:

| Visible state | Computed transform | Y travel |
|---|---|---:|
| hidden 90% | `matrix(1, 0, 0, 1, 0, 207.253)` | 207.253px |
| hidden 50% | `matrix(1, 0, 0, 1, 0, 115.141)` | 115.141px |
| resting | `matrix(1, 0, 0, 1, 0, 0)` | 0px |

The captured values are exactly proportional to the 230.28px line height. The
reference therefore reveals complete word bodies through a clipped vertical
travel rather than fading an already complete word.

## Card hierarchy and projection

Relevant captured hierarchy:

```text
.about-capability-card
  position: absolute
  transform-style: preserve-3d
  transform-origin: 201.492px 281.055px
  base plane: about 402.984375 x 562.109375px

immediate stage wrapper
  transform: matrix(1, 0, 0, 1, 0, 558.974)
  perspective: 964px
  width: 1727.125px
  height: 562.109375px
```

The transformed bounding rectangle changes during `rotateY`; the stable base
plane above is the geometry input. Narrow edge-on bounding widths are projection
results, not changing CSS card widths.

## Raw card matrix snapshots

The owner captured these transforms from `.about-capability-card` during the
reference sequence:

```text
matrix3d(-0.999944, 0.0105584, 0, 0,
         0.0105584, 0.999944, 0, 0,
         0, 0, -1, 0,
         662.07, -6.09499e-16, 0, 1)
```

```text
matrix3d(-0.969943, 0.231876, -0.0737794, 0,
         0.232509, 0.972594, 0, 0,
         0.0717574, -0.0171544, -0.997275, 0,
         88.3427, 0.92392, 0, 1)
```

```text
matrix3d(-0.518213, 0.11574, -0.847384, 0,
         0.217973, 0.975955, 0, 0,
         0.827008, -0.184707, -0.530981, 0,
         45.7965, 5.23846, 0, 1)
```

```text
matrix3d(0.950979, 0, 0.309254, 0,
         0, 1, 0, 0,
         -0.309254, 0, 0.950979, 0,
         5.42089, 8.61335, 0, 1)
```

```text
matrix(1, 0, 0, 1, 0, -2.68264)
```

These snapshots establish one continuous 3D path: back-facing, fanned,
edge-on, front-side overshoot, then settled. They do not establish the original
reference easing function.

## Raw projected front-face samples

The selected `.about-capability-card-front` produced these owner-captured
bounding boxes while the containing card rotated:

| State | Width | Height | X | Y |
|---|---:|---:|---:|---:|
| compact stack | 410.716 | 567.625 | 754.121 | 545.393 |
| fan spreading | 437.796 | 638.682 | 163.888 | 151.105 |
| approximately edge-on | 148.876 | 667.314 | 260.906 | 112.832 |
| front begins to appear | 517.957 | 661.350 | 23.980 | 152.596 |
| full front | 399.178 | 564.654 | 111.551 | 206.781 |
| front overshoot | 302.590 | 600.025 | 162.158 | 173.527 |

The width collapse near the edge-on point confirms a real perspective flip. A
separate width animation is not required.

## Six owner-supplied transform samples

Raw values below use the reference coordinate system and must be normalized for
SchoolAI viewport/card geometry.

| Sample | Container Y | Card | X | Y | rotateZ | rotateY |
|---|---:|---|---:|---:|---:|---:|
| 1 | 471.417 | 1 | 46.398 | 4.191 | -12.639 | 123.539 |
| 1 | 471.417 | 2 | 456.846 | 7.161 | -4.213 | 150.520 |
| 1 | 471.417 | 3 | 867.294 | 3.547 | 4.213 | 167.831 |
| 1 | 471.417 | 4 | 1277.740 | -3.328 | 12.639 | 179.079 |
| 2 | 582.862 | 1 | 29.565 | -7.550 | -7.612 | 65.918 |
| 2 | 582.862 | 2 | 451.235 | -5.996 | -2.537 | 107.185 |
| 2 | 582.862 | 3 | 872.905 | 1.071 | 2.537 | 136.954 |
| 2 | 582.862 | 4 | 1294.580 | 7.153 | 7.612 | 156.925 |
| 3 | 719.985 | 1 | 17.085 | 5.806 | -0.746 | 14.942 |
| 3 | 719.985 | 2 | 447.075 | 8.509 | -0.249 | 45.001 |
| 3 | 719.985 | 3 | 877.066 | 3.390 | 0.249 | 80.369 |
| 3 | 719.985 | 4 | 1307.060 | -4.847 | 0.746 | 114.728 |
| 4 | 857.109 | 1 | 9.872 | -1.307 | -0.072 | -10.699 |
| 4 | 857.109 | 2 | 444.672 | 6.976 | -0.024 | 6.053 |
| 4 | 857.109 | 3 | 879.470 | 8.845 | 0.024 | 30.105 |
| 4 | 857.109 | 4 | 1314.270 | 2.582 | 0.072 | 59.303 |
| 5 | 1048.540 | 1 | 4.591 | 5.045 | 0 | -17.398 |
| 5 | 1048.540 | 2 | 442.911 | 9.772 | 0 | -16.491 |
| 5 | 1048.540 | 3 | 881.235 | 5.515 | 0 | -7.624 |
| 5 | 1048.540 | 4 | 1319.550 | -3.812 | 0 | 8.021 |
| 6 | 1405.600 | 1 | 1.101 | 8.360 | 0 | -0.172 |
| 6 | 1405.600 | 2 | 441.747 | 0.036 | 0 | -6.322 |
| 6 | 1405.600 | 3 | 882.393 | -8.321 | 0 | -13.860 |
| 6 | 1405.600 | 4 | 1323.040 | -9.027 | 0 | 0 |

## Evidence-backed implementation implications

The following are derived decisions, not raw reference ownership facts:

- heading entry should start as soon as the section enters the viewport and run
  independently of continued scrolling;
- Latin heading should remain editorial-light but thicker than the current
  weight `200`;
- the two title masks need a small visible center seam instead of overlapping
  line boxes;
- the PC lead card may begin low, but the deck/fan/flip plane must rise and hold
  near the stage center;
- `rotateZ`, `rotateY`, X, and Y must evolve together;
- per-sample easing that reaches zero velocity at every captured sample creates
  mechanical pauses not present in the recordings;
- production interpolation therefore needs a continuous first derivative across
  samples, plus the existing scroll inertia and bounded float;
- phone, tablet, semantic content, locale ownership, reduced motion, and the
  static fallback remain outside this raw evidence correction.

## Proof boundary

The recordings prove desired chronology and continuity. Computed styles prove
reference geometry at captured instants. Neither proves the reference source
code, its exact easing function, SchoolAI browser parity, performance,
accessibility, or successful implementation.
