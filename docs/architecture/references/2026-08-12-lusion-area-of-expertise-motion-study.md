# Lusion Area of Expertise — Card Motion Study

Status: `ACTIVE REFERENCE STUDY / SOURCE FROZEN`
Date opened: 2026-08-12
Repository: `Asyraf2003/schoolai`
Reference surface: `https://lusion.co/about/`
SchoolAI comparison surface: Homepage → Values / Pondasi Karakter
Source head when study opened: `f8306496575876cfcfd680270871fa0e50b893ed`

This document is **reference evidence**, not an implementation blueprint.

Its purpose is to stop further screenshot-driven tuning of responsive Values cards and replace it with a measured motion model. No new responsive card architecture should be pushed until this study has enough evidence to describe one card's complete path from entry to upright state.

## 1. Problem statement

The current SchoolAI responsive Values card behavior can look visually plausible at slow scroll but does not yet feel like one coherent spatial system.

Observed owner feedback:

- Lusion cards feel like objects constrained to a soft rail regardless of scroll speed;
- SchoolAI cards can feel like document-flow cards whose rotation and position are corrected separately;
- Lusion maintains a consistent upward-facing perspective during entry;
- SchoolAI has shown excessive top-edge projection and an over-steep intermediate angle;
- Lusion's top card edge changes dynamically as the card travels through the scene;
- SchoolAI's responsive implementation has relied on viewport visibility, rotation phases, and later Y compensation rather than one pose trajectory;
- desktop SchoolAI already feels substantially better because its choreography is closer to `progress → pose`, but it is still not as smooth as the reference.

## 2. Source boundary during study

Until this study reaches the proof threshold below:

### Frozen

- `resources/js/surfaces/home/values/desktop-layout.js`
- `resources/js/surfaces/home/values/desktop-keyframes.js`
- Values heading animation
- Values worm/line/spatial renderer
- Values card content and styling
- Program formation
- Program → Values white/blue handoff
- Gallery, Article, Hero, Vision/Mission, About, Testimonial

### Responsive experiment state

The current mode-3 experiment at the source head above is **not accepted as the final architecture**.

The following concepts are considered experimental evidence only:

- `tabletRailY(...)` compensation;
- separate raw target progress being passed into responsive card paint;
- the staged tablet angle sequence `180° → 100° → 10° → 0°`.

Do not add another patch on top of these before the motion study is complete.

## 3. Hypothesis to prove or reject

The working hypothesis is:

> A Lusion-like card should be modeled as one object sampled from one normalized spatial trajectory. Position, depth, rotation, and scale should be functions of the same local card progress. Native scroll provides a target; a smoothed visual progress samples the trajectory. Scroll speed may change how quickly the target moves but must not change the geometric path the card follows.

This hypothesis is not yet proof of Lusion's internal implementation.

We must distinguish:

- **observed fact** from DevTools/runtime evidence;
- **inference** about Lusion's architecture;
- **SchoolAI design decision** made after the evidence.

## 4. Reference capture conditions

Record every capture with these fields:

- URL;
- local date/time;
- browser + exact version;
- OS;
- viewport width × height;
- device pixel ratio;
- browser zoom;
- DevTools device-emulation state;
- reduced-motion state;
- scroll input type: wheel, trackpad, touch emulation, keyboard, scrollbar;
- selected card identity: Creative / Strategy / Production / Tech;
- selected DOM node and relevant transformed parent(s).

Primary comparison viewport for the current tablet investigation:

- `841 × 878` CSS px, matching the owner's SchoolAI comparison capture.

Secondary checkpoints:

- `768 × 878` CSS px;
- one narrower viewport only after the 841 px model is understood.

Do not mix measurements from different viewport widths into one trajectory table.

## 5. Required one-card checkpoint study

Study **one card first**. Do not average four cards until one card's trajectory is understood.

Capture at least 12 ordered checkpoints:

| Checkpoint | Intended visual state |
|---|---|
| P00 | card not yet visible / first detectable transformed parent state |
| P01 | first card edge enters viewport |
| P02 | early perspective entry |
| P03 | lower-quarter travel |
| P04 | approaching lower-middle |
| P05 | lower-middle |
| P06 | approaching viewport center |
| P07 | center corridor |
| P08 | leaving center corridor |
| P09 | mostly frontal |
| P10 | upright / row-established |
| P11 | immediately after upright state |

Add extra checkpoints when a transform phase changes rapidly.

## 6. Measurement payload per checkpoint

For the selected card and every transformed ancestor up to the scene owner, record:

### Geometry

- `window.scrollY`;
- `getBoundingClientRect().top`;
- `left`;
- `right`;
- `bottom`;
- `width`;
- `height`;
- viewport-normalized card-center X;
- viewport-normalized card-center Y.

### Computed transform

- `transform` exactly as returned by computed style;
- matrix type: `none`, `matrix(...)`, or `matrix3d(...)`;
- `transform-origin`;
- `perspective`;
- `perspective-origin`;
- `transform-style`;
- `position`;
- `top/left/right/bottom` where relevant;
- `will-change`;
- overflow/clipping owner.

### Parent ownership

For each transformed ancestor record whether it contributes:

- translation;
- rotation;
- scale;
- perspective;
- sticky/fixed positioning;
- clipping;
- camera-like scene movement.

The card's own matrix alone is insufficient when the visual trajectory is partly owned by a parent.

## 7. Normalized coordinate reconstruction

After collecting raw checkpoints, derive normalized values instead of copying viewport-specific pixels directly into SchoolAI.

Use definitions such as:

```text
centerXNorm = (cardCenterX - viewportCenterX) / viewportWidth
centerYNorm = (cardCenterY - viewportCenterY) / viewportHeight
```

Where matrix decomposition is reliable, derive:

```text
translationX
translationY
translationZ
rotationX
rotationY
rotationZ
scaleX
scaleY
scaleZ
```

If decomposition is ambiguous because parent transforms participate, preserve the matrix and parent relationship rather than inventing Euler angles.

## 8. Trajectory table

Populate only from measured evidence.

| P | scrollY | centerXNorm | centerYNorm | X | Y | Z | RX | RY | RZ | Scale | Parent contribution | Evidence |
|---|---:|---:|---:|---:|---:|---:|---:|---:|---:|---:|---|---|
| P00 | | | | | | | | | | | | |
| P01 | | | | | | | | | | | | |
| P02 | | | | | | | | | | | | |
| P03 | | | | | | | | | | | | |
| P04 | | | | | | | | | | | | |
| P05 | | | | | | | | | | | | |
| P06 | | | | | | | | | | | | |
| P07 | | | | | | | | | | | | |
| P08 | | | | | | | | | | | | |
| P09 | | | | | | | | | | | | |
| P10 | | | | | | | | | | | | |
| P11 | | | | | | | | | | | | |

## 9. Top-edge geometry study

Because the current visible defect is strongly expressed by the card's top edge, record the projected top-left and top-right corners when possible.

For each checkpoint record:

- visual top-left Y;
- visual top-right Y;
- signed top-edge slope;
- whether the top edge rises toward the viewport center or away from it;
- whether the sign ever reverses during entry;
- whether left-column and right-column cards are mirror-like, identical, or driven by a shared camera/perspective owner.

Do not reduce this to a guessed `rotateY` degree until the evidence supports that interpretation.

## 10. Scroll-speed invariance test

A valid rail model should preserve path geometry across input speed.

Repeat the same card sequence under:

1. very slow scroll;
2. normal scroll;
3. fast wheel/trackpad scroll;
4. rapid down → up reverse;
5. rapid down → up → down interruption.

For each run determine:

- whether intermediate card poses are still traversed;
- whether the trajectory itself changes;
- whether only temporal lag changes;
- whether any overshoot exists;
- whether reversal follows the same path backward;
- whether position and rotation remain phase-locked.

## 11. One-driver test

Determine whether observed runtime evidence is consistent with one normalized driver.

Look for whether these channels move coherently:

```text
card center
card depth
card rotation
card scale
parent/camera transform
```

If one channel can jump independently under fast input, document it.

Do not assume the presence of GSAP, Lenis, Three.js, WebGL, or a particular framework unless runtime/source evidence proves it.

## 12. SchoolAI target model after study

Only after the reference study is sufficiently complete may a SchoolAI blueprint choose a model resembling:

```text
native scroll
    ↓
scene target progress
    ↓
existing critically damped visual progress
    ↓
card-local progress + per-card offset
    ↓
sample one responsive rail
    ↓
{x, y, z, rx, ry, rz, scale}
    ↓
paint one coherent card pose
```

Important: this is a proposed SchoolAI architecture, not a claim about Lusion's source.

## 13. Responsive reuse decision

Do not automatically use one exact rail for all six tiers.

After the 841 px study:

- determine invariant motion grammar;
- separate geometry that must adapt by tier;
- keep desktop mode 4 visually frozen until a measured replacement is proven superior;
- only then decide whether desktop and responsive variants share one rail sampler with different control points or remain separate adapters behind one motion contract.

## 14. Proof threshold before blueprint

A normative `values-responsive-card-rail` blueprint may be created only when all of these are true:

- [ ] one reference card has at least 12 ordered checkpoints;
- [ ] transformed parent ownership is identified;
- [ ] perspective and transform origins are recorded;
- [ ] top-edge geometry is understood well enough to explain its direction dynamically;
- [ ] slow-scroll trajectory is reconstructed;
- [ ] fast-scroll behavior has been compared to slow scroll;
- [ ] reverse behavior has been observed;
- [ ] position and rotation ownership can be described without contradictory drivers;
- [ ] observed facts are separated from inference;
- [ ] the SchoolAI target can be stated as a single coherent motion contract.

## 15. DevTools capture helper

After selecting the relevant element as `$0` in DevTools Elements:

```js
window.inspectMotionNode = () => {
  let el = $0;
  const rows = [];

  for (let level = 0; el && level < 10; level += 1, el = el.parentElement) {
    const style = getComputedStyle(el);
    const rect = el.getBoundingClientRect();

    rows.push({
      level,
      tag: el.tagName,
      className: String(el.className),
      top: rect.top,
      left: rect.left,
      right: rect.right,
      bottom: rect.bottom,
      width: rect.width,
      height: rect.height,
      transform: style.transform,
      transformOrigin: style.transformOrigin,
      perspective: style.perspective,
      perspectiveOrigin: style.perspectiveOrigin,
      transformStyle: style.transformStyle,
      position: style.position,
      willChange: style.willChange,
      overflow: style.overflow,
    });
  }

  console.table(rows);
  return rows;
};
```

Run `inspectMotionNode()` at every checkpoint and preserve the output with the checkpoint identifier.

## 16. Exit rule

Do not implement another responsive Values-card motion patch merely because one screenshot appears closer.

The next source mutation must be justified by the measured trajectory and must replace, not accumulate on top of, the current mode-3 experimental compensation if that experiment is disproven.

## NEXT VALID STEP

Capture P00–P11 for one Lusion Area of Expertise card at `841 × 878` using the protocol above, including transformed ancestors and slow/fast/reverse evidence.
